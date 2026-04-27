<?php

namespace App\Services\Inventory;

use App\Models\Lot;
use App\Models\Product;
use App\Models\Remission;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\Warehouse;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use RuntimeException;

/**
 * Servicio central de inventario.
 *
 * Cualquier mutación de stock (entrada, salida, ajuste, cancelación)
 * debe pasar por aquí, dentro de una transacción, generando el
 * StockMovement correspondiente para tener trazabilidad total.
 */
class StockService
{
    /**
     * Incrementa existencia (entrada por OC, ajuste positivo, traspaso IN, etc).
     */
    public function increase(
        Product $product,
        Warehouse $warehouse,
        ?Lot $lot,
        float $quantity,
        string $type = StockMovement::TYPE_IN,
        float $unitCost = 0,
        ?Model $reference = null,
        ?int $userId = null,
        ?string $reason = null,
    ): StockMovement {
        $this->guardPositive($quantity);

        return DB::transaction(function () use ($product, $warehouse, $lot, $quantity, $type, $unitCost, $reference, $userId, $reason) {
            $stock = $this->findOrCreateStockRow($product, $warehouse, $lot);

            // Costo promedio ponderado
            if ($unitCost > 0) {
                $currentValue = (float) $stock->quantity * (float) $stock->avg_cost;
                $incomingValue = $quantity * $unitCost;
                $newQty = (float) $stock->quantity + $quantity;
                $stock->avg_cost = $newQty > 0
                    ? ($currentValue + $incomingValue) / $newQty
                    : $unitCost;
            }

            $stock->quantity = (float) $stock->quantity + $quantity;
            $stock->save();

            return $this->logMovement(
                $product, $warehouse, $lot, $type,
                $quantity, $unitCost, $reference, $userId, $reason
            );
        });
    }

    /**
     * Decrementa existencia (salida por remisión, ajuste negativo, traspaso OUT).
     * Respeta pacis.inventory.allow_negative_stock.
     */
    public function decrease(
        Product $product,
        Warehouse $warehouse,
        ?Lot $lot,
        float $quantity,
        string $type = StockMovement::TYPE_OUT,
        ?Model $reference = null,
        ?int $userId = null,
        ?string $reason = null,
    ): StockMovement {
        $this->guardPositive($quantity);

        return DB::transaction(function () use ($product, $warehouse, $lot, $quantity, $type, $reference, $userId, $reason) {
            $stock = Stock::where('product_id', $product->id)
                ->where('warehouse_id', $warehouse->id)
                ->where('lot_id', $lot?->id)
                ->lockForUpdate()
                ->first();

            $allowNegative = (bool) config('pacis.inventory.allow_negative_stock', false);

            if (! $stock || ((float) $stock->quantity - $quantity) < 0) {
                if (! $allowNegative) {
                    throw new RuntimeException(sprintf(
                        'Existencia insuficiente: %s en almacén %s (disponible %s, solicitado %s).',
                        $product->reference,
                        $warehouse->code,
                        $stock?->quantity ?? 0,
                        $quantity
                    ));
                }
                $stock = $stock ?? $this->findOrCreateStockRow($product, $warehouse, $lot);
            }

            $stock->quantity = (float) $stock->quantity - $quantity;
            $stock->save();

            return $this->logMovement(
                $product, $warehouse, $lot, $type,
                -$quantity, (float) $stock->avg_cost, $reference, $userId, $reason
            );
        });
    }

    /**
     * Sugerencia FEFO: devuelve los lotes disponibles del producto en el almacén
     * ordenados por fecha de caducidad más cercana primero (nulos al final).
     *
     * @return Collection<int, Stock>
     */
    public function fefoLots(Product $product, Warehouse $warehouse): Collection
    {
        return Stock::with('lot')
            ->where('product_id', $product->id)
            ->where('warehouse_id', $warehouse->id)
            ->where('quantity', '>', 0)
            ->get()
            ->sortBy(function (Stock $s) {
                return $s->lot?->expires_at?->timestamp ?? PHP_INT_MAX;
            })
            ->values();
    }

    /**
     * Consume "quantity" unidades del producto en un almacén siguiendo FEFO.
     * Puede generar múltiples movimientos si la cantidad se reparte en varios lotes.
     *
     * @return StockMovement[]
     */
    public function consumeFefo(
        Product $product,
        Warehouse $warehouse,
        float $quantity,
        Remission $remission,
        ?int $userId = null,
    ): array {
        $this->guardPositive($quantity);

        $movements = [];
        $remaining = $quantity;

        foreach ($this->fefoLots($product, $warehouse) as $stock) {
            if ($remaining <= 0) break;
            $take = min($remaining, (float) $stock->quantity);
            if ($take <= 0) continue;

            $movements[] = $this->decrease(
                $product,
                $warehouse,
                $stock->lot,
                $take,
                StockMovement::TYPE_REMISSION,
                $remission,
                $userId,
                "Remisión {$remission->folio}"
            );
            $remaining -= $take;
        }

        if ($remaining > 0 && ! config('pacis.inventory.allow_negative_stock')) {
            throw new RuntimeException(sprintf(
                'No hay existencia suficiente para remisionar %s de %s (faltan %s).',
                $quantity, $product->reference, $remaining
            ));
        }

        return $movements;
    }

    /**
     * Reingresa al almacén los productos de una remisión que se está cancelando.
     * Se reconstruye a partir de stock_movements ligados a la remisión para
     * devolver exactamente los mismos lotes y cantidades a los mismos almacenes.
     */
    public function revertRemission(Remission $remission, int $userId, string $reason): void
    {
        if (! $remission->canBeCancelled()) {
            throw new RuntimeException('La remisión no se puede cancelar en su estado actual.');
        }

        DB::transaction(function () use ($remission, $userId, $reason) {
            $movements = StockMovement::where('reference_type', Remission::class)
                ->where('reference_id', $remission->id)
                ->where('type', StockMovement::TYPE_REMISSION)
                ->get();

            foreach ($movements as $mv) {
                $product   = Product::findOrFail($mv->product_id);
                $warehouse = Warehouse::findOrFail($mv->warehouse_id);
                $lot       = $mv->lot_id ? Lot::find($mv->lot_id) : null;
                $qty       = abs((float) $mv->quantity);

                $this->increase(
                    $product,
                    $warehouse,
                    $lot,
                    $qty,
                    StockMovement::TYPE_REMISSION_CANCEL,
                    (float) $mv->unit_cost,
                    $remission,
                    $userId,
                    "Cancelación remisión {$remission->folio}: {$reason}"
                );
            }

            $remission->update([
                'status'              => Remission::STATUS_CANCELLED,
                'cancellation_reason' => $reason,
                'cancelled_by'        => $userId,
                'cancelled_at'        => now(),
            ]);
        });
    }

    // --- privados ---

    private function findOrCreateStockRow(Product $p, Warehouse $w, ?Lot $lot): Stock
    {
        return Stock::lockForUpdate()->firstOrCreate(
            [
                'product_id'   => $p->id,
                'warehouse_id' => $w->id,
                'lot_id'       => $lot?->id,
            ],
            [
                'quantity' => 0,
                'reserved' => 0,
                'avg_cost' => 0,
            ]
        );
    }

    private function logMovement(
        Product $p, Warehouse $w, ?Lot $lot, string $type,
        float $quantity, float $unitCost, ?Model $ref, ?int $userId, ?string $reason,
    ): StockMovement {
        return StockMovement::create([
            'product_id'      => $p->id,
            'warehouse_id'    => $w->id,
            'lot_id'          => $lot?->id,
            'user_id'         => $userId,
            'type'            => $type,
            'quantity'        => $quantity,
            'unit_cost'       => $unitCost,
            'reference_type'  => $ref ? $ref::class : null,
            'reference_id'    => $ref?->getKey(),
            'reason'          => $reason,
            'moved_at'        => now(),
        ]);
    }

    private function guardPositive(float $qty): void
    {
        if ($qty <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor que 0.');
        }
    }
}

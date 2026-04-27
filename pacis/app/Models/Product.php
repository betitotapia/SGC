<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'reference','alt_key','barcode','barcode_generated',
        'description','long_description','brand','presentation',
        'category_id','unit_id',
        'requires_lot','requires_expiry','controlled','sat_product_code',
        'cost','price','tax_rate',
        'min_stock','max_stock','active','image_path',
    ];

    protected function casts(): array
    {
        return [
            'requires_lot'      => 'boolean',
            'requires_expiry'   => 'boolean',
            'controlled'        => 'boolean',
            'active'            => 'boolean',
            'barcode_generated' => 'boolean',
            'cost'              => 'decimal:4',
            'price'             => 'decimal:4',
            'tax_rate'          => 'decimal:4',
        ];
    }

    // --- Relaciones ---

    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(ProductUnit::class, 'unit_id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function suppliers(): BelongsToMany
    {
        return $this->belongsToMany(Supplier::class, 'supplier_products')
            ->withPivot(['supplier_ref','cost','lead_time_days','is_primary'])
            ->withTimestamps();
    }

    // --- Helpers ---

    public function availableStock(?int $warehouseId = null): float
    {
        $q = $this->stocks();
        if ($warehouseId) {
            $q->where('warehouse_id', $warehouseId);
        }
        return (float) ($q->sum('quantity') - $q->sum('reserved'));
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('active', true);
    }

    public function scopeSearch(Builder $q, string $term): Builder
    {
        return $q->where(function ($w) use ($term) {
            $w->where('reference', 'like', "%{$term}%")
              ->orWhere('alt_key', 'like', "%{$term}%")
              ->orWhere('barcode', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }
}

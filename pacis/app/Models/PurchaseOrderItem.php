<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id','product_id','description',
        'quantity_ordered','quantity_received','unit_cost','tax_rate',
        'subtotal','tax_amount','total','notes',
    ];

    protected function casts(): array
    {
        return [
            'quantity_ordered'  => 'decimal:4',
            'quantity_received' => 'decimal:4',
            'unit_cost'         => 'decimal:4',
            'tax_rate'          => 'decimal:4',
            'subtotal'          => 'decimal:4',
            'tax_amount'        => 'decimal:4',
            'total'             => 'decimal:4',
        ];
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getOutstandingAttribute(): float
    {
        return (float) max(0, $this->quantity_ordered - $this->quantity_received);
    }
}

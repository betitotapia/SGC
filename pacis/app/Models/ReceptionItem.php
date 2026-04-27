<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReceptionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'reception_id','purchase_order_item_id','product_id','lot_id',
        'lot_number','expires_at','quantity','unit_cost',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
            'quantity'   => 'decimal:4',
            'unit_cost'  => 'decimal:4',
        ];
    }

    public function reception(): BelongsTo
    {
        return $this->belongsTo(Reception::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }
}

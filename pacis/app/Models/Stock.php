<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id','warehouse_id','lot_id','quantity','reserved','avg_cost',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'reserved' => 'decimal:4',
            'avg_cost' => 'decimal:4',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function lot(): BelongsTo
    {
        return $this->belongsTo(Lot::class);
    }

    public function getAvailableAttribute(): float
    {
        return (float) ($this->quantity - $this->reserved);
    }
}

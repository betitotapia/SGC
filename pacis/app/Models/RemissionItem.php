<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RemissionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'remission_id','product_id','warehouse_id','lot_id',
        'description','lot_number','expires_at',
        'quantity','unit_price','discount_rate','tax_rate',
        'subtotal','discount_amount','tax_amount','total',
    ];

    protected function casts(): array
    {
        return [
            'expires_at'      => 'date',
            'quantity'        => 'decimal:4',
            'unit_price'      => 'decimal:4',
            'discount_rate'   => 'decimal:4',
            'tax_rate'        => 'decimal:4',
            'subtotal'        => 'decimal:4',
            'discount_amount' => 'decimal:4',
            'tax_amount'      => 'decimal:4',
            'total'           => 'decimal:4',
        ];
    }

    public function remission(): BelongsTo
    {
        return $this->belongsTo(Remission::class);
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
}

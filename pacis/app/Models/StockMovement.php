<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    public const TYPE_IN                = 'in';
    public const TYPE_OUT               = 'out';
    public const TYPE_PURCHASE          = 'purchase';
    public const TYPE_REMISSION         = 'remission';
    public const TYPE_REMISSION_CANCEL  = 'remission_cancel';
    public const TYPE_TRANSFER_IN       = 'transfer_in';
    public const TYPE_TRANSFER_OUT      = 'transfer_out';
    public const TYPE_ADJUSTMENT        = 'adjustment';
    public const TYPE_INITIAL           = 'initial';

    protected $fillable = [
        'product_id','warehouse_id','lot_id','user_id',
        'type','quantity','unit_cost',
        'reference_type','reference_id',
        'reason','notes','moved_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity'  => 'decimal:4',
            'unit_cost' => 'decimal:4',
            'moved_at'  => 'datetime',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}

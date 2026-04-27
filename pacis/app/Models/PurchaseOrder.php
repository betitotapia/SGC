<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PurchaseOrder extends Model
{
    use HasFactory;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_SENT      = 'sent';
    public const STATUS_PARTIAL   = 'partial';
    public const STATUS_RECEIVED  = 'received';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'folio','supplier_id','warehouse_id','user_id','status',
        'ordered_at','expected_at','closed_at',
        'subtotal','tax_total','total','currency','exchange_rate',
        'notes','cancellation_reason','cancelled_by','cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'ordered_at'    => 'date',
            'expected_at'   => 'date',
            'closed_at'     => 'date',
            'cancelled_at'  => 'datetime',
            'subtotal'      => 'decimal:4',
            'tax_total'     => 'decimal:4',
            'total'         => 'decimal:4',
            'exchange_rate' => 'decimal:6',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function receptions(): HasMany
    {
        return $this->hasMany(Reception::class);
    }

    public function isOpen(): bool
    {
        return in_array($this->status, [self::STATUS_SENT, self::STATUS_PARTIAL]);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Remission extends Model
{
    use HasFactory;

    public const STATUS_DRAFT     = 'draft';
    public const STATUS_OPEN      = 'open';
    public const STATUS_INVOICED  = 'invoiced';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'folio','customer_id','seller_id','user_id','warehouse_id','status',
        'issued_at','subtotal','discount_total','tax_total','total','currency',
        'notes','cancellation_reason','cancelled_by','cancelled_at',
        'invoice_id','invoice_uuid','invoiced_at',
    ];

    protected function casts(): array
    {
        return [
            'issued_at'      => 'date',
            'cancelled_at'   => 'datetime',
            'invoiced_at'    => 'datetime',
            'subtotal'       => 'decimal:4',
            'discount_total' => 'decimal:4',
            'tax_total'      => 'decimal:4',
            'total'          => 'decimal:4',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(RemissionItem::class);
    }

    public function canBeCancelled(): bool
    {
        return in_array($this->status, [self::STATUS_DRAFT, self::STATUS_OPEN]);
    }
}

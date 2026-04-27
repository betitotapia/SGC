<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','fiscal_profile_id','display_name','contact_name','email','phone',
        'credit_limit','credit_days','price_list','notes','seller_id','active',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'credit_days'  => 'integer',
            'active'       => 'boolean',
        ];
    }

    public function fiscalProfile(): BelongsTo
    {
        return $this->belongsTo(FiscalProfile::class);
    }

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function remissions(): HasMany
    {
        return $this->hasMany(Remission::class);
    }

    public function scopeActive($q)
    {
        return $q->where('active', true);
    }
}

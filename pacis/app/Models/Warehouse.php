<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'code','name','address','city','state','zip','phone','manager',
        'active','is_default','notes',
    ];

    protected function casts(): array
    {
        return [
            'active'     => 'boolean',
            'is_default' => 'boolean',
        ];
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    public function scopeActive($q)
    {
        return $q->where('active', true);
    }
}

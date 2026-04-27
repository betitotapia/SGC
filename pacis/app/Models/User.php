<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'active'            => 'boolean',
        ];
    }

    // --- Relaciones ---

    public function remissionsAsSeller(): HasMany
    {
        return $this->hasMany(Remission::class, 'seller_id');
    }

    public function remissionsAsOperator(): HasMany
    {
        return $this->hasMany(Remission::class, 'user_id');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'seller_id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'user_id');
    }

    // --- Helpers ---

    public function isAdmin(): bool
    {
        return $this->hasRole(config('pacis.roles.admin'));
    }

    public function isSeller(): bool
    {
        return $this->hasRole(config('pacis.roles.vendedor'));
    }

    public function isBilling(): bool
    {
        return $this->hasRole(config('pacis.roles.facturacion'));
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}

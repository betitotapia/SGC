<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class FiscalProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'rfc','legal_name','commercial_name','tax_regime_code','tax_regime_name',
        'cfdi_use','zip','street','exterior_number','interior_number','neighborhood',
        'municipality','state','country','email','phone','csf_file_path','csf_raw',
    ];

    protected function casts(): array
    {
        return [
            'csf_raw' => 'array',
        ];
    }

    public function customer(): HasOne
    {
        return $this->hasOne(Customer::class);
    }

    public function supplier(): HasOne
    {
        return $this->hasOne(Supplier::class);
    }

    public function isPerson(): bool
    {
        return strlen($this->rfc) === 13;
    }

    public function isMoral(): bool
    {
        return strlen($this->rfc) === 12;
    }
}

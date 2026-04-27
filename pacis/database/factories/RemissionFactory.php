<?php

namespace Database\Factories;

use App\Models\Remission;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class RemissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'folio'     => 'REM-'.strtoupper(Str::random(6)),
            'issued_at' => now(),
            'status'    => Remission::STATUS_DRAFT,
            'subtotal'  => 0,
            'tax_total' => 0,
            'total'     => 0,
        ];
    }
}

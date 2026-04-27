<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class LotFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lot_number' => 'L-'.fake()->numerify('######'),
            'expires_at' => now()->addMonths(fake()->numberBetween(3, 24)),
        ];
    }
}

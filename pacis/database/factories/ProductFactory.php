<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'reference'   => 'REF-'.strtoupper(Str::random(6)),
            'description' => fake()->sentence(3),
            'cost'        => fake()->randomFloat(2, 1, 500),
            'price'       => fake()->randomFloat(2, 1, 1000),
            'tax_rate'    => 0.16,
            'active'      => true,
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class WarehouseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'   => strtoupper(Str::random(6)),
            'name'   => fake()->company(),
            'active' => true,
        ];
    }
}

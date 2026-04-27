<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'code'         => 'C-'.strtoupper(Str::random(6)),
            'display_name' => fake()->company(),
            'active'       => true,
        ];
    }
}

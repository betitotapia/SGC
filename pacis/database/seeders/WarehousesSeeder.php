<?php

namespace Database\Seeders;

use App\Models\Warehouse;
use Illuminate\Database\Seeder;

class WarehousesSeeder extends Seeder
{
    public function run(): void
    {
        Warehouse::firstOrCreate(
            ['code' => 'CEDIS'],
            [
                'name'       => 'Centro de Distribución',
                'active'     => true,
                'is_default' => true,
            ],
        );
    }
}

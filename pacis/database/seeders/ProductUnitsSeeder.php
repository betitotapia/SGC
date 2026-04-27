<?php

namespace Database\Seeders;

use App\Models\ProductUnit;
use Illuminate\Database\Seeder;

class ProductUnitsSeeder extends Seeder
{
    public function run(): void
    {
        // Claves SAT comunes para insumos médicos
        $units = [
            ['code' => 'H87', 'name' => 'Pieza'],
            ['code' => 'XBX', 'name' => 'Caja'],
            ['code' => 'EA',  'name' => 'Cada uno'],
            ['code' => 'PR',  'name' => 'Par'],
            ['code' => 'BG',  'name' => 'Bolsa'],
            ['code' => 'PK',  'name' => 'Paquete'],
            ['code' => 'KGM', 'name' => 'Kilogramo'],
            ['code' => 'GRM', 'name' => 'Gramo'],
            ['code' => 'MLT', 'name' => 'Mililitro'],
            ['code' => 'LTR', 'name' => 'Litro'],
            ['code' => 'MTR', 'name' => 'Metro'],
        ];
        foreach ($units as $u) {
            ProductUnit::firstOrCreate(['code' => $u['code']], ['name' => $u['name']]);
        }
    }
}

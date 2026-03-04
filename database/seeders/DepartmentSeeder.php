<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Sistema de Gestión de Calidad',
            'Almacén',
            'Compras',
            'Logística',
            'Atención a Clientes',
            'Juridico',
            'Tecnologías de la Información y DS',
            'Administración y Finanzas',
            'Facturación',
            'Recursos Humanos',
            'Ventas',
            'Marketing',
            'Ventas Gobierno',
            'Dirección',
        ];

        foreach ($names as $name) {
            Department::firstOrCreate(['name' => $name], ['is_active' => true]);
        }
    }
}
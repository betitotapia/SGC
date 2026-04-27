<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // ------- Permisos -------
        $permissions = [
            // usuarios
            'users.view','users.create','users.update','users.delete',

            // roles
            'roles.manage',

            // almacenes
            'warehouses.view','warehouses.create','warehouses.update','warehouses.delete',

            // productos
            'products.view','products.create','products.update','products.delete',
            'products.import','products.export',

            // clientes
            'customers.view','customers.create','customers.update','customers.delete',

            // proveedores
            'suppliers.view','suppliers.create','suppliers.update','suppliers.delete',

            // inventario
            'inventory.view','inventory.adjust','inventory.transfer',

            // órdenes de compra
            'purchase_orders.view','purchase_orders.create','purchase_orders.update',
            'purchase_orders.cancel','purchase_orders.receive',

            // remisiones
            'remissions.view','remissions.create','remissions.update',
            'remissions.cancel',   // solo admin
            'remissions.invoice',  // facturación

            // facturación
            'invoices.view','invoices.create','invoices.cancel',

            // auditoría
            'audit.view',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => 'web']);
        }

        // ------- Roles -------
        $admin = Role::firstOrCreate(['name' => config('pacis.roles.admin'), 'guard_name' => 'web']);
        $admin->syncPermissions(Permission::all());

        $vendedor = Role::firstOrCreate(['name' => config('pacis.roles.vendedor'), 'guard_name' => 'web']);
        $vendedor->syncPermissions([
            'customers.view','customers.create','customers.update',
            'products.view',
            'inventory.view',
            'remissions.view','remissions.create','remissions.update',
        ]);

        $facturacion = Role::firstOrCreate(['name' => config('pacis.roles.facturacion'), 'guard_name' => 'web']);
        $facturacion->syncPermissions([
            'customers.view','customers.update',
            'products.view',
            'remissions.view','remissions.invoice',
            'invoices.view','invoices.create','invoices.cancel',
        ]);

        $almacen = Role::firstOrCreate(['name' => config('pacis.roles.almacen'), 'guard_name' => 'web']);
        $almacen->syncPermissions([
            'warehouses.view',
            'products.view',
            'inventory.view','inventory.adjust','inventory.transfer',
            'purchase_orders.view','purchase_orders.receive',
            'suppliers.view',
        ]);
    }
}

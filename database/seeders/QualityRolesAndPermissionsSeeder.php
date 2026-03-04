<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class QualityRolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $guard = 'web';

        $permissions = [
            // ✅ permisos “base” que tus middlewares ya están usando en controllers
            'quality.plans.view',       // <- requerido por QualityPlanController
            'quality.tasks.manage',     // <- requerido por QualityTaskController

            // Planes (granular)
            'quality.plans.view_all',
            'quality.plans.view_own_dept',
            'quality.plans.create',
            'quality.plans.update',
            'quality.plans.delete',

            // Tareas (granular)
            'quality.tasks.create',
            'quality.tasks.update',
            'quality.tasks.delete',

            // Evidencias
            'quality.evidences.create',
            'quality.evidences.delete',

            // Kanban / tablero
            'quality.kanban.view',
            'quality.kanban.manage',

            // Catálogos
            'quality.departments.manage',

            // Administración
            'users.manage',
            'audit.view',
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(['name' => $p, 'guard_name' => $guard]);
        }

        // Roles
        $roleColaborador = Role::firstOrCreate(['name' => 'Colaborador', 'guard_name' => $guard]);
        $roleAnalista    = Role::firstOrCreate(['name' => 'Analista de Calidad', 'guard_name' => $guard]);
        $roleCoord       = Role::firstOrCreate(['name' => 'Coordinador de Calidad', 'guard_name' => $guard]);
        $roleGerente     = Role::firstOrCreate(['name' => 'Gerente de Calidad', 'guard_name' => $guard]);
        $roleAdmin       = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => $guard]);

        // Colaborador:
        // - ✅ puede entrar a planes y tareas (porque controllers lo piden)
        // - ✅ ve solo su depto (por filtro en controller)
        // - ✅ crea tareas/evidencias
        // - ❌ no edita/elimina planes
        // - ❌ no edita/elimina tareas (eso lo controlas con Policies)
        $roleColaborador->syncPermissions([
            'quality.plans.view',
            'quality.plans.view_own_dept',

            'quality.tasks.manage',
            'quality.tasks.create',

            'quality.evidences.create',

            'quality.kanban.view',
        ]);

        // Analista de Calidad:
        $roleAnalista->syncPermissions([
            'quality.plans.view',
            'quality.plans.view_all',
            'quality.plans.create',
            'quality.plans.update',

            'quality.tasks.manage',
            'quality.tasks.create',
            'quality.tasks.update',

            'quality.evidences.create',

            'quality.kanban.view',
            'quality.kanban.manage',
        ]);

        // Coordinador de Calidad:
        $roleCoord->syncPermissions([
            'quality.plans.view',
            'quality.plans.view_all',
            'quality.plans.create',
            'quality.plans.update',
            'quality.plans.delete',

            'quality.tasks.manage',
            'quality.tasks.create',
            'quality.tasks.update',
            'quality.tasks.delete',

            'quality.evidences.create',
            'quality.evidences.delete',

            'quality.kanban.view',
            'quality.kanban.manage',

            'quality.departments.manage',
            'audit.view',
        ]);

        // Gerente de Calidad:
        $roleGerente->syncPermissions([
            'quality.plans.view',
            'quality.plans.view_all',
            'quality.plans.create',
            'quality.plans.update',
            'quality.plans.delete',

            'quality.tasks.manage',
            'quality.tasks.create',
            'quality.tasks.update',
            'quality.tasks.delete',

            'quality.evidences.create',
            'quality.evidences.delete',

            'quality.kanban.view',
            'quality.kanban.manage',

            'quality.departments.manage',

            'users.manage',
            'audit.view',
        ]);

        // Admin: todo
        $roleAdmin->syncPermissions($permissions);
    }
}
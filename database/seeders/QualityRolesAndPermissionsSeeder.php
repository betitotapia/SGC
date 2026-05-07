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
            // Permisos base usados por controllers
            'quality.plans.view',
            'quality.tasks.manage',

            // Planes
            'quality.plans.view_all',
            'quality.plans.view_own_dept',
            'quality.plans.create',
            'quality.plans.update',
            'quality.plans.delete',

            // Tareas
            'quality.tasks.create',
            'quality.tasks.update',
            'quality.tasks.delete',

            // Evidencias
            'quality.evidences.create',
            'quality.evidences.delete',

            // Kanban
            'quality.kanban.view',
            'quality.kanban.manage',

            // Catálogos
            'quality.departments.manage',

            // Control Documental
            'documents.view',
            'documents.view_all',
            'documents.create',
            'documents.update',
            'documents.delete',
            'documents.manage_approvals',
            'documents.approve', // Firmar como autorizador (Dirección)

            // Administración
            'users.manage',
            'audit.view',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => $guard,
            ]);
        }

        $roleColaborador = Role::firstOrCreate(['name' => 'Colaborador', 'guard_name' => $guard]);
        $roleAnalista    = Role::firstOrCreate(['name' => 'Analista de Calidad', 'guard_name' => $guard]);
        $roleCoord       = Role::firstOrCreate(['name' => 'Coordinador de Calidad', 'guard_name' => $guard]);
        $roleGerente     = Role::firstOrCreate(['name' => 'Gerente de Calidad', 'guard_name' => $guard]);
        $roleDirector    = Role::firstOrCreate(['name' => 'Director', 'guard_name' => $guard]);
        $roleAdmin       = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => $guard]);

        // Colaborador:
        // ve solo su depto, crea tareas y evidencias, no toca planes
        $roleColaborador->syncPermissions([
            'quality.plans.view',
            'quality.plans.view_own_dept',

            'quality.tasks.manage',
            'quality.tasks.create',

            'quality.evidences.create',

            'quality.kanban.view',

            'documents.view',
        ]);

        // Analista
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

            'documents.view',
            'documents.view_all',
            'documents.create',
            'documents.update',
        ]);

        // Coordinador
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

            'documents.view',
            'documents.view_all',
            'documents.create',
            'documents.update',
            'documents.delete',
            'documents.manage_approvals',
        ]);

        // Gerente
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

            'documents.view',
            'documents.view_all',
            'documents.create',
            'documents.update',
            'documents.delete',
            'documents.manage_approvals',
        ]);

        // Director: solo ve y firma documentos que se le asignen
        $roleDirector->syncPermissions([
            'documents.view',
            'documents.view_all',
            'documents.approve',
        ]);

        // Admin
        $roleAdmin->syncPermissions($permissions);
    }
}
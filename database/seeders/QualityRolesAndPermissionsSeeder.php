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
        // Limpia cache de spatie
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        // =========================
        // PERMISOS DEL SISTEMA
        // =========================
        $permissions = [
            // Planes
            'quality.plans.view_all',        // Ver todos los planes (Calidad)
            'quality.plans.view_own_dept',   // Ver solo planes de su departamento (Colaboradores)
            'quality.plans.create',
            'quality.plans.update',
            'quality.plans.delete',

            // Tareas
            'quality.tasks.create',          // Crear tareas (Colaborador y Calidad)
            'quality.tasks.update',          // Editar tareas (Calidad)
            'quality.tasks.delete',          // Eliminar tareas (Coordinación/Gerencia)

            // Evidencias
            'quality.evidences.create',      // Subir evidencias (Colaborador y Calidad)
            'quality.evidences.delete',      // Eliminar evidencias (Coordinación/Gerencia) opcional

            // Kanban / tablero
            'quality.kanban.view',
            'quality.kanban.manage',         // mover columnas, etc. (Calidad)

            // Catálogos
            'quality.departments.manage',    // CRUD departamentos (Coordinación/Gerencia)

            // Administración
            'users.manage',                  // CRUD usuarios (Gerencia o Admin)
            'audit.view',                    // ver logs de auditoría (opcional)
        ];

        foreach ($permissions as $p) {
            Permission::firstOrCreate(
                ['name' => $p, 'guard_name' => $guard]
            );
        }

        // =========================
        // ROLES
        // =========================
        $roleColaborador = Role::firstOrCreate(['name' => 'Colaborador', 'guard_name' => $guard]);

        $roleAnalista = Role::firstOrCreate(['name' => 'Analista de Calidad', 'guard_name' => $guard]);
        $roleCoord    = Role::firstOrCreate(['name' => 'Coordinador de Calidad', 'guard_name' => $guard]);
        $roleGerente  = Role::firstOrCreate(['name' => 'Gerente de Calidad', 'guard_name' => $guard]);

        // (Opcional) Admin general
        $roleAdmin    = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => $guard]);

        // =========================
        // ASIGNACIÓN DE PERMISOS POR ROL
        // =========================

        // Colaborador (todas las áreas: TI, Compras, Almacén, etc.)
        // - Solo ve planes de su depto
        // - NO crea/edita/elimina planes
        // - SÍ crea tareas + sube evidencias
        // - NO edita/elimina tareas
        $roleColaborador->syncPermissions([
            'quality.plans.view_own_dept',
            'quality.tasks.create',
            'quality.evidences.create',
            'quality.kanban.view',
        ]);

        // Analista de Calidad:
        // - Ve todos los planes
        // - Crea/edita planes
        // - Crea/edita tareas
        // - No elimina planes ni tareas
        $roleAnalista->syncPermissions([
            'quality.plans.view_all',
            'quality.plans.create',
            'quality.plans.update',

            'quality.tasks.create',
            'quality.tasks.update',

            'quality.evidences.create',

            'quality.kanban.view',
            'quality.kanban.manage',
        ]);

        // Coordinador de Calidad:
        // - Todo lo del analista
        // - Puede eliminar tareas y planes
        // - Puede administrar departamentos
        // - Puede borrar evidencias (opcional)
        $roleCoord->syncPermissions([
            'quality.plans.view_all',
            'quality.plans.create',
            'quality.plans.update',
            'quality.plans.delete',

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
        // - Todo (incluye usuarios)
        $roleGerente->syncPermissions([
            'quality.plans.view_all',
            'quality.plans.create',
            'quality.plans.update',
            'quality.plans.delete',

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

        // Admin (si decides usarlo)
        $roleAdmin->syncPermissions($permissions);
    }
}
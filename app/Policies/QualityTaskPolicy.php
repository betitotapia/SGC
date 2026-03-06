<?php

namespace App\Policies;

use App\Models\QualityTask;
use App\Models\User;

class QualityTaskPolicy
{
    /**
     * Crear tareas:
     * - Colaborador
     * - Analista
     * - Coordinador
     * - Gerente
     */
    public function create(User $user): bool
    {
        return $user->can('quality.tasks.create');
    }

    /**
     * Editar tareas:
     * - Solo Analista / Coordinador / Gerente
     * - Colaborador NO
     */
    public function update(User $user, QualityTask $task): bool
    {
        if ($user->hasAnyRole([
            'Analista de Calidad',
            'Coordinador de Calidad',
            'Gerente de Calidad',
            'Admin',
        ]) && $user->can('quality.tasks.update')) {
            return true;
        }

        return false;
    }

    /**
     * Eliminar tareas:
     * - Solo Coordinador / Gerente / Admin
     */
    public function delete(User $user, QualityTask $task): bool
    {
        if ($user->hasAnyRole([
            'Coordinador de Calidad',
            'Gerente de Calidad',
            'Admin',
        ]) && $user->can('quality.tasks.delete')) {
            return true;
        }

        return false;
    }
}
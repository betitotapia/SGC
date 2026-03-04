<?php

namespace App\Policies;

use App\Models\QualityTask;
use App\Models\User;

class QualityTaskPolicy
{
    public function create(User $user): bool
    {
        return $user->can('quality.tasks.create');
    }

    public function update(User $user, QualityTask $task): bool
    {
        // Calidad puede editar
        if ($user->isQuality() && $user->can('quality.tasks.update')) {
            return true;
        }

        // Colaborador NO puede editar después de crear
        return false;
    }

    public function delete(User $user, QualityTask $task): bool
    {
        // Solo coordinación o gerencia (según permisos)
        return $user->can('quality.tasks.delete');
    }
}
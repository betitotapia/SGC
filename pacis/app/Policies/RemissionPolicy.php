<?php

namespace App\Policies;

use App\Models\Remission;
use App\Models\User;

/**
 * Políticas específicas de Remisión.
 *
 * Regla de negocio clave:
 *  - Solo los administradores pueden cancelar remisiones.
 *  - El operador que capturó o el vendedor asignado pueden ver/editar
 *    remisiones en estado draft/open; los admins pueden todo.
 */
class RemissionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('remissions.view');
    }

    public function view(User $user, Remission $remission): bool
    {
        if ($user->isAdmin()) return true;
        if (! $user->can('remissions.view')) return false;

        return $remission->user_id === $user->id
            || $remission->seller_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->can('remissions.create');
    }

    public function update(User $user, Remission $remission): bool
    {
        if ($user->isAdmin()) return true;
        if (! $user->can('remissions.update')) return false;
        if (! in_array($remission->status, [Remission::STATUS_DRAFT, Remission::STATUS_OPEN])) return false;

        return $remission->user_id === $user->id
            || $remission->seller_id === $user->id;
    }

    public function cancel(User $user, Remission $remission): bool
    {
        // ⚠️ Solo admins pueden cancelar (requisito explícito del sistema)
        return $user->isAdmin() && $remission->canBeCancelled();
    }

    public function invoice(User $user, Remission $remission): bool
    {
        return $user->can('remissions.invoice')
            && $remission->status === Remission::STATUS_OPEN;
    }
}

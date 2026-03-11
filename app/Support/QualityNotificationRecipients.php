<?php

namespace App\Support;

use App\Models\QualityPlan;
use App\Models\QualityTask;
use App\Models\User;
use Illuminate\Support\Collection;

class QualityNotificationRecipients
{
    public static function usersForPlan(QualityPlan $plan): Collection
    {
        $users = collect();

        // Responsable de soporte
        if ($plan->owner_id) {
            $ownerSupport = User::find($plan->owner_id);
            if ($ownerSupport) {
                $users->push($ownerSupport);
            }
        }

        // Propietario si su email pertenece a un usuario del sistema
        if (!empty($plan->owner_email)) {
            $ownerUser = User::where('email', $plan->owner_email)->first();
            if ($ownerUser) {
                $users->push($ownerUser);
            }
        }

        // Usuarios de calidad
        $qualityUsers = User::role([
            'Analista de Calidad',
            'Coordinador de Calidad',
            'Gerente de Calidad',
            'Admin',
        ])->get();

        $users = $users->merge($qualityUsers);

        return $users->unique('id')->values();
    }

    public static function usersForTask(QualityPlan $plan, ?QualityTask $task = null): Collection
    {
        $users = self::usersForPlan($plan);

        // Asignado a la tarea
        if ($task && $task->assignee_id) {
            $assignee = User::find($task->assignee_id);
            if ($assignee) {
                $users->push($assignee);
            }
        }

        return $users->unique('id')->values();
    }

    public static function emailsForPlan(QualityPlan $plan): array
    {
        $plan->loadMissing('owner');

        $emails = [];

        if (!empty($plan->owner_email)) {
            $emails[] = $plan->owner_email;
        }

        if ($plan->owner && !empty($plan->owner->email)) {
            $emails[] = $plan->owner->email;
        }

        if (config('quality.general_email')) {
            $emails[] = config('quality.general_email');
        }

        return array_values(array_unique(array_filter($emails)));
    }
}
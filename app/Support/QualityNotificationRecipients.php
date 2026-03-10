<?php

namespace App\Support;

use App\Models\QualityPlan;
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

        // Usuarios de calidad dentro del sistema
        $qualityUsers = User::role([
            'Analista de Calidad',
            'Coordinador de Calidad',
            'Gerente de Calidad',
            'Admin',
        ])->get();

        $users = $users->merge($qualityUsers);

        return $users->unique('id')->values();
    }

    public static function emailsForPlan(QualityPlan $plan): array
    {
        $plan->loadMissing('owner');

        $emails = [];

        // Correo del responsable propietario
        if (!empty($plan->owner_email)) {
            $emails[] = $plan->owner_email;
        }

        // Correo del responsable de soporte
        if ($plan->owner && !empty($plan->owner->email)) {
            $emails[] = $plan->owner->email;
        }

        // Correo general de calidad
        if (config('quality.general_email')) {
            $emails[] = config('quality.general_email');
        }

        return array_values(array_unique(array_filter($emails)));
    }
}
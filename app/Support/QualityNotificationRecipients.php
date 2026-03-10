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

        if ($plan->owner_id) {
            $ownerSupport = User::find($plan->owner_id);
            if ($ownerSupport) {
                $users->push($ownerSupport);
            }
        }

        // Responsable propietario por correo
        if (!empty($plan->owner_email)) {
            $ownerByEmail = User::where('email', $plan->owner_email)->first();
            if ($ownerByEmail) {
                $users->push($ownerByEmail);
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

    public static function emailsForPlan(QualityPlan $plan): array
    {
        $emails = [];

        if (!empty($plan->owner_email)) {
            $emails[] = $plan->owner_email;
        }

        if ($plan->owner && !empty($plan->owner->email)) {
            $emails[] = $plan->owner->email;
        }

        $emails[] = config('quality.general_email');

        return array_values(array_unique(array_filter($emails)));
    }
}

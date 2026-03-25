<?php

namespace App\Notifications;

use App\Models\QualityPlan;

class PlanCreatedNotification extends QualityEventNotification
 {
    public function __construct(QualityPlan $plan)
    {
        parent::__construct(
            'Plan creado',
            'Se creó un nuevo plan de acción.',
            $plan
        );
    }
}
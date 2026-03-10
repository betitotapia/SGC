<?php

namespace App\Notifications;

use App\Models\QualityPlan;

class PlanClosedNotification extends QualityEventNotification
{
    public function __construct(QualityPlan $plan)
    {
        parent::__construct(
            'Plan cerrado',
            'El plan de acción fue cerrado.',
            $plan
        );
    }
}

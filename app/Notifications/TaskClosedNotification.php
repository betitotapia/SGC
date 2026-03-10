<?php

namespace App\Notifications;

use App\Models\QualityPlan;
use App\Models\QualityTask;

class TaskClosedNotification extends QualityEventNotification
{
    public function __construct(QualityPlan $plan, QualityTask $task)
    {
        parent::__construct(
            'Tarea cerrada',
            'La tarea fue cerrada.',
            $plan,
            $task
        );
    }
}
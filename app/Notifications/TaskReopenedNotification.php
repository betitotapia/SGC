<?php

namespace App\Notifications;

use App\Models\QualityPlan;
use App\Models\QualityTask;

class TaskReopenedNotification extends QualityEventNotification
{
    public function __construct(QualityPlan $plan, QualityTask $task)
    {
        parent::__construct(
            'Tarea reabierta',
            'La tarea fue reabierta.',
            $plan,
            $task
        );
    }
}
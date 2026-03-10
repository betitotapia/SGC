<?php

namespace App\Notifications;

use App\Models\QualityPlan;
use App\Models\QualityTask;

class TaskCreatedNotification extends QualityEventNotification
{
    public function __construct(QualityPlan $plan, QualityTask $task)
    {
        parent::__construct(
            'Tarea creada',
            'Se creó una nueva tarea en el plan.',
            $plan,
            $task
        );
    }
}
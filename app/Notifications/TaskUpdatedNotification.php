<?php

namespace App\Notifications;

use App\Models\QualityPlan;
use App\Models\QualityTask;

class TaskUpdatedNotification extends QualityEventNotification
{
    public function __construct(QualityPlan $plan, QualityTask $task)
    {
        parent::__construct(
            'Tarea actualizada',
            'La tarea fue actualizada.',
            $plan,
            $task
        );
    }
}
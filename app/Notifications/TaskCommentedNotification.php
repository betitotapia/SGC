<?php

namespace App\Notifications;

use App\Models\QualityPlan;
use App\Models\QualityTask;

class TaskCommentedNotification extends QualityEventNotification
{
    public function __construct(QualityPlan $plan, QualityTask $task)
    {
        parent::__construct(
            'Comentario de revisión',
            'Se agregó un comentario de revisión a la tarea.',
            $plan,
            $task
        );
    }
}
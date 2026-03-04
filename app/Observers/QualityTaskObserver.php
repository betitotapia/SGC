<?php

namespace App\Observers;

use App\Models\QualityTask;

class QualityTaskObserver
{
    public function saved(QualityTask $task): void
    {
        $task->plan?->recalcProgress();
    }

    public function deleted(QualityTask $task): void
    {
        $task->plan?->recalcProgress();
    }
}

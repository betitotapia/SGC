<?php
namespace App\Observers;

use App\Models\Task;

class TaskObserver
{
    public function saved(Task $task): void
    {
        // si cambió estatus o se guardó una tarea, recalcular
        $task->plan?->recalcProgress();
    }

    public function deleted(Task $task): void
    {
        $task->plan?->recalcProgress();
    }
}
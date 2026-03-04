<?php

namespace App\Console\Commands;

use App\Models\QualityTask;
use App\Notifications\QualityTaskDueNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use Spatie\Permission\Models\Role;

class NotifyDueQualityTasks extends Command
{
    protected $signature = 'quality:notify-due-tasks {--days=1 : Notificar también tareas que vencen en N días}';
    protected $description = 'Notifica tareas por fecha compromiso (hoy/mañana/vencidas) a asignado y roles de Calidad.';

    public function handle(): int
    {
        $today = Carbon::today();
        $daysAhead = (int) $this->option('days');
        $tomorrow = $today->copy()->addDays($daysAhead);

        // Candidatas: no cerradas y con fecha
        $tasks = QualityTask::query()
            ->whereIn('status', [QualityTask::STATUS_OPEN, QualityTask::STATUS_IN_PROGRESS])
            ->whereNotNull('commitment_date')
            ->with(['plan', 'assignee'])
            ->get();

        $count = 0;

        foreach ($tasks as $task) {
            $due = $task->commitment_date;
            if (!$due) continue;

            $when = null;
            if ($due->lt($today)) $when = 'VENCIDA';
            elseif ($due->equalTo($today)) $when = 'HOY';
            elseif ($due->equalTo($tomorrow)) $when = 'MAÑANA';

            if (!$when) continue;

            // Notificar asignado
            if ($task->assignee) {
                $task->assignee->notify(new QualityTaskDueNotification($task, $when));
                $count++;
            }

            // Notificar roles de Calidad (todos) - evita duplicar si el asignado también es de calidad (no es crítico)
            $qualityRoleNames = ['Analista de Calidad','Coordinador de Calidad','Gerente de Calidad'];
            $usersToNotify = collect();

            foreach ($qualityRoleNames as $rName) {
                $role = Role::where('name', $rName)->first();
                if ($role && method_exists($role, 'users')) {
                    $usersToNotify = $usersToNotify->merge($role->users()->get());
                }
            }

            if ($usersToNotify->isNotEmpty()) {
                Notification::send($usersToNotify->unique('id'), new QualityTaskDueNotification($task, $when));
                $count += $usersToNotify->unique('id')->count();
            }
        }

        $this->info("Notificaciones enviadas: {$count}");
        return self::SUCCESS;
    }
}

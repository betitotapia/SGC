<?php

namespace App\Notifications;

use App\Models\QualityTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QualityTaskDueNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public QualityTask $task,
        public string $whenLabel // "HOY" | "MAÑANA" | "VENCIDA"
    ) {}

    public function via(object $notifiable): array
    {
        // Puedes quitar 'mail' si no usas correo
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $plan = $this->task->plan;

        return (new MailMessage)
            ->subject("Tarea {$this->whenLabel}: {$this->task->title}")
            ->line("Plan: {$plan->folio}")
            ->line("Tarea: {$this->task->title}")
            ->line("Fecha compromiso: ".optional($this->task->commitment_date)->format('Y-m-d'))
            ->action('Ver Plan', url(route('quality.plans.show', $plan)));
    }

    public function toArray(object $notifiable): array
    {
        $plan = $this->task->plan;

        return [
            'type' => 'quality_task_due',
            'when' => $this->whenLabel,
            'plan_id' => $plan->id,
            'plan_folio' => $plan->folio,
            'task_id' => $this->task->id,
            'task_title' => $this->task->title,
            'commitment_date' => optional($this->task->commitment_date)->format('Y-m-d'),
            'url' => route('quality.plans.show', $plan),
        ];
    }
}

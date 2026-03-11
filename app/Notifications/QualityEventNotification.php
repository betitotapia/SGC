<?php

namespace App\Notifications;

use App\Models\QualityPlan;
use App\Models\QualityTask;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class QualityEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $eventTitle,
        public string $eventMessage,
        public QualityPlan $plan,
        public ?QualityTask $task = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail', WebPushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->eventTitle . ' | Plan ' . $this->plan->folio)
            ->line($this->eventMessage)
            ->line('Plan: ' . $this->plan->folio)
            ->line('Departamento: ' . optional($this->plan->department)->name)
            ->line('Responsable: ' . $this->plan->owner_name);

        if ($this->task) {
            $mail->line('Tarea: ' . $this->task->title)
                 ->line('Estatus de tarea: ' . $this->task->status);
        }

        return $mail->action('Ver plan', route('quality.plans.show', $this->plan));
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->eventTitle,
            'message' => $this->eventMessage,
            'plan_id' => $this->plan->id,
            'plan_folio' => $this->plan->folio,
            'task_id' => $this->task?->id,
            'task_title' => $this->task?->title,
            'url' => route('quality.plans.show', $this->plan),
        ];
    }

    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        $body = $this->eventMessage;

        if ($this->task) {
            $body .= ' Tarea: '.$this->task->title;
        }

        return (new WebPushMessage)
            ->title($this->eventTitle.' | '.$this->plan->folio)
            ->icon('/img/icons/icon-192x192.png')
            ->badge('/img/icons/badge-72x72.png')
            ->body($body)
            ->data([
                'url' => route('quality.plans.show', $this->plan),
                'plan_id' => $this->plan->id,
                'task_id' => $this->task?->id,
            ])
            ->action('Ver plan', 'open_url');
    }
}
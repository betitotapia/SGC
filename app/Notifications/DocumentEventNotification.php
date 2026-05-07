<?php

namespace App\Notifications;

use App\Models\Document;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class DocumentEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string   $eventTitle,
        public string   $eventMessage,
        public Document $document,
        public string   $actionUrl = '',
        public string   $actionLabel = 'Ver documento'
    ) {
        $this->actionUrl = $actionUrl ?: route('quality.documents.show', $document);
    }

    public function via(object $notifiable): array
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return ['mail'];
        }

        return ['database', 'mail', WebPushChannel::class];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject($this->eventTitle . ' — ' . $this->document->folio)
            ->line($this->eventMessage)
            ->line('Documento: ' . $this->document->folio . ' — ' . $this->document->title)
            ->line('Departamento: ' . optional($this->document->department)->name)
            ->line('Tipo: ' . $this->document->typeLabel())
            ->action($this->actionLabel, $this->actionUrl);
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title'          => $this->eventTitle,
            'message'        => $this->eventMessage,
            'document_id'    => $this->document->id,
            'document_folio' => $this->document->folio,
            'url'            => $this->actionUrl,
        ];
    }

    public function toWebPush(object $notifiable, object $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->eventTitle . ' | ' . $this->document->folio)
            ->icon('/img/icons/icon-192x192.png')
            ->badge('/img/icons/badge-72x72.png')
            ->body($this->eventMessage)
            ->data(['url' => $this->actionUrl, 'document_id' => $this->document->id])
            ->action($this->actionLabel, 'open_url');
    }
}

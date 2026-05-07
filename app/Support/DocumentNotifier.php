<?php

namespace App\Support;

use App\Models\Document;
use App\Models\DocumentApproval;
use App\Models\User;
use App\Notifications\DocumentEventNotification;
use Illuminate\Support\Facades\Notification;

class DocumentNotifier
{
    public static function submitted(Document $document, DocumentApproval $nextApproval): void
    {
        $signer = $nextApproval->user;

        if (! $signer) {
            return;
        }

        Notification::send($signer, new DocumentEventNotification(
            'Firma pendiente',
            "El documento {$document->folio} requiere tu firma como {$nextApproval->roleLabel()}.",
            $document
        ));
    }

    public static function nextSignerNotified(Document $document, DocumentApproval $nextApproval): void
    {
        $signer = $nextApproval->user;

        if (! $signer) {
            return;
        }

        Notification::send($signer, new DocumentEventNotification(
            'Firma pendiente',
            "Es tu turno de firmar el documento {$document->folio} como {$nextApproval->roleLabel()}.",
            $document
        ));
    }

    public static function published(Document $document): void
    {
        // Notificar a todos los usuarios del departamento
        $users = User::where('department_id', $document->department_id)
            ->where('is_active', true)
            ->get();

        Notification::send($users, new DocumentEventNotification(
            'Documento publicado',
            "Se publicó la versión {$document->currentVersion?->version_number} del documento {$document->folio}: {$document->title}.",
            $document
        ));
    }

    public static function rejected(Document $document, DocumentApproval $rejectedApproval): void
    {
        // Notificar al creador del documento
        $creator = $document->creator;

        if (! $creator) {
            return;
        }

        Notification::send($creator, new DocumentEventNotification(
            'Documento rechazado',
            "{$rejectedApproval->roleLabel()} rechazó el documento {$document->folio}. Motivo: {$rejectedApproval->comments}",
            $document
        ));
    }
}

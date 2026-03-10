<?php

namespace App\Support;

use App\Models\QualityPlan;
use App\Models\QualityTask;
use App\Notifications\PlanClosedNotification;
use App\Notifications\PlanCreatedNotification;
use App\Notifications\TaskClosedNotification;
use App\Notifications\TaskCommentedNotification;
use App\Notifications\TaskCreatedNotification;
use App\Notifications\TaskReopenedNotification;
use App\Notifications\TaskUpdatedNotification;
use Illuminate\Support\Facades\Notification;

class QualityNotifier
{
    protected static function notifyPlan(QualityPlan $plan, object $notification): void
    {
        // Asegura relaciones necesarias
        $plan->loadMissing('owner', 'department');

        // 1) Usuarios internos del sistema: campana + correo + database
        $users = QualityNotificationRecipients::usersForPlan($plan);

        Notification::send($users, $notification);

        // Correos ya cubiertos por usuarios internos
        $userEmails = $users->pluck('email')
            ->filter()
            ->map(fn ($email) => mb_strtolower(trim($email)))
            ->unique()
            ->values()
            ->all();

        // 2) Correos externos / adicionales: solo mail
        $extraEmails = collect(QualityNotificationRecipients::emailsForPlan($plan))
            ->filter()
            ->map(fn ($email) => mb_strtolower(trim($email)))
            ->reject(fn ($email) => in_array($email, $userEmails, true))
            ->unique()
            ->values();

        foreach ($extraEmails as $email) {
            Notification::route('mail', $email)->notify(clone $notification);
        }
    }

    public static function planCreated(QualityPlan $plan): void
    {
        self::notifyPlan($plan, new PlanCreatedNotification($plan));
    }

    public static function planClosed(QualityPlan $plan): void
    {
        self::notifyPlan($plan, new PlanClosedNotification($plan));
    }

    public static function taskCreated(QualityPlan $plan, QualityTask $task): void
    {
        self::notifyPlan($plan, new TaskCreatedNotification($plan, $task));
    }

    public static function taskUpdated(QualityPlan $plan, QualityTask $task): void
    {
        self::notifyPlan($plan, new TaskUpdatedNotification($plan, $task));
    }

    public static function taskClosed(QualityPlan $plan, QualityTask $task): void
    {
        self::notifyPlan($plan, new TaskClosedNotification($plan, $task));
    }

    public static function taskReopened(QualityPlan $plan, QualityTask $task): void
    {
        self::notifyPlan($plan, new TaskReopenedNotification($plan, $task));
    }

    public static function taskCommented(QualityPlan $plan, QualityTask $task): void
    {
        self::notifyPlan($plan, new TaskCommentedNotification($plan, $task));
    }
}
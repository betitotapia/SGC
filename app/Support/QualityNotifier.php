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
        $plan->loadMissing('owner', 'department');

        $users = QualityNotificationRecipients::usersForPlan($plan);

        Notification::send($users, $notification);

        $userEmails = $users->pluck('email')
            ->filter()
            ->map(fn ($email) => mb_strtolower(trim($email)))
            ->unique()
            ->values()
            ->all();

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

    protected static function notifyTask(QualityPlan $plan, QualityTask $task, object $notification): void
    {
        $plan->loadMissing('owner', 'department');
        $task->loadMissing('assignee');

        $users = QualityNotificationRecipients::usersForTask($plan, $task);

        Notification::send($users, $notification);

        $userEmails = $users->pluck('email')
            ->filter()
            ->map(fn ($email) => mb_strtolower(trim($email)))
            ->unique()
            ->values()
            ->all();

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
        self::notifyTask($plan, $task, new TaskCreatedNotification($plan, $task));
    }

    public static function taskUpdated(QualityPlan $plan, QualityTask $task): void
    {
        self::notifyTask($plan, $task, new TaskUpdatedNotification($plan, $task));
    }

    public static function taskClosed(QualityPlan $plan, QualityTask $task): void
    {
        self::notifyTask($plan, $task, new TaskClosedNotification($plan, $task));
    }

    public static function taskReopened(QualityPlan $plan, QualityTask $task): void
    {
        self::notifyTask($plan, $task, new TaskReopenedNotification($plan, $task));
    }

    public static function taskCommented(QualityPlan $plan, QualityTask $task): void
    {
        self::notifyTask($plan, $task, new TaskCommentedNotification($plan, $task));
    }
}
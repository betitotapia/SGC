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
    public static function planCreated(QualityPlan $plan): void
    {
        Notification::send(
            QualityNotificationRecipients::usersForPlan($plan),
            new PlanCreatedNotification($plan)
        );
    }

    public static function planClosed(QualityPlan $plan): void
    {
        Notification::send(
            QualityNotificationRecipients::usersForPlan($plan),
            new PlanClosedNotification($plan)
        );
    }

    public static function taskCreated(QualityPlan $plan, QualityTask $task): void
    {
        Notification::send(
            QualityNotificationRecipients::usersForPlan($plan),
            new TaskCreatedNotification($plan, $task)
        );
    }

    public static function taskUpdated(QualityPlan $plan, QualityTask $task): void
    {
        Notification::send(
            QualityNotificationRecipients::usersForPlan($plan),
            new TaskUpdatedNotification($plan, $task)
        );
    }

    public static function taskClosed(QualityPlan $plan, QualityTask $task): void
    {
        Notification::send(
            QualityNotificationRecipients::usersForPlan($plan),
            new TaskClosedNotification($plan, $task)
        );
    }

    public static function taskReopened(QualityPlan $plan, QualityTask $task): void
    {
        Notification::send(
            QualityNotificationRecipients::usersForPlan($plan),
            new TaskReopenedNotification($plan, $task)
        );
    }

    public static function taskCommented(QualityPlan $plan, QualityTask $task): void
    {
        Notification::send(
            QualityNotificationRecipients::usersForPlan($plan),
            new TaskCommentedNotification($plan, $task)
        );
    }
}
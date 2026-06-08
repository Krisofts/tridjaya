<?php

namespace App\CRM\Observers;

use App\CRM\Enums\LeadActivityType;
use App\CRM\Models\LeadReminder;
use App\CRM\Services\LeadActivityService;

class LeadReminderObserver
{
    public function __construct(
        protected LeadActivityService $activityService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATED
    |--------------------------------------------------------------------------
    */

    public function created(LeadReminder $reminder): void
    {
        $this->activityService->create(
            lead: $reminder->lead,
            type: LeadActivityType::REMINDER_CREATED,
            description: "Reminder dibuat: {$reminder->title} ({$reminder->type})",
            title: 'Reminder Created'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATED
    |--------------------------------------------------------------------------
    */

    public function updated(LeadReminder $reminder): void
    {
        $changes = $reminder->getChanges();
        $original = $reminder->getOriginal();

        /*
        | STATUS CHANGED
        */
        if (array_key_exists('status', $changes)) {

            $this->activityService->create(
                lead: $reminder->lead,
                type: LeadActivityType::REMINDER_STATUS_CHANGED,
                description: "Reminder status diubah dari {$original['status']} ke {$changes['status']}",
                title: 'Reminder Status Changed'
            );
        }

        /*
        | ASSIGNED CHANGED
        */
        if (array_key_exists('assigned_to', $changes)) {

            $this->activityService->create(
                lead: $reminder->lead,
                type: LeadActivityType::REMINDER_ASSIGNED,
                description: "Reminder ditugaskan ke user ID {$changes['assigned_to']}",
                title: 'Reminder Assigned'
            );
        }

        /*
        | RESCHEDULED
        */
        if (array_key_exists('remind_at', $changes)) {

            $this->activityService->create(
                lead: $reminder->lead,
                type: LeadActivityType::REMINDER_RESCHEDULED,
                description: "Reminder dijadwalkan ulang ke {$changes['remind_at']}",
                title: 'Reminder Rescheduled'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETED
    |--------------------------------------------------------------------------
    */

    public function deleted(LeadReminder $reminder): void
    {
        $this->activityService->create(
            lead: $reminder->lead,
            type: LeadActivityType::REMINDER_DELETED,
            description: "Reminder dihapus: {$reminder->title}",
            title: 'Reminder Deleted'
        );
    }
}
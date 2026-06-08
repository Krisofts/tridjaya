<?php

namespace App\CRM\Observers;

use App\CRM\Models\Lead;
use App\CRM\Services\AutoReminderService;
use App\CRM\Services\AutoTaskService;
use App\CRM\Services\LeadActivityService;

class LeadObserver
{
    public function __construct(
        protected LeadActivityService $activityService,
        protected AutoReminderService $autoReminderService,
        protected AutoTaskService $autoTaskService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATED
    |--------------------------------------------------------------------------
    */
    public function created(Lead $lead): void
    {
        $this->activityService->leadCreated($lead);

        $this->autoReminderService->createForLead($lead);
        $this->autoTaskService->createForLead($lead);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATED
    |--------------------------------------------------------------------------
    */
    public function updated(Lead $lead): void
    {
        $lead->refresh(); // pastikan data terbaru

        /*
        |--------------------------------------------------------------------------
        | STATUS CHANGED
        |--------------------------------------------------------------------------
        */
        if ($lead->wasChanged('status')) {

            $this->activityService->statusChanged(
                $lead,
                $lead->getOriginal('status'),
                $lead->status
            );

            // auto regenerate workflow
            $this->autoReminderService->replaceForLead($lead);
            $this->autoTaskService->replaceForLead($lead);
        }

        /*
        |--------------------------------------------------------------------------
        | SOURCE CHANGED
        |--------------------------------------------------------------------------
        */
        if ($lead->wasChanged('source')) {

            $this->activityService->sourceChanged(
                $lead,
                $lead->getOriginal('source'),
                $lead->source
            );
        }

        /*
        |--------------------------------------------------------------------------
        | INTEREST CHANGED
        |--------------------------------------------------------------------------
        */
        if ($lead->wasChanged('interest')) {

            $this->activityService->interestChanged(
                $lead,
                $lead->getOriginal('interest'),
                $lead->interest
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ASSIGNMENT CHANGED
        |--------------------------------------------------------------------------
        */
        if ($lead->wasChanged('assigned_to')) {

            $lead->loadMissing('assignedTo');

            if ($lead->assignedTo) {
                $this->activityService->assigned(
                    $lead,
                    $lead->assignedTo
                );
            } else {
                $this->activityService->unassigned($lead);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | NOTES CHANGED
        |--------------------------------------------------------------------------
        */
        if ($lead->wasChanged('notes')) {

            $this->activityService->noteChanged(
                $lead,
                $lead->getOriginal('notes'),
                $lead->notes
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETED
    |--------------------------------------------------------------------------
    */
    public function deleted(Lead $lead): void
    {
        $this->activityService->deleted($lead);
    }
}
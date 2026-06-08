<?php

namespace App\CRM\Services;

use App\CRM\Models\Lead;
use Carbon\Carbon;

class AutoReminderService
{
    public function __construct(
        protected LeadReminderService $reminderService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATE AUTO REMINDER
    |--------------------------------------------------------------------------
    */
    public function createForLead(Lead $lead): void
    {
        $rule = $this->getRule($lead->status);

        if (!$rule) {
            return;
        }

        $exists = $lead->reminders()
            ->where('status', 'pending')
            ->where('type', 'auto_follow_up')
            ->exists();

        if ($exists) {
            return;
        }

        $this->reminderService->create([
            'lead_id' => $lead->id,
            'title' => $rule['title'],
            'description' => $rule['description'],
            'type' => 'auto_follow_up',
            'status' => 'pending',
            'assigned_to' => $lead->assigned_to,
            'created_by' => $lead->created_by,
            'remind_at' => Carbon::now()->addDays($rule['days']),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | REPLACE AUTO REMINDER (IMPORTANT)
    |--------------------------------------------------------------------------
    */
    public function replaceForLead(Lead $lead): void
    {
        // cancel old auto reminders
        $lead->reminders()
            ->where('status', 'pending')
            ->where('type', 'auto_follow_up')
            ->update([
                'status' => 'cancelled',
            ]);

        // create new based on updated status
        $this->createForLead($lead);
    }

    /*
    |--------------------------------------------------------------------------
    | RULE ENGINE
    |--------------------------------------------------------------------------
    */
    protected function getRule(string $status): ?array
    {
        return match ($status) {

            'new' => [
                'days' => 1,
                'title' => 'Hubungi lead baru',
                'description' => 'Lead baru masuk, segera lakukan kontak pertama.',
            ],

            'contacted' => [
                'days' => 3,
                'title' => 'Follow up prospek',
                'description' => 'Lakukan follow up untuk menjaga momentum.',
            ],

            'negotiation' => [
                'days' => 7,
                'title' => 'Tindak lanjut negosiasi',
                'description' => 'Pastikan proses negosiasi tidak terhenti.',
            ],

            'waiting_payment' => [
                'days' => 2,
                'title' => 'Follow up pembayaran',
                'description' => 'Pantau status pembayaran pelanggan.',
            ],

            default => null,
        };
    }
}
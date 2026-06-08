<?php

namespace App\CRM\Services;

use App\CRM\Models\LeadReminder;
use Illuminate\Http\Request;

class LeadReminderService
{
    /*
    |--------------------------------------------------------------------------
    | ADMIN FILTER + SEARCH (SAFE)
    |--------------------------------------------------------------------------
    */
    public function filterForAdmin(Request $request)
    {
        $query = LeadReminder::query()
            ->with(['lead', 'assignedTo', 'createdBy'])
            ->latest();

        // SEARCH LEAD NAME / PHONE
        $query->when($request->search, function ($q) use ($request) {
            $q->whereHas('lead', function ($lead) use ($request) {
                $lead->where('name', 'like', "%{$request->search}%")
                     ->orWhere('phone', 'like', "%{$request->search}%");
            });
        });

        // STATUS
        $query->when($request->status, fn($q) =>
            $q->where('status', $request->status)
        );

        // TYPE
        $query->when($request->type, fn($q) =>
            $q->where('type', $request->type)
        );

        // USER
        $query->when($request->user_id, fn($q) =>
            $q->where('assigned_to', $request->user_id)
        );

        // OVERDUE (SAFE SQLITE)
        if ($request->status === 'overdue') {
            $query->where('status', 'pending')
                  ->where('remind_at', '<', now()->format('Y-m-d H:i:s'));
        }

        return $query->get();
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(array $data): LeadReminder
    {
        return LeadReminder::create([
            'lead_id' => $data['lead_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'] ?? 'follow_up',
            'status' => $data['status'] ?? 'pending',
            'remind_at' => $data['remind_at'],
            'assigned_to' => $data['assigned_to'] ?? null,
            'created_by' => $data['created_by'] ?? auth()->id(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(LeadReminder $reminder, array $data): LeadReminder
    {
        $reminder->update($data);
        return $reminder->fresh();
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS
    |--------------------------------------------------------------------------
    */
    public function markDone(LeadReminder $reminder)
    {
        $reminder->update(['status' => 'done']);
        return $reminder;
    }

    public function cancel(LeadReminder $reminder)
    {
        $reminder->update(['status' => 'cancelled']);
        return $reminder;
    }

    public function reopen(LeadReminder $reminder)
    {
        $reminder->update(['status' => 'pending']);
        return $reminder;
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN
    |--------------------------------------------------------------------------
    */
    public function assign(LeadReminder $reminder, int $userId)
    {
        $reminder->update(['assigned_to' => $userId]);
        return $reminder;
    }
}
<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmNotification;
use App\CRM\Models\CrmTask;
use App\User\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class NotificationService
{
    public function unread(User $user, int $limit = 15): Collection
    {
        return CrmNotification::forUser($user->id)
            ->unread()
            ->due()
            ->with(['lead', 'task'])
            ->orderByDesc('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Count di-cache 30 detik — di-hit setiap page load via dropdown.
     */
    public function unreadCount(User $user): int
    {
        return CrmCacheService::rememberNotif(
            CrmCacheService::keyNotifCount($user->id),
            fn () => CrmNotification::forUser($user->id)->unread()->due()->count()
        );
    }

    public function all(User $user, int $perPage = 20)
    {
        return CrmNotification::forUser($user->id)
            ->due()
            ->with(['lead', 'task'])
            ->orderByDesc('created_at')
            ->paginate($perPage);
    }

    // -------------------------------------------------------------------------
    // MARK READ
    // -------------------------------------------------------------------------

    public function markRead(CrmNotification $notification): void
    {
        $notification->markAsRead();
        CrmCacheService::flushNotifCount($notification->user_id);
    }

    public function markAllRead(User $user): void
    {
        CrmNotification::forUser($user->id)
            ->unread()
            ->update(['is_read' => true, 'read_at' => now()]);

        CrmCacheService::flushNotifCount($user->id);
    }

    // -------------------------------------------------------------------------
    // CREATE — Manual
    // -------------------------------------------------------------------------

    public function createManual(
        User    $user,
        string  $title,
        ?string $message    = null,
        ?int    $leadId     = null,
        ?int    $taskId     = null,
        ?\Carbon\Carbon $remindAt = null,
    ): CrmNotification {
        $notif = CrmNotification::create([
            'user_id'    => $user->id,
            'type'       => CrmNotification::TYPE_MANUAL,
            'title'      => $title,
            'message'    => $message,
            'lead_id'    => $leadId,
            'task_id'    => $taskId,
            'action_url' => $leadId ? '/crm/leads/' . $leadId : null,
            'remind_at'  => $remindAt,
            'is_read'    => false,
        ]);

        CrmCacheService::flushNotifCount($user->id);

        return $notif;
    }

    // -------------------------------------------------------------------------
    // CREATE — Sistem
    // -------------------------------------------------------------------------

    public function notifyTaskOverdue(CrmTask $task): CrmNotification
    {
        $notif = CrmNotification::create([
            'user_id'    => $task->assigned_to,
            'type'       => CrmNotification::TYPE_TASK_OVERDUE,
            'title'      => 'Task terlambat: ' . $task->title,
            'message'    => 'Deadline: ' . $task->due_at->format('d M Y, H:i'),
            'task_id'    => $task->id,
            'lead_id'    => $task->lead_id,
            'action_url' => '/crm/tasks',
            'is_read'    => false,
        ]);

        CrmCacheService::flushNotifCount($task->assigned_to);

        return $notif;
    }

    public function notifyFollowUpOverdue(CrmLead $lead): CrmNotification
    {
        $notif = CrmNotification::create([
            'user_id'    => $lead->assigned_to,
            'type'       => CrmNotification::TYPE_FOLLOWUP_OVERDUE,
            'title'      => 'Follow-up terlewat: ' . $lead->name,
            'message'    => 'Dijadwalkan ' . $lead->next_follow_up_at->format('d M Y, H:i'),
            'lead_id'    => $lead->id,
            'action_url' => '/crm/leads/' . $lead->id,
            'is_read'    => false,
        ]);

        CrmCacheService::flushNotifCount($lead->assigned_to);

        return $notif;
    }

    public function notifyLeadWon(CrmLead $lead, User $notifyTo): CrmNotification
    {
        $notif = CrmNotification::create([
            'user_id'    => $notifyTo->id,
            'type'       => CrmNotification::TYPE_LEAD_WON,
            'title'      => '🎉 Lead Won: ' . $lead->name,
            'message'    => 'Lead berhasil ditutup sebagai Won.',
            'lead_id'    => $lead->id,
            'action_url' => '/crm/leads/' . $lead->id,
            'is_read'    => false,
        ]);

        CrmCacheService::flushNotifCount($notifyTo->id);

        return $notif;
    }

    public function notifyLeadAssigned(CrmLead $lead, User $notifyTo): CrmNotification
    {
        $notif = CrmNotification::create([
            'user_id'    => $notifyTo->id,
            'type'       => CrmNotification::TYPE_LEAD_ASSIGNED,
            'title'      => 'Lead baru ditugaskan: ' . $lead->name,
            'message'    => $lead->name . ' (' . $lead->phone . ') ditugaskan ke kamu.',
            'lead_id'    => $lead->id,
            'action_url' => '/crm/leads/' . $lead->id,
            'is_read'    => false,
        ]);

        CrmCacheService::flushNotifCount($notifyTo->id);

        return $notif;
    }

    public function notifyLeadLost(CrmLead $lead, User $notifyTo): CrmNotification
    {
        $notif = CrmNotification::create([
            'user_id'    => $notifyTo->id,
            'type'       => CrmNotification::TYPE_LEAD_LOST,
            'title'      => 'Lead Lost: ' . $lead->name,
            'message'    => 'Lead ditutup sebagai Lost.'
                . ($lead->lostReason ? ' Alasan: ' . $lead->lostReason->name : ''),
            'lead_id'    => $lead->id,
            'action_url' => '/crm/leads/' . $lead->id,
            'is_read'    => false,
        ]);

        CrmCacheService::flushNotifCount($notifyTo->id);

        return $notif;
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function delete(CrmNotification $notification): void
    {
        $userId = $notification->user_id;
        $notification->delete();
        CrmCacheService::flushNotifCount($userId);
    }

    public function deleteAllRead(User $user): void
    {
        CrmNotification::forUser($user->id)->where('is_read', true)->delete();
        CrmCacheService::flushNotifCount($user->id);
    }
}
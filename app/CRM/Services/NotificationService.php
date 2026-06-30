<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmNotification;
use App\CRM\Models\CrmTask;
use App\User\Models\User;
use Illuminate\Support\Collection;

class NotificationService
{
    // -------------------------------------------------------------------------
    // READ
    // -------------------------------------------------------------------------

    /**
     * Ambil notifikasi user yang belum dibaca & sudah waktunya tampil.
     */
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
     * Hitung jumlah notifikasi belum dibaca.
     */
    public function unreadCount(User $user): int
    {
        return CrmNotification::forUser($user->id)
            ->unread()
            ->due()
            ->count();
    }

    /**
     * Ambil semua notifikasi (dengan paginasi) untuk halaman notifikasi.
     */
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
    }

    public function markAllRead(User $user): void
    {
        CrmNotification::forUser($user->id)
            ->unread()
            ->update(['is_read' => true, 'read_at' => now()]);
    }

    // -------------------------------------------------------------------------
    // CREATE — Manual reminder
    // -------------------------------------------------------------------------

    /**
     * Buat reminder manual dari sales.
     */
    public function createManual(
        User    $user,
        string  $title,
        ?string $message    = null,
        ?int    $leadId     = null,
        ?int    $taskId     = null,
        ?\Carbon\Carbon $remindAt = null,
    ): CrmNotification {
        return CrmNotification::create([
            'user_id'    => $user->id,
            'type'       => CrmNotification::TYPE_MANUAL,
            'title'      => $title,
            'message'    => $message,
            'lead_id'    => $leadId,
            'task_id'    => $taskId,
            'action_url' => $leadId ? route('crm.leads.show', $leadId) : null,
            'remind_at'  => $remindAt,
            'is_read'    => false,
        ]);
    }

    // -------------------------------------------------------------------------
    // CREATE — Sistem otomatis
    // -------------------------------------------------------------------------

    public function notifyTaskOverdue(CrmTask $task): CrmNotification
    {
        return CrmNotification::create([
            'user_id'    => $task->assigned_to,
            'type'       => CrmNotification::TYPE_TASK_OVERDUE,
            'title'      => 'Task terlambat: ' . $task->title,
            'message'    => 'Task ini sudah melewati deadline ' . $task->due_at->format('d M Y, H:i') . '.',
            'task_id'    => $task->id,
            'lead_id'    => $task->lead_id,
            'action_url' => route('crm.tasks.index'),
            'is_read'    => false,
        ]);
    }

    public function notifyFollowUpOverdue(CrmLead $lead): CrmNotification
    {
        return CrmNotification::create([
            'user_id'    => $lead->assigned_to,
            'type'       => CrmNotification::TYPE_FOLLOWUP_OVERDUE,
            'title'      => 'Follow-up terlewat: ' . $lead->name,
            'message'    => 'Follow-up dijadwalkan pada ' . $lead->next_follow_up_at->format('d M Y, H:i') . ' tapi belum dilakukan.',
            'lead_id'    => $lead->id,
            'action_url' => route('crm.leads.show', $lead),
            'is_read'    => false,
        ]);
    }

    public function notifyLeadWon(CrmLead $lead, User $notifyTo): CrmNotification
    {
        return CrmNotification::create([
            'user_id'    => $notifyTo->id,
            'type'       => CrmNotification::TYPE_LEAD_WON,
            'title'      => '🎉 Lead Won: ' . $lead->name,
            'message'    => 'Lead berhasil ditutup sebagai Won.',
            'lead_id'    => $lead->id,
            'action_url' => route('crm.leads.show', $lead),
            'is_read'    => false,
        ]);
    }

    public function notifyLeadLost(CrmLead $lead, User $notifyTo): CrmNotification
    {
        return CrmNotification::create([
            'user_id'    => $notifyTo->id,
            'type'       => CrmNotification::TYPE_LEAD_LOST,
            'title'      => 'Lead Lost: ' . $lead->name,
            'message'    => 'Lead ditutup sebagai Lost.'
                . ($lead->lostReason ? ' Alasan: ' . $lead->lostReason->name : ''),
            'lead_id'    => $lead->id,
            'action_url' => route('crm.leads.show', $lead),
            'is_read'    => false,
        ]);
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function delete(CrmNotification $notification): void
    {
        $notification->delete();
    }

    public function deleteAllRead(User $user): void
    {
        CrmNotification::forUser($user->id)
            ->where('is_read', true)
            ->delete();
    }
}
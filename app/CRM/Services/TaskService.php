<?php

namespace App\CRM\Services;

use App\CRM\Models\CrmTask;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TaskService
{
    // -------------------------------------------------------------------------
    // READ
    // -------------------------------------------------------------------------

    /**
     * Daftar task dengan filter.
     *
     * Filter: lead_id, assigned_to, status, priority,
     *         due_date (today | overdue | upcoming), search
     */
    public function list(array $filters = [], int $perPage = 20): LengthAwarePaginator
    {
        return CrmTask::query()
            ->with(['lead', 'assignedUser', 'createdBy'])
            ->when(
                isset($filters['lead_id']),
                fn ($q) => $q->forLead($filters['lead_id'])
            )
            ->when(
                isset($filters['assigned_to']),
                fn ($q) => $q->assignedTo($filters['assigned_to'])
            )
            ->when(
                isset($filters['status']),
                fn ($q) => $q->where('status', $filters['status'])
            )
            ->when(
                isset($filters['priority']),
                fn ($q) => $q->byPriority($filters['priority'])
            )
            ->when(
                ($filters['due_date'] ?? '') === 'today',
                fn ($q) => $q->dueToday()
            )
            ->when(
                ($filters['due_date'] ?? '') === 'overdue',
                fn ($q) => $q->overdue()
            )
            ->when(
                ($filters['due_date'] ?? '') === 'upcoming',
                fn ($q) => $q->open()->where('due_at', '>', now())
            )
            ->when(
                ! empty($filters['search']),
                fn ($q) => $q->where('title', 'like', "%{$filters['search']}%")
            )
            ->orderByRaw("FIELD(status, 'open', 'done', 'cancelled')")
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_at')
            ->paginate($perPage);
    }

    /**
     * Task milik lead tertentu — untuk panel di show lead.
     */
    public function listByLead(int $leadId): \Illuminate\Database\Eloquent\Collection
    {
        return CrmTask::with(['assignedUser'])
            ->forLead($leadId)
            ->orderByRaw("FIELD(status, 'open', 'done', 'cancelled')")
            ->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")
            ->orderBy('due_at')
            ->get();
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(array $data): CrmTask
    {
        $user = Auth::user();

        return CrmTask::create([
            'lead_id'     => $data['lead_id'] ?? null,
            'assigned_to' => $data['assigned_to'] ?? $user->id,
            'created_by'  => $user->id,
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'priority'    => $data['priority'] ?? CrmTask::PRIORITY_MEDIUM,
            'status'      => CrmTask::STATUS_OPEN,
            'due_at'      => $data['due_at'],
            'is_reminded' => false,
        ]);
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    public function update(CrmTask $task, array $data): CrmTask
    {
        $fillable = [
            'lead_id', 'assigned_to',
            'title', 'description',
            'priority', 'due_at',
        ];

        $task->update(
            collect($data)->only($fillable)->toArray()
        );

        return $task->fresh();
    }

    // -------------------------------------------------------------------------
    // STATUS
    // -------------------------------------------------------------------------

    /**
     * Tandai task selesai.
     */
    public function markDone(CrmTask $task): CrmTask
    {
        $task->update([
            'status'  => CrmTask::STATUS_DONE,
            'done_at' => now(),
        ]);

        return $task->fresh();
    }

    /**
     * Buka kembali task yang done/cancelled.
     */
    public function reopen(CrmTask $task): CrmTask
    {
        $task->update([
            'status'  => CrmTask::STATUS_OPEN,
            'done_at' => null,
        ]);

        return $task->fresh();
    }

    /**
     * Batalkan task.
     */
    public function cancel(CrmTask $task): CrmTask
    {
        $task->update([
            'status'  => CrmTask::STATUS_CANCELLED,
            'done_at' => null,
        ]);

        return $task->fresh();
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function delete(CrmTask $task): void
    {
        $task->delete();
    }

    public function restore(int $id): CrmTask
    {
        $task = CrmTask::withTrashed()->findOrFail($id);
        $task->restore();

        return $task->fresh();
    }

    // -------------------------------------------------------------------------
    // STATS — untuk dashboard / widget
    // -------------------------------------------------------------------------

    /**
     * Hitung ringkasan task milik user yang login.
     */
    public function statsForUser(int $userId): array
    {
        $base = CrmTask::assignedTo($userId);

        return [
            'open'     => (clone $base)->open()->count(),
            'overdue'  => (clone $base)->overdue()->count(),
            'today'    => (clone $base)->dueToday()->count(),
            'done'     => (clone $base)->done()->count(),
        ];
    }
}
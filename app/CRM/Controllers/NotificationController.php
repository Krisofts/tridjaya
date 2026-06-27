<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmTask;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    public function tasks(): JsonResponse
    {
        $userId = auth()->id();

        $tasks = CrmTask::query()
            ->with(['lead:id,name,lead_code'])
            ->where('user_id', $userId)
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_at')
            ->where('due_at', '<=', now()->addHours(24))
            ->orderBy('due_at')
            ->limit(10)
            ->get();

        $overdue = $tasks->filter(fn ($t) => $t->due_at->isPast());
        $dueSoon = $tasks->filter(fn ($t) => ! $t->due_at->isPast());

        return response()->json([
            'total'    => $tasks->count(),
            'overdue'  => $overdue->count(),
            'tasks'    => $tasks->map(fn ($task) => [
                'id'         => $task->id,
                'title'      => $task->title,
                'lead_name'  => $task->lead?->name,
                'lead_code'  => $task->lead?->lead_code,
                'lead_id'    => $task->lead_id,
                'due_at'     => $task->due_at->format('H:i'),
                'due_date'   => $task->due_at->isToday() ? 'Hari ini' : $task->due_at->translatedFormat('d M'),
                'is_overdue' => $task->due_at->isPast(),
                'priority'   => $task->priority,
                'status'     => $task->status,
            ]),
        ]);
    }
}
<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmTask;
use App\CRM\Services\TaskService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $tasks,
    ) {}

    // -------------------------------------------------------------------------
    // START
    // -------------------------------------------------------------------------

    public function start(CrmTask $task): RedirectResponse
    {
        $this->tasks->start($task);

        return $this->redirectToLead($task, 'Task berhasil dimulai.');
    }

    // -------------------------------------------------------------------------
    // COMPLETE
    // -------------------------------------------------------------------------

    public function complete(Request $request, CrmTask $task): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['nullable', 'string'],
            'result_id'   => ['nullable', 'exists:crm_results,id'],
        ]);

        if (filled($validated['description'] ?? null)) {
            $this->tasks->update($task, [
                'description' => $validated['description'],
            ]);
        }

        $this->tasks->complete($task, $validated['result_id'] ?? null);

        return $this->redirectToLead($task, 'Task berhasil diselesaikan.');
    }

    // -------------------------------------------------------------------------
    // CANCEL
    // -------------------------------------------------------------------------

    public function cancel(CrmTask $task): RedirectResponse
    {
        $this->tasks->cancel($task);

        return $this->redirectToLead($task, 'Task berhasil dibatalkan.');
    }

    // -------------------------------------------------------------------------
    // PRIVATE
    // -------------------------------------------------------------------------

    private function redirectToLead(CrmTask $task, string $message): RedirectResponse
    {
        return redirect()
            ->route('crm.leads.show', $task->lead_id)
            ->with('success', $message);
    }
}
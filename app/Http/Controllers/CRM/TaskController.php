<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\CRM\Models\Task;
use App\CRM\Models\Lead;
use App\CRM\Services\TaskService;
use App\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        return view('crm.tasks.index', [
            'tasks' => $this->taskService->paginate(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        return view('crm.tasks.create', [
            'leads' => Lead::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'lead_id' => ['nullable', 'exists:leads,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],

            'priority' => [
                'required',
                'in:' . implode(',', array_keys(config('crm.task_priority'))),
            ],

            'due_date' => ['nullable', 'date'],
        ]);

        $validated['status'] = Task::defaultStatus();
        $validated['created_by'] = auth()->id();

        $this->taskService->create($validated);

        return redirect()
            ->route('crm.tasks.index')
            ->with('success', 'Task berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Task $task): View
    {
        return view('crm.tasks.edit', [
            'task' => $task,
            'leads' => Lead::query()->orderBy('name')->get(),
            'users' => User::query()->orderBy('name')->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(
        Request $request,
        Task $task
    ): RedirectResponse {

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'lead_id' => ['nullable', 'exists:leads,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],

            'priority' => [
                'required',
                'in:' . implode(',', array_keys(config('crm.task_priority'))),
            ],

            'status' => [
                'required',
                'in:' . implode(',', array_keys(config('crm.task_status'))),
            ],

            'due_date' => ['nullable', 'date'],
        ]);

        $this->taskService->update($task, $validated);

        return redirect()
            ->route('crm.tasks.index')
            ->with('success', 'Task berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | INLINE STATUS UPDATE
    |--------------------------------------------------------------------------
    */
    public function updateStatus(
        Task $task,
        Request $request
    ): RedirectResponse {

        $request->validate([
            'status' => [
                'required',
                'in:' . implode(',', array_keys(config('crm.task_status'))),
            ],
        ]);

        $this->taskService->updateStatus($task, $request->status);

        return back()->with('success', 'Status task berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Task $task): RedirectResponse
    {
        $this->taskService->delete($task);

        return redirect()
            ->route('crm.tasks.index')
            ->with('success', 'Task berhasil dihapus.');
    }
}
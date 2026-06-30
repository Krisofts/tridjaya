<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmTask;
use App\CRM\Services\TaskService;
use App\Http\Controllers\Controller;
use App\User\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TaskController extends Controller
{
    public function __construct(
        private readonly TaskService $service,
    ) {}

    // -------------------------------------------------------------------------
    // INDEX — halaman daftar semua task
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $filters = $request->only([
            'lead_id', 'assigned_to', 'status',
            'priority', 'due_date', 'search',
        ]);

        // Default: tampilkan task milik user yang login
        if (! isset($filters['assigned_to'])) {
            $filters['assigned_to'] = Auth::id();
        }

        $tasks = $this->service->list($filters, perPage: 20);
        $users = User::orderBy('name')->get();
        $stats = $this->service->statsForUser(Auth::id());

        return view('pages.crm.tasks.index', compact('tasks', 'filters', 'users', 'stats'));
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(Request $request): View
    {
        $lead  = $request->lead_id ? CrmLead::find($request->lead_id) : null;
        $users = User::orderBy('name')->get();

        return view('pages.crm.tasks.create', compact('lead', 'users'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'lead_id'     => ['nullable', 'exists:crm_leads,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['required', 'in:low,medium,high'],
            'due_at'      => ['required', 'date'],
        ]);

        $task = $this->service->create($data);

        // Kalau dari halaman show lead, redirect balik ke lead
        if ($task->lead_id) {
            return redirect()
                ->route('crm.leads.show', $task->lead_id)
                ->with('success', 'Task berhasil ditambahkan.');
        }

        return redirect()
            ->route('crm.tasks.index')
            ->with('success', 'Task berhasil ditambahkan.');
    }

    // -------------------------------------------------------------------------
    // EDIT
    // -------------------------------------------------------------------------

    public function edit(CrmTask $task): View
    {
        $users = User::orderBy('name')->get();
        $leads = CrmLead::open()->orderBy('name')->limit(50)->get();

        return view('pages.crm.tasks.edit', compact('task', 'users', 'leads'));
    }

    public function update(Request $request, CrmTask $task): RedirectResponse
    {
        $data = $request->validate([
            'lead_id'     => ['nullable', 'exists:crm_leads,id'],
            'assigned_to' => ['nullable', 'exists:users,id'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'priority'    => ['required', 'in:low,medium,high'],
            'due_at'      => ['required', 'date'],
        ]);

        $this->service->update($task, $data);

        return redirect()
            ->route('crm.tasks.index')
            ->with('success', 'Task berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // STATUS
    // -------------------------------------------------------------------------

    public function markDone(CrmTask $task): RedirectResponse
    {
        $this->service->markDone($task);

        return back()->with('success', 'Task ditandai selesai.');
    }

    public function reopen(CrmTask $task): RedirectResponse
    {
        $this->service->reopen($task);

        return back()->with('success', 'Task dibuka kembali.');
    }

    public function cancel(CrmTask $task): RedirectResponse
    {
        $this->service->cancel($task);

        return back()->with('success', 'Task dibatalkan.');
    }

    // -------------------------------------------------------------------------
    // DELETE
    // -------------------------------------------------------------------------

    public function destroy(CrmTask $task): RedirectResponse
    {
        $leadId = $task->lead_id;
        $this->service->delete($task);

        if ($leadId && url()->previous() !== route('crm.tasks.index')) {
            return redirect()
                ->route('crm.leads.show', $leadId)
                ->with('success', 'Task dihapus.');
        }

        return redirect()
            ->route('crm.tasks.index')
            ->with('success', 'Task dihapus.');
    }
}
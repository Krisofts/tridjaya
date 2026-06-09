<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Http\Requests\CRM\AssignLeadTaskRequest;
use App\Http\Requests\CRM\CompleteLeadTaskRequest;
use App\Http\Requests\CRM\StoreLeadTaskRequest;
use App\Http\Requests\CRM\UpdateLeadTaskRequest;
use App\CRM\Models\LeadTask;
use App\CRM\Services\LeadTaskService;
use Illuminate\Http\Request;

class LeadTaskController extends Controller
{
    public function __construct(
        protected LeadTaskService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        $tasks = $this->service->getTasks(
            $request->all()
        );

        return view('crm.tasks.index', compact('tasks'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('crm.tasks.create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */

    public function store(StoreLeadTaskRequest $request)
    {
        $this->service->create($request->validated());

        return redirect()
            ->route('crm.tasks.index')
            ->with('success', 'Task created successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */

    public function show(LeadTask $task)
    {
        $task->load([
            'lead',
            'assignedTo',
            'createdBy',
            'completedBy',
            'parentTask'
        ]);

        return view('crm.tasks.show', compact('task'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */

    public function edit(LeadTask $task)
    {
        return view('crm.tasks.edit', compact('task'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */

    public function update(UpdateLeadTaskRequest $request, LeadTask $task)
    {
        $this->service->update($task, $request->validated());

        return redirect()
            ->route('crm.tasks.index')
            ->with('success', 'Task updated successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETE
    |--------------------------------------------------------------------------
    */

    public function complete(CompleteLeadTaskRequest $request, LeadTask $task)
    {
        $this->service->complete(
            $task,
            $request->validated('outcome'),
            $request->validated('notes')
        );

        return back()->with('success', 'Task completed successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | ASSIGN
    |--------------------------------------------------------------------------
    */

    public function assign(AssignLeadTaskRequest $request, LeadTask $task)
    {
        $this->service->assign(
            $task,
            $request->validated('assigned_to')
        );

        return back()->with('success', 'Task assigned successfully');
    }

    /*
    |--------------------------------------------------------------------------
    | START
    |--------------------------------------------------------------------------
    */

    public function start(LeadTask $task)
    {
        $this->service->start($task);

        return back()->with('success', 'Task started');
    }

    /*
    |--------------------------------------------------------------------------
    | REOPEN
    |--------------------------------------------------------------------------
    */

    public function reopen(LeadTask $task)
    {
        $this->service->reopen($task);

        return back()->with('success', 'Task reopened');
    }

    /*
    |--------------------------------------------------------------------------
    | CANCEL
    |--------------------------------------------------------------------------
    */

    public function cancel(LeadTask $task)
    {
        $this->service->cancel($task);

        return back()->with('success', 'Task cancelled');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */

    public function destroy(LeadTask $task)
    {
        $this->service->delete($task);

        return redirect()
            ->route('crm.tasks.index')
            ->with('success', 'Task deleted successfully');
    }
}
<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmTask;
use App\CRM\Services\ActivityService;
use App\CRM\Services\LeadService;
use App\CRM\Services\TaskService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MyLeadController extends Controller
{
    public function __construct(
        protected LeadService     $leads,
        protected ActivityService $activities,
        protected TaskService     $tasks,
    ) {}

    // -------------------------------------------------------------------------
    // LEAD SAYA
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        return view('crm.my-leads.index', [
            'leads' => $this->leads->getPaginated(
                search:      $request->string('search')->toString() ?: null,
                sourceId:    $request->integer('source_id') ?: null,
                pipelineId:  $request->integer('pipeline_id') ?: null,
                temperature: $request->string('temperature')->toString() ?: null,
                assignedTo:  auth()->id(),
            ),
        ]);
    }

    // -------------------------------------------------------------------------
    // SHOW (operasional — tampilan sederhana)
    // -------------------------------------------------------------------------

    public function show(CrmLead $lead): View
    {
        return view('crm.my-leads.show', [
            'lead'       => $lead->loadMissing([
                'source',
                'pipeline.stages',
                'stage',
                'interest',
                'assignee',
                'creator',
                'branch',
            ]),
            'activities' => $this->activities->getByLead($lead->id),
            'tasks'      => $this->tasks->getActiveByLead($lead->id),
        ]);
    }

    // -------------------------------------------------------------------------
    // TASK HARI INI
    // -------------------------------------------------------------------------

    public function tasks(): View
    {
        $tasks = CrmTask::query()
            ->with(['lead:id,name,lead_code,pipeline_stage_id', 'lead.stage:id,name,temperature', 'result'])
            ->where('user_id', auth()->id())
            ->whereIn('status', ['pending', 'in_progress'])
            ->whereNotNull('due_at')
            ->where('due_at', '<=', now()->endOfDay())
            ->orderBy('due_at')
            ->get();

        $overdue  = $tasks->filter(fn ($t) => $t->due_at->isPast());
        $dueSoon  = $tasks->filter(fn ($t) => ! $t->due_at->isPast());

        return view('crm.my-leads.tasks', compact('tasks', 'overdue', 'dueSoon'));
    }
}
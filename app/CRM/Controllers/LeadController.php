<?php

namespace App\CRM\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\CRM\Requests\StoreLeadRequest;
use App\CRM\Models\CrmLead;
use App\CRM\Requests\StoreActivityRequest;
use App\CRM\Requests\UpdateLeadRequest;
use App\CRM\Services\LeadService;
use App\CRM\Services\ActivityService;
use App\CRM\Services\TaskService;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService,
        protected ActivityService $activityService,
        protected TaskService $taskService
    ) {}

    /* -------------------------------------------------
     | INDEX
     |-------------------------------------------------*/
    public function index(Request $request)
    {
        $leads = $this->leadService->getPaginated(
            search: $request->string('search')->toString(),
            sourceId: $request->integer('source_id') ?: null,
            pipelineId: $request->integer('pipeline_id') ?: null,
            stageId: $request->integer('stage_id') ?: null,
        );

        return view('crm.leads.index', array_merge([
            'leads' => $leads,
        ], $this->leadService->getFilterData()));
    }

    /* -------------------------------------------------
     | CREATE
     |-------------------------------------------------*/
    public function create()
    {
        return view(
            'crm.leads.create',
            $this->leadService->getCreateData()
        );
    }

    public function store(StoreLeadRequest $request)
{
    $validated = $request->validated();
    $validated['created_by'] = auth()->id();

    $lead = $this->leadService->create($validated);

    return redirect()
        ->route('crm.leads.index')
        ->with('success', 'Lead created successfully.');
}

    /* -------------------------------------------------
     | SHOW
     |-------------------------------------------------*/
    public function show(CrmLead $lead)
{
    $lead = $this->leadService->find($lead->id);

    $activities = $this->activityService->getByLead($lead->id);

    $tasks = $this->taskService->getByLead($lead->id);

    $stages = $lead->pipeline
        ? $lead->pipeline->stages()->orderBy('sort_order')->get()
        : collect();

    return view('crm.leads.show', compact(
        'lead',
        'activities',
        'tasks',
        'stages'
    ));
}

    /* -------------------------------------------------
     | EDIT
     |-------------------------------------------------*/
    public function edit(CrmLead $lead)
    {
        return view(
            'crm.leads.edit',
            $this->leadService->getUpdateData($lead)
        );
    }

    /* -------------------------------------------------
     | UPDATE
     |-------------------------------------------------*/
    public function update(UpdateLeadRequest $request, CrmLead $lead)
    {
        $this->leadService->update($lead, $request->validated());

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Lead updated successfully.');
    }

    /* -------------------------------------------------
     | CHANGE STAGE
     |-------------------------------------------------*/
    public function changeStage(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'stage_id' => ['required', 'exists:crm_pipeline_stages,id'],
        ]);

        $oldStage = $lead->stage?->name;

        $updatedLead = $this->leadService->changeStage(
            $lead,
            $validated['stage_id']
        );

        $newStage = $updatedLead->stage?->name;

        $this->activityService->create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'stage_changed',
            'title' => 'Stage Changed',
            'description' => "{$oldStage} → {$newStage}",
        ]);

        return back()->with('success', 'Lead stage updated successfully.');
    }

    /* -------------------------------------------------
     | STORE ACTIVITY
     |-------------------------------------------------*/
    public function storeActivity(StoreActivityRequest $request, CrmLead $lead)
    {
        $validated = $request->validated();

        $this->activityService->create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'title' => $validated['title'] ?? null,
            'description' => $validated['description'] ?? null,
        ]);

        return back()->with('success', 'Activity created successfully.');
    }

    /* -------------------------------------------------
     | DELETE
     |-------------------------------------------------*/
    public function destroy(CrmLead $lead)
    {
        $this->leadService->delete($lead);

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead deleted successfully.');
    }
}

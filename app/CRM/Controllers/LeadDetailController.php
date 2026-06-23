<?php

namespace App\CRM\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmResult;

use App\CRM\Services\LeadService;
use App\CRM\Services\ActivityService;
use App\CRM\Services\TaskService;

class LeadDetailController extends Controller
{
    public function __construct(
        protected LeadService $leadService,
        protected ActivityService $activityService,
        protected TaskService $taskService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | SHOW LEAD DETAIL
    |--------------------------------------------------------------------------
    */
    public function show($id)
    {
        $lead = $this->leadService->find($id);

        $activities = $this->activityService->getByLead($lead->id);
        $tasks = $this->taskService->getActiveByLead($lead->id);

        $stages = $lead->pipeline
            ? $lead->pipeline->stages()->orderBy('sort_order')->get()
            : collect();

        $results = CrmResult::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('crm.leads.show', compact(
            'lead',
            'activities',
            'tasks',
            'stages',
            'results'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE ACTIVITY (MANUAL LOG)
    |--------------------------------------------------------------------------
    */
    public function storeActivity(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => ['required', 'exists:crm_leads,id'],
            'type' => ['required', 'string'],
            'title' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],

            // ✅ UPDATED: result_id (bukan string)
            'result_id' => ['nullable', 'exists:crm_results,id'],

            'next_follow_up_at' => ['nullable', 'date'],
            'stage_id' => ['nullable', 'exists:crm_pipeline_stages,id'],
        ]);

        $this->activityService->create([
            'lead_id' => $validated['lead_id'],
            'user_id' => auth()->id(),
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],

            // ✅ CRM ENGINE READY
            'result_id' => $validated['result_id'],

            'next_follow_up_at' => $validated['next_follow_up_at'],
            'stage_id' => $validated['stage_id'],
        ]);

        return back()->with('success', 'Activity berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE TASK (MANUAL)
    |--------------------------------------------------------------------------
    */
    public function storeTask(Request $request)
    {
        $validated = $request->validate([
            'lead_id' => ['required', 'exists:crm_leads,id'],
            'title' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'type' => ['nullable', 'string'],
            'priority' => ['nullable', 'string'],
            'user_id' => ['nullable', 'exists:users,id'],
            'due_at' => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
        ]);

        $this->taskService->create($validated);

        return back()->with('success', 'Task berhasil ditambahkan.');
    }

    /*
    |--------------------------------------------------------------------------
    | COMPLETE TASK (CRM ENGINE ENTRY POINT)
    |--------------------------------------------------------------------------
    */
    public function completeTask(Request $request, $taskId)
    {
        $task = $this->taskService->find($taskId);

        $validated = $request->validate([
            'result_id' => ['required', 'exists:crm_results,id'],
        ]);

        $this->taskService->complete(
            $task,
            $validated['result_id']
        );

        return back()->with('success', 'Task selesai.');
    }

    /*
    |--------------------------------------------------------------------------
    | START TASK
    |--------------------------------------------------------------------------
    */
    public function startTask($taskId)
    {
        $task = $this->taskService->find($taskId);

        $this->taskService->start($task);

        return back()->with('success', 'Task dimulai.');
    }

    /*
    |--------------------------------------------------------------------------
    | WHATSAPP QUICK ACTION
    |--------------------------------------------------------------------------
    */
    public function whatsapp(CrmLead $lead)
    {
        // OPTIONAL: auto move stage via service
        $stage = $lead->pipeline
            ? $lead->pipeline->stages()
                ->where('name', 'Contacted')
                ->first()
            : null;

        if ($stage) {
            $this->leadService->changeStage($lead, $stage->id);
        }

        // activity log
        $this->activityService->create([
            'lead_id' => $lead->id,
            'user_id' => auth()->id(),
            'type' => 'whatsapp',
            'title' => 'WhatsApp Clicked',
            'description' => 'User membuka chat WhatsApp',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $lead->phone);

        return redirect()->away("https://wa.me/{$phone}");
    }

    /*
    |--------------------------------------------------------------------------
    | CHANGE STAGE MANUAL
    |--------------------------------------------------------------------------
    */
    public function changeStage(Request $request, CrmLead $lead)
    {
        $validated = $request->validate([
            'stage_id' => ['required', 'exists:crm_pipeline_stages,id'],
        ]);

        $this->leadService->changeStage(
            $lead,
            $validated['stage_id']
        );

        return back()->with('success', 'Stage berhasil diubah.');
    }
}
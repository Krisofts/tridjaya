<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmLead;
use App\CRM\Requests\StoreActivityRequest;
use App\CRM\Requests\StoreTaskRequest;
use App\CRM\Services\ActivityService;
use App\CRM\Services\LeadService;
use App\CRM\Services\TaskService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadDetailController extends Controller
{
    public function __construct(
        protected LeadService     $leads,
        protected ActivityService $activities,
        protected TaskService     $tasks,
    ) {}

    // -------------------------------------------------------------------------
    // SHOW
    // -------------------------------------------------------------------------

    public function show(CrmLead $lead): View
    {
        return view('crm.leads.show', [
            'lead'         => $lead->loadMissing([
                'source',
                'pipeline.stages',
                'stage',
                'interest',
                'assignee',
                'creator',
                'branch',
            ]),
            'activities'   => $this->activities->getByLead($lead->id),
            'tasks'        => $this->tasks->getActiveByLead($lead->id),
            'transactions' => $lead->transactions()->with('creator')->get(),
        ]);
    }

    // -------------------------------------------------------------------------
    // CHANGE STAGE
    // -------------------------------------------------------------------------

    public function changeStage(Request $request, CrmLead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'stage_id' => ['required', 'exists:crm_pipeline_stages,id'],
        ]);

        $this->leads->changeStage($lead, $validated['stage_id']);

        return back()->with('success', 'Stage berhasil diubah.');
    }

    // -------------------------------------------------------------------------
    // STORE TASK
    // -------------------------------------------------------------------------

    public function storeTask(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lead_id'     => ['required', 'exists:crm_leads,id'],
            'title'       => ['required', 'string', 'max:255'],
            'type'        => ['nullable', 'string'],
            'priority'    => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'due_at'      => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
        ]);

        $this->tasks->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Task berhasil ditambahkan.');
    }

    // -------------------------------------------------------------------------
    // STORE ACTIVITY
    // -------------------------------------------------------------------------

    public function storeActivity(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'lead_id'          => ['required', 'exists:crm_leads,id'],
            'type'             => ['required', 'string'],
            'title'            => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'result_id'        => ['nullable', 'exists:crm_results,id'],
            'next_follow_up_at'=> ['nullable', 'date'],
            'stage_id'         => ['nullable', 'exists:crm_pipeline_stages,id'],
        ]);

        $this->activities->create([
            ...$validated,
            'user_id' => auth()->id(),
        ]);

        return back()->with('success', 'Activity berhasil ditambahkan.');
    }

    // -------------------------------------------------------------------------
    // WHATSAPP
    // -------------------------------------------------------------------------

    public function whatsapp(CrmLead $lead): RedirectResponse
    {
        // Auto-move ke stage "Contacted" kalau ada
        $contactedStage = $lead->pipeline?->stages()
            ->where('name', 'Contacted')
            ->first();

        if ($contactedStage) {
            $this->leads->changeStage($lead, $contactedStage->id);
        }

        // Log activity
        $this->activities->create([
            'lead_id'     => $lead->id,
            'user_id'     => auth()->id(),
            'type'        => 'whatsapp',
            'title'       => 'WhatsApp Clicked',
            'description' => 'User membuka chat WhatsApp',
        ]);

        $phone = preg_replace('/[^0-9]/', '', $lead->phone);

        return redirect()->away("https://wa.me/{$phone}");
    }
}
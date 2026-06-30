<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmLeadActivity;
use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmActivityType;
use App\CRM\Services\ActivityService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function __construct(
        private readonly ActivityService $service,
    ) {}

    // -------------------------------------------------------------------------
    // AJAX — Cascade dropdown: results by activity type
    // -------------------------------------------------------------------------

    public function resultsByType(CrmActivityType $type)
    {
        return response()->json(
            $this->service->getResultsByType($type->id)
        );
    }

    // -------------------------------------------------------------------------
    // STORE
    // -------------------------------------------------------------------------

    public function store(Request $request, CrmLead $lead): RedirectResponse
    {
        $data = $request->validate([
            'activity_type_id'   => ['required', 'exists:crm_activity_types,id'],
            'activity_result_id' => ['nullable', 'exists:crm_activity_results,id'],
            'title'              => ['required', 'string', 'max:255'],
            'activity_at'        => ['required', 'date'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'location'           => ['nullable', 'string', 'max:255'],
            'is_contacted'       => ['boolean'],
            'next_follow_up_at'  => ['nullable', 'date'],
        ]);

        $this->service->create($lead, $data);

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Aktivitas berhasil dicatat.');
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    public function update(Request $request, CrmLeadActivity $activity): RedirectResponse
    {
        $data = $request->validate([
            'activity_type_id'   => ['required', 'exists:crm_activity_types,id'],
            'activity_result_id' => ['nullable', 'exists:crm_activity_results,id'],
            'title'              => ['required', 'string', 'max:255'],
            'activity_at'        => ['required', 'date'],
            'notes'              => ['nullable', 'string', 'max:2000'],
            'location'           => ['nullable', 'string', 'max:255'],
            'is_contacted'       => ['boolean'],
        ]);

        $this->service->update($activity, $data);

        return redirect()
            ->route('crm.leads.show', $activity->lead_id)
            ->with('success', 'Aktivitas berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------------------------

    public function destroy(CrmLeadActivity $activity): RedirectResponse
    {
        $leadId = $activity->lead_id;
        $this->service->delete($activity);

        return redirect()
            ->route('crm.leads.show', $leadId)
            ->with('success', 'Aktivitas dihapus.');
    }
}
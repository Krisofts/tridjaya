<?php

namespace App\CRM\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\CRM\Requests\StoreLeadRequest;
use App\CRM\Requests\UpdateLeadRequest;

use App\CRM\Models\CrmLead;
use App\CRM\Services\LeadService;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request)
{
    $leads = $this->leadService->getPaginated(
        search: $request->string('search')->toString(),
        sourceId: $request->integer('source_id') ?: null,
        pipelineId: $request->integer('pipeline_id') ?: null,
        temperature: $request->string('temperature')->toString() ?: null,
        assignedTo: $request->integer('assigned_to') ?: null, // 👤 ADD THIS
    );

    return view('crm.leads.index', array_merge([
        'leads' => $leads,
    ], $this->leadService->getFilterData()));
}

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view(
            'crm.leads.create',
            $this->leadService->getCreateData()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(StoreLeadRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = auth()->id();

        $this->leadService->create($data);

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(CrmLead $lead)
    {
        return view(
            'crm.leads.edit',
            $this->leadService->getUpdateData($lead)
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(UpdateLeadRequest $request, CrmLead $lead)
    {
        $this->leadService->update($lead, $request->validated());

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(CrmLead $lead)
    {
        $this->leadService->delete($lead);

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead deleted successfully.');
    }
}
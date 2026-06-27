<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmLead;
use App\CRM\Requests\StoreLeadRequest;
use App\CRM\Requests\UpdateLeadRequest;
use App\CRM\Services\LeadService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function __construct(
        protected LeadService $leads,
    ) {}

    // -------------------------------------------------------------------------
    // INDEX
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        return view('crm.leads.index', [
            'leads' => $this->leads->getPaginated(
                search:      $request->string('search')->toString() ?: null,
                sourceId:    $request->integer('source_id') ?: null,
                pipelineId:  $request->integer('pipeline_id') ?: null,
                temperature: $request->string('temperature')->toString() ?: null,
                assignedTo:  $request->integer('assigned_to') ?: null,
            ),
        ]);
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(): View
    {
        return view('crm.leads.create');
    }

    // -------------------------------------------------------------------------
    // STORE
    // -------------------------------------------------------------------------

    public function store(StoreLeadRequest $request): RedirectResponse
    {
        $lead = $this->leads->create([
            ...$request->validated(),
            'created_by' => auth()->id(),
        ]);

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Lead berhasil ditambahkan.');
    }

    // -------------------------------------------------------------------------
    // SHOW
    // -------------------------------------------------------------------------

    public function show(CrmLead $lead): View
    {
        return view('crm.leads.show', [
            'lead' => $lead->loadMissing([
                'source',
                'pipeline',
                'stage',
                'assignee',
                'creator',
                'branch',
            ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // EDIT
    // -------------------------------------------------------------------------

    public function edit(CrmLead $lead): View
    {
        return view('crm.leads.edit', [
            'lead' => $lead->loadMissing([
                'source',
                'pipeline',
                'stage',
                'assignee',
                'creator',
                'branch',
            ]),
        ]);
    }

    // -------------------------------------------------------------------------
    // UPDATE
    // -------------------------------------------------------------------------

    public function update(UpdateLeadRequest $request, CrmLead $lead): RedirectResponse
    {
        $this->leads->update($lead, $request->validated());

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Lead berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------------------------

    public function destroy(CrmLead $lead): RedirectResponse
    {
        $this->leads->delete($lead);

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead berhasil dihapus.');
    }
}
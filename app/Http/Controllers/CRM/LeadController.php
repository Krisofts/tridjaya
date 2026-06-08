<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\CRM\Models\Lead;
use App\CRM\Services\LeadService;
use App\Http\Requests\CRM\LeadRequest;
use App\User\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeadController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected LeadService $leadService,
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Lead::class);

        return view('crm.leads.index', [
            'leads' => $this->leadService->paginateWithFilters(
                $request->only([
                    'search',
                    'status',
                    'interest',
                    'source',
                    'assigned_to',
                    'sort',
                ])
            ),

            'users' => User::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE
    |--------------------------------------------------------------------------
    */
    public function create(): View
    {
        $this->authorize('create', Lead::class);

        return view('crm.leads.create', [
            'users' => User::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(LeadRequest $request): RedirectResponse
    {
        $this->authorize('create', Lead::class);

        $lead = $this->leadService->create(
            array_merge(
                $request->toArrayData(),
                [
                    'created_by' => auth()->id(),
                ]
            )
        );

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Lead berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW (🔥 IMPORTANT: LOAD TRANSACTIONS + ACTIVITY)
    |--------------------------------------------------------------------------
    */
    public function show(Lead $lead): View
    {
        $this->authorize('view', $lead);

        $lead->load([
            'assignedTo:id,name',
            'createdBy:id,name',

            // 🔥 IMPORTANT FOR CRM PANEL
            'activities.createdBy',
            'transactions.createdBy',
        ]);

        return view('crm.leads.show', [
            'lead' => $lead,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Lead $lead): View
    {
        $this->authorize('update', $lead);

        $lead->load([
            'assignedTo:id,name',
        ]);

        return view('crm.leads.edit', [
            'lead' => $lead,

            'users' => User::query()
                ->select('id', 'name')
                ->orderBy('name')
                ->get(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(LeadRequest $request, Lead $lead): RedirectResponse
    {
        $this->authorize('update', $lead);

        $this->leadService->update(
            $lead,
            $request->toArrayData()
        );

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Lead berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Lead $lead): RedirectResponse
    {
        $this->authorize('delete', $lead);

        $this->leadService->delete($lead);

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead berhasil dihapus.');
    }
}
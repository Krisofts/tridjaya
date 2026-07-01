<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmSource;
use App\CRM\Services\LeadService;
use App\Http\Controllers\Controller;
use App\Models\District;
use App\Models\Product;
use App\Models\Province;
use App\Models\Regency;
use App\User\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LeadController extends Controller
{
    public function __construct(
        private readonly LeadService $service,
    ) {}

    // -------------------------------------------------------------------------
    // MY LEADS
    // -------------------------------------------------------------------------

    public function myLeads(Request $request): View
    {
        $filters = $request->only([
            'pipeline_id', 'status', 'search',
        ]);

        $leads     = $this->service->myLeads($filters, perPage: 20);
        $pipelines = CrmPipeline::orderBy('name')->get();

        return view('pages.crm.leads.my-leads', compact(
            'leads', 'filters', 'pipelines'
        ));
    }

    // -------------------------------------------------------------------------
    // INDEX
    // -------------------------------------------------------------------------

    public function index(Request $request): View
    {
        $filters = $request->only([
            'pipeline_id', 'stage_id', 'source_id',
            'product_id', 'assigned_to', 'status', 'search',
        ]);

        $leads     = $this->service->list($filters, perPage: 20);
        $pipelines = CrmPipeline::orderBy('name')->get();
        $sources   = CrmSource::orderBy('name')->get();
        $products  = Product::orderBy('name')->get();
        $users     = User::orderBy('name')->get();

        return view('pages.crm.leads.index', compact(
            'leads', 'filters', 'pipelines', 'sources', 'products', 'users'
        ));
    }

    // -------------------------------------------------------------------------
    // CREATE
    // -------------------------------------------------------------------------

    public function create(): View
    {
        $pipelines  = CrmPipeline::with('stages')->orderBy('name')->get();
        $sources    = CrmSource::orderBy('name')->get();
        $products   = Product::orderBy('name')->get();
        $interests  = \App\CRM\Models\CrmInterest::active()->ordered()->get();
        $provinces  = Province::orderBy('name')->get();
        $users      = User::orderBy('name')->get();

        return view('pages.crm.leads.create', compact(
            'pipelines', 'sources', 'products', 'interests', 'provinces', 'users'
        ));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pipeline_id'       => ['required', 'exists:crm_pipelines,id'],
            'name'              => ['required', 'string', 'max:255'],
            'phone'             => ['required', 'string', 'max:25'],
            'source_id'         => ['nullable', 'exists:crm_sources,id'],
            'product_id'        => ['nullable', 'exists:products,id'],
            'interest_id'       => ['nullable', 'exists:crm_interests,id'],
            'assigned_to'       => ['nullable', 'exists:users,id'],
            'province_id'       => ['nullable', 'exists:provinces,id'],
            'regency_id'        => ['nullable', 'exists:regencies,id'],
            'district_id'       => ['nullable', 'exists:districts,id'],
            'address'           => ['nullable', 'string'],
            'estimated_value'   => ['nullable', 'numeric', 'min:0'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        // Cek duplikat nomor HP
        $existing = CrmLead::where('phone', $data['phone'])
            ->whereNull('deleted_at')
            ->with(['assignedUser', 'pipeline', 'stage'])
            ->first();

        if ($existing) {
            return back()
                ->withInput()
                ->withErrors(['phone' => 'Nomor HP ini sudah terdaftar.'])
                ->with('duplicate_lead', $existing);
        }

        $lead = $this->service->create($data);

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Lead berhasil dibuat.');
    }

    // -------------------------------------------------------------------------
    // SHOW
    // -------------------------------------------------------------------------

    public function show(CrmLead $lead): View
    {
        $lead->load([
            'pipeline.stages',
            'stage',
            'assignedUser',
            'createdBy',
            'source',
            'product',
            'interest',
            'province',
            'regency',
            'district',
            'lostReason',
            'activities.type',
            'activities.result',
            'activities.user',
            'stageHistories.fromStage',
            'stageHistories.toStage',
            'stageHistories.changedByUser',
            'tasks.assignedUser',
            'tasks.assignedUser',
        ]);

        $lostReasons   = \App\CRM\Models\CrmLostReason::active()
            ->forPipeline($lead->pipeline_id)
            ->ordered()
            ->get();
        $activityTypes = \App\CRM\Models\CrmActivityType::active()->ordered()->get();

        return view('pages.crm.leads.show', compact('lead', 'lostReasons', 'activityTypes'));
    }

    // -------------------------------------------------------------------------
    // EDIT
    // -------------------------------------------------------------------------

    public function edit(CrmLead $lead): View
    {
        $lead->load(['province', 'regency', 'district']);

        $pipelines  = CrmPipeline::with('stages')->orderBy('name')->get();
        $sources    = CrmSource::orderBy('name')->get();
        $products   = Product::orderBy('name')->get();
        $interests  = \App\CRM\Models\CrmInterest::active()->ordered()->get();
        $provinces  = Province::orderBy('name')->get();
        $regencies  = $lead->province_id
            ? Regency::where('province_id', $lead->province_id)->orderBy('name')->get()
            : collect();
        $districts  = $lead->regency_id
            ? District::where('regency_id', $lead->regency_id)->orderBy('name')->get()
            : collect();
        $users      = User::orderBy('name')->get();

        return view('pages.crm.leads.edit', compact(
            'lead', 'pipelines', 'sources', 'products', 'interests',
            'provinces', 'regencies', 'districts', 'users'
        ));
    }

    public function update(Request $request, CrmLead $lead): RedirectResponse
    {
        $data = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'phone'             => ['required', 'string', 'max:25'],
            'source_id'         => ['nullable', 'exists:crm_sources,id'],
            'product_id'        => ['nullable', 'exists:products,id'],
            'interest_id'       => ['nullable', 'exists:crm_interests,id'],
            'assigned_to'       => ['nullable', 'exists:users,id'],
            'province_id'       => ['nullable', 'exists:provinces,id'],
            'regency_id'        => ['nullable', 'exists:regencies,id'],
            'district_id'       => ['nullable', 'exists:districts,id'],
            'address'           => ['nullable', 'string'],
            'estimated_value'   => ['nullable', 'numeric', 'min:0'],
            'next_follow_up_at' => ['nullable', 'date'],
        ]);

        $this->service->update($lead, $data);

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Lead berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // STAGE MANAGEMENT
    // -------------------------------------------------------------------------

    public function moveStage(Request $request, CrmLead $lead): RedirectResponse
    {
        $data = $request->validate([
            'stage_id' => ['required', 'exists:crm_pipeline_stages,id'],
        ]);

        $this->service->moveToStage($lead, $data['stage_id']);

        return back()->with('success', 'Stage berhasil dipindahkan.');
    }

    // -------------------------------------------------------------------------
    // STATUS LIFECYCLE
    // -------------------------------------------------------------------------

    public function markWon(CrmLead $lead): RedirectResponse
    {
        $this->service->markWon($lead);

        return back()->with('success', 'Lead ditandai sebagai Won.');
    }

    public function markLost(Request $request, CrmLead $lead): RedirectResponse
    {
        $data = $request->validate([
            'lost_reason_id' => ['nullable', 'exists:crm_lost_reasons,id'],
            'lost_note'      => ['nullable', 'string', 'max:1000'],
        ]);

        $this->service->markLost($lead, $data['lost_reason_id'] ?? null, $data['lost_note'] ?? null);

        return back()->with('success', 'Lead ditandai sebagai Lost.');
    }

    public function reopen(CrmLead $lead): RedirectResponse
    {
        $this->service->reopen($lead);

        return back()->with('success', 'Lead dibuka kembali.');
    }

    // -------------------------------------------------------------------------
    // DELETE & RESTORE
    // -------------------------------------------------------------------------

    public function destroy(CrmLead $lead): RedirectResponse
    {
        $this->service->delete($lead);

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead dihapus.');
    }

    public function restore(int $id): RedirectResponse
    {
        $this->service->restore($id);

        return redirect()
            ->route('crm.leads.index')
            ->with('success', 'Lead berhasil dipulihkan.');
    }

    // -------------------------------------------------------------------------
    // AJAX — Cascade dropdown
    // -------------------------------------------------------------------------

    public function regenciesByProvince(Province $province)
    {
        return response()->json(
            Regency::where('province_id', $province->id)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    public function districtsByRegency(Regency $regency)
    {
        return response()->json(
            District::where('regency_id', $regency->id)
                ->orderBy('name')
                ->get(['id', 'name'])
        );
    }

    /**
     * AJAX — cek duplikat nomor HP real-time
     */
    public function checkPhone(Request $request)
    {
        $phone = $request->get('phone');
        $excludeId = $request->get('exclude_id'); // untuk edit — exclude lead saat ini

        if (! $phone) {
            return response()->json(['exists' => false]);
        }

        $lead = CrmLead::where('phone', $phone)
            ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
            ->whereNull('deleted_at')
            ->with(['assignedUser', 'pipeline', 'stage'])
            ->first();

        if (! $lead) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => true,
            'lead'   => [
                'id'           => $lead->id,
                'name'         => $lead->name,
                'phone'        => $lead->phone,
                'status'       => $lead->statusLabel(),
                'pipeline'     => $lead->pipeline->name ?? '—',
                'stage'        => $lead->stage->name    ?? '—',
                'assigned_to'  => $lead->assignedUser->name ?? '—',
                'url'          => route('crm.leads.show', $lead),
            ],
        ]);
    }
}
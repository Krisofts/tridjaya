<?php

namespace App\CRM\Controllers;

use App\CRM\Models\CrmLead;
use App\CRM\Models\CrmPipelineStage;
use App\CRM\Models\CrmTransaction;
use App\CRM\Services\LeadService;
use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(
        protected LeadService $leads,
    ) {}

    // -------------------------------------------------------------------------
    // STORE
    // -------------------------------------------------------------------------

    public function store(Request $request, CrmLead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'type'             => ['required', 'in:cash,credit'],
            'amount'           => ['required', 'integer', 'min:1'],
            'dp_amount'        => ['nullable', 'integer', 'min:0'],
            'leasing'          => ['nullable', 'string', 'max:100'],
            'tenor'            => ['nullable', 'integer', 'min:1', 'max:360'],
            'notes'            => ['nullable', 'string'],
            'transaction_date' => ['required', 'date'],
            'status'           => ['required', 'in:pending,paid,cancelled'],
        ]);

        CrmTransaction::create([
            ...$validated,
            'lead_id'    => $lead->id,
            'created_by' => auth()->id(),
            'paid_at'    => $validated['status'] === 'paid' ? now() : null,
        ]);

        // Otomatis pindah stage ke "Won/Selesai" kalau status paid
        if ($validated['status'] === 'paid') {
            $wonStage = CrmPipelineStage::where('pipeline_id', $lead->pipeline_id)
                ->where('is_won', true)
                ->first();

            if ($wonStage && $lead->pipeline_stage_id !== $wonStage->id) {
                $this->leads->changeStage($lead, $wonStage->id);
            }
        }

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Transaksi berhasil dicatat.');
    }

    // -------------------------------------------------------------------------
    // UPDATE STATUS
    // -------------------------------------------------------------------------

    public function updateStatus(Request $request, CrmTransaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,cancelled'],
        ]);

        $transaction->update([
            'status'  => $validated['status'],
            'paid_at' => $validated['status'] === 'paid' ? now() : $transaction->paid_at,
        ]);

        // Otomatis pindah stage kalau lunas
        if ($validated['status'] === 'paid') {
            $lead     = $transaction->lead;
            $wonStage = CrmPipelineStage::where('pipeline_id', $lead->pipeline_id)
                ->where('is_won', true)
                ->first();

            if ($wonStage && $lead->pipeline_stage_id !== $wonStage->id) {
                $this->leads->changeStage($lead, $wonStage->id);
            }
        }

        return redirect()
            ->route('crm.leads.show', $transaction->lead_id)
            ->with('success', 'Status transaksi berhasil diperbarui.');
    }

    // -------------------------------------------------------------------------
    // DESTROY
    // -------------------------------------------------------------------------

    public function destroy(CrmTransaction $transaction): RedirectResponse
    {
        $leadId = $transaction->lead_id;

        $transaction->delete();

        return redirect()
            ->route('crm.leads.show', $leadId)
            ->with('success', 'Transaksi berhasil dihapus.');
    }
}
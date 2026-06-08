<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\CRM\Models\Lead;
use App\CRM\Models\LeadTransaction;
use App\CRM\Services\LeadTransactionService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LeadTransactionController extends Controller
{
    public function __construct(
        protected LeadTransactionService $service
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(): View
    {
        return view('crm.transactions.index', [
            'transactions' => $this->service->paginate(),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE (FROM LEAD)
    |--------------------------------------------------------------------------
    */
    public function create(Lead $lead): View
    {
        return view('crm.transactions.create', [
            'lead' => $lead,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STORE
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:cash,credit'],
            'amount' => ['required', 'numeric'],
            'down_payment' => ['nullable', 'numeric'],
            'tenor_months' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->service->create($lead, $validated);

        return redirect()
            ->route('crm.leads.show', $lead)
            ->with('success', 'Transaction berhasil dibuat.');
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(LeadTransaction $transaction): View
    {
        return view('crm.transactions.edit', [
            'transaction' => $transaction,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, LeadTransaction $transaction): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', 'in:cash,credit'],
            'amount' => ['required', 'numeric'],
            'down_payment' => ['nullable', 'numeric'],
            'tenor_months' => ['nullable', 'integer'],
            'status' => ['required', 'in:pending,approved,rejected,active,completed'],
            'notes' => ['nullable', 'string'],
        ]);

        $this->service->update($transaction, $validated);

        return back()->with('success', 'Transaction updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(LeadTransaction $transaction): RedirectResponse
    {
        $this->service->delete($transaction);

        return back()->with('success', 'Transaction deleted.');
    }

    /*
    |--------------------------------------------------------------------------
    | APPROVE
    |--------------------------------------------------------------------------
    */
    public function approve(LeadTransaction $transaction): RedirectResponse
    {
        $this->service->approve($transaction);

        return back()->with('success', 'Transaction approved.');
    }

    /*
    |--------------------------------------------------------------------------
    | REJECT
    |--------------------------------------------------------------------------
    */
    public function reject(LeadTransaction $transaction): RedirectResponse
    {
        $this->service->reject($transaction);

        return back()->with('success', 'Transaction rejected.');
    }
}
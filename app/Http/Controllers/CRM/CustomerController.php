<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\CRM\Models\Customer;
use App\CRM\Models\Lead;
use App\CRM\Services\CustomerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CustomerController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        protected CustomerService $customerService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | INDEX
    |--------------------------------------------------------------------------
    */
    public function index(Request $request): View
    {
        $customers = Customer::query()
            ->with(['lead', 'createdBy'])
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('phone', 'like', "%{$request->search}%");
            })
            ->latest()
            ->paginate(15);

        return view('crm.customers.index', compact('customers'));
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE (FROM LEAD)
    |--------------------------------------------------------------------------
    */
    public function create(Request $request): View
    {
        $lead = null;

        if ($request->lead_id) {
            $lead = Lead::findOrFail($request->lead_id);
        }

        return view('crm.customers.create', compact('lead'));
    }

    /*
    |--------------------------------------------------------------------------
    | STORE (CONVERT LEAD → CUSTOMER)
    |--------------------------------------------------------------------------
    */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
        ]);

        $lead = Lead::findOrFail($request->lead_id);

        $customer = $this->customerService->convert(
            $lead,
            $request->status ?? 'active'
        );

        return redirect()
            ->route('crm.customers.show', $customer)
            ->with('success', 'Customer berhasil dibuat dari lead.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW
    |--------------------------------------------------------------------------
    */
    public function show(Customer $customer): View
    {
        $customer->load(['lead', 'transactions']);

        return view('crm.customers.show', compact('customer'));
    }

    /*
    |--------------------------------------------------------------------------
    | EDIT
    |--------------------------------------------------------------------------
    */
    public function edit(Customer $customer): View
    {
        return view('crm.customers.edit', compact('customer'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($request->all());

        return redirect()
            ->route('crm.customers.show', $customer)
            ->with('success', 'Customer berhasil diperbarui.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()
            ->route('crm.customers.index')
            ->with('success', 'Customer berhasil dihapus.');
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE STATUS
    |--------------------------------------------------------------------------
    */
    public function updateStatus(Request $request, Customer $customer): RedirectResponse
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        $customer->update([
            'status' => $request->status,
        ]);

        return back()->with('success', 'Status customer diperbarui.');
    }
}
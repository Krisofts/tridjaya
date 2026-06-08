@extends('layouts.app')

@section('title', 'Create Transaction')

@section('content')

<div class="p-6 max-w-3xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">
            Create Transaction
        </h1>
        <p class="text-sm text-gray-500">
            Lead: {{ $lead->name }}
        </p>
    </div>

    <form method="POST"
          action="{{ route('crm.leads.transactions.store', $lead) }}"
          class="bg-white rounded-xl shadow p-6 space-y-5">

        @csrf

        {{-- TYPE --}}
        <div>
            <label class="block text-sm font-medium mb-1">Transaction Type</label>

            <select name="type" class="w-full border rounded-lg px-3 py-2" required>
                <option value="">Select Type</option>

                @foreach(['cash','credit','installment','dp','refund'] as $type)
                    <option value="{{ $type }}">
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- AMOUNT --}}
        <div>
            <label class="block text-sm font-medium mb-1">Amount</label>
            <input type="number" name="amount"
                   class="w-full border rounded-lg px-3 py-2"
                   placeholder="10000000"
                   required>
        </div>

        {{-- CREDIT FIELDS --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

            <div>
                <label class="block text-sm font-medium mb-1">Down Payment</label>
                <input type="number" name="down_payment"
                       class="w-full border rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Tenor (Months)</label>
                <input type="number" name="tenor_months"
                       class="w-full border rounded-lg px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Monthly Payment</label>
                <input type="number" name="monthly_payment"
                       class="w-full border rounded-lg px-3 py-2">
            </div>

        </div>

        {{-- STATUS (FROM CONFIG) --}}
        <div>
            <label class="block text-sm font-medium mb-1">Status</label>

            <select name="status" class="w-full border rounded-lg px-3 py-2">
                @foreach(config('crm.lead_transaction_status') ?? [
                    'pending' => 'Pending',
                    'approved' => 'Approved',
                    'rejected' => 'Rejected',
                    'active' => 'Active',
                    'completed' => 'Completed',
                ] as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        {{-- NOTES --}}
        <div>
            <label class="block text-sm font-medium mb-1">Notes</label>
            <textarea name="notes" rows="3"
                      class="w-full border rounded-lg px-3 py-2"
                      placeholder="Optional notes..."></textarea>
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-end gap-3">

            <a href="{{ route('crm.leads.show', $lead) }}"
               class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                Cancel
            </a>

            <button type="submit"
                    class="px-5 py-2 rounded-lg bg-green-600 text-white hover:bg-green-700">
                Save Transaction
            </button>

        </div>

    </form>

</div>

@endsection
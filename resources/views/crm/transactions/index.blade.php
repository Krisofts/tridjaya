@extends('layouts.app')

@section('title', 'Transactions')

@section('content')
<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex items-center justify-between">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Lead Transactions
            </h1>
            <p class="text-sm text-gray-500">
                Cash & Credit Management
            </p>
        </div>

        {{-- ADD BUTTON --}}
        <a href="#"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + Add Transaction
        </a>

    </div>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="p-3 text-left">Lead</th>
                    <th class="p-3 text-left">Type</th>
                    <th class="p-3 text-left">Amount</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Created By</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>

            <tbody>

                @forelse($transactions as $trx)
                    <tr class="border-t hover:bg-gray-50">

                        {{-- LEAD --}}
                        <td class="p-3 font-medium text-gray-900">
                            {{ $trx->lead?->name ?? '-' }}
                        </td>

                        {{-- TYPE --}}
                        <td class="p-3">
                            <span class="px-2 py-1 text-xs rounded bg-gray-100">
                                {{ strtoupper($trx->type) }}
                            </span>
                        </td>

                        {{-- AMOUNT --}}
                        <td class="p-3">
                            Rp {{ number_format($trx->amount, 0, ',', '.') }}
                        </td>

                        {{-- STATUS --}}
                        <td class="p-3">
                            @if($trx->status === 'approved')
                                <span class="text-green-600 text-xs font-medium">Approved</span>
                            @elseif($trx->status === 'rejected')
                                <span class="text-red-600 text-xs font-medium">Rejected</span>
                            @else
                                <span class="text-gray-600 text-xs font-medium">Pending</span>
                            @endif
                        </td>

                        {{-- CREATED BY --}}
                        <td class="p-3">
                            {{ $trx->createdBy?->name ?? '-' }}
                        </td>

                        {{-- ACTION --}}
                        <td class="p-3 text-right space-x-3">

                            <a href="{{ route('crm.transactions.edit', $trx) }}"
                               class="text-blue-600 hover:underline">
                                Edit
                            </a>

                            @if($trx->status === 'pending')
                                <form method="POST"
                                      action="{{ route('crm.transactions.approve', $trx) }}"
                                      class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-green-600 hover:underline">
                                        Approve
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('crm.transactions.reject', $trx) }}"
                                      class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="text-red-600 hover:underline">
                                        Reject
                                    </button>
                                </form>
                            @endif

                        </td>

                    </tr>
                @empty

                    <tr>
                        <td colspan="6" class="p-6 text-center text-gray-500">
                            No transactions found
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $transactions->links() }}
    </div>

</div>
@endsection
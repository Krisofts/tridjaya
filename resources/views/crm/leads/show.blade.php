@extends('layouts.app')

@section('title', $lead->name)

@section('content')

<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $lead->name }}
            </h1>
            <p class="text-sm text-gray-500">
                Lead detail, transactions & activity timeline
            </p>
        </div>

        <div class="flex gap-2 flex-wrap">

            <a href="{{ route('crm.leads.edit', $lead) }}"
               class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600">
                Edit
            </a>

            <a href="{{ route('crm.leads.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                Back
            </a>

        </div>

    </div>

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT --}}
        <div class="space-y-6">

            {{-- LEAD INFO --}}
            <div class="bg-white rounded-xl shadow p-5 space-y-4">

                <h2 class="font-semibold text-gray-900">Lead Info</h2>

                <div class="space-y-3 text-sm">

                    <div>
                        <div class="text-gray-500">Phone</div>
                        <div class="font-medium">{{ $lead->phone ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Status</div>
                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100">
                            {{ config('crm.lead_status')[$lead->status] ?? $lead->status }}
                        </span>
                    </div>

                    <div>
                        <div class="text-gray-500">Source</div>
                        <div>{{ config('crm.lead_source')[$lead->source] ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Interest</div>
                        <div>{{ config('crm.lead_interest')[$lead->interest] ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Assigned To</div>
                        <div>{{ $lead->assignedTo?->name ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Created By</div>
                        <div>{{ $lead->createdBy?->name ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Estimated Value</div>
                        <div>
                            Rp {{ number_format((float) $lead->estimated_value, 0, ',', '.') }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Created At</div>
                        <div>{{ $lead->created_at?->format('d M Y H:i') }}</div>
                    </div>

                </div>

            </div>

            {{-- ADDRESS --}}
            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="font-semibold mb-2">Address</h2>
                <p class="text-sm text-gray-600 whitespace-pre-line">
                    {{ $lead->address ?? '-' }}
                </p>
            </div>

            {{-- NOTES --}}
            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="font-semibold mb-2">Notes</h2>
                <p class="text-sm text-gray-600 whitespace-pre-line">
                    {{ $lead->notes ?? '-' }}
                </p>
            </div>

        </div>

        {{-- RIGHT --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TRANSACTIONS --}}
            <div class="bg-white rounded-xl shadow p-5">

                <div class="flex justify-between items-center mb-4">
                    <h2 class="font-semibold text-gray-900">
                        Transactions
                    </h2>

                    <a href="{{ route('crm.leads.transactions.create', $lead) }}"
                       class="px-3 py-1 bg-blue-600 text-white rounded-lg text-sm">
                        + Add Transaction
                    </a>
                </div>

                <div class="space-y-3">

                    @forelse($lead->transactions ?? [] as $trx)

                        <div class="border rounded-lg p-3 flex justify-between items-center">

                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ config('crm.transaction_type')[$trx->type] ?? $trx->type }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ config('crm.transaction_status')[$trx->status] ?? $trx->status }}
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="font-semibold">
                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </div>

                                @if($trx->type === 'credit')
                                    <div class="text-xs text-gray-500">
                                        {{ $trx->tenor_months }}x • Rp {{ number_format($trx->monthly_payment, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                        </div>

                    @empty
                        <div class="text-center text-gray-500 py-6">
                            No transactions yet
                        </div>
                    @endforelse

                </div>

            </div>

            {{-- ACTIVITY --}}
            <div class="bg-white rounded-xl shadow p-5">

                <h2 class="font-semibold mb-4">Activity Timeline</h2>

                <div class="space-y-4">

                    @forelse($lead->activities as $activity)

                        <div class="flex gap-3 border-l-2 border-gray-200 pl-4">

                            <div class="w-2 h-2 mt-2 rounded-full bg-blue-600"></div>

                            <div class="flex-1">

                                <div class="flex justify-between">
                                    <div class="font-medium">
                                        {{ $activity->title }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $activity->created_at?->format('d M Y H:i') }}
                                    </div>
                                </div>

                                @if($activity->description)
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ $activity->description }}
                                    </p>
                                @endif

                                <div class="text-xs text-gray-400 mt-2">
                                    by {{ $activity->createdBy?->name ?? 'System' }}
                                </div>

                            </div>

                        </div>

                    @empty
                        <div class="text-center text-gray-500 py-10">
                            No activity found
                        </div>
                    @endforelse

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
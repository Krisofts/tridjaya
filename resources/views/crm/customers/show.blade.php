@extends('layouts.app')

@section('title', $customer->name)

@section('content')

<div class="p-6 max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $customer->name }}
            </h1>
            <p class="text-sm text-gray-500">
                Customer Detail & History
            </p>
        </div>

        <div class="flex gap-2">

            <a href="{{ route('crm.customers.edit', $customer) }}"
               class="px-4 py-2 rounded-lg bg-yellow-500 text-white hover:bg-yellow-600">
                Edit
            </a>

            <a href="{{ route('crm.customers.index') }}"
               class="px-4 py-2 rounded-lg bg-gray-200 text-gray-700 hover:bg-gray-300">
                Back
            </a>

        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT --}}
        <div class="space-y-6">

            {{-- CUSTOMER INFO --}}
            <div class="bg-white rounded-xl shadow p-5 space-y-4">

                <h2 class="font-semibold text-gray-900">Customer Info</h2>

                <div class="text-sm space-y-3">

                    <div>
                        <div class="text-gray-500">Phone</div>
                        <div class="font-medium">{{ $customer->phone ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Type</div>
                        <div class="font-medium">{{ $customer->type ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-gray-500">Converted At</div>
                        <div class="font-medium">
                            {{ $customer->converted_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Converted By</div>
                        <div class="font-medium">
                            {{ $customer->convertedBy?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Created By</div>
                        <div class="font-medium">
                            {{ $customer->createdBy?->name ?? '-' }}
                        </div>
                    </div>

                </div>

            </div>

            {{-- ADDRESS --}}
            <div class="bg-white rounded-xl shadow p-5">
                <h2 class="font-semibold mb-2">Address</h2>
                <p class="text-sm text-gray-600 whitespace-pre-line">
                    {{ $customer->address ?? '-' }}
                </p>
            </div>

            {{-- SOURCE LEAD --}}
            <div class="bg-white rounded-xl shadow p-5">

                <h2 class="font-semibold mb-3">Source Lead</h2>

                <div class="text-sm space-y-2">

                    <div>
                        <div class="text-gray-500">Lead Name</div>
                        <div class="font-medium">
                            {{ $customer->lead->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500">Lead Status</div>
                        <div class="font-medium">
                            {{ $customer->lead->status ?? '-' }}
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TRANSACTIONS --}}
            <div class="bg-white rounded-xl shadow p-5">

                <h2 class="font-semibold text-gray-900 mb-4">
                    Transactions History
                </h2>

                <div class="space-y-3">

                    @forelse($customer->transactions as $trx)

                        <div class="border rounded-lg p-3 flex justify-between">

                            <div>
                                <div class="font-medium">
                                    {{ $trx->type }}
                                </div>

                                <div class="text-xs text-gray-500">
                                    {{ $trx->status }}
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
                            No transactions
                        </div>
                    @endforelse

                </div>

            </div>

            {{-- SUMMARY --}}
            <div class="bg-white rounded-xl shadow p-5">

                <h2 class="font-semibold mb-4">Summary</h2>

                <div class="grid grid-cols-2 gap-4 text-sm">

                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-gray-500">Total Transaction</div>
                        <div class="font-bold">
                            Rp {{ number_format($customer->totalTransactionAmount(), 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-lg">
                        <div class="text-gray-500">Cash Revenue</div>
                        <div class="font-bold">
                            Rp {{ number_format($customer->totalCashRevenue(), 0, ',', '.') }}
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
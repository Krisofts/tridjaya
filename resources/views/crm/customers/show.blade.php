@extends('layouts.app')

@section('title', $customer->name)

@section('content')

<x-common.page-breadcrumb :pageTitle="$customer->name" />

<div class="max-w-7xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

        <div>
            <h1 class="text-2xl font-semibold text-gray-800 dark:text-white/90">
                {{ $customer->name }}
            </h1>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Customer detail & transaction history
            </p>
        </div>

        <div class="flex gap-2">

            <a href="{{ route('crm.customers.edit', $customer) }}"
               class="inline-flex items-center justify-center rounded-lg bg-warning-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-warning-600">

                Edit
            </a>

            <a href="{{ route('crm.customers.index') }}"
               class="inline-flex items-center justify-center rounded-lg border border-gray-300
                      bg-white px-4 py-2.5 text-sm font-medium text-gray-700
                      hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800
                      dark:text-gray-300 dark:hover:bg-gray-700">

                Back
            </a>

        </div>

    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- LEFT --}}
        <div class="space-y-6">

            {{-- CUSTOMER INFO --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">

                <h2 class="font-semibold text-gray-800 dark:text-white/90 mb-4">
                    Customer Info
                </h2>

                <div class="space-y-3 text-sm">

                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Phone</div>
                        <div class="font-medium text-gray-800 dark:text-white/90">
                            {{ $customer->phone ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Type</div>
                        <div class="font-medium text-gray-800 dark:text-white/90">
                            {{ $customer->type ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Converted At</div>
                        <div class="font-medium text-gray-800 dark:text-white/90">
                            {{ $customer->converted_at?->format('d M Y H:i') ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Converted By</div>
                        <div class="font-medium text-gray-800 dark:text-white/90">
                            {{ $customer->convertedBy?->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Created By</div>
                        <div class="font-medium text-gray-800 dark:text-white/90">
                            {{ $customer->createdBy?->name ?? '-' }}
                        </div>
                    </div>

                </div>

            </div>

            {{-- ADDRESS --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">

                <h2 class="font-semibold text-gray-800 dark:text-white/90 mb-2">
                    Address
                </h2>

                <p class="text-sm text-gray-500 dark:text-gray-400 whitespace-pre-line">
                    {{ $customer->address ?? '-' }}
                </p>

            </div>

            {{-- SOURCE LEAD --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">

                <h2 class="font-semibold text-gray-800 dark:text-white/90 mb-4">
                    Source Lead
                </h2>

                <div class="space-y-3 text-sm">

                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Lead Name</div>
                        <div class="font-medium text-gray-800 dark:text-white/90">
                            {{ $customer->lead->name ?? '-' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-gray-500 dark:text-gray-400">Lead Status</div>
                        <div class="font-medium text-gray-800 dark:text-white/90">
                            {{ $customer->lead->status ?? '-' }}
                        </div>
                    </div>

                </div>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- TRANSACTIONS --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">

                <h2 class="font-semibold text-gray-800 dark:text-white/90 mb-4">
                    Transactions History
                </h2>

                <div class="space-y-3">

                    @forelse($customer->transactions as $trx)

                        <div class="flex items-center justify-between rounded-xl border border-gray-100 p-4 dark:border-gray-800">

                            <div>
                                <div class="font-medium text-gray-800 dark:text-white/90">
                                    {{ ucfirst($trx->type) }}
                                </div>

                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ ucfirst($trx->status) }}
                                </div>
                            </div>

                            <div class="text-right">
                                <div class="font-semibold text-gray-800 dark:text-white/90">
                                    Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                </div>

                                @if($trx->type === 'credit')
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $trx->tenor_months }}x • Rp {{ number_format($trx->monthly_payment, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                        </div>

                    @empty

                        <div class="text-center text-gray-500 dark:text-gray-400 py-6">
                            No transactions found
                        </div>

                    @endforelse

                </div>

            </div>

            {{-- SUMMARY --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03]">

                <h2 class="font-semibold text-gray-800 dark:text-white/90 mb-4">
                    Summary
                </h2>

                <div class="grid grid-cols-2 gap-4 text-sm">

                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900">
                        <div class="text-gray-500 dark:text-gray-400">Total Transaction</div>
                        <div class="font-bold text-gray-800 dark:text-white/90">
                            Rp {{ number_format($customer->totalTransactionAmount(), 0, ',', '.') }}
                        </div>
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-900">
                        <div class="text-gray-500 dark:text-gray-400">Cash Revenue</div>
                        <div class="font-bold text-gray-800 dark:text-white/90">
                            Rp {{ number_format($customer->totalCashRevenue(), 0, ',', '.') }}
                        </div>
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@endsection
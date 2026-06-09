@extends('layouts.app')

@section('title', $lead->name)

@section('content')

<x-common.page-breadcrumb :pageTitle="$lead->name" />

<div class="max-w-7xl mx-auto space-y-6">

    {{-- MAIN GRID --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        {{-- LEFT: WORKFLOW (PRIMARY) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- PROFILE --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

                @php
                    $initial = strtoupper(substr($lead->name, 0, 1));
                    $phone = preg_replace('/[^0-9]/', '', $lead->phone);
                    $wa = $phone ? "https://wa.me/62" . ltrim($phone, '0') : '#';
                @endphp

                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-700 dark:text-white font-bold">
                            {{ $initial }}
                        </div>

                        <div>
                            <p class="font-semibold text-gray-900 dark:text-white">
                                {{ $lead->name }}
                            </p>
                            <p class="text-xs text-gray-500">
                                Lead Profile
                            </p>
                        </div>
                    </div>

                    <div class="flex gap-2">

                        <a href="{{ $wa }}" target="_blank"
                           class="px-4 py-2 text-sm rounded-xl bg-green-500 text-white hover:bg-green-600 transition">
                            WhatsApp
                        </a>

                        <a href="tel:{{ $lead->phone }}"
                           class="px-4 py-2 text-sm rounded-xl bg-blue-500 text-white hover:bg-blue-600 transition">
                            Call
                        </a>

                        <form method="POST" action="#">
                            @csrf
                            <button class="px-4 py-2 text-sm rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
                                Convert
                            </button>
                        </form>

                    </div>

                </div>

            </div>

            {{-- WORKFLOW --}}
            <div x-data="{ tab: 'activity' }"
                 class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

                {{-- HEADER --}}
                <div class="flex items-center justify-between border-b border-gray-200 dark:border-gray-800 px-5 py-3">

                    <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                        Workflow & Activity
                    </h2>

                    <div class="flex gap-2 text-xs">

                        @foreach(['activity','notes','calls','tasks'] as $t)
                            <button
                                @click="tab='{{ $t }}'"
                                class="px-3 py-1.5 rounded-lg transition"
                                :class="tab==='{{ $t }}'
                                    ? 'bg-gray-900 text-white dark:bg-white dark:text-gray-900'
                                    : 'text-gray-500 hover:bg-gray-100 dark:hover:bg-gray-800'">
                                {{ ucfirst($t) }}
                            </button>
                        @endforeach

                    </div>

                </div>

                {{-- CONTENT --}}
                <div class="p-5 md:p-6 min-h-[420px]">

                    <div x-show="tab==='activity'" class="space-y-4">

                        @forelse($lead->activities as $activity)
                            <div class="flex gap-3">
                                <div class="w-2 h-2 mt-2 rounded-full bg-blue-500"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $activity->title }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ $activity->created_at?->format('d M Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">No activity yet</p>
                        @endforelse

                    </div>

                    <div x-show="tab==='notes'" class="text-sm text-gray-500">
                        Notes workflow
                    </div>

                    <div x-show="tab==='calls'" class="text-sm text-gray-500">
                        Calls workflow
                    </div>

                    <div x-show="tab==='tasks'" class="text-sm text-gray-500">
                        Tasks workflow
                    </div>

                </div>

            </div>

            {{-- TRANSACTIONS --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                    Transactions
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                    @forelse($lead->transactions ?? [] as $trx)

                        <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-4">

                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $trx->type }}
                            </p>

                            <p class="text-xs text-gray-500 mt-1">
                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </p>

                        </div>

                    @empty
                        <p class="text-sm text-gray-500">No transactions</p>
                    @endforelse

                </div>

            </div>

        </div>

        {{-- RIGHT: INFO (SECONDARY) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- LEAD INFO --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-gray-800"></div>

                    <div>
                        <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                            Lead Info
                        </h2>
                        <p class="text-xs text-gray-500">Customer detail</p>
                    </div>
                </div>

                <div class="space-y-3 text-sm">

                    <div>
                        <p class="text-xs text-gray-500">Phone</p>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $lead->phone ?? '-' }}</p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Status</p>
                        <span class="inline-flex px-2 py-1 text-xs rounded-lg bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                            {{ config('crm.lead_status')[$lead->status] ?? $lead->status }}
                        </span>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Source</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ config('crm.lead_source')[$lead->source] ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Interest</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ config('crm.lead_interest')[$lead->interest] ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">Assigned</p>
                        <p class="font-medium text-gray-900 dark:text-white">
                            {{ $lead->assignedTo?->name ?? '-' }}
                        </p>
                    </div>

                </div>

            </div>

            {{-- NOTES --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

                <h2 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">
                    Notes
                </h2>

                <p class="text-sm text-gray-600 dark:text-gray-400 whitespace-pre-line">
                    {{ $lead->notes ?? '-' }}
                </p>

            </div>

        </div>

    </div>

</div>

@endsection
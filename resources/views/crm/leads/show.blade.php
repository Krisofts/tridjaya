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
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

                    @php
                        $initial = strtoupper(substr($lead->name, 0, 1));
                        $phone = preg_replace('/[^0-9]/', '', $lead->phone);
                        $wa = $phone ? 'https://wa.me/62' . ltrim($phone, '0') : '#';
                    @endphp

                    <div class="flex items-center justify-between">

                        <div class="flex items-center gap-3">
                            <div
                                class="w-12 h-12 rounded-xl bg-gray-100 dark:bg-gray-800 flex items-center justify-center text-gray-700 dark:text-white font-bold">
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
                                <button
                                    class="px-4 py-2 text-sm rounded-xl bg-gray-900 text-white hover:bg-gray-800 transition">
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
                    <div
                        class="flex flex-col w-full gap-5 xl:flex-row xl:items-center border-b border-gray-200 dark:border-gray-800 px-5 py-4">

                        {{-- TAB GROUP ONLY --}}
                        <div
                            class="flex flex-wrap items-center gap-x-1 gap-y-2 rounded-lg bg-gray-100 p-0.5 dark:bg-gray-900">

                            @foreach (['activity', 'notes', 'calls', 'tasks'] as $t)
                                <button @click="tab='{{ $t }}'"
                                    class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-md transition"
                                    :class="tab === '{{ $t }}' ?
                                        'text-gray-900 dark:text-white bg-white dark:bg-gray-800' :
                                        'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'">

                                    {{ ucfirst($t) }}

                                </button>
                            @endforeach

                        </div>

                    </div>

                    {{-- CONTENT --}}
                    <div class="p-5 md:p-6 min-h-[420px]">

                        <div x-show="tab==='activity'" x-cloak>

                            <div class="space-y-3">

                                @forelse($lead->activities as $activity)
                                    <div
                                        class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.02]">

                                        <div class="flex items-start justify-between gap-4">

                                            <div class="min-w-0 flex-1">

                                                <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                                    {{ $activity->title }}
                                                </h4>

                                                @if ($activity->description)
                                                    <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                        {{ $activity->description }}
                                                    </p>
                                                @endif

                                                <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">

                                                    <div
                                                        class="flex h-6 w-6 items-center justify-center rounded-full bg-gray-100 font-medium dark:bg-gray-800">
                                                        {{ strtoupper(substr($activity->createdBy?->name ?? 'S', 0, 1)) }}
                                                    </div>

                                                    <span>
                                                        Created by
                                                        <span class="font-medium text-gray-700 dark:text-gray-300">
                                                            {{ $activity->createdBy?->name ?? 'System' }}
                                                        </span>
                                                    </span>

                                                </div>

                                            </div>

                                            <div class="shrink-0 text-right">

                                                <p class="text-xs font-medium text-gray-900 dark:text-white">
                                                    {{ $activity->created_at?->format('d M Y') }}
                                                </p>

                                                <p class="mt-1 text-xs text-gray-500">
                                                    {{ $activity->created_at?->format('H:i') }}
                                                </p>

                                            </div>

                                        </div>

                                    </div>

                                @empty

                                    <div class="py-10 text-center">
                                        <p class="text-sm text-gray-500">
                                            No activity recorded yet.
                                        </p>
                                    </div>
                                @endforelse

                            </div>

                        </div>

                        <div x-show="tab==='notes'" x-cloak>

                            @if (filled($lead->notes))
                                <div
                                    class="rounded-xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-white/[0.02]">

                                    <div class="flex items-center justify-between mb-4">

                                        <div>
                                            <h3 class="text-sm font-medium text-gray-900 dark:text-white">
                                                Lead Notes
                                            </h3>

                                            <p class="text-xs text-gray-500">
                                                Internal notes and customer information
                                            </p>
                                        </div>

                                        <div class="text-right">
                                            <p class="text-xs text-gray-500">
                                                Updated
                                            </p>
                                            <p class="text-xs font-medium text-gray-900 dark:text-white">
                                                {{ $lead->updated_at?->diffForHumans() }}
                                            </p>
                                        </div>

                                    </div>

                                    <div class="rounded-lg bg-gray-50 dark:bg-gray-900/50 p-4">

                                        <p class="text-sm leading-6 text-gray-700 dark:text-gray-300 whitespace-pre-line">
                                            {{ $lead->notes }}
                                        </p>

                                    </div>

                                </div>
                            @else
                                <div
                                    class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-8 text-center">

                                    <p class="text-sm text-gray-500">
                                        No notes available.
                                    </p>

                                </div>
                            @endif

                        </div>

                        <div x-show="tab==='calls'" class="text-sm text-gray-500">
                            Calls workflow
                        </div>

                        <div x-show="tab==='tasks'" x-cloak>

    <div class="space-y-3">

        @forelse($lead->tasks as $task)

            <div
                class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">

                <div class="flex items-start justify-between gap-4">

                    <div class="min-w-0 flex-1">

                        <div class="flex items-center gap-2 flex-wrap">

                            <h4 class="font-medium text-gray-900 dark:text-white">
                                {{ $task->title }}
                            </h4>

                            <span
                                class="inline-flex rounded-lg px-2 py-0.5 text-xs font-medium
                                @class([
                                    'bg-gray-100 text-gray-700' => $task->status === 'open',
                                    'bg-blue-100 text-blue-700' => $task->status === 'in_progress',
                                    'bg-green-100 text-green-700' => $task->status === 'done',
                                    'bg-red-100 text-red-700' => $task->status === 'cancelled',
                                ])">

                                {{ str($task->status)->replace('_',' ')->title() }}

                            </span>

                        </div>

                        @if($task->description)
                            <p class="mt-2 text-sm text-gray-500">
                                {{ $task->description }}
                            </p>
                        @endif

                        <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-500">

                            <span>
                                Assigned to
                                <span class="font-medium text-gray-700 dark:text-gray-300">
                                    {{ $task->assignedTo?->name ?? '-' }}
                                </span>
                            </span>

                            @if($task->due_date)
                                <span>
                                    Due
                                    <span class="font-medium text-gray-700 dark:text-gray-300">
                                        {{ $task->due_date->format('d M Y H:i') }}
                                    </span>
                                </span>
                            @endif

                        </div>

                    </div>

                    <div class="text-right">

                        <span
                            class="inline-flex rounded-lg px-2 py-1 text-xs font-medium
                            @class([
                                'bg-red-100 text-red-700' => $task->priority === 'high',
                                'bg-yellow-100 text-yellow-700' => $task->priority === 'medium',
                                'bg-green-100 text-green-700' => $task->priority === 'low',
                            ])">

                            {{ ucfirst($task->priority) }}

                        </span>

                        @if(
                            $task->due_date &&
                            $task->due_date->isPast() &&
                            !in_array($task->status, ['done','cancelled'])
                        )
                            <div class="mt-2 text-xs font-medium text-red-600">
                                Overdue
                            </div>
                        @endif

                    </div>

                </div>

            </div>

        @empty

            <div class="py-10 text-center">
                <p class="text-sm text-gray-500">
                    No tasks available.
                </p>
            </div>

        @endforelse

    </div>

</div>

                    </div>

                </div>


            </div>

            {{-- RIGHT: INFO (SECONDARY) --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- LEAD INFO --}}
                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

                    @php
                        $initial = strtoupper(substr($lead->name, 0, 1));
                    @endphp

                    {{-- PROFILE --}}
                    <div class="flex items-center gap-3">

                        <div
                            class="flex h-12 w-12 items-center justify-center rounded-xl bg-gray-100 dark:bg-gray-800 font-semibold text-gray-700 dark:text-white">
                            {{ $initial }}
                        </div>

                        <div class="min-w-0">

                            <h2 class="font-semibold text-gray-900 dark:text-white truncate">
                                {{ $lead->name }}
                            </h2>

                            <p class="text-xs text-gray-500">
                                Lead Customer
                            </p>

                        </div>

                    </div>

                    {{-- INFO --}}
                    <div class="mt-5 space-y-4">

                        <div>
                            <p class="text-xs text-gray-500 mb-1">
                                Phone
                            </p>

                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $lead->phone ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">
                                Status
                            </p>

                            <span
                                class="inline-flex rounded-lg bg-gray-100 dark:bg-gray-800 px-2.5 py-1 text-xs font-medium text-gray-700 dark:text-gray-300">
                                {{ $lead->status_label }}
                            </span>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">
                                Source
                            </p>

                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $lead->source_label ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">
                                Interest
                            </p>

                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $lead->interest_label ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="text-xs text-gray-500 mb-1">
                                Assigned To
                            </p>

                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $lead->assignedTo?->name ?: '-' }}
                            </p>
                        </div>

                    </div>

                    {{-- FOOTER --}}
                    <div class="mt-5 pt-4 border-t border-gray-200 dark:border-gray-800">

                        <p class="text-xs text-gray-500">
                            Created
                        </p>

                        <p class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ $lead->created_at?->format('d M Y') }}
                        </p>

                    </div>

                </div>

                <div
                    class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

                    <div class="flex items-center justify-between mb-4">

                        <div>
                            <h2 class="text-sm font-semibold text-gray-900 dark:text-white">
                                Transactions
                            </h2>

                            <p class="text-xs text-gray-500">
                                Customer purchases
                            </p>
                        </div>

                        <button @click="$dispatch('open-create-transaction-modal')"
                            class="inline-flex items-center gap-1 rounded-lg bg-brand-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-brand-600">

                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4v16m8-8H4" />
                            </svg>

                            New
                        </button>

                    </div>

                    @if ($lead->transactions->isNotEmpty())

                        <div class="space-y-3">

                            @foreach ($lead->transactions as $trx)
                                <div class="rounded-xl border border-gray-200 dark:border-gray-800 p-3">

                                    <div class="flex items-start justify-between gap-3">

                                        <div class="min-w-0 flex-1">

                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                                {{ $trx->type }}
                                            </p>

                                            <p class="text-xs text-gray-500 mt-1">
                                                {{ $trx->created_at?->format('d M Y') }}
                                            </p>

                                        </div>

                                        <div class="text-right shrink-0">

                                            <p class="text-sm font-semibold text-green-600">
                                                Rp {{ number_format($trx->amount, 0, ',', '.') }}
                                            </p>

                                        </div>

                                    </div>

                                </div>
                            @endforeach

                        </div>

                        {{-- Summary --}}
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-800">

                            <div class="flex items-center justify-between">

                                <span class="text-sm text-gray-500">
                                    Total
                                </span>

                                <span class="font-semibold text-gray-900 dark:text-white">
                                    Rp {{ number_format($lead->transactions->sum('amount'), 0, ',', '.') }}
                                </span>

                            </div>

                        </div>
                    @else
                        <div class="rounded-xl border border-dashed border-gray-300 dark:border-gray-700 p-6 text-center">

                            <p class="text-sm text-gray-500">
                                No transactions yet.
                            </p>

                        </div>

                    @endif

                </div>


            </div>

        </div>

    </div>



    <x-ui.modal x-data="{ open: false }" @open-create-transaction-modal.window="open = true" :isOpen="false"
        class="max-w-[700px]">

        <div class="relative w-full max-w-[700px] rounded-3xl bg-white p-6 dark:bg-gray-900">

            <div class="mb-6">

                <h4 class="text-2xl font-semibold text-gray-800 dark:text-white">
                    New Transaction
                </h4>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Create a transaction for {{ $lead->name }}
                </p>

            </div>

            <form method="POST" action="{{ route('crm.leads.transactions.store', ['lead' => $lead->id]) }}"
                class="space-y-5">
                @csrf

                <input type="hidden" name="lead_id" value="{{ $lead->id }}">

                {{-- TYPE --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Transaction Type
                    </label>

                    <select name="type" required
                        class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                        <option value="">Select Type</option>
                        <option value="cash">Cash Sale</option>
                        <option value="credit">Credit Sale</option>
                        <option value="dp">Down Payment</option>
                        <option value="payment">Installment Payment</option>
                    </select>

                </div>

                {{-- AMOUNT --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Amount
                    </label>

                    <input type="number" name="amount" min="0" required placeholder="0"
                        class="h-11 w-full rounded-lg border border-gray-300 px-4 text-sm dark:border-gray-700 dark:bg-gray-900">

                </div>

                {{-- DESCRIPTION --}}
                <div>

                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                        Description
                    </label>

                    <textarea name="description" rows="4"
                        class="w-full rounded-lg border border-gray-300 px-4 py-3 text-sm dark:border-gray-700 dark:bg-gray-900"
                        placeholder="Additional notes..."></textarea>

                </div>

                {{-- FOOTER --}}
                <div class="flex items-center justify-end gap-3 pt-4">

                    <button @click="open = false" type="button"
                        class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:text-gray-300">
                        Cancel
                    </button>

                    <button type="submit"
                        class="rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                        Save Transaction
                    </button>

                </div>

            </form>

        </div>

    </x-ui.modal>

@endsection

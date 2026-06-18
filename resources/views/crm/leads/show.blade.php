@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Lead Detail" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'CRM Leads', 'url' => route('crm.leads.index')],
        ['label' => 'Detail'],
    ]" />

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- ===================================================== --}}
        {{-- LEFT CONTENT --}}
        {{-- ===================================================== --}}
        <div class="space-y-6 xl:col-span-2">

            {{-- LEAD PROFILE --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

                {{-- HEADER --}}
                <div class="bg-gradient-to-r from-brand-500/10 via-brand-500/5 to-transparent px-5 py-5 lg:px-6">

                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                        {{-- PROFILE --}}
                        <div class="flex items-center gap-4">

                            {{-- Avatar --}}
                            <div
                                class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-500 text-xl font-bold text-white shadow-sm">
                                {{ strtoupper(substr($lead->name, 0, 1)) }}
                            </div>

                            <div>

                                <div class="flex flex-wrap items-center gap-2">

                                    <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                        {{ $lead->name }}
                                    </h2>

                                    @if ($lead->stage)
                                        <x-ui.badge color="primary">
                                            {{ $lead->stage->name }}
                                        </x-ui.badge>
                                    @endif

                                </div>

                                <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">

                                    <span>{{ $lead->lead_code }}</span>

                                    @if ($lead->pipeline)
                                        <span>•</span>
                                        <span>{{ $lead->pipeline->name }}</span>
                                    @endif

                                    @if ($lead->assignedUser)
                                        <span>•</span>
                                        <span>{{ $lead->assignedUser->name }}</span>
                                    @endif

                                </div>

                            </div>

                        </div>

                        {{-- ACTIONS --}}
                        <div class="flex flex-wrap gap-2">

                            @if ($lead->phone)
                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $lead->phone) }}" target="_blank"
                                    class="inline-flex items-center rounded-xl bg-success-500 px-4 py-2 text-sm font-medium text-white transition hover:opacity-90">
                                    WhatsApp
                                </a>

                                <a href="tel:{{ $lead->phone }}"
                                    class="inline-flex items-center rounded-xl bg-brand-500 px-4 py-2 text-sm font-medium text-white transition hover:opacity-90">
                                    Call
                                </a>
                            @endif

                            <a href="{{ route('crm.leads.edit', $lead) }}"
                                class="inline-flex items-center rounded-xl border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                                Edit Lead
                            </a>

                        </div>

                    </div>

                </div>

                {{-- QUICK INFO --}}
                <div class="grid grid-cols-2 gap-px bg-gray-200 dark:bg-gray-800 lg:grid-cols-4">

                    <div class="bg-white p-4 dark:bg-gray-900">
                        <p class="text-xs uppercase tracking-wide text-gray-500">
                            Phone
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $lead->phone ?: '-' }}
                        </p>
                    </div>

                    <div class="bg-white p-4 dark:bg-gray-900">
                        <p class="text-xs uppercase tracking-wide text-gray-500">
                            Source
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $lead->source?->name ?? '-' }}
                        </p>
                    </div>

                    <div class="bg-white p-4 dark:bg-gray-900">
                        <p class="text-xs uppercase tracking-wide text-gray-500">
                            Activities
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $activities->count() }}
                        </p>
                    </div>

                    <div class="bg-white p-4 dark:bg-gray-900">
                        <p class="text-xs uppercase tracking-wide text-gray-500">
                            Lead Age
                        </p>

                        <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                            {{ $lead->created_at->diffForHumans() }}
                        </p>
                    </div>

                </div>

                {{-- EXTRA INFO --}}
                <div class="border-t border-gray-200 px-5 py-4 dark:border-gray-800">

                    <div class="grid grid-cols-1 gap-4 lg:grid-cols-2">

                        <div>
                            <p class="mb-1 text-xs uppercase tracking-wide text-gray-500">
                                Interest
                            </p>

                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                {{ $lead->interest ?: '-' }}
                            </p>
                        </div>

                        <div>
                            <p class="mb-1 text-xs uppercase tracking-wide text-gray-500">
                                Address
                            </p>

                            <p class="text-sm text-gray-800 dark:text-gray-200">
                                {{ $lead->address ?: '-' }}
                            </p>
                        </div>

                    </div>

                </div>

            </div>

            {{-- ===================================================== --}}
            {{-- TABS --}}
            {{-- ===================================================== --}}
            <x-common.component-card>

                <div x-data="{ tab: 'timeline' }">

                    {{-- TAB HEADER --}}
                    <div class="mb-6">

                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                            {{-- TABS --}}
                            <div
                                class="flex flex-wrap items-center gap-x-1 gap-y-2 rounded-xl bg-gray-100 p-1 dark:bg-gray-900">

                                <button @click="tab='timeline'"
                                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                                    :class="tab === 'timeline'
                                        ?
                                        'bg-white text-gray-900 shadow-sm dark:bg-gray-800 dark:text-white' :
                                        'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">

                                    Timeline

                                    <span
                                        class="inline-flex rounded-full bg-brand-50 px-2 py-0.5 text-xs font-medium text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                                        {{ $activities->count() }}
                                    </span>

                                </button>

                                <button @click="tab='tasks'"
                                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                                    :class="tab === 'tasks'
                                        ?
                                        'bg-white text-gray-900 shadow-sm dark:bg-gray-800 dark:text-white' :
                                        'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">

                                    Tasks

                                    <span
                                        class="inline-flex rounded-full bg-white px-2 py-0.5 text-xs font-medium dark:bg-white/[0.03]">
                                        0
                                    </span>

                                </button>

                                <button @click="tab='notes'"
                                    class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                                    :class="tab === 'notes'
                                        ?
                                        'bg-white text-gray-900 shadow-sm dark:bg-gray-800 dark:text-white' :
                                        'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">

                                    Notes

                                    <span
                                        class="inline-flex rounded-full bg-white px-2 py-0.5 text-xs font-medium dark:bg-white/[0.03]">
                                        0
                                    </span>

                                </button>

                            </div>

                            {{-- ACTION --}}
                            <div class="flex flex-wrap gap-2">



                                <button
                                    class="inline-flex items-center gap-2 rounded-lg bg-brand-500 px-4 py-2 text-sm font-medium text-white hover:bg-brand-600">
                                    <svg class="fill-current" width="18" height="18" viewBox="0 0 20 20">
                                        <path d="M10 4v12M4 10h12" stroke="currentColor" stroke-width="2"
                                            stroke-linecap="round" />
                                    </svg>
                                    Add Activity



                                </button>

                            </div>

                        </div>

                    </div>

                    {{-- TIMELINE --}}
                    <div x-show="tab==='timeline'">

                        <div class="space-y-5">

                            

                            @foreach ($activities as $activity)
                                <div class="border-l-2 border-gray-300 pl-4">

                                    <p class="font-semibold">
                                        {{ ucfirst(str_replace('_', ' ', $activity->type)) }}
                                    </p>

                                    @if ($activity->title)
                                        <p class="text-sm">
                                            {{ $activity->title }}
                                        </p>
                                    @endif

                                    @if ($activity->description)
                                        <p class="mt-1 text-sm text-gray-500">
                                            {{ $activity->description }}
                                        </p>
                                    @endif

                                    <p class="mt-1 text-xs text-gray-400">
                                        {{ $activity->created_at->format('d M Y H:i') }}

                                        @if ($activity->user)
                                            • {{ $activity->user->name }}
                                        @endif
                                    </p>

                                </div>
                            @endforeach

                            {{ $activities->links() }}

                        </div>

                    </div>

                    {{-- TASK --}}
                    <div x-show="tab==='tasks'" class="space-y-4">

                        @forelse ($tasks as $task)
                            <div class="rounded-xl border border-gray-200 p-4 dark:border-gray-800">

                                <div class="flex items-start justify-between gap-4">

                                    <div>
                                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ $task->title }}
                                        </p>

                                        @if ($task->description)
                                            <p class="mt-1 text-sm text-gray-500">
                                                {{ $task->description }}
                                            </p>
                                        @endif

                                        <div class="mt-2 flex flex-wrap gap-2 text-xs text-gray-500">

                                            <span class="rounded-full bg-gray-100 px-2 py-1 dark:bg-gray-800">
                                                {{ ucfirst($task->status) }}
                                            </span>

                                            <span class="rounded-full bg-gray-100 px-2 py-1 dark:bg-gray-800">
                                                Priority: {{ ucfirst($task->priority) }}
                                            </span>

                                            <span class="rounded-full bg-gray-100 px-2 py-1 dark:bg-gray-800">
                                                Due:
                                                {{ $task->due_at ? $task->due_at->format('d M Y H:i') : '-' }}
                                            </span>

                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2">

                                        @if ($task->status !== 'completed')
                                            <form method="POST" action="{{ route('crm.tasks.complete', $task) }}">
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    class="rounded-lg bg-success-500 px-3 py-1 text-xs font-medium text-white hover:bg-success-600">
                                                    Done
                                                </button>
                                            </form>
                                        @else
                                            <span
                                                class="rounded-lg bg-gray-200 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                                Completed
                                            </span>
                                        @endif

                                    </div>

                                </div>

                            </div>

                        @empty

                            <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center">
                                <p class="text-sm text-gray-500">
                                    No task available.
                                </p>
                            </div>
                        @endforelse

                    </div>

                    {{-- NOTE --}}
                    <div x-show="tab==='notes'">

                        <div class="rounded-xl border border-dashed border-gray-300 p-8 text-center">
                            <p class="text-sm text-gray-500">
                                No notes available.
                            </p>
                        </div>

                    </div>

                </div>

            </x-common.component-card>

        </div>

        {{-- ===================================================== --}}
        {{-- RIGHT SIDEBAR --}}
        {{-- ===================================================== --}}
        <div class="space-y-6">

            {{-- LEAD STATUS --}}
            <x-common.component-card title="Lead Status" desc="Current lead progress">

                <div class="space-y-4">

                    <div>
                        <p class="text-xs text-gray-500">
                            Pipeline
                        </p>

                        <p class="font-semibold">
                            {{ $lead->pipeline?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">
                            Stage
                        </p>

                        <p class="font-semibold">
                            {{ $lead->stage?->name ?? '-' }}
                        </p>
                    </div>

                    <div>
                        <p class="text-xs text-gray-500">
                            Created
                        </p>

                        <p class="font-semibold">
                            {{ $lead->created_at->format('d M Y') }}
                        </p>
                    </div>

                </div>

            </x-common.component-card>

            {{-- TRANSACTION --}}
            <x-common.component-card title="Transaction Detail" desc="Sales transaction summary">

                <div class="space-y-4">

                    <div class="rounded-xl bg-gray-50 p-4 dark:bg-gray-800">

                        <p class="text-sm text-gray-500">
                            Total Transaction
                        </p>

                        <h3 class="mt-1 text-2xl font-bold">
                            Rp 0
                        </h3>

                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Orders
                        </span>

                        <span class="font-medium">
                            0
                        </span>
                    </div>

                    <div class="flex justify-between">
                        <span class="text-gray-500">
                            Last Transaction
                        </span>

                        <span class="font-medium">
                            -
                        </span>
                    </div>

                </div>

            </x-common.component-card>

            {{-- QUICK ACTION --}}
            <x-common.component-card title="Quick Actions">

                <div class="space-y-3">

                    <a href="{{ route('crm.leads.edit', $lead) }}"
                        class="block w-full rounded-lg bg-warning-500 px-4 py-2 text-center text-sm font-medium text-white">
                        Edit Lead
                    </a>

                    <a href="{{ route('crm.leads.index') }}"
                        class="block w-full rounded-lg border border-gray-300 px-4 py-2 text-center text-sm">
                        Back to List
                    </a>

                </div>

            </x-common.component-card>

        </div>

    </div>
@endsection

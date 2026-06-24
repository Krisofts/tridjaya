@extends('layouts.app')

@section('content')


<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">


    {{-- ===================================================== --}}
    {{-- LEFT CONTENT --}}
    {{-- ===================================================== --}}
    <div class="space-y-6 xl:col-span-2">

        {{-- LEAD PROFILE --}}
        <div class="bg-gradient-to-r from-brand-500/10 via-brand-500/5 to-transparent overflow-visible rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">


            {{-- HEADER --}}
            <div class="px-5 py-5 lg:px-6">

                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                    {{-- PROFILE --}}
                    <div class="flex items-center gap-4">

                        {{-- Avatar --}}
                        <div class="flex h-16 w-16 items-center justify-center rounded-full bg-brand-500 text-xl font-bold text-white shadow-sm">
                            {{ strtoupper(substr($lead->name, 0, 1)) }}
                        </div>

                        <div>

                            <div class="flex flex-wrap items-center gap-2">

                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">
                                    {{ $lead->name }}
                                </h2>

                                {{-- STAGE BADGE + DROPDOWN --}}
                                @if ($lead->pipeline)

                                <div x-data="{ stageOpen: false }" class="relative">

                                    <button @click="stageOpen = !stageOpen" type="button"
                                        class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 transition hover:bg-brand-100 dark:bg-brand-500/15 dark:text-brand-400 dark:hover:bg-brand-500/25">

                                        {{ $lead->stage->name ?? 'Set Stage' }}

                                        <svg class="h-3 w-3 transition-transform" :class="{ 'rotate-180': stageOpen }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>

                                    <div x-show="stageOpen" @click.outside="stageOpen = false" x-cloak
                                        class="absolute left-0 z-20 mt-2 w-48 rounded-xl border border-gray-200 bg-white py-1.5 shadow-lg dark:border-gray-800 dark:bg-gray-900">

                                        @foreach ($lead->pipeline->stages()->orderBy('sort_order')->get() as $stage)
                                        <button type="button"
                                            @click="stageOpen = false; $refs.stageForm.stage_id.value = {{ $stage->id }}; $refs.stageForm.submit()"
                                            class="flex w-full items-center justify-between px-4 py-2 text-sm transition
                                            {{ $lead->pipeline_stage_id == $stage->id
                                            ? 'bg-brand-50 font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400'
                                            : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }}">

                                            {{ $stage->name }}

                                            @if ($lead->pipeline_stage_id == $stage->id)
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                            </svg>
                                            @endif

                                        </button>
                                        @endforeach

                                    </div>

                                </div>

                                {{-- HIDDEN FORM untuk submit perubahan stage --}}
                                <form x-ref="stageForm" method="POST" action="{{ route('crm.leads.change-stage', $lead) }}" class="hidden">
                                    @csrf
                                    <input type="hidden" name="stage_id" value="{{ $lead->pipeline_stage_id }}">
                                </form>

                                @endif

                            </div>

                            <div class="mt-1 flex flex-wrap items-center gap-2 text-sm text-gray-500">

                                <span>{{ $lead->phone }}</span>

                                @if ($lead->pipeline)
                                <span>•</span>
                                <span>{{ $lead->pipeline->name }}</span>
                                @endif

                                @php
                                $temperature = $lead->stage?->temperature;

                                $color = match ($temperature) {
                                'cold' => 'info',
                                'warm' => 'warning',
                                'hot' => 'error',
                                'customer' => 'success',
                                'lost' => 'dark',
                                default => 'light',
                                };
                                @endphp

                                <x-ui.badge size="sm" :color="$color">
                                    {{ $lead->stage?->temperature_label ?? 'No Temperature' }}
                                </x-ui.badge>

                            </div>

                        </div>

                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex items-center gap-2">

                        @if ($lead->phone)

                        {{-- WHATSAPP (AUTO UPDATE STAGE) --}}
                        <form method="POST" action="{{ route('crm.leads.whatsapp', $lead) }}">
                            @csrf
                            <button type="submit" title="Chat via WhatsApp"
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-success-500 text-white transition hover:opacity-90">
                                <x-icons.whatsapp class="w-6 h-6 text-white" />
                            </button>
                        </form>

                        {{-- CALL --}}
                        <a href="tel:{{ $lead->phone }}" title="Telepon {{ $lead->phone }}"
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500 text-white transition hover:opacity-90">
                            <x-icons.call class="w-6 h-6 text-white" />
                        </a>

                        @endif

                        {{-- EDIT LEAD --}}
                        <a href="{{ route('crm.leads.edit', $lead) }}" title="Edit Lead"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-300 text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                            <x-icons.edit class="w-6 h-6 text-white" />
                        </a>

                    </div>

                </div>

            </div>



        </div>

        {{-- ===================================================== --}}
        {{-- TABS --}}
        {{-- ===================================================== --}}
        <x-common.component-card>

            <div
                x-data="{
                tab: localStorage.getItem('lead_tab') || 'timeline'
                }"
                x-init="
                $watch('tab', value => localStorage.setItem('lead_tab', value))
                "
                >

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
                                    {{ $tasks->count() }}
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


                    </div>

                </div>

                <div x-show="tab==='timeline'">

                    <div class="relative">

                        {{-- Timeline line --}}
                        <div class="absolute left-3 top-4 bottom-0 w-px bg-gray-200 dark:bg-gray-800"></div>

                        @forelse ($activities as $activity)

                        <div class="relative mb-6 pl-10 last:mb-0">

                            {{-- Type Badge (di kanan title) --}}
                            <div class="flex items-center justify-between gap-3">

                                <h4 class="font-semibold text-gray-800 dark:text-white">
                                    {{ $activity->title ?? '-' }}
                                </h4>

                                <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                    {{ ucfirst(str_replace('_', ' ', $activity->type)) }}
                                </span>

                            </div>

                            {{-- Description --}}
                            @if ($activity->description)
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                {{ $activity->description }}
                            </p>
                            @endif

                            {{-- Footer --}}
                            <div class="mt-2 text-xs text-gray-400 flex items-center gap-1">
                                <span>
                                    Created by {{ $activity->user?->name ?? 'System' }}
                                </span>

                                <span>•</span>

                                <span>
                                    {{ $activity->created_at->format('d M Y H:i') }}
                                </span>
                            </div>

                        </div>

                        @empty
                        <div class="py-10 text-center text-gray-500">
                            No activity found.
                        </div>
                        @endforelse

                    </div>

                    <div class="mt-6">
                        {{ $activities->links() }}
                    </div>

                </div>

                {{-- TASK --}}
                <div x-show="tab==='tasks'" class="space-y-4">

                    {{-- ACTION --}}
                    <div class="flex flex-wrap gap-2">



                        <div x-data="{ isTaskModal: false }">

                            {{-- BUTTON TRIGGER --}}
                            <button
                                @click="isTaskModal = true"
                                class="shadow-theme-xs flex h-10 w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 lg:inline-flex lg:w-auto dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">

                                Add Task
                            </button>

                            {{-- MODAL --}}
                            <x-modal.modal
                                show="isTaskModal"
                                title="Add Task"
                                description="Buat task follow up untuk lead ini">

                                <form method="POST" action="{{ route('crm.leads.tasks.store') }}">
                                    @csrf

                                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">

                                    <div class="grid grid-cols-1 gap-x-6 gap-y-5">

                                        {{-- TITLE --}}
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Title
                                            </label>

                                            <input
                                            type="text"
                                            name="title"
                                            placeholder="Contoh: Follow up WhatsApp"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        </div>

                                        {{-- TYPE --}}
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Type
                                            </label>

                                            <select
                                                name="type"
                                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                                <option value="follow_up">Follow Up</option>
                                                <option value="call">Call</option>
                                                <option value="visit">Visit</option>
                                                <option value="email">Email</option>
                                                <option value="quotation">Quotation</option>

                                            </select>
                                        </div>

                                        {{-- PRIORITY --}}
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Priority
                                            </label>

                                            <select
                                                name="priority"
                                                class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                                <option value="low">Low</option>
                                                <option value="medium" selected>Medium</option>
                                                <option value="high">High</option>
                                                <option value="urgent">Urgent</option>

                                            </select>
                                        </div>

                                        {{-- DESCRIPTION --}}
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Description
                                            </label>

                                            <textarea
                                                name="description"
                                                rows="4"
                                                class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"></textarea>
                                        </div>

                                        {{-- DUE DATE --}}
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Due Date
                                            </label>

                                            <input
                                            type="datetime-local"
                                            name="due_at"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        </div>

                                        {{-- REMINDER --}}
                                        <div>
                                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                                Reminder
                                            </label>

                                            <input
                                            type="datetime-local"
                                            name="reminder_at"
                                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                        </div>

                                    </div>

                                    {{-- ACTION --}}
                                    <div class="flex items-center gap-3 mt-6 lg:justify-end">

                                        <button
                                            type="button"
                                            @click="isTaskModal = false"
                                            class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">

                                            Close
                                        </button>

                                        <button
                                            type="submit"
                                            class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">

                                            Save Task
                                        </button>

                                    </div>

                                </form>

                            </x-modal.modal>

                        </div>
                    </div>


                    @forelse ($tasks as $task)

                    @php
                    $statusConfig = match($task->status) {
                    'completed'   => ['class' => 'border-success-500 bg-success-500 text-white', 'next' => null],
                    'cancelled'   => ['class' => 'border-gray-300 bg-gray-100 text-gray-400 dark:border-gray-700 dark:bg-gray-800', 'next' => null],
                    'in_progress' => ['class' => 'border-brand-500 text-brand-500 hover:bg-brand-50 dark:hover:bg-brand-500/10', 'next' => 'complete'],
                    default       => ['class' => 'border-gray-300 text-gray-300 hover:border-brand-400 hover:text-brand-400 dark:border-gray-700', 'next' => 'start'],
                    };

                    $priorityColor = match($task->priority) {
                    'urgent' => 'bg-error-600',
                    'high'   => 'bg-error-500',
                    'medium' => 'bg-warning-500',
                    default  => 'bg-gray-400',
                    };
                    @endphp

                    <div x-data="{ open: true, menu: false, completeModal: false }"
                        class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">

                        {{-- HEADER --}}
                        <div class="flex flex-wrap items-center justify-between gap-3 px-5 py-4">

                            <div class="flex items-center gap-3">

                                <button @click="open = !open" type="button" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                    <svg class="h-4 w-4 transition-transform" :class="{ '-rotate-90': !open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>

                                <span class="flex h-9 w-9 items-center justify-center rounded-full bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400">
                                    <svg class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h6m-6 4h6" />
                                    </svg>
                                </span>

                                <p class="text-sm text-gray-800 dark:text-gray-200">
                                    Task dibuat <span class="font-semibold text-gray-900 dark:text-white">{{ $task->user?->name ?? 'System' }}</span>
                                </p>

                            </div>

                            <div class="flex items-center gap-3 text-sm">

                                @if ($task->due_at)
                                <span class="text-gray-400">Due:</span>
                                <span class="inline-flex items-center gap-1.5 font-medium text-gray-700 dark:text-gray-300">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3M16 7V3M4 11h16M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    {{ $task->due_at->isToday() ? 'Today, ' . $task->due_at->format('H:i') : $task->due_at->format('d M Y, H:i') }}
                                </span>
                                @endif

                                {{-- STATUS BADGE --}}
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium
                                    @class([
                                    'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400' => $task->status === 'completed',
                                    'bg-gray-100 text-gray-400 dark:bg-gray-800' => $task->status === 'cancelled',
                                    'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400' => $task->status === 'in_progress',
                                    'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400' => $task->status === 'pending',
                                    ])">
                                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                </span>

                                <div class="relative">
                                    <button @click="menu = !menu" type="button" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z" /></svg>
                                    </button>
                                    <div x-show="menu" @click.outside="menu = false" x-cloak
                                        class="absolute right-0 z-10 mt-1 w-40 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-800 dark:bg-gray-900">

                                        @if (in_array($task->status, ['pending', 'in_progress']))
                                        <form method="POST" action="{{ route('crm.tasks.cancel', $task) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="block w-full px-3 py-2 text-left text-sm text-error-500 hover:bg-gray-50 dark:hover:bg-gray-800">
                                                Batalkan Task
                                            </button>
                                        </form>
                                        @else
                                        <span class="block px-3 py-2 text-sm text-gray-400">
                                            Tidak ada aksi
                                        </span>
                                        @endif

                                    </div>
                                </div>

                            </div>

                        </div>

                        <div x-show="open" x-cloak>

                            <div class="border-t border-gray-100 dark:border-gray-800"></div>

                            {{-- BODY --}}
                            <div class="flex items-start gap-4 px-5 py-5">

                                {{-- TOMBOL AKSI TASK --}}
                                @if ($statusConfig['next'] === 'start')

                                {{-- START: langsung submit --}}
                                <form method="POST" action="{{ route('crm.tasks.start', $task) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit"
                                        title="Mulai task"
                                        class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full border-2 transition {{ $statusConfig['class'] }}">
                                        <svg class="h-3.5 w-3.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </button>
                                </form>

                                @elseif ($statusConfig['next'] === 'complete')

                                {{-- COMPLETE: buka modal dulu --}}
                                <button type="button"
                                    @click="completeModal = true"
                                    title="Tandai selesai"
                                    class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full border-2 transition {{ $statusConfig['class'] }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>

                                @else

                                <span title="{{ ucfirst(str_replace('_', ' ', $task->status)) }}"
                                    class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full border-2 {{ $statusConfig['class'] }}">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                        @if ($task->status === 'completed')
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        @else
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        @endif
                                    </svg>
                                </span>

                                @endif

                                <div class="w-full">

                                    <p class="text-base font-semibold
                                        {{ $task->status === 'completed' ? 'text-gray-400 line-through' : '' }}
                                        {{ $task->status === 'cancelled' ? 'text-gray-400' : '' }}
                                        {{ in_array($task->status, ['pending', 'in_progress']) ? 'text-gray-900 dark:text-white' : '' }}">
                                        {{ $task->title }}
                                    </p>

                                    @if ($task->description)
                                    <p class="mt-2 text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                                        {{ $task->description }}
                                    </p>
                                    @endif

                                    @if ($task->result)
                                    <div class="mt-3 rounded-lg bg-gray-50 px-3 py-2 text-sm text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                                        <span class="font-medium text-gray-500 dark:text-gray-400">Hasil:</span> {{ $task->result }}
                                    </div>
                                    @endif

                                </div>

                            </div>

                            {{-- META BOX --}}
                            <div class="px-5 pb-5">

                                <div class="flex flex-col divide-y divide-gray-200 rounded-xl border border-gray-200 dark:divide-gray-800 dark:border-gray-800 sm:flex-row sm:divide-x sm:divide-y-0">

                                    <div class="flex-1 px-5 py-3">
                                        <p class="text-xs text-gray-400">
                                            Reminder
                                        </p>
                                        <p class="mt-1 text-sm font-medium text-gray-800 dark:text-gray-200">
                                            {{ $task->reminder_at ? $task->reminder_at->format('d M Y, H:i') : 'No reminder' }}
                                        </p>
                                    </div>

                                    <div class="flex-1 px-5 py-3">
                                        <p class="text-xs text-gray-400">
                                            Task Priority
                                        </p>
                                        <p class="mt-1 flex items-center gap-2 text-sm font-medium text-gray-800 dark:text-gray-200">
                                            <span class="h-3 w-3 rounded-sm {{ $priorityColor }}"></span>
                                            {{ ucfirst($task->priority) }}
                                        </p>
                                    </div>



                                </div>

                            </div>

                        </div>

                        {{-- MODAL: COMPLETE TASK (isi description + result) --}}
                        @if ($statusConfig['next'] === 'complete')

                        <x-modal.modal
                            show="completeModal"
                            title="Selesaikan Task"
                            description="Lengkapi deskripsi & hasil pengerjaan sebelum menandai task ini selesai">

                            <form method="POST" action="{{ route('crm.tasks.complete', $task) }}">
                                @csrf
                                @method('PATCH')

                                <div class="grid grid-cols-1 gap-y-5">

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Description
                                        </label>
                                        <textarea
                                            name="description"
                                            rows="3"
                                            placeholder="Update deskripsi task (opsional)"
                                            class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">{{ $task->description }}</textarea>
                                    </div>

                                    <div>
                                        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                            Hasil / Result
                                        </label>
<div>
    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
        Hasil / Result
    </label>

    <select
        name="result_id"
        class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

        <option value="">-- Pilih Hasil --</option>

        @foreach(\App\CRM\Models\CrmResult::where('is_active', true)->orderBy('name')->get() as $result)
            <option value="{{ $result->id }}">
                {{ $result->name }}
            </option>
        @endforeach

    </select>
</div>             </div>
 
                                </div>

                                <div class="mt-6 flex items-center gap-3 lg:justify-end">

                                    <button
                                        type="button"
                                        @click="completeModal = false"
                                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] sm:w-auto">
                                        Batal
                                    </button>

                                    <button
                                        type="submit"
                                        class="flex w-full justify-center rounded-lg bg-success-500 px-4 py-2.5 text-sm font-medium text-white hover:opacity-90 sm:w-auto">
                                        Selesaikan Task
                                    </button>

                                </div>

                            </form>

                        </x-modal.modal>

                        @endif

                    </div>

                    @empty
                    <div class="py-10 text-center text-gray-500">
                        Belum ada task.
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



    </div>

</div>
@endsection
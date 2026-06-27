@extends('layouts.app')

@section('content')

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

    {{-- ===================================================== --}}
    {{-- LEFT CONTENT --}}
    {{-- ===================================================== --}}
    <div class="space-y-6 xl:col-span-2">

        {{-- LEAD PROFILE --}}
        <div class="overflow-visible rounded-2xl border border-gray-200 bg-gradient-to-r from-brand-500/10 via-brand-500/5 to-transparent bg-white dark:border-gray-800 dark:bg-gray-900">
            <div class="px-5 py-5 lg:px-6">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">

                    <div class="flex items-center gap-4">
                        <div class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-full bg-brand-500 text-xl font-bold text-white shadow-sm">
                            {{ strtoupper(substr($lead->name, 0, 1)) }}
                        </div>

                        <div>
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $lead->name }}</h2>

                                {{-- STAGE DROPDOWN --}}
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
                                            class="absolute left-0 z-20 mt-2 w-52 rounded-xl border border-gray-200 bg-white py-1.5 shadow-lg dark:border-gray-800 dark:bg-gray-900">
                                            @foreach ($lead->pipeline->stages->sortBy('sort_order') as $stage)
                                                <button type="button"
                                                    @click="stageOpen = false; $refs.stageForm.stage_id.value = {{ $stage->id }}; $refs.stageForm.submit()"
                                                    class="flex w-full items-center justify-between px-4 py-2.5 text-sm transition
                                                        {{ $lead->pipeline_stage_id == $stage->id
                                                            ? 'bg-brand-50 font-semibold text-brand-600 dark:bg-brand-500/15 dark:text-brand-400'
                                                            : 'text-gray-600 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800' }}">
                                                    <span class="flex items-center gap-2">
                                                        <span class="h-2 w-2 rounded-full
                                                            {{ $stage->temperature === 'cold'     ? 'bg-blue-400'    : '' }}
                                                            {{ $stage->temperature === 'warm'     ? 'bg-yellow-400'  : '' }}
                                                            {{ $stage->temperature === 'hot'      ? 'bg-red-400'     : '' }}
                                                            {{ $stage->temperature === 'customer' ? 'bg-green-400'   : '' }}
                                                            {{ $stage->temperature === 'lost'     ? 'bg-gray-400'    : '' }}
                                                        "></span>
                                                        {{ $stage->name }}
                                                    </span>
                                                    @if ($lead->pipeline_stage_id == $stage->id)
                                                        <svg class="h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                    @endif
                                                </button>
                                            @endforeach
                                        </div>

                                        <form x-ref="stageForm" method="POST" action="{{ route('crm.leads.change-stage', $lead) }}" class="hidden">
                                            @csrf
                                            <input type="hidden" name="stage_id" value="{{ $lead->pipeline_stage_id }}">
                                        </form>
                                    </div>
                                @endif
                            </div>

                            <div class="mt-1.5 flex flex-wrap items-center gap-2 text-sm text-gray-500">
                                @if ($lead->phone)
                                    <span>{{ $lead->phone }}</span>
                                @endif
                                @if ($lead->pipeline)
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <span>{{ $lead->pipeline->name }}</span>
                                @endif
                                @if ($lead->interest)
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <span>{{ $lead->interest->name }}</span>
                                @endif
                                @if ($lead->source)
                                    <span class="text-gray-300 dark:text-gray-600">|</span>
                                    <span>{{ $lead->source->name }}</span>
                                @endif
                                <x-ui.badge size="sm" :color="$lead->stage?->temperatureBadgeType() ?? 'light'">
                                    {{ $lead->stage?->temperatureLabel() ?? '-' }}
                                </x-ui.badge>
                            </div>
                        </div>
                    </div>

                    {{-- ACTIONS --}}
                    <div class="flex items-center gap-2">
                        @if ($lead->phone)
                            <form method="POST" action="{{ route('crm.leads.whatsapp', $lead) }}">
                                @csrf
                                <button type="submit" title="Chat via WhatsApp"
                                    class="flex h-10 w-10 items-center justify-center rounded-xl bg-success-500 text-white transition hover:opacity-90">
                                    <x-icons.whatsapp class="h-6 w-6" />
                                </button>
                            </form>
                            <a href="tel:{{ $lead->phone }}" title="Telepon {{ $lead->phone }}"
                                class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-500 text-white transition hover:opacity-90">
                                <x-icons.call class="h-6 w-6" />
                            </a>
                        @endif
                        <a href="{{ route('crm.leads.edit', $lead) }}" title="Edit Lead"
                            class="flex h-10 w-10 items-center justify-center rounded-xl border border-gray-300 text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                            <x-icons.edit class="h-6 w-6" />
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- ===================================================== --}}
        {{-- TABS --}}
        {{-- ===================================================== --}}
        <x-common.component-card>
            <div x-data="{ tab: localStorage.getItem('lead_tab') || 'tasks' }"
                 x-init="$watch('tab', value => localStorage.setItem('lead_tab', value))">

                {{-- TAB HEADER --}}
                <div class="mb-6 flex flex-wrap items-center gap-x-1 gap-y-2 rounded-xl bg-gray-100 p-1 dark:bg-gray-900">

                    <button @click="tab='tasks'"
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                        :class="tab === 'tasks' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                        Tasks
                        @if ($tasks->count() > 0)
                            <span class="inline-flex rounded-full bg-brand-500 px-1.5 py-0.5 text-xs font-medium text-white">
                                {{ $tasks->count() }}
                            </span>
                        @endif
                    </button>

                    <button @click="tab='timeline'"
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                        :class="tab === 'timeline' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Timeline
                        <span class="inline-flex rounded-full bg-gray-200 px-1.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                            {{ $activities->total() }}
                        </span>
                    </button>

                    <button @click="tab='notes'"
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-medium transition"
                        :class="tab === 'notes' ? 'bg-white text-gray-900 shadow-sm dark:bg-gray-800 dark:text-white' : 'text-gray-500 hover:text-gray-900 dark:text-gray-400 dark:hover:text-white'">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Catatan
                    </button>

                </div>

                {{-- ================================================= --}}
                {{-- TAB: TASKS --}}
                {{-- ================================================= --}}
                <div x-show="tab==='tasks'" class="space-y-3">

                    {{-- TAMBAH TASK --}}
                    <div x-data="{ isTaskModal: false }">
                        <button @click="isTaskModal = true"
                            class="shadow-theme-xs inline-flex items-center gap-2 rounded-lg border border-dashed border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-500 transition hover:border-brand-400 hover:text-brand-500 dark:border-gray-700 dark:bg-transparent dark:text-gray-400 dark:hover:border-brand-500 dark:hover:text-brand-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah Task
                        </button>

                        <x-modal.modal show="isTaskModal" title="Tambah Task" description="Buat task follow up untuk lead ini">
                            <form method="POST" action="{{ route('crm.leads.tasks.store') }}">
                                @csrf
                                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                                <div class="grid grid-cols-1 gap-4">
                                    <x-form.input.input-with-label name="title" label="Judul Task" placeholder="Contoh: Follow up via WhatsApp" :required="true" />
                                    <div class="grid grid-cols-2 gap-4">
                                        <x-form.select.select-with-label name="type" label="Tipe"
                                            :options="['follow_up' => 'Follow Up', 'call' => 'Telepon', 'visit' => 'Kunjungan', 'email' => 'Email', 'quotation' => 'Penawaran']" />
                                        <x-form.select.select-with-label name="priority" label="Prioritas" :selected="'medium'"
                                            :options="['low' => 'Rendah', 'medium' => 'Sedang', 'high' => 'Tinggi', 'urgent' => 'Mendesak']" />
                                    </div>
                                    <x-form.textarea.textarea-with-label name="description" label="Catatan" placeholder="Detail task (opsional)" :rows="3" />
                                    <div class="grid grid-cols-2 gap-4">
                                        <x-form.input.input-with-label name="due_at" type="datetime-local" label="Tenggat Waktu" />
                                        <x-form.input.input-with-label name="reminder_at" type="datetime-local" label="Pengingat" />
                                    </div>
                                </div>
                                <div class="mt-5 flex items-center gap-3 lg:justify-end">
                                    <button type="button" @click="isTaskModal = false"
                                        class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 sm:w-auto">
                                        Batal
                                    </button>
                                    <button type="submit"
                                        class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                                        Simpan Task
                                    </button>
                                </div>
                            </form>
                        </x-modal.modal>
                    </div>

                    {{-- TASK LIST --}}
                    @forelse ($tasks as $task)
                        @php
                            $isPending    = $task->status === 'pending';
                            $isInProgress = $task->status === 'in_progress';
                            $isCompleted  = $task->status === 'completed';
                            $isCancelled  = $task->status === 'cancelled';
                            $isOverdue    = in_array($task->status, ['pending', 'in_progress']) && $task->due_at?->isPast();

                            $priorityConfig = match ($task->priority) {
                                'urgent' => ['color' => 'bg-red-500',    'label' => 'Mendesak'],
                                'high'   => ['color' => 'bg-orange-400', 'label' => 'Tinggi'],
                                'medium' => ['color' => 'bg-yellow-400', 'label' => 'Sedang'],
                                default  => ['color' => 'bg-gray-300',   'label' => 'Rendah'],
                            };
                        @endphp

                        <div x-data="{ open: true, menu: false, completeModal: false }"
                            class="overflow-hidden rounded-xl border transition
                                {{ $isCompleted  ? 'border-success-200 bg-success-50/30 dark:border-success-900/30 dark:bg-success-500/5' : '' }}
                                {{ $isCancelled  ? 'border-gray-200 bg-gray-50 opacity-60 dark:border-gray-800 dark:bg-gray-900' : '' }}
                                {{ $isOverdue    ? 'border-red-200 bg-red-50/30 dark:border-red-900/30 dark:bg-red-500/5' : '' }}
                                {{ $isPending    && !$isOverdue ? 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900' : '' }}
                                {{ $isInProgress && !$isOverdue ? 'border-brand-200 bg-brand-50/30 dark:border-brand-900/30 dark:bg-brand-500/5' : '' }}
                            ">

                            {{-- TASK HEADER --}}
                            <div class="flex items-center gap-3 px-4 py-3">

                                {{-- STATUS BUTTON --}}
                                @if ($isPending)
                                    <form method="POST" action="{{ route('crm.tasks.start', $task) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Mulai task"
                                            class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border-2 border-gray-300 text-gray-300 transition hover:border-brand-400 hover:text-brand-400 dark:border-gray-600">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z" />
                                            </svg>
                                        </button>
                                    </form>
                                @elseif ($isInProgress)
                                    <button type="button" @click="completeModal = true" title="Tandai selesai"
                                        class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border-2 border-brand-500 text-brand-500 transition hover:bg-brand-50 dark:hover:bg-brand-500/10">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </button>
                                @elseif ($isCompleted)
                                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-success-500">
                                        <svg class="h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    </span>
                                @else
                                    <span class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full bg-gray-200 dark:bg-gray-700">
                                        <svg class="h-3.5 w-3.5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </span>
                                @endif

                                {{-- TITLE & META --}}
                                <div class="min-w-0 flex-1" @click="open = !open" style="cursor:pointer">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold
                                            {{ $isCompleted ? 'text-gray-400 line-through' : '' }}
                                            {{ $isCancelled ? 'text-gray-400' : '' }}
                                            {{ !$isCompleted && !$isCancelled ? 'text-gray-900 dark:text-white' : '' }}">
                                            {{ $task->title }}
                                        </p>

                                        {{-- PRIORITY DOT --}}
                                        <span class="h-2 w-2 rounded-full {{ $priorityConfig['color'] }}" title="{{ $priorityConfig['label'] }}"></span>

                                        {{-- OVERDUE --}}
                                        @if ($isOverdue)
                                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-600 dark:bg-red-500/15 dark:text-red-400">
                                                Terlambat
                                            </span>
                                        @endif
                                    </div>

                                    <div class="mt-0.5 flex flex-wrap items-center gap-2 text-xs text-gray-400">
                                        @if ($task->due_at)
                                            <span class="{{ $isOverdue ? 'text-red-400' : '' }}">
                                                Tenggat: {{ $task->due_at->isToday() ? 'Hari ini ' . $task->due_at->format('H:i') : $task->due_at->translatedFormat('d M Y, H:i') }}
                                            </span>
                                        @endif
                                        @if ($task->user)
                                            <span>•</span>
                                            <span>{{ $task->user->name }}</span>
                                        @endif
                                    </div>
                                </div>

                                {{-- STATUS BADGE --}}
                                <span @class([
                                    'shrink-0 rounded-full px-2.5 py-1 text-xs font-medium',
                                    'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400' => $isCompleted,
                                    'bg-gray-100 text-gray-400 dark:bg-gray-800'                                  => $isCancelled,
                                    'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400'        => $isInProgress,
                                    'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400' => $isPending,
                                ])>
                                    {{ match($task->status) {
                                        'completed'   => 'Selesai',
                                        'cancelled'   => 'Dibatalkan',
                                        'in_progress' => 'Dikerjakan',
                                        default       => 'Menunggu',
                                    } }}
                                </span>

                                {{-- MENU --}}
                                <div class="relative shrink-0">
                                    <button @click="menu = !menu" type="button"
                                        class="rounded-lg p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z" />
                                        </svg>
                                    </button>
                                    <div x-show="menu" @click.outside="menu = false" x-cloak
                                        class="absolute right-0 z-10 mt-1 w-44 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-800 dark:bg-gray-900">
                                        @if ($isInProgress)
                                            <button type="button" @click="menu = false; completeModal = true"
                                                class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-success-600 hover:bg-gray-50 dark:hover:bg-gray-800">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                                Selesaikan Task
                                            </button>
                                        @endif
                                        @if (in_array($task->status, ['pending', 'in_progress']))
                                            <form method="POST" action="{{ route('crm.tasks.cancel', $task) }}">
                                                @csrf @method('PATCH')
                                                <button type="submit"
                                                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-500 hover:bg-gray-50 dark:hover:bg-gray-800">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                    Batalkan Task
                                                </button>
                                            </form>
                                        @else
                                            <span class="block px-3 py-2 text-sm text-gray-400">Tidak ada aksi</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- TASK DETAIL (collapsible) --}}
                            <div x-show="open" x-cloak>
                                @if ($task->description || $task->result_id || $task->reminder_at)
                                    <div class="border-t border-gray-100 px-4 py-3 dark:border-gray-800">

                                        @if ($task->description)
                                            <p class="text-sm leading-relaxed text-gray-500 dark:text-gray-400">
                                                {{ $task->description }}
                                            </p>
                                        @endif

                                        <div class="mt-3 flex flex-wrap gap-4 text-xs text-gray-400">
                                            @if ($task->reminder_at)
                                                <span class="flex items-center gap-1">
                                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                                    </svg>
                                                    Pengingat: {{ $task->reminder_at->translatedFormat('d M Y, H:i') }}
                                                </span>
                                            @endif
                                            <span class="flex items-center gap-1">
                                                <span class="h-2 w-2 rounded-full {{ $priorityConfig['color'] }}"></span>
                                                Prioritas {{ $priorityConfig['label'] }}
                                            </span>
                                        </div>

                                        @if ($task->result_id)
                                            <div class="mt-3 inline-flex items-center gap-1.5 rounded-lg bg-success-50 px-3 py-1.5 text-sm font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Hasil: {{ $task->result?->name ?? '-' }}
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>

                            {{-- MODAL: COMPLETE TASK --}}
                            @if ($isInProgress)
                                <x-modal.modal show="completeModal" title="Selesaikan Task" description="Pilih hasil dan tambahkan catatan sebelum menandai selesai">
                                    <form method="POST" action="{{ route('crm.tasks.complete', $task) }}">
                                        @csrf @method('PATCH')
                                        <div class="grid grid-cols-1 gap-4">
                                            <x-form.select.select-with-label
                                                name="result_id"
                                                label="Hasil Aktivitas"
                                                placeholder="-- Pilih Hasil --"
                                                :options="$results"
                                            />
                                            <x-form.textarea.textarea-with-label
                                                name="description"
                                                label="Catatan Tambahan"
                                                :value="$task->description"
                                                placeholder="Tambahkan catatan hasil follow up (opsional)"
                                                :rows="3"
                                            />
                                        </div>
                                        <div class="mt-5 flex items-center gap-3 lg:justify-end">
                                            <button type="button" @click="completeModal = false"
                                                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 sm:w-auto">
                                                Batal
                                            </button>
                                            <button type="submit"
                                                class="flex w-full justify-center rounded-lg bg-success-500 px-4 py-2.5 text-sm font-medium text-white hover:opacity-90 sm:w-auto">
                                                ✓ Selesaikan Task
                                            </button>
                                        </div>
                                    </form>
                                </x-modal.modal>
                            @endif

                        </div>
                    @empty
                        <div class="rounded-xl border border-dashed border-gray-200 py-10 text-center dark:border-gray-800">
                            <svg class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                            <p class="mt-2 text-sm text-gray-400">Belum ada task aktif.</p>
                        </div>
                    @endforelse

                </div>

                {{-- ================================================= --}}
                {{-- TAB: TIMELINE --}}
                {{-- ================================================= --}}
                <div x-show="tab==='timeline'">
                    <div class="relative space-y-0">
                        {{-- Garis vertikal --}}
                        <div class="absolute bottom-0 left-4 top-2 w-px bg-gray-100 dark:bg-gray-800"></div>

                        @forelse ($activities as $activity)
                            @php
                                $iconConfig = match($activity->type) {
                                    'stage_changed'  => ['bg' => 'bg-brand-100 dark:bg-brand-500/20',   'text' => 'text-brand-500'],
                                    'task_completed' => ['bg' => 'bg-success-100 dark:bg-success-500/20', 'text' => 'text-success-500'],
                                    'task_created'   => ['bg' => 'bg-gray-100 dark:bg-gray-800',         'text' => 'text-gray-400'],
                                    'whatsapp'       => ['bg' => 'bg-success-100 dark:bg-success-500/20', 'text' => 'text-success-500'],
                                    'call'           => ['bg' => 'bg-brand-100 dark:bg-brand-500/20',   'text' => 'text-brand-400'],
                                    default          => ['bg' => 'bg-gray-100 dark:bg-gray-800',         'text' => 'text-gray-400'],
                                };

                                $badgeConfig = match($activity->type) {
                                    'stage_changed'  => 'bg-brand-50 text-brand-600 dark:bg-brand-500/15 dark:text-brand-400',
                                    'task_completed' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400',
                                    'whatsapp'       => 'bg-green-50 text-green-600 dark:bg-green-500/15 dark:text-green-400',
                                    default          => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
                                };

                                $typeLabel = match($activity->type) {
                                    'stage_changed'  => 'Pindah Stage',
                                    'task_completed' => 'Task Selesai',
                                    'task_created'   => 'Task Dibuat',
                                    'whatsapp'       => 'WhatsApp',
                                    'call'           => 'Telepon',
                                    'visit'          => 'Kunjungan',
                                    'note'           => 'Catatan',
                                    default          => ucfirst(str_replace('_', ' ', $activity->type)),
                                };
                            @endphp

                            <div class="relative mb-4 flex gap-4 last:mb-0">

                                {{-- Icon --}}
                                <div class="relative z-10 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full {{ $iconConfig['bg'] }}">
                                    @if ($activity->type === 'stage_changed')
                                        <svg class="h-3.5 w-3.5 {{ $iconConfig['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    @elseif ($activity->type === 'task_completed')
                                        <svg class="h-3.5 w-3.5 {{ $iconConfig['text'] }}" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @elseif ($activity->type === 'whatsapp')
                                        <svg class="h-3.5 w-3.5 {{ $iconConfig['text'] }}" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                                        </svg>
                                    @else
                                        <div class="h-2 w-2 rounded-full {{ $iconConfig['text'] }} bg-current"></div>
                                    @endif
                                </div>

                                {{-- Content --}}
                                <div class="flex-1 rounded-xl border border-gray-100 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">
                                                {{ $activity->title ?? '-' }}
                                            </p>
                                            @if ($activity->description)
                                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $activity->description }}
                                                </p>
                                            @endif
                                        </div>
                                        <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeConfig }}">
                                            {{ $typeLabel }}
                                        </span>
                                    </div>
                                    <div class="mt-2 flex items-center gap-1.5 text-xs text-gray-400">
                                        <span>{{ $activity->user?->name ?? 'System' }}</span>
                                        <span>•</span>
                                        <span>{{ $activity->created_at->translatedFormat('d M Y, H:i') }}</span>
                                    </div>
                                </div>

                            </div>
                        @empty
                            <div class="py-10 text-center">
                                <p class="text-sm text-gray-400">Belum ada aktivitas tercatat.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($activities->hasPages())
                        <div class="mt-4">{{ $activities->links() }}</div>
                    @endif
                </div>

                {{-- ================================================= --}}
                {{-- TAB: NOTES --}}
                {{-- ================================================= --}}
                <div x-show="tab==='notes'">
                    <div class="rounded-xl border border-dashed border-gray-200 py-10 text-center dark:border-gray-800">
                        <svg class="mx-auto h-8 w-8 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-400">Belum ada catatan.</p>
                    </div>
                </div>

            </div>
        </x-common.component-card>

    </div>

    {{-- ===================================================== --}}
    {{-- RIGHT SIDEBAR --}}
    {{-- ===================================================== --}}
    <div class="space-y-6">

        {{-- TRANSAKSI --}}
        <x-common.component-card>
            <div x-data="{ txModal: false }">

                {{-- HEADER --}}
                <div class="mb-4 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-800 dark:text-white">Transaksi</h3>
                        @if ($transactions->count() > 0)
                            <p class="mt-0.5 text-xs text-gray-400">
                                Total: <span class="font-semibold text-gray-700 dark:text-gray-300">
                                    Rp {{ number_format($transactions->where('status', 'paid')->sum('amount'), 0, ',', '.') }}
                                </span> lunas
                            </p>
                        @endif
                    </div>
                    <button @click="txModal = true"
                        class="inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-xs font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah
                    </button>
                </div>

                {{-- LIST TRANSAKSI --}}
                @if ($transactions->isEmpty())
                    <div class="rounded-xl border border-dashed border-gray-200 py-6 text-center dark:border-gray-800">
                        <svg class="mx-auto h-7 w-7 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75" />
                        </svg>
                        <p class="mt-2 text-xs text-gray-400">Belum ada transaksi.</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach ($transactions as $tx)
                            <div class="rounded-xl border border-gray-100 p-3 dark:border-gray-800">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-2">
                                            <span class="rounded-full px-2 py-0.5 text-xs font-medium
                                                {{ $tx->type === 'cash' ? 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400' : 'bg-purple-50 text-purple-600 dark:bg-purple-500/15 dark:text-purple-400' }}">
                                                {{ $tx->getTypeLabel() }}
                                            </span>
                                            <x-ui.badge size="sm" :color="$tx->getStatusColor()">
                                                {{ $tx->getStatusLabel() }}
                                            </x-ui.badge>
                                        </div>
                                        <p class="mt-1.5 text-sm font-semibold text-gray-800 dark:text-white">
                                            {{ $tx->amount_formatted }}
                                        </p>
                                        @if ($tx->type === 'credit' && $tx->dp_amount)
                                            <p class="text-xs text-gray-400">DP: {{ $tx->dp_amount_formatted }}</p>
                                        @endif
                                        @if ($tx->leasing)
                                            <p class="text-xs text-gray-400">Leasing: {{ $tx->leasing }}</p>
                                        @endif
                                        @if ($tx->tenor)
                                            <p class="text-xs text-gray-400">Tenor: {{ $tx->tenor }} bulan</p>
                                        @endif
                                        <p class="mt-1 text-xs text-gray-400">
                                            {{ $tx->transaction_date->translatedFormat('d M Y') }}
                                        </p>
                                    </div>

                                    {{-- MENU --}}
                                    <div x-data="{ menu: false }" class="relative shrink-0">
                                        <button @click="menu = !menu" type="button"
                                            class="rounded p-1 text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-800">
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M12 6a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4zm0 8a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                        <div x-show="menu" @click.outside="menu = false" x-cloak
                                            class="absolute right-0 z-20 mt-1 w-44 rounded-lg border border-gray-200 bg-white py-1 shadow-lg dark:border-gray-800 dark:bg-gray-900">

                                            @if ($tx->status === 'pending')
                                                <form method="POST" action="{{ route('crm.transactions.update-status', $tx) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="paid">
                                                    <button type="submit"
                                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-success-600 hover:bg-gray-50 dark:hover:bg-gray-800">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                        </svg>
                                                        Tandai Lunas
                                                    </button>
                                                </form>
                                                <form method="POST" action="{{ route('crm.transactions.update-status', $tx) }}">
                                                    @csrf @method('PATCH')
                                                    <input type="hidden" name="status" value="cancelled">
                                                    <button type="submit"
                                                        class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-500 hover:bg-gray-50 dark:hover:bg-gray-800">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                        Batalkan
                                                    </button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('crm.transactions.destroy', $tx) }}"
                                                onsubmit="return confirm('Hapus transaksi ini?')">
                                                @csrf @method('DELETE')
                                                <button type="submit"
                                                    class="flex w-full items-center gap-2 px-3 py-2 text-left text-sm text-red-500 hover:bg-gray-50 dark:hover:bg-gray-800">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                    Hapus
                                                </button>
                                            </form>

                                        </div>
                                    </div>
                                </div>

                                @if ($tx->notes)
                                    <p class="mt-2 text-xs text-gray-400 italic">{{ $tx->notes }}</p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- MODAL TAMBAH TRANSAKSI --}}
                <x-modal.modal show="txModal" title="Tambah Transaksi" description="Catat transaksi untuk lead ini">
                    <form method="POST" action="{{ route('crm.transactions.store', $lead) }}" x-data="{ txType: 'cash' }">
                        @csrf
                        <div class="grid grid-cols-1 gap-4">

                            {{-- TIPE --}}
                            <div>
                                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Tipe Transaksi</label>
                                <div class="flex gap-2">
                                    <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-lg border px-3 py-2.5 transition"
                                        :class="txType === 'cash' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-300 dark:border-gray-700'">
                                        <input type="radio" name="type" value="cash" x-model="txType" class="hidden">
                                        <span class="text-sm font-medium" :class="txType === 'cash' ? 'text-brand-600 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400'">💵 Tunai</span>
                                    </label>
                                    <label class="flex flex-1 cursor-pointer items-center gap-2 rounded-lg border px-3 py-2.5 transition"
                                        :class="txType === 'credit' ? 'border-brand-500 bg-brand-50 dark:bg-brand-500/10' : 'border-gray-300 dark:border-gray-700'">
                                        <input type="radio" name="type" value="credit" x-model="txType" class="hidden">
                                        <span class="text-sm font-medium" :class="txType === 'credit' ? 'text-brand-600 dark:text-brand-400' : 'text-gray-600 dark:text-gray-400'">🏦 Kredit</span>
                                    </label>
                                </div>
                            </div>

                            {{-- NOMINAL --}}
                            <x-form.input.input-with-label
                                name="amount"
                                type="number"
                                label="Nominal (Rp)"
                                placeholder="Contoh: 5000000"
                                :required="true"
                            />

                            {{-- KREDIT FIELDS --}}
                            <div x-show="txType === 'credit'" class="space-y-4">
                                <x-form.input.input-with-label
                                    name="dp_amount"
                                    type="number"
                                    label="Uang Muka / DP (Rp)"
                                    placeholder="Contoh: 1000000"
                                />
                                <div class="grid grid-cols-2 gap-3">
                                    <x-form.input.input-with-label
                                        name="leasing"
                                        label="Nama Leasing"
                                        placeholder="Contoh: FIF, ACC"
                                    />
                                    <x-form.input.input-with-label
                                        name="tenor"
                                        type="number"
                                        label="Tenor (Bulan)"
                                        placeholder="12"
                                    />
                                </div>
                            </div>

                            {{-- STATUS --}}
                            <x-form.select.select-with-label
                                name="status"
                                label="Status"
                                :selected="'pending'"
                                :options="['pending' => 'Menunggu', 'paid' => 'Lunas', 'cancelled' => 'Dibatalkan']"
                                hint="Pilih 'Lunas' untuk otomatis pindah stage ke Closing."
                            />

                            {{-- TANGGAL --}}
                            <x-form.input.input-with-label
                                name="transaction_date"
                                type="date"
                                label="Tanggal Transaksi"
                                :value="now()->format('Y-m-d')"
                                :required="true"
                            />

                            {{-- CATATAN --}}
                            <x-form.textarea.textarea-with-label
                                name="notes"
                                label="Catatan"
                                placeholder="Catatan tambahan (opsional)"
                                :rows="2"
                            />

                        </div>

                        <div class="mt-5 flex items-center gap-3 lg:justify-end">
                            <button type="button" @click="txModal = false"
                                class="flex w-full justify-center rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 sm:w-auto">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex w-full justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600 sm:w-auto">
                                Simpan Transaksi
                            </button>
                        </div>
                    </form>
                </x-modal.modal>

            </div>
        </x-common.component-card>

        {{-- INFO LEAD --}}
        <x-common.component-card title="Info Lead">
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Kode Lead</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lead->lead_code }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Pipeline</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lead->pipeline->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Stage</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lead->stage->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Sumber</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lead->source->name ?? '-' }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Minat</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lead->interest->name ?? '-' }}</span>
                </div>
                @if ($lead->address)
                    <div class="flex justify-between gap-4 text-sm">
                        <span class="text-gray-500">Alamat</span>
                        <span class="text-right font-medium text-gray-800 dark:text-gray-200">{{ $lead->address }}</span>
                    </div>
                @endif
                @if ($lead->city_name || $lead->province_name)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Kota</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">
                            {{ collect([$lead->city_name, $lead->province_name])->filter()->implode(', ') }}
                        </span>
                    </div>
                @endif
                <div class="border-t border-gray-100 pt-3 dark:border-gray-800">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Ditugaskan ke</span>
                        <span class="font-medium text-gray-800 dark:text-gray-200">{{ $lead->assignee->name ?? '-' }}</span>
                    </div>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-500">Dibuat</span>
                    <span class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $lead->created_at->translatedFormat('d M Y') }}
                    </span>
                </div>
            </div>
        </x-common.component-card>

        {{-- PROGRESS STAGE --}}
        @if ($lead->pipeline && $lead->pipeline->stages->count() > 0)
            <x-common.component-card title="Progress Pipeline">
                <div class="space-y-1.5">
                    @foreach ($lead->pipeline->stages->sortBy('sort_order') as $stage)
                        <div class="flex items-center gap-2.5">
                            <span class="h-2 w-2 flex-shrink-0 rounded-full
                                {{ $lead->pipeline_stage_id == $stage->id ? 'bg-brand-500 ring-2 ring-brand-200 dark:ring-brand-500/30' : '' }}
                                {{ $stage->is_won  && $lead->pipeline_stage_id != $stage->id ? 'bg-success-400' : '' }}
                                {{ $stage->is_lost && $lead->pipeline_stage_id != $stage->id ? 'bg-gray-300 dark:bg-gray-600' : '' }}
                                {{ !$stage->is_won && !$stage->is_lost && $lead->pipeline_stage_id != $stage->id ? 'bg-gray-200 dark:bg-gray-700' : '' }}
                            "></span>
                            <span class="text-sm {{ $lead->pipeline_stage_id == $stage->id ? 'font-semibold text-brand-600 dark:text-brand-400' : 'text-gray-500 dark:text-gray-400' }}">
                                {{ $stage->name }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </x-common.component-card>
        @endif

    </div>

</div>

@endsection
@extends('layouts.app')

@section('content')

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

    {{-- LEFT CONTENT --}}
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

                                {{-- STAGE BADGE (readonly untuk operasional) --}}
                                @if ($lead->stage)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-600 dark:bg-brand-500/15 dark:text-brand-400">
                                        <span class="h-1.5 w-1.5 rounded-full
                                            {{ $lead->stage->temperature === 'cold'     ? 'bg-blue-400'    : '' }}
                                            {{ $lead->stage->temperature === 'warm'     ? 'bg-yellow-400'  : '' }}
                                            {{ $lead->stage->temperature === 'hot'      ? 'bg-red-400'     : '' }}
                                            {{ $lead->stage->temperature === 'customer' ? 'bg-green-400'   : '' }}
                                            {{ $lead->stage->temperature === 'lost'     ? 'bg-gray-400'    : '' }}
                                        "></span>
                                        {{ $lead->stage->name }}
                                    </span>
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
                                <x-ui.badge size="sm" :color="$lead->stage?->temperatureBadgeType() ?? 'light'">
                                    {{ $lead->stage?->temperatureLabel() ?? '-' }}
                                </x-ui.badge>
                            </div>
                        </div>
                    </div>

                    {{-- ACTIONS (hanya WA dan telepon) --}}
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
                    </div>

                </div>
            </div>
        </div>

        {{-- TABS --}}
        <x-common.component-card>
            <div x-data="{ tab: localStorage.getItem('my_lead_tab') || 'tasks' }"
                 x-init="$watch('tab', value => localStorage.setItem('my_lead_tab', value))">

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

                </div>

                {{-- TAB: TASKS --}}
                <div x-show="tab==='tasks'" class="space-y-3">

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

                        <div x-data="{ completeModal: false }"
                            class="overflow-hidden rounded-xl border transition
                                {{ $isCompleted  ? 'border-success-200 bg-success-50/30 dark:border-success-900/30 dark:bg-success-500/5' : '' }}
                                {{ $isCancelled  ? 'border-gray-200 bg-gray-50 opacity-60 dark:border-gray-800 dark:bg-gray-900' : '' }}
                                {{ $isOverdue    ? 'border-red-200 bg-red-50/30 dark:border-red-900/30 dark:bg-red-500/5' : '' }}
                                {{ $isPending    && !$isOverdue ? 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900' : '' }}
                                {{ $isInProgress && !$isOverdue ? 'border-brand-200 bg-brand-50/30 dark:border-brand-900/30 dark:bg-brand-500/5' : '' }}
                            ">

                            <div class="flex items-center gap-3 px-4 py-3.5">

                                {{-- STATUS BUTTON --}}
                                @if ($isPending)
                                    <form method="POST" action="{{ route('crm.tasks.start', $task) }}">
                                        @csrf @method('PATCH')
                                        <button type="submit" title="Mulai task"
                                            class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border-2 border-gray-300 text-gray-300 transition hover:border-brand-400 hover:text-brand-400">
                                            <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                                        </button>
                                    </form>
                                @elseif ($isInProgress)
                                    <button @click="completeModal = true" type="button" title="Selesaikan"
                                        class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border-2 border-brand-500 text-brand-500 transition hover:bg-brand-50">
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

                                {{-- CONTENT --}}
                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <p class="text-sm font-semibold
                                            {{ $isCompleted ? 'text-gray-400 line-through' : '' }}
                                            {{ $isCancelled ? 'text-gray-400' : '' }}
                                            {{ !$isCompleted && !$isCancelled ? 'text-gray-900 dark:text-white' : '' }}">
                                            {{ $task->title }}
                                        </p>
                                        <span class="h-2 w-2 rounded-full {{ $priorityConfig['color'] }}" title="{{ $priorityConfig['label'] }}"></span>
                                        @if ($isOverdue)
                                            <span class="rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-600 dark:bg-red-500/15 dark:text-red-400">Terlambat</span>
                                        @endif
                                    </div>
                                    @if ($task->description)
                                        <p class="mt-0.5 text-xs text-gray-400 line-clamp-1">{{ $task->description }}</p>
                                    @endif
                                    @if ($task->due_at)
                                        <p class="mt-0.5 text-xs {{ $isOverdue ? 'text-red-400' : 'text-gray-400' }}">
                                            Tenggat: {{ $task->due_at->isToday() ? 'Hari ini ' . $task->due_at->format('H:i') : $task->due_at->translatedFormat('d M Y, H:i') }}
                                        </p>
                                    @endif
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

                            </div>

                            {{-- HASIL TASK --}}
                            @if ($task->result_id)
                                <div class="border-t border-gray-100 px-4 py-2 dark:border-gray-800">
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-success-600 dark:text-success-400">
                                        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Hasil: {{ $task->result?->name ?? '-' }}
                                    </span>
                                </div>
                            @endif

                            {{-- MODAL COMPLETE --}}
                            @if ($isInProgress)
                                <x-modal.modal show="completeModal" title="Selesaikan Task" description="Pilih hasil sebelum menandai selesai">
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
                                                label="Catatan"
                                                :value="$task->description"
                                                placeholder="Tambahkan catatan (opsional)"
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
                            <p class="text-sm text-gray-400">Belum ada task aktif.</p>
                        </div>
                    @endforelse

                </div>

                {{-- TAB: TIMELINE --}}
                <div x-show="tab==='timeline'">
                    <div class="relative space-y-0">
                        <div class="absolute bottom-0 left-4 top-2 w-px bg-gray-100 dark:bg-gray-800"></div>

                        @forelse ($activities as $activity)
                            @php
                                $iconBg = match($activity->type) {
                                    'stage_changed'  => 'bg-brand-100 dark:bg-brand-500/20',
                                    'task_completed' => 'bg-success-100 dark:bg-success-500/20',
                                    'whatsapp'       => 'bg-success-100 dark:bg-success-500/20',
                                    default          => 'bg-gray-100 dark:bg-gray-800',
                                };
                                $badgeClass = match($activity->type) {
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
                                    default          => ucfirst(str_replace('_', ' ', $activity->type)),
                                };
                            @endphp

                            <div class="relative mb-4 flex gap-4 last:mb-0">
                                <div class="relative z-10 flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full {{ $iconBg }}">
                                    @if ($activity->type === 'stage_changed')
                                        <svg class="h-3.5 w-3.5 text-brand-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                                        </svg>
                                    @elseif ($activity->type === 'task_completed')
                                        <svg class="h-3.5 w-3.5 text-success-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                        </svg>
                                    @else
                                        <div class="h-2 w-2 rounded-full bg-gray-400 dark:bg-gray-600"></div>
                                    @endif
                                </div>

                                <div class="flex-1 rounded-xl border border-gray-100 bg-white px-4 py-3 dark:border-gray-800 dark:bg-gray-900">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $activity->title ?? '-' }}</p>
                                            @if ($activity->description)
                                                <p class="mt-0.5 text-sm text-gray-500 dark:text-gray-400">{{ $activity->description }}</p>
                                            @endif
                                        </div>
                                        <span class="shrink-0 rounded-full px-2 py-0.5 text-xs font-medium {{ $badgeClass }}">
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
                                <p class="text-sm text-gray-400">Belum ada aktivitas.</p>
                            </div>
                        @endforelse
                    </div>

                    @if ($activities->hasPages())
                        <div class="mt-4">{{ $activities->links() }}</div>
                    @endif
                </div>

            </div>
        </x-common.component-card>

    </div>

    {{-- RIGHT SIDEBAR --}}
    <div class="space-y-6">

        {{-- INFO LEAD --}}
        <x-common.component-card title="Info Prospek">
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
                @if ($lead->phone)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Telepon</span>
                        <a href="tel:{{ $lead->phone }}" class="font-medium text-brand-500 hover:underline">{{ $lead->phone }}</a>
                    </div>
                @endif
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
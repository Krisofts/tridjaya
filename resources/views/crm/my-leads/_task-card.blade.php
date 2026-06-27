@php
    $priorityConfig = match ($task->priority) {
        'urgent' => ['color' => 'bg-red-500',    'label' => 'Mendesak'],
        'high'   => ['color' => 'bg-orange-400', 'label' => 'Tinggi'],
        'medium' => ['color' => 'bg-yellow-400', 'label' => 'Sedang'],
        default  => ['color' => 'bg-gray-300',   'label' => 'Rendah'],
    };
@endphp

<div x-data="{ completeModal: false }"
    class="overflow-hidden rounded-xl border transition
        {{ $isOverdue ? 'border-red-200 bg-red-50/30 dark:border-red-900/30 dark:bg-red-500/5' : 'border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900' }}">

    <div class="flex items-center gap-4 px-4 py-3.5">

        {{-- STATUS BUTTON --}}
        @if ($task->status === 'pending')
            <form method="POST" action="{{ route('crm.tasks.start', $task) }}">
                @csrf @method('PATCH')
                <button type="submit" title="Mulai"
                    class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border-2 border-gray-300 text-gray-300 transition hover:border-brand-400 hover:text-brand-400">
                    <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z" /></svg>
                </button>
            </form>
        @elseif ($task->status === 'in_progress')
            <button @click="completeModal = true" type="button" title="Selesaikan"
                class="flex h-6 w-6 flex-shrink-0 items-center justify-center rounded-full border-2 border-brand-500 text-brand-500 transition hover:bg-brand-50">
                <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </button>
        @endif

        {{-- CONTENT --}}
        <div class="min-w-0 flex-1">
            <div class="flex flex-wrap items-center gap-2">
                <p class="text-sm font-semibold text-gray-900 dark:text-white">{{ $task->title }}</p>
                <span class="h-2 w-2 rounded-full {{ $priorityConfig['color'] }}" title="{{ $priorityConfig['label'] }}"></span>
            </div>
            <div class="mt-0.5 flex flex-wrap items-center gap-1.5 text-xs text-gray-400">
                <a href="{{ route('crm.leads.show', $task->lead_id) }}" class="font-medium text-brand-500 hover:underline">
                    {{ $task->lead?->name ?? '-' }}
                </a>
                <span>·</span>
                <span>{{ $task->lead?->lead_code }}</span>
                @if ($task->lead?->stage)
                    <span>·</span>
                    <span>{{ $task->lead->stage->name }}</span>
                @endif
            </div>
        </div>

        {{-- DUE TIME --}}
        <div class="shrink-0 text-right">
            <p class="text-sm font-semibold {{ $isOverdue ? 'text-red-500' : 'text-gray-700 dark:text-gray-300' }}">
                {{ $task->due_at->format('H:i') }}
            </p>
            <p class="text-xs {{ $isOverdue ? 'text-red-400' : 'text-gray-400' }}">
                {{ $isOverdue ? 'Terlambat ' . $task->due_at->diffForHumans(null, true) : 'Hari ini' }}
            </p>
        </div>

    </div>

    {{-- MODAL COMPLETE --}}
    @if ($task->status === 'in_progress')
        <x-modal.modal show="completeModal" title="Selesaikan Task" description="Pilih hasil sebelum menandai selesai">
            <form method="POST" action="{{ route('crm.tasks.complete', $task) }}">
                @csrf @method('PATCH')
                <div class="grid grid-cols-1 gap-4">
                    <x-form.select.select-with-label
                        name="result_id"
                        label="Hasil Aktivitas"
                        placeholder="-- Pilih Hasil --"
                        :options="$results ?? []"
                    />
                    <x-form.textarea.textarea-with-label
                        name="description"
                        label="Catatan"
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
                        ✓ Selesaikan
                    </button>
                </div>
            </form>
        </x-modal.modal>
    @endif

</div>
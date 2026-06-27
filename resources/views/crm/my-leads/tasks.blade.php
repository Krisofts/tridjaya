@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-(--breakpoint-2xl) p-4 pb-20 md:p-6 md:pb-6">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Task Hari Ini</h2>
            <p class="mt-0.5 text-sm text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>
        <div class="flex items-center gap-3">
            @if ($overdue->count() > 0)
                <span class="inline-flex items-center gap-1.5 rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-600 dark:bg-red-500/15 dark:text-red-400">
                    <span class="h-2 w-2 rounded-full bg-red-500"></span>
                    {{ $overdue->count() }} terlambat
                </span>
            @endif
            <span class="inline-flex items-center gap-1.5 rounded-full bg-gray-100 px-3 py-1 text-sm font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                {{ $tasks->count() }} task aktif
            </span>
        </div>
    </div>

    @if ($tasks->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 py-20 dark:border-gray-800">
            <svg class="h-12 w-12 text-gray-300 dark:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <p class="mt-3 text-base font-medium text-gray-500">Semua task selesai! 🎉</p>
            <p class="mt-1 text-sm text-gray-400">Tidak ada task yang perlu dikerjakan hari ini.</p>
            <a href="{{ route('crm.leads.index') }}" class="mt-4 text-sm text-brand-500 hover:underline">Lihat semua lead →</a>
        </div>
    @else

        {{-- TERLAMBAT --}}
        @if ($overdue->count() > 0)
            <div class="mb-6">
                <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-red-500">
                    <span class="h-2 w-2 animate-pulse rounded-full bg-red-500"></span>
                    Terlambat ({{ $overdue->count() }})
                </h3>
                <div class="space-y-3">
                    @foreach ($overdue as $task)
                        @include('crm.my-leads._task-card', ['task' => $task, 'isOverdue' => true])
                    @endforeach
                </div>
            </div>
        @endif

        {{-- AKAN DATANG --}}
        @if ($dueSoon->count() > 0)
            <div>
                <h3 class="mb-3 flex items-center gap-2 text-sm font-semibold text-gray-500 dark:text-gray-400">
                    <span class="h-2 w-2 rounded-full bg-brand-500"></span>
                    Hari Ini ({{ $dueSoon->count() }})
                </h3>
                <div class="space-y-3">
                    @foreach ($dueSoon as $task)
                        @include('crm.my-leads._task-card', ['task' => $task, 'isOverdue' => false])
                    @endforeach
                </div>
            </div>
        @endif

    @endif

</div>

@endsection
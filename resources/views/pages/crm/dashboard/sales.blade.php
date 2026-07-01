{{-- resources/views/pages/crm/dashboard/sales.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard CRM')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Dashboard</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Selamat datang, {{ Auth::user()->name }}</p>
    </div>
    <a href="{{ route('crm.leads.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Lead
    </a>
</div>

{{-- ------------------------------------------------------------------ --}}
{{-- STAT CARDS                                                           --}}
{{-- ------------------------------------------------------------------ --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Lead Open</p>
            <div class="w-8 h-8 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['leads_open'] }}</p>
        <a href="{{ route('crm.leads.my-leads', ['status' => 'open']) }}"
           class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 block">Lihat semua →</a>
    </div>

    {{-- BARU: Lead Hari Ini --}}
    <div class="bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-800 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-purple-600 dark:text-purple-400">Lead Hari Ini</p>
            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-purple-700 dark:text-purple-400">{{ $stats['leads_today'] }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Masuk hari ini</p>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-green-600 dark:text-green-400">Lead Won</p>
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $stats['leads_won'] }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Total semua waktu</p>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-yellow-600 dark:text-yellow-400">Task Hari Ini</p>
            <div class="w-8 h-8 bg-yellow-100 dark:bg-yellow-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-yellow-700 dark:text-yellow-400">{{ $stats['tasks_today'] }}</p>
        @if($stats['tasks_overdue'] > 0)
            <p class="text-xs text-red-500 mt-1">+ {{ $stats['tasks_overdue'] }} terlambat</p>
        @else
            <a href="{{ route('crm.tasks.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline mt-1 block">Lihat tasks →</a>
        @endif
    </div>

</div>

{{-- Alert overdue --}}
@if($stats['tasks_overdue'] > 0 || $stats['followup_overdue'] > 0)
<div class="flex items-start gap-3 px-4 py-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl mb-5 text-sm text-red-700 dark:text-red-400">
    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>
        Perhatian:
        @if($stats['tasks_overdue'] > 0)
            <strong>{{ $stats['tasks_overdue'] }} task terlambat</strong>
        @endif
        @if($stats['tasks_overdue'] > 0 && $stats['followup_overdue'] > 0) dan @endif
        @if($stats['followup_overdue'] > 0)
            <strong>{{ $stats['followup_overdue'] }} follow-up terlewat</strong>
        @endif
        — segera ditindaklanjuti.
    </div>
</div>
@endif

{{-- Lead hari ini --}}
@if($todayLeads->isNotEmpty())
<div class="bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-800 rounded-xl mb-5">
    <div class="flex items-center justify-between px-5 py-4 border-b border-purple-100 dark:border-purple-800">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></div>
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
                Lead Masuk Hari Ini
                <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400 rounded-md">{{ $stats['leads_today'] }}</span>
            </h2>
        </div>
        <a href="{{ route('crm.leads.my-leads') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lihat semua</a>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        @foreach($todayLeads as $lead)
        <div class="flex items-center gap-3 px-5 py-3">
            <div class="w-7 h-7 rounded-full bg-purple-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                {{ strtoupper(substr($lead->name, 0, 1)) }}
            </div>
            <div class="flex-1 min-w-0">
                <a href="{{ route('crm.leads.show', $lead) }}"
                   class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline truncate block">
                    {{ $lead->name }}
                </a>
                <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $lead->pipeline->name ?? '—' }}</span>
                    <span class="text-gray-300 dark:text-gray-600">·</span>
                    <span class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->stage->name ?? '—' }}</span>
                </div>
            </div>
            <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">{{ $lead->created_at->format('H:i') }}</span>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ------------------------------------------------------------------ --}}
{{-- 2 KOLOM: Tasks + Lead Saya                                           --}}
{{-- ------------------------------------------------------------------ --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">

    {{-- Tasks hari ini --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Task Hari Ini & Terlambat</h2>
            <a href="{{ route('crm.tasks.index') }}"
               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($tasks as $task)
            @php
                $priorityColors = [
                    'high'   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                    'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                    'low'    => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
                ];
            @endphp
            <div class="flex items-start gap-3 px-5 py-3">
                <form method="POST" action="{{ route('crm.tasks.done', $task) }}" class="flex-shrink-0 mt-0.5">
                    @csrf @method('PATCH')
                    <button type="submit" class="w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600 hover:border-blue-500 transition-colors"></button>
                </form>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-800 dark:text-gray-200 truncate">{{ $task->title }}</p>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $priorityColors[$task->priority] }}">
                            {{ $task->priorityLabel() }}
                        </span>
                        <span class="text-xs {{ $task->isOverdue() ? 'text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $task->due_at->format('d M, H:i') }}
                            @if($task->isOverdue()) ⚠ @endif
                        </span>
                        @if($task->lead)
                            <a href="{{ route('crm.leads.show', $task->lead) }}"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline truncate max-w-[100px]">
                                {{ $task->lead->name }}
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                <svg class="w-8 h-8 mx-auto mb-2 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Semua task selesai! 🎉
            </div>
            @endforelse
        </div>
        @if($tasks->isNotEmpty())
        <div class="px-5 py-3 border-t border-gray-100 dark:border-gray-700">
            <a href="{{ route('crm.tasks.create') }}"
               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">+ Tambah task baru</a>
        </div>
        @endif
    </div>

    {{-- Lead saya --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Lead Saya</h2>
            <a href="{{ route('crm.leads.index', ['assigned_to' => Auth::id()]) }}"
               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($myLeads as $lead)
            <div class="flex items-start gap-3 px-5 py-3">
                <div class="flex-1 min-w-0">
                    <a href="{{ route('crm.leads.show', $lead) }}"
                       class="text-sm font-medium text-blue-600 dark:text-blue-400 hover:underline truncate block">
                        {{ $lead->name }}
                    </a>
                    <div class="flex items-center gap-2 mt-0.5">
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $lead->pipeline->name ?? '—' }}</span>
                        <span class="text-gray-300 dark:text-gray-600">·</span>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->stage->name ?? '—' }}</span>
                    </div>
                </div>
                <div class="text-right flex-shrink-0">
                    <p class="text-xs font-medium text-gray-700 dark:text-gray-300">
                        {{ $lead->probability }}%
                    </p>
                    @if($lead->next_follow_up_at)
                        <p class="text-xs {{ $lead->next_follow_up_at->isPast() ? 'text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $lead->next_follow_up_at->format('d M') }}
                        </p>
                    @endif
                </div>
            </div>
            @empty
            <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                Belum ada lead.
                <a href="{{ route('crm.leads.create') }}" class="text-blue-600 dark:text-blue-400 hover:underline block mt-1">+ Tambah lead</a>
            </div>
            @endforelse
        </div>
    </div>

</div>

{{-- ------------------------------------------------------------------ --}}
{{-- Aktivitas terbaru                                                    --}}
{{-- ------------------------------------------------------------------ --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Aktivitas Terakhir Saya</h2>
    </div>
    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        @forelse($activities as $activity)
        @php $isSystem = $activity->type?->slug === 'sistem'; @endphp
        <div class="flex items-start gap-3 px-5 py-3 {{ $isSystem ? 'bg-gray-50 dark:bg-gray-700/30' : '' }}">
            <div class="w-7 h-7 flex-shrink-0 rounded-full {{ $isSystem ? 'bg-gray-200 dark:bg-gray-600' : 'bg-blue-100 dark:bg-blue-900/30' }} flex items-center justify-center mt-0.5">
                <svg class="w-3.5 h-3.5 {{ $isSystem ? 'text-gray-500' : 'text-blue-500 dark:text-blue-400' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    @if($isSystem)
                        <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                    @else
                        <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.07 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3 1.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 8 8l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/>
                    @endif
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm {{ $isSystem ? 'text-gray-500 dark:text-gray-400 italic' : 'text-gray-800 dark:text-gray-200' }}">
                    {{ $activity->title }}
                </p>
                <div class="flex items-center gap-2 mt-0.5">
                    @if($activity->lead)
                        <a href="{{ route('crm.leads.show', $activity->lead) }}"
                           class="text-xs text-blue-600 dark:text-blue-400 hover:underline truncate max-w-[150px]">
                            {{ $activity->lead->name }}
                        </a>
                        <span class="text-gray-300 dark:text-gray-600">·</span>
                    @endif
                    @if(! $isSystem && $activity->result)
                        <span class="text-xs {{ $activity->result->is_success ? 'text-green-600 dark:text-green-400' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $activity->result->name }}
                        </span>
                        <span class="text-gray-300 dark:text-gray-600">·</span>
                    @endif
                    <span class="text-xs text-gray-400 dark:text-gray-500">{{ $activity->activity_at->diffForHumans() }}</span>
                </div>
            </div>
        </div>
        @empty
        <div class="px-5 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
            Belum ada aktivitas.
        </div>
        @endforelse
    </div>
</div>

@endsection
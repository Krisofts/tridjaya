{{-- resources/views/pages/crm/tasks/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Tasks')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Tasks</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Daftar tugas yang perlu diselesaikan</p>
    </div>
    <a href="{{ route('crm.tasks.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Task
    </a>
</div>

{{-- Stats cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Open</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['open'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 border border-red-200 dark:border-red-800 rounded-xl p-4">
        <p class="text-xs text-red-500 mb-1">Terlambat</p>
        <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $stats['overdue'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 border border-yellow-200 dark:border-yellow-800 rounded-xl p-4">
        <p class="text-xs text-yellow-600 dark:text-yellow-400 mb-1">Hari ini</p>
        <p class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">{{ $stats['today'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 rounded-xl p-4">
        <p class="text-xs text-green-600 dark:text-green-400 mb-1">Selesai</p>
        <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $stats['done'] }}</p>
    </div>
</div>

{{-- Filter --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-5">
    <form method="GET" action="{{ route('crm.tasks.index') }}" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-[160px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Cari</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                   placeholder="Judul task…"
                   class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="min-w-[130px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Status</label>
            <select name="status"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua status</option>
                <option value="open"      @selected(($filters['status'] ?? '') === 'open')>Open</option>
                <option value="done"      @selected(($filters['status'] ?? '') === 'done')>Selesai</option>
                <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Dibatalkan</option>
            </select>
        </div>

        <div class="min-w-[130px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Prioritas</label>
            <select name="priority"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua prioritas</option>
                <option value="high"   @selected(($filters['priority'] ?? '') === 'high')>Tinggi</option>
                <option value="medium" @selected(($filters['priority'] ?? '') === 'medium')>Sedang</option>
                <option value="low"    @selected(($filters['priority'] ?? '') === 'low')>Rendah</option>
            </select>
        </div>

        <div class="min-w-[140px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Due Date</label>
            <select name="due_date"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua</option>
                <option value="overdue"  @selected(($filters['due_date'] ?? '') === 'overdue')>Terlambat</option>
                <option value="today"    @selected(($filters['due_date'] ?? '') === 'today')>Hari ini</option>
                <option value="upcoming" @selected(($filters['due_date'] ?? '') === 'upcoming')>Akan datang</option>
            </select>
        </div>

        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Assigned to</label>
            <select name="assigned_to"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(($filters['assigned_to'] ?? '') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Filter
            </button>
            <a href="{{ route('crm.tasks.index') }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                Reset
            </a>
        </div>

    </form>
</div>

{{-- Task list --}}
<div class="space-y-2">
    @forelse($tasks as $task)
    @php
        $priorityColors = [
            'high'   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
            'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
            'low'    => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
        ];
        $isOverdue = $task->isOverdue();
    @endphp
    <div class="bg-white dark:bg-gray-800 border {{ $isOverdue ? 'border-red-200 dark:border-red-800' : 'border-gray-200 dark:border-gray-700' }} rounded-xl px-4 py-3 flex items-start gap-3 group">

        {{-- Checkbox done --}}
        @if($task->isOpen())
        <form method="POST" action="{{ route('crm.tasks.done', $task) }}" class="flex-shrink-0 mt-0.5">
            @csrf @method('PATCH')
            <button type="submit" title="Tandai selesai"
                    class="w-5 h-5 rounded border-2 border-gray-300 dark:border-gray-600 hover:border-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/30 transition-colors flex items-center justify-center">
            </button>
        </form>
        @else
        <div class="flex-shrink-0 mt-0.5 w-5 h-5 rounded border-2 flex items-center justify-center
                    {{ $task->isDone() ? 'border-green-500 bg-green-500' : 'border-gray-300 dark:border-gray-600 bg-gray-100 dark:bg-gray-700' }}">
            @if($task->isDone())
                <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
            @else
                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            @endif
        </div>
        @endif

        {{-- Konten --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium {{ $task->isDone() ? 'line-through text-gray-400 dark:text-gray-500' : 'text-gray-800 dark:text-gray-200' }}">
                        {{ $task->title }}
                    </p>
                    <div class="flex items-center flex-wrap gap-2 mt-1">
                        {{-- Priority --}}
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium {{ $priorityColors[$task->priority] }}">
                            {{ $task->priorityLabel() }}
                        </span>
                        {{-- Due date --}}
                        <span class="text-xs {{ $isOverdue ? 'text-red-500 font-medium' : 'text-gray-400 dark:text-gray-500' }}">
                            <svg class="w-3 h-3 inline mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                            {{ $isOverdue ? 'Terlambat — ' : '' }}{{ $task->due_at->format('d M Y, H:i') }}
                        </span>
                        {{-- Lead terkait --}}
                        @if($task->lead)
                            <a href="{{ route('crm.leads.show', $task->lead) }}"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                <svg class="w-3 h-3 inline mr-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                                {{ $task->lead->name }}
                            </a>
                        @endif
                        {{-- Assigned --}}
                        @if($task->assignedUser)
                            <span class="text-xs text-gray-400 dark:text-gray-500">
                                → {{ $task->assignedUser->name }}
                            </span>
                        @endif
                    </div>
                    @if($task->description)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($task->description, 100) }}</p>
                    @endif
                </div>

                {{-- Actions --}}
                <div class="flex items-center gap-1 flex-shrink-0 opacity-0 group-hover:opacity-100 transition-opacity">
                    @if(! $task->isOpen())
                    <form method="POST" action="{{ route('crm.tasks.reopen', $task) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="Buka kembali"
                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/></svg>
                        </button>
                    </form>
                    @endif
                    @if($task->isOpen())
                    <form method="POST" action="{{ route('crm.tasks.cancel', $task) }}">
                        @csrf @method('PATCH')
                        <button type="submit" title="Batalkan"
                                class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </form>
                    @endif
                    <a href="{{ route('crm.tasks.edit', $task) }}"
                       class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded-lg transition-colors" title="Edit">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                    </a>
                    <form method="POST" action="{{ route('crm.tasks.destroy', $task) }}"
                          onsubmit="return confirm('Hapus task ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" title="Hapus"
                                class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-4 py-16 text-center">
        <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-500">
            <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            <p class="text-sm">Tidak ada task.</p>
            <a href="{{ route('crm.tasks.create') }}"
               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                + Tambah task pertama
            </a>
        </div>
    </div>
    @endforelse
</div>

@if($tasks->hasPages())
<div class="mt-4 flex justify-end">
    {{ $tasks->appends($filters)->links() }}
</div>
@endif

@endsection
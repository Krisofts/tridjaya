{{-- resources/views/pages/crm/notifications/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Notifikasi</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Semua notifikasi dan reminder</p>
    </div>
    <div class="flex items-center gap-2">
        <form method="POST" action="{{ route('crm.notifications.destroy-read') }}">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                Hapus yang sudah dibaca
            </button>
        </form>
    </div>
</div>

{{-- Form tambah reminder manual --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl mb-5"
     x-data="{ open: false }">
    <button @click="open = !open"
            class="w-full flex items-center justify-between px-5 py-4 text-sm font-semibold text-gray-800 dark:text-white hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors rounded-xl">
        <span>+ Tambah Reminder Manual</span>
        <svg class="w-4 h-4 transition-transform" :class="open ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="m6 9 6 6 6-6"/>
        </svg>
    </button>
    <div x-show="open" x-collapse class="border-t border-gray-100 dark:border-gray-700">
        <form method="POST" action="{{ route('crm.notifications.store') }}" class="p-5">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">

                <div class="md:col-span-2">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                        Judul Reminder <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" required
                           placeholder="Contoh: Telepon balik Budi jam 10"
                           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Ingatkan pada</label>
                    <input type="datetime-local" name="remind_at"
                           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Kosongkan = tampil sekarang</p>
                </div>

                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Catatan</label>
                    <input type="text" name="message"
                           placeholder="Detail opsional…"
                           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

            </div>
            <div class="flex justify-end">
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    Simpan Reminder
                </button>
            </div>
        </form>
    </div>
</div>

{{-- List notifikasi --}}
<div class="space-y-2">
    @forelse($notifications as $notif)
    @php
        $colors = [
            'red'    => ['bg' => 'bg-red-100 dark:bg-red-900/30',    'text' => 'text-red-600 dark:text-red-400'],
            'green'  => ['bg' => 'bg-green-100 dark:bg-green-900/30', 'text' => 'text-green-600 dark:text-green-400'],
            'yellow' => ['bg' => 'bg-yellow-100 dark:bg-yellow-900/30','text' => 'text-yellow-600 dark:text-yellow-400'],
            'orange' => ['bg' => 'bg-orange-100 dark:bg-orange-900/30','text' => 'text-orange-600 dark:text-orange-400'],
            'blue'   => ['bg' => 'bg-blue-100 dark:bg-blue-900/30',  'text' => 'text-blue-600 dark:text-blue-400'],
        ];
        $color = $colors[$notif->iconColor()] ?? $colors['blue'];
    @endphp
    <div class="bg-white dark:bg-gray-800 border {{ $notif->is_read ? 'border-gray-200 dark:border-gray-700' : 'border-blue-200 dark:border-blue-800' }} rounded-xl px-4 py-3 flex items-start gap-3">

        {{-- Icon --}}
        <div class="w-9 h-9 flex-shrink-0 rounded-full {{ $color['bg'] }} flex items-center justify-center mt-0.5">
            @if($notif->icon() === 'won')
                <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
            @elseif($notif->icon() === 'task')
                <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
            @elseif(in_array($notif->icon(), ['lost', 'followup']))
                <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            @else
                <svg class="w-4 h-4 {{ $color['text'] }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            @endif
        </div>

        {{-- Konten --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-2">
                <div>
                    <p class="text-sm font-medium {{ $notif->is_read ? 'text-gray-500 dark:text-gray-400' : 'text-gray-800 dark:text-gray-200' }}">
                        {{ $notif->title }}
                        @if(! $notif->is_read)
                            <span class="inline-block w-1.5 h-1.5 bg-blue-500 rounded-full ml-1 align-middle"></span>
                        @endif
                    </p>
                    @if($notif->message)
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $notif->message }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-1">
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $notif->created_at->diffForHumans() }}</span>
                        @if($notif->lead)
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <a href="{{ route('crm.leads.show', $notif->lead) }}"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                {{ $notif->lead->name }}
                            </a>
                        @endif
                        @if($notif->task)
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $notif->task->title }}</span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-1 flex-shrink-0">
                    @if(! $notif->is_read)
                    <form method="POST" action="{{ route('crm.notifications.read', $notif) }}">
                        @csrf
                        <button type="submit" title="Tandai dibaca"
                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                        </button>
                    </form>
                    @endif
                    <form method="POST" action="{{ route('crm.notifications.destroy', $notif) }}"
                          onsubmit="return confirm('Hapus notifikasi ini?')">
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
            <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
            <p class="text-sm">Tidak ada notifikasi.</p>
        </div>
    </div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="mt-4 flex justify-end">
    {{ $notifications->links() }}
</div>
@endif

@endsection
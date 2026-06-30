{{-- resources/views/pages/crm/leads/show.blade.php --}}
@extends('layouts.app')

@section('title', $lead->name)

@section('content')

{{-- Breadcrumb --}}
<nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-5">
    <a href="{{ route('crm.leads.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Leads</a>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
    <span class="text-gray-800 dark:text-white font-medium">{{ $lead->name }}</span>
</nav>

{{-- ------------------------------------------------------------------ --}}
{{-- Top bar                                                              --}}
{{-- ------------------------------------------------------------------ --}}
<div class="flex flex-wrap items-center justify-between gap-3 mb-5">
    <div class="flex items-center gap-3">
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $lead->name }}</h1>
        @php
            $badgeClass = match($lead->status) {
                'won'   => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                'lost'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
            };
        @endphp
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
            {{ $lead->statusLabel() }}
        </span>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        @if($lead->isOpen())
            <a href="{{ route('crm.leads.edit', $lead) }}"
               class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                Edit
            </a>
            <button onclick="document.getElementById('modalWon').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                Won
            </button>
            <button onclick="document.getElementById('modalLost').classList.remove('hidden')"
                    class="inline-flex items-center gap-1.5 px-3 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                Lost
            </button>
        @else
            <form method="POST" action="{{ route('crm.leads.reopen', $lead) }}">
                @csrf @method('PATCH')
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="1 4 1 10 7 10"/><path d="M3.51 15a9 9 0 1 0 .49-4.5"/></svg>
                    Reopen
                </button>
            </form>
        @endif
        <form method="POST" action="{{ route('crm.leads.destroy', $lead) }}"
              onsubmit="return confirm('Hapus lead {{ addslashes($lead->name) }}?')">
            @csrf @method('DELETE')
            <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-red-600 dark:text-red-400 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                Hapus
            </button>
        </form>
    </div>
</div>

{{-- ------------------------------------------------------------------ --}}
{{-- Stage Rail                                                           --}}
{{-- ------------------------------------------------------------------ --}}
@if($lead->isOpen())
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-5">
    <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Pindah Stage</p>
    <div class="flex gap-0 overflow-x-auto rounded-lg border border-gray-200 dark:border-gray-700">
        @foreach($lead->pipeline->stages->sortBy('order') as $stage)
        <form method="POST" action="{{ route('crm.leads.moveStage', $lead) }}" class="flex-1 min-w-[100px]">
            @csrf @method('PATCH')
            <input type="hidden" name="stage_id" value="{{ $stage->id }}">
            <button type="submit"
                    class="w-full px-3 py-2.5 text-xs font-medium text-center transition-colors whitespace-nowrap
                           {{ $lead->stage_id === $stage->id
                               ? 'bg-blue-600 text-white cursor-default'
                               : 'bg-white dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 dark:hover:text-blue-400' }}"
                    {{ $lead->stage_id === $stage->id ? 'disabled' : '' }}>
                {{ $stage->name }}
            </button>
        </form>
        @endforeach
    </div>
</div>
@endif

{{-- ------------------------------------------------------------------ --}}
{{-- Main Layout                                                          --}}
{{-- ------------------------------------------------------------------ --}}
<div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

    {{-- ============================================================== --}}
    {{-- LEFT (2/3)                                                       --}}
    {{-- ============================================================== --}}
    <div class="xl:col-span-2 space-y-5">

        {{-- Detail Card --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Detail Lead</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">

                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Nomor HP</span>
                    <a href="tel:{{ $lead->phone }}" class="text-sm text-blue-600 dark:text-blue-400 hover:underline">{{ $lead->phone }}</a>
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Pipeline</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $lead->pipeline->name ?? '—' }}</span>
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Stage</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $lead->stage->name ?? '—' }}</span>
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Sumber</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $lead->source->name ?? '—' }}</span>
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Produk</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $lead->product->name ?? '—' }}</span>
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Sales</span>
                    @if($lead->assignedUser)
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0">
                                {{ strtoupper(substr($lead->assignedUser->name, 0, 1)) }}
                            </div>
                            <span class="text-sm text-gray-800 dark:text-gray-200">{{ $lead->assignedUser->name }}</span>
                        </div>
                    @else
                        <span class="text-sm text-gray-400">—</span>
                    @endif
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Dibuat oleh</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $lead->createdBy->name ?? '—' }}</span>
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Estimasi Nilai</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($lead->estimated_value, 0, ',', '.') }}</span>
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Probabilitas</span>
                    <div class="flex items-center gap-2 flex-1 max-w-[200px]">
                        <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                            <div class="h-full bg-blue-500 rounded-full" style="width:{{ $lead->probability }}%"></div>
                        </div>
                        <span class="text-sm text-gray-700 dark:text-gray-300 font-medium">{{ $lead->probability }}%</span>
                    </div>
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Follow-up</span>
                    @if($lead->next_follow_up_at)
                        <span class="text-sm {{ $lead->next_follow_up_at->isPast() ? 'text-red-500' : 'text-gray-800 dark:text-gray-200' }}">
                            {{ $lead->next_follow_up_at->format('d M Y, H:i') }}
                            @if($lead->next_follow_up_at->isPast())
                                <span class="text-xs ml-1">(terlambat)</span>
                            @endif
                        </span>
                    @else
                        <span class="text-sm text-gray-400">—</span>
                    @endif
                </div>
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Dibuat pada</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $lead->created_at->format('d M Y, H:i') }}</span>
                </div>
                @if($lead->closed_at)
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Ditutup pada</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $lead->closed_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
                @if($lead->isLost())
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Alasan Lost</span>
                    <div>
                        <span class="text-sm text-red-600 dark:text-red-400 font-medium">{{ $lead->lostReason->name ?? '—' }}</span>
                        @if($lead->lost_note)
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $lead->lost_note }}</p>
                        @endif
                    </div>
                </div>
                @endif

            </div>
        </div>

        {{-- Lokasi --}}
        @if($lead->province || $lead->address)
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Lokasi</h2>
            </div>
            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Wilayah</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">
                        {{ collect([$lead->district?->name, $lead->regency?->name, $lead->province?->name])->filter()->implode(', ') ?: '—' }}
                    </span>
                </div>
                @if($lead->address)
                <div class="flex items-start gap-4 px-5 py-3">
                    <span class="text-xs text-gray-400 dark:text-gray-500 min-w-[130px] pt-0.5 font-medium">Alamat</span>
                    <span class="text-sm text-gray-800 dark:text-gray-200">{{ $lead->address }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ---------------------------------------------------------- --}}
        {{-- AKTIVITAS                                                    --}}
        {{-- ---------------------------------------------------------- --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl" id="activity-section">
            <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
                    Aktivitas
                    <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-md">
                        {{ $lead->activities->count() }}
                    </span>
                </h2>
                <button onclick="document.getElementById('modalActivity').classList.remove('hidden')"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Aktivitas
                </button>
            </div>

            <div class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($lead->activities as $activity)
                @php $isSystem = $activity->type?->slug === 'sistem'; @endphp
                <div class="flex items-start gap-3 px-5 py-4 group {{ $isSystem ? 'bg-gray-50 dark:bg-gray-700/30' : '' }}">

                    {{-- Icon --}}
                    <div class="w-8 h-8 flex-shrink-0 rounded-full flex items-center justify-center mt-0.5
                                {{ $isSystem
                                    ? 'bg-gray-200 dark:bg-gray-600'
                                    : 'bg-blue-50 dark:bg-blue-900/30 border border-blue-100 dark:border-blue-800' }}">
                        @if($isSystem)
                            <svg class="w-3.5 h-3.5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        @else
                            <svg class="w-3.5 h-3.5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.07 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3 1.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 8 8l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium {{ $isSystem ? 'text-gray-500 dark:text-gray-400 italic' : 'text-gray-800 dark:text-gray-200' }} leading-snug">
                                    {{ $activity->title }}
                                </p>
                                @if(! $isSystem)
                                <div class="flex items-center flex-wrap gap-2 mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                                        {{ $activity->type->name ?? '—' }}
                                    </span>
                                    @if($activity->result)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs
                                            {{ $activity->result->is_success
                                                ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                                : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                            @if($activity->result->is_success)
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                            @endif
                                            {{ $activity->result->name }}
                                        </span>
                                    @endif
                                    @if($activity->is_contacted)
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-xs bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                                            Terhubung
                                        </span>
                                    @endif
                                </div>
                                @endif
                                @if($activity->notes)
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1.5 leading-relaxed">{{ $activity->notes }}</p>
                                @endif
                                @if($activity->location)
                                    <p class="flex items-center gap-1 text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                                        {{ $activity->location }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                    {{ $activity->user->name ?? '—' }} · {{ $activity->activity_at->format('d M Y, H:i') }}
                                </p>
                            </div>

                            {{-- Actions — sembunyikan untuk activity sistem --}}
                            @if(! $isSystem)
                            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                                <button onclick="openEditActivity({{ $activity->id }})"
                                        class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded-lg transition-colors">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                                </button>
                                <form method="POST" action="{{ route('crm.activities.destroy', $activity) }}"
                                      onsubmit="return confirm('Hapus aktivitas ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="px-5 py-12 text-center">
                    <div class="w-10 h-10 mx-auto mb-3 rounded-full bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.07 12a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3 1.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.09 8.91a16 16 0 0 0 8 8l1.27-1.27a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                    </div>
                    <p class="text-sm text-gray-400 dark:text-gray-500 mb-3">Belum ada aktivitas tercatat.</p>
                    <button onclick="document.getElementById('modalActivity').classList.remove('hidden')"
                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                        + Catat aktivitas pertama
                    </button>
                </div>
                @endforelse
            </div>
        </div>

{{--
    Panel task untuk show.blade.php lead
    Taruh setelah panel Aktivitas, sebelum penutup div xl:col-span-2

    Pastikan di LeadController::show() sudah load:
    'tasks.assignedUser'

    Dan di show() tambahkan:
    $lead->load([..., 'tasks.assignedUser']);
--}}

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
            Tasks
            <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 dark:text-gray-400 rounded-md">
                {{ $lead->tasks->where('status', 'open')->count() }} open
            </span>
        </h2>
        <a href="{{ route('crm.tasks.create', ['lead_id' => $lead->id]) }}"
           class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
            Tambah Task
        </a>
    </div>

    <div class="divide-y divide-gray-100 dark:divide-gray-700">
        @forelse($lead->tasks as $task)
        @php
            $priorityColors = [
                'high'   => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                'medium' => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400',
                'low'    => 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-400',
            ];
        @endphp
        <div class="flex items-start gap-3 px-5 py-3 group">

            {{-- Checkbox --}}
            @if($task->isOpen())
            <form method="POST" action="{{ route('crm.tasks.done', $task) }}" class="flex-shrink-0 mt-0.5">
                @csrf @method('PATCH')
                <button type="submit" title="Tandai selesai"
                        class="w-4 h-4 rounded border-2 border-gray-300 dark:border-gray-600 hover:border-blue-500 transition-colors flex-shrink-0">
                </button>
            </form>
            @else
            <div class="flex-shrink-0 mt-0.5 w-4 h-4 rounded border-2 flex items-center justify-center
                        {{ $task->isDone() ? 'border-green-500 bg-green-500' : 'border-gray-300 dark:border-gray-600' }}">
                @if($task->isDone())
                    <svg class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                @endif
            </div>
            @endif

            {{-- Konten --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm {{ $task->isDone() ? 'line-through text-gray-400 dark:text-gray-500' : 'text-gray-800 dark:text-gray-200' }}">
                    {{ $task->title }}
                </p>
                <div class="flex items-center flex-wrap gap-2 mt-0.5">
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium {{ $priorityColors[$task->priority] }}">
                        {{ $task->priorityLabel() }}
                    </span>
                    <span class="text-xs {{ $task->isOverdue() ? 'text-red-500' : 'text-gray-400 dark:text-gray-500' }}">
                        {{ $task->due_at->format('d M Y, H:i') }}
                        @if($task->isOverdue()) (terlambat) @endif
                    </span>
                    @if($task->assignedUser)
                        <span class="text-xs text-gray-400 dark:text-gray-500">→ {{ $task->assignedUser->name }}</span>
                    @endif
                </div>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity flex-shrink-0">
                <a href="{{ route('crm.tasks.edit', $task) }}"
                   class="p-1 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                </a>
                <form method="POST" action="{{ route('crm.tasks.destroy', $task) }}"
                      onsubmit="return confirm('Hapus task ini?')">
                    @csrf @method('DELETE')
                    <button type="submit"
                            class="p-1 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded transition-colors">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
            Belum ada task untuk lead ini.
        </div>
        @endforelse
    </div>
</div>



    </div>

    {{-- ============================================================== --}}
    {{-- RIGHT (1/3)                                                      --}}
    {{-- ============================================================== --}}
    <div class="space-y-5">

        {{-- Ringkasan --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-5">
            <p class="text-xs font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider mb-3">Ringkasan</p>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Total aktivitas</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $lead->activities->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Kontak berhasil</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $lead->activities->where('is_contacted', true)->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Perpindahan stage</span>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $lead->stageHistories->count() }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-xs text-gray-500 dark:text-gray-400">Aktivitas terakhir</span>
                    <span class="text-xs text-gray-700 dark:text-gray-300">
                        {{ $lead->last_activity_at ? $lead->last_activity_at->diffForHumans() : '—' }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Riwayat Stage --}}
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Riwayat Stage</h2>
            </div>
            <div class="p-5">
                @forelse($lead->stageHistories as $history)
                <div class="relative flex gap-3 pb-5 last:pb-0">
                    @if(!$loop->last)
                    <div class="absolute left-[9px] top-5 bottom-0 w-px bg-gray-200 dark:bg-gray-700"></div>
                    @endif
                    <div class="relative flex-shrink-0 w-5 h-5 rounded-full bg-blue-600 border-2 border-white dark:border-gray-800 shadow-sm mt-0.5 z-10"></div>
                    <div class="flex-1 min-w-0 pb-1">
                        <p class="text-sm text-gray-800 dark:text-gray-200 leading-snug">
                            @if($history->fromStage)
                                <span class="text-gray-500 dark:text-gray-400">{{ $history->fromStage->name }}</span>
                                <span class="mx-1 text-gray-400">→</span>
                            @endif
                            <span class="font-medium">{{ $history->toStage->name ?? '—' }}</span>
                        </p>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">
                            {{ $history->changedByUser->name ?? '—' }} · {{ $history->created_at->diffForHumans() }}
                        </p>
                    </div>
                </div>
                @empty
                <p class="text-sm text-gray-400 dark:text-gray-500 text-center py-4">Belum ada riwayat.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>

{{-- ================================================================== --}}
{{-- MODAL: Tambah Aktivitas                                              --}}
{{-- ================================================================== --}}
<div id="modalActivity"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) closeActivityModal()">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-lg shadow-xl" onclick="event.stopPropagation()">

        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 id="modalActivityTitle" class="text-base font-semibold text-gray-900 dark:text-white">Tambah Aktivitas</h3>
            <button onclick="closeActivityModal()" class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <form id="activityForm" method="POST" action="{{ route('crm.leads.activities.store', $lead) }}">
            @csrf
            <span id="activityMethodField"></span>

            <div class="p-6 space-y-4">

                {{-- Title --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                        Judul <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" id="activityTitle" required
                           placeholder="Contoh: Follow-up via telepon"
                           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                {{-- Type & Result --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                            Jenis Aktivitas <span class="text-red-500">*</span>
                        </label>
                        <select name="activity_type_id" id="activityType" required
                                class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">— Pilih jenis —</option>
                            @foreach($activityTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Hasil</label>
                        <select name="activity_result_id" id="activityResult"
                                class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                            <option value="">— Pilih hasil —</option>
                        </select>
                    </div>
                </div>

                {{-- Waktu aktivitas --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                        Waktu Aktivitas <span class="text-red-500">*</span>
                    </label>
                    <input type="datetime-local" name="activity_at" id="activityAt" required
                           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

                {{-- Lokasi & Contacted --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Lokasi</label>
                        <input type="text" name="location" id="activityLocation"
                               placeholder="Opsional"
                               class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    </div>
                    <div class="flex items-end pb-2">
                        <label class="flex items-center gap-2 cursor-pointer select-none">
                            <input type="checkbox" name="is_contacted" id="activityContacted" value="1"
                                   class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="text-xs font-medium text-gray-600 dark:text-gray-400">Berhasil dihubungi</span>
                        </label>
                    </div>
                </div>

                {{-- Catatan --}}
                <div>
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Catatan</label>
                    <textarea name="notes" id="activityNotes" rows="3"
                              placeholder="Hasil diskusi, poin penting, dsb…"
                              class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none"></textarea>
                </div>

                {{-- Follow-up (hanya saat tambah baru) --}}
                <div id="followUpRow">
                    <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                        Jadwal Follow-up Berikutnya
                    </label>
                    <input type="datetime-local" name="next_follow_up_at" id="activityFollowUp"
                           class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                </div>

            </div>

            <div class="flex items-center justify-end gap-2 px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                <button type="button" onclick="closeActivityModal()"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">
                    Simpan Aktivitas
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ================================================================== --}}
{{-- MODAL: Won                                                           --}}
{{-- ================================================================== --}}
<div id="modalWon"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md shadow-xl p-6">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900/40 flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Tandai sebagai Won</h3>
        </div>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-5">
            Konfirmasi bahwa lead <strong class="text-gray-800 dark:text-white">{{ $lead->name }}</strong> berhasil ditutup sebagai <strong class="text-green-600">Won</strong>.
        </p>
        <div class="flex justify-end gap-2">
            <button onclick="document.getElementById('modalWon').classList.add('hidden')"
                    class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                Batal
            </button>
            <form method="POST" action="{{ route('crm.leads.markWon', $lead) }}">
                @csrf @method('PATCH')
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors">
                    Ya, Won
                </button>
            </form>
        </div>
    </div>
</div>

{{-- ================================================================== --}}
{{-- MODAL: Lost                                                          --}}
{{-- ================================================================== --}}
<div id="modalLost"
     class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
     onclick="if(event.target===this) this.classList.add('hidden')">
    <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-md shadow-xl p-6">
        <div class="flex items-center gap-2 mb-2">
            <div class="w-8 h-8 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center">
                <svg class="w-4 h-4 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">Tandai sebagai Lost</h3>
        </div>
        <form method="POST" action="{{ route('crm.leads.markLost', $lead) }}">
            @csrf @method('PATCH')
            <div class="mb-4">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Alasan Lost</label>
                <select name="lost_reason_id"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-red-500 transition">
                    <option value="">— Pilih alasan (opsional) —</option>
                    @foreach($lostReasons as $reason)
                        <option value="{{ $reason->id }}">{{ $reason->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-5">
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Catatan</label>
                <textarea name="lost_note" rows="3" placeholder="Apa yang terjadi?"
                          class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-red-500 transition resize-none"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalLost').classList.add('hidden')"
                        class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-lg transition-colors">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors">
                    Ya, Lost
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ============================================================
// Data aktivitas untuk edit — di-encode dari PHP ke JS
// ============================================================
const activities = @json($lead->activities->keyBy('id'));
const activityTypeResultsUrl = '{{ url('/crm/activity-types') }}';
const storeUrl   = '{{ route('crm.leads.activities.store', $lead) }}';
const updateUrl  = '{{ url('/crm/activities') }}'; // /{id} ditambah di JS

// ============================================================
// Cascade dropdown: type → result
// ============================================================
document.getElementById('activityType').addEventListener('change', function () {
    loadResults(this.value, null);
});

async function loadResults(typeId, selectedResultId) {
    const el = document.getElementById('activityResult');
    el.innerHTML = '<option value="">Memuat…</option>';

    if (!typeId) {
        el.innerHTML = '<option value="">— Pilih hasil —</option>';
        return;
    }

    try {
        const res  = await fetch(`${activityTypeResultsUrl}/${typeId}/results`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();

        el.innerHTML = '<option value="">— Pilih hasil —</option>';
        data.forEach(item => {
            const opt      = document.createElement('option');
            opt.value      = item.id;
            opt.textContent = item.name;
            if (selectedResultId && item.id == selectedResultId) opt.selected = true;
            el.appendChild(opt);
        });
    } catch {
        el.innerHTML = '<option value="">— Gagal memuat —</option>';
    }
}

// ============================================================
// Modal: buka untuk tambah baru
// ============================================================
function closeActivityModal() {
    document.getElementById('modalActivity').classList.add('hidden');
    document.getElementById('activityForm').reset();
    document.getElementById('activityResult').innerHTML = '<option value="">— Pilih hasil —</option>';
    document.getElementById('activityMethodField').innerHTML = '';
    document.getElementById('activityForm').action = storeUrl;
    document.getElementById('modalActivityTitle').textContent = 'Tambah Aktivitas';
    document.getElementById('followUpRow').classList.remove('hidden');

    // Reset datetime ke sekarang
    const now = new Date();
    now.setSeconds(0, 0);
    document.getElementById('activityAt').value = now.toISOString().slice(0, 16);
}

// Set default datetime saat halaman load
(function () {
    const now = new Date();
    now.setSeconds(0, 0);
    document.getElementById('activityAt').value = now.toISOString().slice(0, 16);
})();

// ============================================================
// Modal: buka untuk edit
// ============================================================
async function openEditActivity(id) {
    const act = activities[id];
    if (!act) return;

    document.getElementById('modalActivityTitle').textContent = 'Edit Aktivitas';
    document.getElementById('activityForm').action = `${updateUrl}/${id}`;
    document.getElementById('activityMethodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
    document.getElementById('followUpRow').classList.add('hidden');

    // Isi field
    document.getElementById('activityTitle').value    = act.title ?? '';
    document.getElementById('activityNotes').value    = act.notes ?? '';
    document.getElementById('activityLocation').value = act.location ?? '';
    document.getElementById('activityContacted').checked = act.is_contacted ?? false;

    // Format datetime-local
    if (act.activity_at) {
        const d = new Date(act.activity_at);
        d.setSeconds(0, 0);
        document.getElementById('activityAt').value = d.toISOString().slice(0, 16);
    }

    // Load type lalu result
    document.getElementById('activityType').value = act.activity_type_id ?? '';
    await loadResults(act.activity_type_id, act.activity_result_id);

    document.getElementById('modalActivity').classList.remove('hidden');
}
</script>
@endpush
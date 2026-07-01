{{-- resources/views/pages/crm/dashboard/manager.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard CRM — Manager')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Dashboard CRM</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">{{ now()->format('l, d F Y') }}</p>
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
                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['leads_open'] }}</p>
        <a href="{{ route('crm.leads.index', ['status' => 'open']) }}"
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
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
            {{ $stats['leads_won_today'] }} won hari ini
        </p>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-green-600 dark:text-green-400">Won Bulan Ini</p>
            <div class="w-8 h-8 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $stats['leads_won_month'] }}</p>
        @if($stats['won_vs_last_month'] !== null)
            <p class="text-xs mt-1 {{ $stats['won_vs_last_month'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                {{ $stats['won_vs_last_month'] >= 0 ? '+' : '' }}{{ $stats['won_vs_last_month'] }}% vs bulan lalu
            </p>
        @endif
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Konversi Bulan Ini</p>
            <div class="w-8 h-8 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </div>
        </div>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['conversion_rate'] }}%</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Dari {{ $stats['leads_new_month'] }} lead masuk</p>
    </div>

</div>

{{-- Lead Hari Ini --}}
@if($todayLeads->isNotEmpty())
<div class="bg-white dark:bg-gray-800 border border-purple-200 dark:border-purple-800 rounded-xl mb-5">
    <div class="px-5 py-4 border-b border-purple-100 dark:border-purple-800 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <div class="w-2 h-2 rounded-full bg-purple-500 animate-pulse"></div>
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
                Lead Masuk Hari Ini
                <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-purple-100 dark:bg-purple-900/40 text-purple-700 dark:text-purple-400 rounded-md">{{ $stats['leads_today'] }}</span>
            </h2>
        </div>
        <a href="{{ route('crm.leads.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lihat semua</a>
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
                    @if($lead->source)
                        <span class="text-gray-300 dark:text-gray-600">·</span>
                        <span class="text-xs text-gray-400 dark:text-gray-500">{{ $lead->source->name }}</span>
                    @endif
                </div>
            </div>
            <div class="text-right flex-shrink-0">
                @if($lead->assignedUser)
                    <div class="flex items-center gap-1.5 justify-end">
                        <div class="w-5 h-5 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center">
                            {{ strtoupper(substr($lead->assignedUser->name, 0, 1)) }}
                        </div>
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->assignedUser->name }}</span>
                    </div>
                @endif
                <span class="text-xs text-gray-400 dark:text-gray-500">{{ $lead->created_at->format('H:i') }}</span>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endif

{{-- ------------------------------------------------------------------ --}}
{{-- Pipeline Summary                                                     --}}
{{-- ------------------------------------------------------------------ --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl mb-5">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Pipeline Summary</h2>
    </div>
    <div class="p-5 grid grid-cols-1 md:grid-cols-{{ count($pipelines) }} gap-5">
        @foreach($pipelines as $pipeline)
        <div>
            <div class="flex items-center justify-between mb-3">
                <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $pipeline['pipeline'] }}</p>
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $pipeline['total_open'] }} open</span>
            </div>
            <div class="space-y-2">
                @foreach($pipeline['stages'] as $stage)
                <div class="flex items-center gap-2">
                    <div class="flex-1 h-2 bg-gray-100 dark:bg-gray-700 rounded-full overflow-hidden">
                        @php
                            $max = collect($pipeline['stages'])->max('count');
                            $width = $max > 0 ? round(($stage['count'] / $max) * 100) : 0;
                        @endphp
                        <div class="h-full bg-blue-500 rounded-full transition-all" style="width:{{ $width }}%"></div>
                    </div>
                    <span class="text-xs text-gray-500 dark:text-gray-400 w-4 text-right">{{ $stage['count'] }}</span>
                    <span class="text-xs text-gray-600 dark:text-gray-300 min-w-[80px]">{{ $stage['name'] }}</span>
                </div>
                @endforeach
            </div>
            <p class="text-xs text-gray-400 dark:text-gray-500 mt-3">
                Nilai: Rp {{ number_format($pipeline['total_value'], 0, ',', '.') }}
            </p>
        </div>
        @endforeach
    </div>
</div>

{{-- ------------------------------------------------------------------ --}}
{{-- 2 KOLOM: Performa Sales + Lead Terbaru                               --}}
{{-- ------------------------------------------------------------------ --}}
<div class="grid grid-cols-1 xl:grid-cols-2 gap-5 mb-5">

    {{-- Performa Sales --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Performa Sales — Bulan Ini</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Sales</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Lead</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600 dark:text-green-400">Won</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Conv.</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($performance as $i => $perf)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                        <td class="px-4 py-2.5">
                            <div class="flex items-center gap-2">
                                @if($i === 0)
                                    <span class="text-yellow-500">🥇</span>
                                @elseif($i === 1)
                                    <span>🥈</span>
                                @elseif($i === 2)
                                    <span>🥉</span>
                                @else
                                    <span class="w-5 h-5 flex items-center justify-center text-xs text-gray-400">{{ $i + 1 }}</span>
                                @endif
                                <span class="text-sm text-gray-800 dark:text-gray-200">{{ $perf['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $perf['total_leads'] }}</td>
                        <td class="px-4 py-2.5 text-center font-semibold text-green-600 dark:text-green-400">{{ $perf['won'] }}</td>
                        <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $perf['conv_rate'] }}%</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-sm text-gray-400 dark:text-gray-500">
                            Belum ada data bulan ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Lead terbaru --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Lead Terbaru</h2>
            <a href="{{ route('crm.leads.index') }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Lihat semua</a>
        </div>
        <div class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($recentLeads as $lead)
            <div class="flex items-start gap-3 px-5 py-3">
                <div class="w-7 h-7 rounded-full bg-blue-600 text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">
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
                        @if($lead->assignedUser)
                            <span class="text-gray-300 dark:text-gray-600">·</span>
                            <span class="text-xs text-gray-400 dark:text-gray-500">{{ $lead->assignedUser->name }}</span>
                        @endif
                    </div>
                </div>
                <span class="text-xs text-gray-400 dark:text-gray-500 flex-shrink-0">{{ $lead->created_at->diffForHumans() }}</span>
            </div>
            @empty
            <div class="px-5 py-8 text-center text-sm text-gray-400 dark:text-gray-500">Belum ada lead.</div>
            @endforelse
        </div>
    </div>

</div>

{{-- ------------------------------------------------------------------ --}}
{{-- Trend 6 Bulan                                                        --}}
{{-- ------------------------------------------------------------------ --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Trend Lead 6 Bulan Terakhir</h2>
    </div>
    <div class="p-5">
        <div class="flex items-end gap-3 h-32">
            @php $maxTotal = collect($trend)->max('total') ?: 1; @endphp
            @foreach($trend as $month)
            <div class="flex-1 flex flex-col items-center gap-1">
                <div class="w-full flex items-end gap-0.5" style="height: 96px;">
                    <div class="flex-1 bg-blue-200 dark:bg-blue-900/50 rounded-t"
                         style="height: {{ round(($month['total'] / $maxTotal) * 96) }}px"
                         title="Total: {{ $month['total'] }}"></div>
                    <div class="flex-1 bg-green-400 dark:bg-green-600 rounded-t"
                         style="height: {{ round(($month['won'] / $maxTotal) * 96) }}px"
                         title="Won: {{ $month['won'] }}"></div>
                </div>
                <span class="text-xs text-gray-400 dark:text-gray-500 text-center leading-tight">{{ $month['month'] }}</span>
            </div>
            @endforeach
        </div>
        <div class="flex items-center gap-4 mt-3 justify-end">
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-sm bg-blue-200 dark:bg-blue-900/50"></div>
                <span class="text-xs text-gray-500 dark:text-gray-400">Total Lead</span>
            </div>
            <div class="flex items-center gap-1.5">
                <div class="w-3 h-3 rounded-sm bg-green-400 dark:bg-green-600"></div>
                <span class="text-xs text-gray-500 dark:text-gray-400">Won</span>
            </div>
        </div>
    </div>
</div>

@endsection
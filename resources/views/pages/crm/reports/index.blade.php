{{-- resources/views/pages/crm/reports/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan CRM')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Laporan CRM</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Analisis data lead, sales, dan aktivitas</p>
    </div>
</div>

{{-- ------------------------------------------------------------------ --}}
{{-- TAB BAR                                                              --}}
{{-- ------------------------------------------------------------------ --}}
<div class="flex gap-1 p-1 bg-gray-100 dark:bg-gray-800 rounded-xl mb-5 w-fit">
    @foreach([
        ['key' => 'leads',      'label' => 'Laporan Lead'],
        ['key' => 'sales',      'label' => 'Performa Sales'],
        ['key' => 'activities', 'label' => 'Laporan Aktivitas'],
    ] as $t)
    <a href="{{ route('crm.reports.index', array_merge(request()->query(), ['tab' => $t['key']])) }}"
       class="px-4 py-2 text-sm font-medium rounded-lg transition-colors
              {{ $tab === $t['key']
                  ? 'bg-white dark:bg-gray-700 text-gray-900 dark:text-white shadow-sm'
                  : 'text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200' }}">
        {{ $t['label'] }}
    </a>
    @endforeach
</div>

{{-- ------------------------------------------------------------------ --}}
{{-- FILTER BAR                                                           --}}
{{-- ------------------------------------------------------------------ --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-5">
    <form method="GET" action="{{ route('crm.reports.index') }}" class="flex flex-wrap gap-3 items-end">

        {{-- Pertahankan tab aktif --}}
        <input type="hidden" name="tab" value="{{ $tab }}">

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Dari Tanggal <span class="text-red-500">*</span>
            </label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] }}"
                   class="px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Sampai Tanggal <span class="text-red-500">*</span>
            </label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] }}"
                   class="px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Pipeline — tampil di tab leads & sales --}}
        @if(in_array($tab, ['leads', 'sales']))
        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Pipeline</label>
            <select name="pipeline_id"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua pipeline</option>
                @foreach($pipelines as $p)
                    <option value="{{ $p->id }}" @selected(($filters['pipeline_id'] ?? '') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Source — tab leads saja --}}
        @if($tab === 'leads')
        <div class="min-w-[140px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Sumber</label>
            <select name="source_id"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua sumber</option>
                @foreach($sources as $s)
                    <option value="{{ $s->id }}" @selected(($filters['source_id'] ?? '') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[130px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Status</label>
            <select name="status"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua status</option>
                <option value="open"  @selected(($filters['status'] ?? '') === 'open')>Open</option>
                <option value="won"   @selected(($filters['status'] ?? '') === 'won')>Won</option>
                <option value="lost"  @selected(($filters['status'] ?? '') === 'lost')>Lost</option>
            </select>
        </div>
        @endif

        {{-- Sales — semua tab --}}
        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Sales</label>
            <select name="assigned_to"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua sales</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(($filters['assigned_to'] ?? '') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Tampilkan
            </button>
            <a href="{{ route('crm.reports.index', ['tab' => $tab]) }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                Reset
            </a>
            @if($data)
            <a href="{{ route('crm.reports.export', array_merge(request()->query(), ['tab' => $tab])) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Excel
            </a>
            @endif
        </div>

    </form>
</div>

{{-- ------------------------------------------------------------------ --}}
{{-- KONTEN TAB                                                           --}}
{{-- ------------------------------------------------------------------ --}}
@if($data)

    {{-- ============================================================== --}}
    {{-- TAB: LEADS                                                       --}}
    {{-- ============================================================== --}}
    @if($tab === 'leads')

    {{-- Summary --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Lead</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $data['summary']['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 rounded-xl p-4">
            <p class="text-xs text-green-600 dark:text-green-400 mb-1">Won</p>
            <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $data['summary']['won'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Konversi {{ $data['summary']['conversion'] }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-red-200 dark:border-red-800 rounded-xl p-4">
            <p class="text-xs text-red-500 mb-1">Lost</p>
            <p class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $data['summary']['lost'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nilai Won</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($data['summary']['won_value'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Breakdown Pipeline & Source --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Per Pipeline</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Pipeline</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600">Won</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-red-500">Lost</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-blue-600">Open</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($data['byPipeline'] as $row)
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $row['total'] }}</td>
                            <td class="px-4 py-2.5 text-center text-green-600 font-medium">{{ $row['won'] }}</td>
                            <td class="px-4 py-2.5 text-center text-red-500">{{ $row['lost'] }}</td>
                            <td class="px-4 py-2.5 text-center text-blue-600">{{ $row['open'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Per Sumber</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Sumber</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600">Won</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($data['bySource'] as $row)
                        <tr>
                            <td class="px-4 py-2.5 text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $row['total'] }}</td>
                            <td class="px-4 py-2.5 text-center text-green-600 font-medium">{{ $row['won'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Detail Lead --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
                Detail Lead
                <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-md">{{ $data['leads']->count() }}</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lead</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pipeline / Stage</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sumber</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nilai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($data['leads'] as $lead)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3">
                            <a href="{{ route('crm.leads.show', $lead) }}" class="font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ $lead->name }}</a>
                            <div class="text-xs text-gray-400">{{ $lead->phone }}</div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">
                            {{ $lead->pipeline->name ?? '—' }}<br>
                            <span class="text-gray-400">{{ $lead->stage->name ?? '—' }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $lead->source->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $lead->assignedUser->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @php $badge = match($lead->status) { 'won' => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400', 'lost' => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400', default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400' }; @endphp
                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badge }}">{{ $lead->statusLabel() }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">Rp {{ number_format($lead->estimated_value, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $lead->created_at->format('d M Y') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif

    {{-- ============================================================== --}}
    {{-- TAB: SALES PERFORMANCE                                           --}}
    {{-- ============================================================== --}}
    @if($tab === 'sales')

    <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Lead</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $data['summary']['total_leads'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 rounded-xl p-4">
            <p class="text-xs text-green-600 dark:text-green-400 mb-1">Total Won</p>
            <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $data['summary']['total_won'] }}</p>
            <p class="text-xs text-gray-400 mt-1">Rata-rata konversi {{ number_format($data['summary']['avg_conv'], 1) }}%</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Nilai Won</p>
            <p class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($data['summary']['total_value'], 0, ',', '.') }}</p>
            <p class="text-xs text-blue-600 dark:text-blue-400 mt-1">Top: {{ $data['summary']['top_sales'] }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Ranking Sales</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Open</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">Won</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-red-500 uppercase tracking-wider">Lost</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Konversi</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nilai Won</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktivitas</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($data['bySales'] as $i => $row)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($i === 0) <span>🥇</span>
                                @elseif($i === 1) <span>🥈</span>
                                @elseif($i === 2) <span>🥉</span>
                                @else <span class="text-xs text-gray-400 w-5 text-center">{{ $i + 1 }}</span>
                                @endif
                                <span class="font-medium text-gray-800 dark:text-gray-200">{{ $row['name'] }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-center text-gray-700 dark:text-gray-300">{{ $row['total'] }}</td>
                        <td class="px-4 py-3 text-center text-blue-600 dark:text-blue-400">{{ $row['open'] }}</td>
                        <td class="px-4 py-3 text-center font-semibold text-green-600 dark:text-green-400">{{ $row['won'] }}</td>
                        <td class="px-4 py-3 text-center text-red-500">{{ $row['lost'] }}</td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                <div class="w-16 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-green-500 rounded-full" style="width:{{ $row['conv_rate'] }}%"></div>
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-300">{{ $row['conv_rate'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">Rp {{ number_format($row['won_value'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['activities'] }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="8" class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">Tidak ada data.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif

    {{-- ============================================================== --}}
    {{-- TAB: AKTIVITAS                                                   --}}
    {{-- ============================================================== --}}
    @if($tab === 'activities')

    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Aktivitas</p>
            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $data['summary']['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 rounded-xl p-4">
            <p class="text-xs text-green-600 dark:text-green-400 mb-1">Berhasil</p>
            <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $data['summary']['total_success'] }}</p>
            <p class="text-xs text-gray-400 mt-1">{{ $data['summary']['success_rate'] }}% success rate</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-blue-200 dark:border-blue-800 rounded-xl p-4">
            <p class="text-xs text-blue-600 dark:text-blue-400 mb-1">Terhubung</p>
            <p class="text-2xl font-bold text-blue-700 dark:text-blue-400">{{ $data['summary']['total_contacted'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tidak Terhubung</p>
            <p class="text-2xl font-bold text-gray-700 dark:text-gray-300">{{ $data['summary']['total'] - $data['summary']['total_contacted'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700"><h2 class="text-sm font-semibold text-gray-800 dark:text-white">Per Jenis Aktivitas</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Jenis</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600">Berhasil</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Rate</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($data['byType'] as $row)
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $row['total'] }}</td>
                            <td class="px-4 py-2.5 text-center text-green-600 font-medium">{{ $row['success'] }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-500 dark:text-gray-400">{{ $row['success_rate'] }}%</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
            <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700"><h2 class="text-sm font-semibold text-gray-800 dark:text-white">Per Sales</h2></div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead><tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Sales</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600">Berhasil</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-blue-600">Terhubung</th>
                    </tr></thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach($data['bySales'] as $row)
                        <tr>
                            <td class="px-4 py-2.5 font-medium text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                            <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $row['total'] }}</td>
                            <td class="px-4 py-2.5 text-center text-green-600 font-medium">{{ $row['success'] }}</td>
                            <td class="px-4 py-2.5 text-center text-blue-600">{{ $row['contacted'] }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
                Detail Aktivitas
                <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-md">{{ $data['activities']->count() }}</span>
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead><tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lead</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hasil</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terhubung</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                </tr></thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @forelse($data['activities'] as $activity)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                        <td class="px-4 py-3 text-xs text-gray-700 dark:text-gray-300">{{ $activity->user->name ?? '—' }}</td>
                        <td class="px-4 py-3">
                            @if($activity->lead)
                                <a href="{{ route('crm.leads.show', $activity->lead) }}" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">{{ $activity->lead->name }}</a>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $activity->type->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-800 dark:text-gray-200 max-w-[180px] truncate">{{ $activity->title }}</td>
                        <td class="px-4 py-3">
                            @if($activity->result)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs {{ $activity->result->is_success ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
                                    {{ $activity->result->name }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if($activity->is_contacted)
                                <svg class="w-4 h-4 text-green-500 mx-auto" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6 9 17l-5-5"/></svg>
                            @else
                                <span class="text-gray-300 dark:text-gray-600">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">{{ $activity->activity_at->format('d M Y, H:i') }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">Tidak ada aktivitas.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @endif

@else
{{-- Placeholder sebelum filter dijalankan --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-5 py-16 text-center">
    <svg class="w-10 h-10 mx-auto mb-3 opacity-30 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
    <p class="text-sm text-gray-500 dark:text-gray-400">Pilih periode dan klik <strong>Tampilkan</strong> untuk melihat laporan.</p>
</div>
@endif

@endsection
{{-- resources/views/pages/crm/reports/leads.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Lead')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Laporan Lead</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Data lead berdasarkan periode</p>
    </div>
</div>

@include('pages.crm.reports._filter', [
    'action'     => route('crm.reports.leads'),
    'exportUrl'  => route('crm.reports.leads.export'),
    'withStatus' => true,
])

@if($data)

{{-- Summary Cards --}}
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-5">
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
        <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Total Lead</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $data['summary']['total'] }}</p>
    </div>
    <div class="bg-white dark:bg-gray-800 border border-green-200 dark:border-green-800 rounded-xl p-4">
        <p class="text-xs text-green-600 dark:text-green-400 mb-1">Won</p>
        <p class="text-2xl font-bold text-green-700 dark:text-green-400">{{ $data['summary']['won'] }}</p>
        <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Konversi {{ $data['summary']['conversion'] }}%</p>
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

{{-- Breakdown --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

    {{-- Per Pipeline --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Per Pipeline</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Pipeline</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600">Won</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-red-500">Lost</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-blue-600">Open</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($data['byPipeline'] as $row)
                    <tr>
                        <td class="px-4 py-2.5 font-medium text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                        <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $row['total'] }}</td>
                        <td class="px-4 py-2.5 text-center text-green-600 dark:text-green-400 font-medium">{{ $row['won'] }}</td>
                        <td class="px-4 py-2.5 text-center text-red-500">{{ $row['lost'] }}</td>
                        <td class="px-4 py-2.5 text-center text-blue-600 dark:text-blue-400">{{ $row['open'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Per Sumber --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Per Sumber Lead</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Sumber</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600">Won</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($data['bySource'] as $row)
                    <tr>
                        <td class="px-4 py-2.5 text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                        <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $row['total'] }}</td>
                        <td class="px-4 py-2.5 text-center text-green-600 dark:text-green-400 font-medium">{{ $row['won'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Detail Lead --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
            Detail Lead
            <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-md">{{ $data['leads']->count() }}</span>
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lead</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pipeline</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Stage</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sumber</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nilai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($data['leads'] as $lead)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3">
                        <a href="{{ route('crm.leads.show', $lead) }}"
                           class="font-medium text-blue-600 dark:text-blue-400 hover:underline">{{ $lead->name }}</a>
                        <div class="text-xs text-gray-400">{{ $lead->phone }}</div>
                    </td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">{{ $lead->pipeline->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">{{ $lead->stage->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">{{ $lead->source->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-gray-700 dark:text-gray-300 text-xs">{{ $lead->assignedUser->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @php
                            $badgeClass = match($lead->status) {
                                'won'   => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                                'lost'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                                default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                            };
                        @endphp
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                            {{ $lead->statusLabel() }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        Rp {{ number_format($lead->estimated_value, 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $lead->created_at->format('d M Y') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                        Tidak ada data pada periode ini.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@else
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl px-5 py-16 text-center">
    <svg class="w-10 h-10 mx-auto mb-3 opacity-30 text-gray-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5.586a1 1 0 0 1 .707.293l5.414 5.414a1 1 0 0 1 .293.707V19a2 2 0 0 1-2 2z"/></svg>
    <p class="text-sm text-gray-500 dark:text-gray-400">Pilih periode dan klik <strong>Tampilkan</strong> untuk melihat laporan.</p>
</div>
@endif

@endsection
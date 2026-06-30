{{-- resources/views/pages/crm/reports/sales-performance.blade.php --}}
@extends('layouts.app')

@section('title', 'Performa Sales')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Performa Sales</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Rekap won/lost per sales berdasarkan periode</p>
    </div>
</div>

@include('pages.crm.reports._filter', [
    'action'    => route('crm.reports.sales-performance'),
    'exportUrl' => route('crm.reports.sales-performance.export'),
])

@if($data)

{{-- Summary --}}
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

{{-- Tabel performa --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Ranking Sales</h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Lead</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-blue-600 uppercase tracking-wider">Open</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-green-600 uppercase tracking-wider">Won</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-red-500 uppercase tracking-wider">Lost</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Konversi</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nilai Won</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aktivitas</th>
                </tr>
            </thead>
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
                    <td class="px-4 py-3 text-right text-xs text-gray-700 dark:text-gray-300 whitespace-nowrap">
                        Rp {{ number_format($row['won_value'], 0, ',', '.') }}
                    </td>
                    <td class="px-4 py-3 text-center text-gray-600 dark:text-gray-300">{{ $row['activities'] }}</td>
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
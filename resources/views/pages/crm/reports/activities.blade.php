{{-- resources/views/pages/crm/reports/activities.blade.php --}}
@extends('layouts.app')

@section('title', 'Laporan Aktivitas')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Laporan Aktivitas</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Rekap aktivitas sales berdasarkan periode</p>
    </div>
</div>

@include('pages.crm.reports._filter', [
    'action'    => route('crm.reports.activities'),
    'exportUrl' => route('crm.reports.activities.export'),
])

@if($data)

{{-- Summary --}}
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

{{-- Breakdown --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">

    {{-- Per Jenis --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Per Jenis Aktivitas</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Jenis</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600">Berhasil</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($data['byType'] as $row)
                    <tr>
                        <td class="px-4 py-2.5 font-medium text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                        <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $row['total'] }}</td>
                        <td class="px-4 py-2.5 text-center text-green-600 dark:text-green-400 font-medium">{{ $row['success'] }}</td>
                        <td class="px-4 py-2.5 text-center text-gray-500 dark:text-gray-400">{{ $row['success_rate'] }}%</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- Per Sales --}}
    <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl">
        <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Per Sales</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-gray-100 dark:border-gray-700">
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 dark:text-gray-400">Sales</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Total</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-green-600">Berhasil</th>
                        <th class="px-4 py-2.5 text-center text-xs font-semibold text-blue-600">Terhubung</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($data['bySales'] as $row)
                    <tr>
                        <td class="px-4 py-2.5 font-medium text-gray-800 dark:text-gray-200">{{ $row['name'] }}</td>
                        <td class="px-4 py-2.5 text-center text-gray-600 dark:text-gray-300">{{ $row['total'] }}</td>
                        <td class="px-4 py-2.5 text-center text-green-600 dark:text-green-400 font-medium">{{ $row['success'] }}</td>
                        <td class="px-4 py-2.5 text-center text-blue-600 dark:text-blue-400">{{ $row['contacted'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- Detail Aktivitas --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
            Detail Aktivitas
            <span class="ml-1.5 px-1.5 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-500 rounded-md">{{ $data['activities']->count() }}</span>
        </h2>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sales</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lead</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jenis</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Judul</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Hasil</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Terhubung</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Waktu</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($data['activities'] as $activity)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                    <td class="px-4 py-3 text-xs text-gray-700 dark:text-gray-300">{{ $activity->user->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($activity->lead)
                            <a href="{{ route('crm.leads.show', $activity->lead) }}"
                               class="text-xs text-blue-600 dark:text-blue-400 hover:underline">{{ $activity->lead->name }}</a>
                        @else
                            <span class="text-xs text-gray-400">—</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $activity->type->name ?? '—' }}</td>
                    <td class="px-4 py-3 text-xs text-gray-800 dark:text-gray-200 max-w-[180px] truncate">{{ $activity->title }}</td>
                    <td class="px-4 py-3">
                        @if($activity->result)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs
                                {{ $activity->result->is_success ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' }}">
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
                    <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap">
                        {{ $activity->activity_at->format('d M Y, H:i') }}
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-4 py-10 text-center text-sm text-gray-400 dark:text-gray-500">
                        Tidak ada aktivitas pada periode ini.
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
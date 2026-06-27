@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-(--breakpoint-2xl) p-4 pb-20 md:p-6 md:pb-6">

    {{-- HEADER --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Laporan Prospek</h2>
            <p class="mt-0.5 text-sm text-gray-500">Rekap performa sales berdasarkan periode</p>
        </div>
    </div>

    {{-- FILTER --}}
    <div class="mb-6 rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
        <form method="GET" action="{{ route('crm.reports.leads') }}">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Dari Tanggal</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sampai Tanggal</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                </div>

                <div>
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Sales (Opsional)</label>
                    <div class="relative">
                        <select name="user_id"
                            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                            <option value="">Semua Sales</option>
                            @foreach ($users as $id => $name)
                                <option value="{{ $id }}" {{ $userId == $id ? 'selected' : '' }}>{{ $name }}</option>
                            @endforeach
                        </select>
                        <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500">
                            <svg class="stroke-current" width="16" height="16" viewBox="0 0 20 20" fill="none">
                                <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                    </div>
                </div>

                <div class="flex items-end gap-2">
                    <button type="submit"
                        class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex h-11 flex-1 items-center justify-center gap-2 rounded-lg px-4 text-sm font-medium text-white transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        Tampilkan
                    </button>
                    <a href="{{ route('crm.reports.leads.export', request()->all()) }}"
                        class="shadow-theme-xs inline-flex h-11 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Excel
                    </a>
                </div>

            </div>
        </form>
    </div>

    {{-- SUMMARY CARDS --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-3">

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500">Prospek Periode Ini</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $report['total_period'] }}</p>
            <p class="mt-0.5 text-xs text-gray-400">{{ $dateFrom }} s/d {{ $dateTo }}</p>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-sm text-gray-500">Total Semua Prospek</p>
            <p class="mt-1 text-3xl font-bold text-gray-900 dark:text-white">{{ $report['total_all'] }}</p>
            <p class="mt-0.5 text-xs text-gray-400">Sepanjang waktu</p>
        </div>

        <div class="rounded-2xl border border-success-200 bg-success-50 p-5 dark:border-success-900/30 dark:bg-success-500/5">
            <p class="text-sm text-success-600 dark:text-success-400">Total Close Deal</p>
            <p class="mt-1 text-3xl font-bold text-success-700 dark:text-success-300">{{ $report['total_closed'] }}</p>
            <p class="mt-0.5 text-xs text-success-500">
                @if ($report['total_all'] > 0)
                    Close rate {{ round(($report['total_closed'] / $report['total_all']) * 100, 1) }}%
                @endif
            </p>
        </div>

    </div>

    {{-- TABLE --}}
    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">Detail per Sales</h3>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">Nama Sales</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Prospek Hari Ini</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Prospek Periode</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Total Prospek</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Close Deal</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Close Periode</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400">Close Rate</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($report['rows'] as $row)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white">
                                        {{ strtoupper(substr($row['name'], 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-medium text-gray-800 dark:text-white">{{ $row['name'] }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-semibold {{ $row['today_leads'] > 0 ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400' }}">
                                    {{ $row['today_leads'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ $row['period_leads'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $row['total_leads'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ $row['closed_deals'] > 0 ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400' : 'text-gray-400' }}">
                                    {{ $row['closed_deals'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm text-gray-600 dark:text-gray-400">{{ $row['closed_period'] }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="h-1.5 w-16 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                        <div class="h-full rounded-full bg-success-500" style="width: {{ min($row['close_rate'], 100) }}%"></div>
                                    </div>
                                    <span class="text-xs font-medium text-gray-600 dark:text-gray-400">{{ $row['close_rate'] }}%</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500">
                                Tidak ada data.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

                {{-- FOOTER TOTAL --}}
                @if ($report['rows']->count() > 1)
                    <tfoot class="border-t-2 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/50">
                        <tr>
                            <td class="px-6 py-3 text-sm font-bold text-gray-800 dark:text-white">Total</td>
                            <td class="px-6 py-3 text-center text-sm font-bold text-gray-800 dark:text-white">
                                {{ $report['rows']->sum('today_leads') }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm font-bold text-gray-800 dark:text-white">
                                {{ $report['total_period'] }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm font-bold text-gray-800 dark:text-white">
                                {{ $report['total_all'] }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm font-bold text-success-600 dark:text-success-400">
                                {{ $report['total_closed'] }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm font-bold text-gray-800 dark:text-white">
                                {{ $report['rows']->sum('closed_period') }}
                            </td>
                            <td class="px-6 py-3 text-center text-sm font-bold text-gray-800 dark:text-white">
                                @if ($report['total_all'] > 0)
                                    {{ round(($report['total_closed'] / $report['total_all']) * 100, 1) }}%
                                @else
                                    0%
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                @endif

            </table>
        </div>
    </div>

</div>

@endsection
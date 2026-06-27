@extends('layouts.app')

@section('content')

@php
use Illuminate\Support\Str;

$name     = $sales['sales_name'];
$nameFmt  = collect(explode(' ', $name))->map(fn($w) => ucfirst(strtolower($w)))->implode(' ');
$initials = collect(explode(' ', $name))->take(2)->map(fn($w) => $w[0])->implode('');
$pct      = (float) $sales['achievement_percent'];
$barColor = $pct >= 80 ? 'bg-success-500' : ($pct >= 50 ? 'bg-yellow-400' : 'bg-red-500');
$txtColor = $pct >= 80 ? 'text-success-600 dark:text-success-400' : ($pct >= 50 ? 'text-yellow-600 dark:text-yellow-400' : 'text-red-600 dark:text-red-400');

// Chart data — ambil hanya tanggal (hari) dan nilai
$dailyLabels  = $daily->pluck('date')->map(fn($d) => (int) \Carbon\Carbon::parse($d)->format('j'))->toJson();
$dailyUnits   = $daily->pluck('unit')->toJson();
$dailyAmounts = $daily->pluck('amount')->toJson();
@endphp

<div class="mx-auto max-w-(--breakpoint-2xl) p-4 pb-20 md:p-6 md:pb-6">

    {{-- BREADCRUMB --}}
    <div class="mb-5 flex items-center gap-2 text-sm text-gray-400">
        <a href="{{ route('sales.performance') }}" class="hover:text-gray-600 dark:hover:text-gray-300">Performa Sales</a>
        <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
        </svg>
        <span class="text-gray-700 dark:text-gray-300">{{ $nameFmt }}</span>
    </div>

    {{-- HEADER --}}
    <div class="mb-6 flex flex-wrap items-center gap-4 rounded-2xl border border-gray-200 bg-white px-6 py-5 dark:border-gray-800 dark:bg-gray-900">
        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full bg-brand-100 text-lg font-bold text-brand-600 dark:bg-brand-500/20 dark:text-brand-400">
            {{ $initials }}
        </div>
        <div class="flex-1">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $nameFmt }}</h2>
            <p class="mt-0.5 text-sm text-gray-400">
                {{ now()->translatedFormat('F Y') }} · Update {{ now()->translatedFormat('d M Y, H:i') }}
            </p>
        </div>
        <a href="{{ route('sales.performance') }}"
            class="inline-flex items-center gap-2 rounded-xl border border-gray-200 px-4 py-2 text-sm text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400 dark:hover:bg-gray-800">
            ← Kembali
        </a>
    </div>

    {{-- KPI ROW --}}
    <div class="mb-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Unit Hari Ini</p>
            <p class="mt-1.5 text-2xl font-semibold text-gray-900 dark:text-white">{{ $sales['unit_today'] }}</p>
            <p class="mt-0.5 text-xs text-gray-400">{{ now()->translatedFormat('d M') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Unit Bulan Ini</p>
            <p class="mt-1.5 text-2xl font-semibold text-gray-900 dark:text-white">{{ $sales['unit_current_month'] }}</p>
            <p class="mt-0.5 text-xs {{ $sales['growth_unit_percent'] >= 0 ? 'text-success-500' : 'text-red-500' }}">
                {{ $sales['growth_unit_percent'] >= 0 ? '▲' : '▼' }}
                {{ number_format(abs($sales['growth_unit_percent']), 1) }}% vs bln lalu
            </p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Omzet Bln Ini</p>
            <p class="mt-1.5 text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($sales['amount_current_month'], 0, ',', '.') }}</p>
            <p class="mt-0.5 text-xs {{ $sales['growth_amount_percent'] >= 0 ? 'text-success-500' : 'text-red-500' }}">
                {{ $sales['growth_amount_percent'] >= 0 ? '▲' : '▼' }}
                {{ number_format(abs($sales['growth_amount_percent']), 1) }}% vs bln lalu
            </p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
            <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Insentif</p>
            <p class="mt-1.5 text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($sales['incentive_current_month'], 0, ',', '.') }}</p>
            <p class="mt-0.5 text-xs text-gray-400">Bulan berjalan</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

        {{-- CHART --}}
        <div class="xl:col-span-2">
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Tren Penjualan Harian</h3>
                        <p class="mt-0.5 text-xs text-gray-400">
                            Tgl 1 s/d {{ now()->day }} {{ now()->translatedFormat('F Y') }}
                            @if ($daily->isEmpty())
                                <span class="ml-1 text-yellow-500">(belum ada data)</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 rounded-sm" style="background:#465FFF"></span>Unit
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 rounded-sm" style="background:#9CB9FF"></span>Omzet
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    @if ($daily->isEmpty())
                        <div class="flex items-center justify-center py-16 text-sm text-gray-400">
                            Belum ada data harian. SP GetSalesDailyPerformance perlu dijalankan.
                        </div>
                    @else
                        <div id="daily-chart"></div>
                    @endif
                </div>
            </div>
        </div>

        {{-- SIDEBAR --}}
        <div class="space-y-5">

            {{-- PENCAPAIAN --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">Pencapaian Target</h3>
                @if ($sales['target_unit'] > 0)
                    <div class="mb-3 flex items-end justify-between">
                        <span class="text-4xl font-bold {{ $txtColor }}">{{ number_format($pct, 1) }}%</span>
                        <span class="text-sm text-gray-400">{{ $sales['unit_current_month'] }} / {{ $sales['target_unit'] }} unit</span>
                    </div>
                    <div class="h-2.5 overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-full rounded-full {{ $barColor }}" style="width:{{ min($pct, 100) }}%"></div>
                    </div>
                @else
                    <p class="text-sm text-gray-400">Target belum ditetapkan untuk bulan ini.</p>
                @endif

                <div class="mt-4 space-y-2.5 border-t border-gray-100 pt-4 dark:border-gray-800">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Target unit</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            {{ $sales['target_unit'] > 0 ? $sales['target_unit'].' unit' : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Target omzet</span>
                        <span class="font-medium text-gray-800 dark:text-white">
                            {{ $sales['target_amount'] > 0 ? 'Rp '.number_format($sales['target_amount'], 0, ',', '.') : '-' }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-500">Selisih target</span>
                        <span class="font-semibold {{ $sales['difference_to_target'] >= 0 ? 'text-success-600 dark:text-success-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ $sales['difference_to_target'] >= 0 ? '+' : '' }}{{ number_format($sales['difference_to_target'], 0) }} unit
                        </span>
                    </div>
                </div>
            </div>

            {{-- VS BULAN LALU --}}
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                <h3 class="mb-4 text-sm font-semibold text-gray-800 dark:text-white">vs Bulan Lalu</h3>
                <div class="space-y-4">

                    {{-- UNIT --}}
                    <div>
                        <div class="mb-1 flex justify-between text-xs text-gray-400">
                            <span>Unit</span>
                            <span class="{{ $sales['growth_unit_percent'] >= 0 ? 'text-success-500' : 'text-red-500' }}">
                                {{ $sales['growth_unit_percent'] >= 0 ? '+' : '' }}{{ number_format($sales['growth_unit_percent'], 1) }}%
                            </span>
                        </div>
                        <div class="flex items-end justify-between">
                            <div>
                                <p class="text-xs text-gray-400">Lalu: {{ $sales['unit_last_month'] }} unit</p>
                                <p class="text-sm font-semibold text-gray-800 dark:text-white">Ini: {{ $sales['unit_current_month'] }} unit</p>
                            </div>
                            <span class="text-lg font-bold {{ $sales['growth_unit_diff'] >= 0 ? 'text-success-500' : 'text-red-500' }}">
                                {{ $sales['growth_unit_diff'] >= 0 ? '+' : '' }}{{ number_format($sales['growth_unit_diff'], 0) }}
                            </span>
                        </div>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800"></div>

                    {{-- OMZET --}}
                    <div>
                        <div class="mb-1 flex justify-between text-xs text-gray-400">
                            <span>Omzet</span>
                            <span class="{{ $sales['growth_amount_percent'] >= 0 ? 'text-success-500' : 'text-red-500' }}">
                                {{ $sales['growth_amount_percent'] >= 0 ? '+' : '' }}{{ number_format($sales['growth_amount_percent'], 1) }}%
                            </span>
                        </div>
                        <p class="text-xs text-gray-400">Lalu: Rp {{ number_format($sales['amount_last_month'], 0, ',', '.') }}</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">Ini: Rp {{ number_format($sales['amount_current_month'], 0, ',', '.') }}</p>
                    </div>

                    <div class="border-t border-gray-100 dark:border-gray-800"></div>

                    {{-- INSENTIF --}}
                    <div>
                        <p class="mb-1 text-xs text-gray-400">Insentif</p>
                        <p class="text-xs text-gray-400">Lalu: Rp {{ number_format($sales['incentive_last_month'], 0, ',', '.') }}</p>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">Ini: Rp {{ number_format($sales['incentive_current_month'], 0, ',', '.') }}</p>
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

@if ($daily->isNotEmpty())
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels  = {!! $dailyLabels !!};
    const units   = {!! $dailyUnits !!};
    const amounts = {!! $dailyAmounts !!};
    const fmtRp   = v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v);

    const options = {
        series: [
            { name: 'Unit', data: units },
            { name: 'Omzet', data: amounts },
        ],
        legend: {
            show: false,
            position: 'top',
            horizontalAlign: 'left',
        },
        colors: ['#465FFF', '#9CB9FF'],
        chart: {
            fontFamily: 'Outfit, sans-serif',
            height: 310,
            type: 'area',
            toolbar: { show: false },
            zoom: { enabled: false },
        },
        fill: {
            gradient: {
                enabled: true,
                opacityFrom: 0.55,
                opacityTo: 0,
            },
        },
        stroke: {
            curve: 'smooth',
            width: [2, 2],
        },
        markers: { size: 0 },
        labels: { show: false, position: 'top' },
        grid: {
            xaxis: { lines: { show: false } },
            yaxis: { lines: { show: true } },
        },
        dataLabels: { enabled: false },
        xaxis: {
            type: 'category',
            categories: labels,
            axisBorder: { show: false },
            axisTicks: { show: false },
            tooltip: { enabled: false },
        },
        yaxis: [
            {
                seriesName: 'Unit',
                title: { style: { fontSize: '0px' } },
                min: 0,
                max: Math.max(...units) + 1,
                tickAmount: Math.max(...units) + 1,
                labels: {
                    formatter: v => Number.isInteger(v) ? v : '',
                },
            },
            {
                seriesName: 'Omzet',
                opposite: true,
                title: { style: { fontSize: '0px' } },
                min: 0,
                forceNiceScale: true,
                labels: {
                    formatter: v => {
                        if (v >= 1000000000) return (v / 1000000000).toFixed(1) + 'M';
                        if (v >= 1000000)    return (v / 1000000).toFixed(0) + 'jt';
                        if (v >= 1000)       return (v / 1000).toFixed(0) + 'rb';
                        return v;
                    },
                },
            },
        ],
        tooltip: {
            shared: true,
            intersect: false,
            x: { formatter: v => 'Tanggal ' + v },
            y: [
                { formatter: v => v + ' unit' },
                { formatter: v => fmtRp(v) },
            ],
        },
    };

    const el = document.querySelector('#daily-chart');
    if (el && typeof ApexCharts !== 'undefined') {
        new ApexCharts(el, options).render();
    } else if (el) {
        setTimeout(() => {
            if (typeof ApexCharts !== 'undefined') {
                new ApexCharts(el, options).render();
            }
        }, 500);
    }
});
</script>
@endif

@endsection
@extends('layouts.app')

@section('content')
    @php
        $monthLabels = collect($monthlyRevenue)->pluck('month')->toJson();
        $monthAmounts = collect($monthlyRevenue)->pluck('revenue')->toJson();
        $monthUnits = collect($monthlyRevenue)->pluck('unit')->toJson();
        $maxBranch = collect($branchRanking)->max('total_amount') ?: 1;
        $maxProduct = collect($topProducts)->max('unit') ?: 1;
    @endphp

    @php
        $totalFinco = collect($topFinco)->sum('amount');

        $fmtRp = function ($v) {
            if ($v >= 1000000000) {
                return 'Rp ' . number_format($v / 1000000000, 2, ',', '.') . ' M';
            }

            if ($v >= 1000000) {
                return 'Rp ' . number_format($v / 1000000, 1, ',', '.') . ' jt';
            }

            return 'Rp ' . number_format($v, 0, ',', '.');
        };
    @endphp


    @php
        $total = collect($branchRanking)->sum('total_amount');

        $fmtRp = function ($v) {
            if ($v >= 1000000000) {
                return 'Rp ' . number_format($v / 1000000000, 2, ',', '.') . ' M';
            }

            if ($v >= 1000000) {
                return 'Rp ' . number_format($v / 1000000, 1, ',', '.') . ' jt';
            }

            return 'Rp ' . number_format($v, 0, ',', '.');
        };
    @endphp

    @php
        $totalUnit = collect($topProducts)->sum('unit');

        $fmtUnit = fn($v) => number_format($v, 0, ',', '.');
    @endphp

    <div class="grid grid-cols-12 gap-4 md:gap-6">
        {{-- HEADER --}}
        <div class="col-span-12 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Sales Analytics</h2>
                <p class="mt-0.5 text-sm text-gray-500">
                    Ringkasan penjualan {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                    {{ $year }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <form method="GET" class="flex items-center gap-2">
                    <select name="month" onchange="this.form.submit()"
                        class="h-9 rounded-lg border border-gray-300 bg-white px-3 text-xs text-gray-700 focus:border-brand-400 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        @foreach (range(1, 12) as $m)
                            <option value="{{ $m }}" {{ $month == $m ? 'selected' : '' }}>
                                {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                            </option>
                        @endforeach
                    </select>
                    <select name="year" onchange="this.form.submit()"
                        class="h-9 rounded-lg border border-gray-300 bg-white px-3 text-xs text-gray-700 focus:border-brand-400 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                        @foreach (range(now()->year, now()->year - 3) as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}
                            </option>
                        @endforeach
                    </select>
                </form>
                <span class="text-xs text-gray-400">{{ now()->translatedFormat('d M Y, H:i') }}</span>
            </div>
        </div>

        <div class="col-span-12 space-y-6 xl:col-span-7">
            {{-- ROW 1: KPI cards (7) + Ranking Cabang (5) --}}
            <div class="col-span-12 space-y-6 xl:col-span-7">

                {{-- KPI CARDS --}}
                @php
                    $cards = [
                        [
                            'label' => 'Total Amount',
                            'value' => 'Rp ' . number_format($summary['total_revenue'], 0, ',', '.'),
                            'sub' =>
                                ($summary['revenue_growth'] >= 0 ? '▲ ' : '▼ ') .
                                number_format(abs($summary['revenue_growth']), 1) .
                                '% vs bln lalu',
                            'up' => $summary['revenue_growth'] >= 0,
                            'bg' => 'bg-success-50 dark:bg-success-500/10',
                            'color' => 'text-success-500',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>',
                        ],
                        [
                            'label' => 'Gross Income',
                            'value' => '',
                            'sub' =>'',
                            'up' => '',
                            'bg' => 'bg-brand-50 dark:bg-brand-500/10',
                            'color' => 'text-brand-500',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>',
                        ],
                        [
                            'label' => 'Total Unit',
                            'value' => number_format($summary['total_units'], 0, ',', '.'),
                            'sub' =>
                                ($summary['unit_growth'] >= 0 ? '▲ ' : '▼ ') .
                                number_format(abs($summary['unit_growth']), 1) .
                                '% vs bln lalu',
                            'up' => $summary['unit_growth'] >= 0,
                            'bg' => 'bg-purple-50 dark:bg-purple-500/10',
                            'color' => 'text-purple-500',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>',
                        ],
                        [
                            'label' => 'AVG/TRANSAKSI',
                            'value' => 'Rp ' . number_format($summary['asp'], 0, ',', '.'),
                            'sub' =>
                                ($summary['asp_growth'] >= 0 ? '▲ ' : '▼ ') .
                                number_format(abs($summary['asp_growth']), 1) .
                                '% vs bln lalu',
                            'up' => $summary['asp_growth'] >= 0,
                            'bg' => 'bg-orange-50 dark:bg-orange-500/10',
                            'color' => 'text-orange-500',
                            'icon' =>
                                '<path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>',
                        ],
                    ];
                @endphp

                <div class="grid grid-cols-2 gap-4">
                    @foreach ($cards as $card)
                        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-xs font-medium uppercase tracking-wide text-gray-400">{{ $card['label'] }}
                                </p>
                                <span
                                    class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg {{ $card['bg'] }}">
                                    <svg class="h-4 w-4 {{ $card['color'] }}" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" stroke-width="2">
                                        {!! $card['icon'] !!}
                                    </svg>
                                </span>
                            </div>
                            <p class="mt-3 text-xl font-semibold text-gray-900 dark:text-white">{{ $card['value'] }}</p>
                            <p class="mt-0.5 text-xs {{ $card['up'] ? 'text-success-500' : 'text-red-500' }}">
                                {{ $card['sub'] }}
                            </p>
                        </div>
                    @endforeach
                </div>




            </div>
        </div>


        {{-- FINCO --}}


        <div class="col-span-12 xl:col-span-5">

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">

                {{-- Header --}}
                <div class="mb-5 flex items-center justify-between">

                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                            Ranking Finco
                        </h3>

                        <p class="mt-1 text-xs text-gray-400">
                            Kontribusi pembiayaan bulan
                            {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                            {{ $year }}
                        </p>
                    </div>

                    <div
                        class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        {{ count($topFinco) }} Finco
                    </div>

                </div>

                {{-- List --}}
                <div class="divide-y divide-gray-100 dark:divide-gray-800">

                    @forelse($topFinco as $row)
                        @php
                            $sharePct = $totalFinco > 0 ? round(($row['amount'] / $totalFinco) * 100, 1) : 0;
                        @endphp

                        <div class="py-3">

                            <div class="flex items-center justify-between gap-3">

                                {{-- Left --}}
                                <div class="flex min-w-0 items-center gap-3">


                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-medium text-gray-800 dark:text-white">
                                            {{ $row['name'] }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-gray-400">
                                            Kontribusi {{ number_format($sharePct, 1) }}% dari total pembiayaan
                                        </p>

                                    </div>

                                </div>

                                {{-- Right --}}
                                <div class="text-right">

                                    <p class="text-sm font-semibold text-success-600">
                                        {{ $fmtRp($row['amount']) }}
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        {{ number_format($row['unit']) }} Unit
                                    </p>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="py-10 text-center text-sm text-gray-400">
                            Tidak ada data finco.
                        </div>
                    @endforelse

                </div>

            </div>

        </div>

        {{-- CHART BULANAN --}}
        <div class="col-span-12">

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
                <div
                    class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 px-5 py-4 dark:border-gray-800">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">Penjualan Bulanan
                            {{ $year }}</h3>
                        <p class="mt-0.5 text-xs text-gray-400">Amount dan unit per bulan</p>
                    </div>
                    <div class="flex items-center gap-4 text-xs text-gray-500">
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 rounded-sm" style="background:#465FFF"></span>Amount
                        </span>
                        <span class="flex items-center gap-1.5">
                            <span class="inline-block h-2.5 w-2.5 rounded-sm" style="background:#9CB9FF"></span>Unit
                        </span>
                    </div>
                </div>
                <div class="p-5">
                    <div id="monthly-chart"></div>
                </div>
            </div>
        </div>



        {{-- RANKING CABANG --}}
        <div class="col-span-12 xl:col-span-6 space-y-6">

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">

                {{-- Header --}}
                <div class="mb-5 flex items-center justify-between">

                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                            Ranking Cabang
                        </h3>

                        <p class="mt-1 text-xs text-gray-400">
                            Kontribusi revenue bulan
                            {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                            {{ $year }}
                        </p>
                    </div>

                    <div
                        class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        {{ count($branchRanking) }} Cabang
                    </div>

                </div>

                {{-- List --}}
                <div class="divide-y divide-gray-100 dark:divide-gray-800">

                    @forelse($branchRanking as $row)
                        @php
                            $sharePct = $total > 0 ? round(($row['total_amount'] / $total) * 100, 1) : 0;
                        @endphp

                        <div class="py-3">

                            <div class="flex items-center justify-between gap-3">

                                {{-- Left --}}
                                <div class="flex min-w-0 items-center gap-3">



                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-medium text-gray-800 dark:text-white">
                                            {{ $row['branch_name'] }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-gray-400">
                                            Kontribusi dari total amount
                                        </p>

                                    </div>

                                </div>

                                {{-- Right --}}
                                <div class="text-right">

                                    <p class="text-sm font-semibold text-success-600">
                                        {{ $fmtRp($row['total_amount']) }}
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        {{ number_format($sharePct, 1) }}%
                                    </p>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="py-10 text-center text-sm text-gray-400">
                            Belum ada data ranking cabang bulan ini.
                        </div>
                    @endforelse

                </div>



            </div>

        </div>


        {{-- PRODUK TERLARIS --}}


        <div class="col-span-12 xl:col-span-6 space-y-6">

            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-gray-900">

                {{-- Header --}}
                <div class="mb-5 flex items-center justify-between">

                    <div>
                        <h3 class="text-sm font-semibold text-gray-800 dark:text-white">
                            Produk Terlaris
                        </h3>

                        <p class="mt-1 text-xs text-gray-400">
                            Kontribusi penjualan unit bulan
                            {{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}
                            {{ $year }}
                        </p>
                    </div>

                    <div
                        class="rounded-lg bg-gray-100 px-3 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-300">
                        {{ count($topProducts) }} Produk
                    </div>

                </div>

                {{-- List --}}
                <div class="divide-y divide-gray-100 dark:divide-gray-800">

                    @forelse($topProducts as $row)
                        @php
                            $sharePct = $totalUnit > 0 ? round(($row['unit'] / $totalUnit) * 100, 1) : 0;
                        @endphp

                        <div class="py-3">

                            <div class="flex items-center justify-between gap-3">

                                {{-- Left --}}
                                <div class="flex min-w-0 items-center gap-3">

                                    <div class="min-w-0">

                                        <p class="truncate text-sm font-medium text-gray-800 dark:text-white">
                                            {{ $row['name'] ?: $row['kode'] }}
                                        </p>

                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $row['category'] ?: '-' }} • {{ $row['kode'] }}
                                        </p>

                                        <p class="mt-0.5 text-xs text-gray-400">
                                            Kontribusi dari total unit terjual
                                        </p>

                                    </div>

                                </div>

                                {{-- Right --}}
                                <div class="text-right">

                                    <p class="text-sm font-semibold text-success-600">
                                        {{ $fmtUnit($row['unit']) }} Unit
                                    </p>

                                    <p class="text-xs text-gray-400">
                                        {{ number_format($sharePct, 1) }}%
                                    </p>

                                </div>

                            </div>

                        </div>

                    @empty

                        <div class="py-10 text-center text-sm text-gray-400">
                            Belum ada data produk terlaris.
                        </div>
                    @endforelse

                </div>


            </div>

        </div>


    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const labels = {!! $monthLabels !!};
            const amounts = {!! $monthAmounts !!};
            const units = {!! $monthUnits !!};

            const options = {
                series: [{
                        name: 'Amount',
                        data: amounts
                    },
                    {
                        name: 'Unit',
                        data: units
                    },
                ],
                legend: {
                    show: false,
                    position: 'top',
                    horizontalAlign: 'left'
                },
                colors: ['#465FFF', '#9CB9FF'],
                chart: {
                    fontFamily: 'Outfit, sans-serif',
                    height: 310,
                    type: 'area',
                    toolbar: {
                        show: false
                    },
                    zoom: {
                        enabled: false
                    },
                },
                fill: {
                    gradient: {
                        enabled: true,
                        opacityFrom: 0.55,
                        opacityTo: 0
                    }
                },
                stroke: {
                    curve: 'smooth',
                    width: [2, 2]
                },
                markers: {
                    size: 0
                },
                dataLabels: {
                    enabled: false
                },
                grid: {
                    xaxis: {
                        lines: {
                            show: false
                        }
                    },
                    yaxis: {
                        lines: {
                            show: true
                        }
                    },
                },
                xaxis: {
                    type: 'category',
                    categories: labels,
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    tooltip: {
                        enabled: false
                    },
                },
                yaxis: [{
                        seriesName: 'Amount',
                        title: {
                            style: {
                                fontSize: '0px'
                            }
                        },
                        min: 0,
                        forceNiceScale: true,
                        labels: {
                            formatter: v => {
                                if (v >= 1000000000) return (v / 1000000000).toFixed(1) + 'M';
                                if (v >= 1000000) return (v / 1000000).toFixed(0) + 'jt';
                                return v;
                            },
                        },
                    },
                    {
                        seriesName: 'Unit',
                        opposite: true,
                        title: {
                            style: {
                                fontSize: '0px'
                            }
                        },
                        min: 0,
                        forceNiceScale: true,
                        labels: {
                            formatter: v => Math.round(v)
                        },
                    },
                ],
                tooltip: {
                    shared: true,
                    intersect: false,
                    y: [{
                            formatter: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
                        },
                        {
                            formatter: v => v + ' unit'
                        },
                    ],
                },
            };

            const el = document.querySelector('#monthly-chart');
            if (el && typeof ApexCharts !== 'undefined') {
                new ApexCharts(el, options).render();
            }
        });
    </script>
@endsection

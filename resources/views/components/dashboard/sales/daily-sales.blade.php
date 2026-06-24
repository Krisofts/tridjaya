@props(['sales', 'sparkline' => []])

@php
    $revenue     = $sales['revenue']     ?? ['today' => 0, 'yesterday' => 0, 'growth_pct' => 0];
    $transaction = $sales['transaction'] ?? ['today' => 0, 'yesterday' => 0, 'growth_pct' => 0];
    $unit        = $sales['unit']        ?? ['today' => 0, 'yesterday' => 0, 'growth_pct' => 0];
    $avg         = $sales['avg_transaction'] ?? 0;

    // Format ringkas: 42,5 jt / 1,20 M — untuk avg & sub-metric
    $fmtRp = function (float|int $v): string {
        if ($v >= 1_000_000_000) return 'Rp ' . number_format($v / 1_000_000_000, 2, ',', '.') . ' M';
        if ($v >= 1_000_000)     return 'Rp ' . number_format($v / 1_000_000, 1, ',', '.') . ' jt';
        return 'Rp ' . number_format((int) $v, 0, ',', '.');
    };

    // Format full tanpa singkatan — untuk revenue utama
    $fmtRpFull = function (float|int $v): string {
        return 'Rp ' . number_format((int) $v, 0, ',', '.');
    };

    $fmtNum = fn (float|int $v): string => number_format((int) $v, 0, ',', '.');

    $growthBadge = function (float $pct): array {
        $abs = number_format(abs($pct), 2);
        if ($pct > 0) return [
            'class' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
            'dir'   => 'up',
            'label' => '+' . $abs . '%',
        ];
        if ($pct < 0) return [
            'class' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
            'dir'   => 'down',
            'label' => '−' . $abs . '%',
        ];
        return [
            'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',
            'dir'   => 'flat',
            'label' => '0%',
        ];
    };

    $revBadge  = $growthBadge((float) $revenue['growth_pct']);
    $txBadge   = $growthBadge((float) $transaction['growth_pct']);
    $unitBadge = $growthBadge((float) $unit['growth_pct']);

    $arrowUp   = '<svg class="fill-current" width="10" height="10" viewBox="0 0 12 12" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M5.56462 1.62393C5.70193 1.47072 5.90135 1.37432 6.12329 1.37432C6.31631 1.37415 6.50845 1.44731 6.65505 1.59381L9.65514 4.5918C9.94814 4.88459 9.94831 5.35947 9.65552 5.65246C9.36273 5.94546 8.88785 5.94562 8.59486 5.65283L6.87329 3.93247L6.87329 10.125C6.87329 10.5392 6.53751 10.875 6.12329 10.875C5.70908 10.875 5.37329 10.5392 5.37329 10.125L5.37329 3.93578L3.65516 5.65282C3.36218 5.94562 2.8873 5.94547 2.5945 5.65248C2.3017 5.35949 2.30185 4.88462 2.59484 4.59182L5.56462 1.62393Z" fill=""/></svg>';
    $arrowDown = '<svg class="fill-current" width="10" height="10" viewBox="0 0 12 12" fill="none"><path fill-rule="evenodd" clip-rule="evenodd" d="M6.43538 10.3761C6.29807 10.5293 6.09865 10.6257 5.87671 10.6257C5.68369 10.6258 5.49155 10.5527 5.34495 10.4062L2.34486 7.4082C2.05186 7.11541 2.05169 6.64053 2.34448 6.34754C2.63727 6.05454 3.11215 6.05438 3.40514 6.34717L5.12671 8.06753L5.12671 1.875C5.12671 1.46079 5.46249 1.125 5.87671 1.125C6.29092 1.125 6.62671 1.46079 6.62671 1.875L6.62671 8.06422L8.34484 6.34718C8.63782 6.05438 9.1127 6.05453 9.4055 6.34752C9.6983 6.64051 9.69815 7.11538 9.40516 7.40818L6.43538 10.3761Z" fill=""/></svg>';

    $sparkJson = json_encode(array_values($sparkline));
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

    {{-- Header --}}
    <div class="mb-5 flex items-center justify-between">
        <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Penjualan hari ini</p>
        <p class="text-xs text-gray-400 dark:text-gray-500">{{ now()->translatedFormat('l, d F Y') }}</p>
    </div>

    {{-- Revenue + Sparkline --}}
    <div class="flex items-end justify-between gap-4">
        <div>
            <p class="text-sm text-gray-500 dark:text-gray-400">Revenue</p>
            <h4 class="mt-1.5 text-3xl font-bold tracking-tight text-gray-800 dark:text-white/90">
                {{ $fmtRpFull($revenue['today']) }}
            </h4>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                Kemarin {{ $fmtRpFull($revenue['yesterday']) }}
            </p>
            <span class="mt-2 inline-flex items-center gap-1 rounded-full py-0.5 pl-1.5 pr-2.5
                         text-xs font-medium {{ $revBadge['class'] }}">
                @if($revBadge['dir'] === 'up') {!! $arrowUp !!}
                @elseif($revBadge['dir'] === 'down') {!! $arrowDown !!}
                @endif
                {{ $revBadge['label'] }}
            </span>
        </div>

        {{-- ApexCharts sparkline container --}}
        <div
            id="daily-sparkline"
            class="mb-1 flex-shrink-0"
            style="min-height: 56px; width: 140px;"
            data-spark="{{ $sparkJson }}"
            data-growth="{{ $revenue['growth_pct'] }}"
        ></div>
    </div>

    {{-- Divider --}}
    <div class="my-5 border-t border-gray-100 dark:border-gray-800"></div>

    {{-- Sub-metric row --}}
    <div class="grid grid-cols-3 divide-x divide-gray-100 dark:divide-gray-800">

        {{-- Transaksi --}}
        <div class="pr-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Jumlah transaksi</p>
            <h4 class="mt-1.5 text-xl font-bold tracking-tight text-gray-800 dark:text-white/90">
                {{ $fmtNum($transaction['today']) }}
            </h4>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                Kemarin {{ $fmtNum($transaction['yesterday']) }}
            </p>
        </div>

        {{-- Unit terjual --}}
        <div class="px-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Unit terjual</p>
            <h4 class="mt-1.5 text-xl font-bold tracking-tight text-gray-800 dark:text-white/90">
                {{ $fmtNum($unit['today']) }}
            </h4>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                Kemarin {{ $fmtNum($unit['yesterday']) }}
            </p>
        </div>

        {{-- Avg per transaksi --}}
        <div class="pl-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Avg / transaksi</p>
            <h4 class="mt-1.5 text-xl font-bold tracking-tight text-gray-800 dark:text-white/90">
                {{ $fmtRp($avg) }}
            </h4>
            <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">avg transaksi</p>
        </div>

    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('daily-sparkline');
    if (!el || typeof ApexCharts === 'undefined') return;

    const raw    = el.dataset.spark && el.dataset.spark !== '[]'
        ? JSON.parse(el.dataset.spark)
        : [28, 32, 27, 35, 31, 38, 33, 40, 36, 42.5].map(v => v * 1e6);

    const growth = parseFloat(el.dataset.growth ?? 0);
    const color  = growth >= 0 ? '#16a34a' : '#dc2626';
    const isDark = document.documentElement.classList.contains('dark');

    const options = {
        series: [{
            name: 'Revenue',
            data: raw.map(v => Math.round(v / 1e6)),
        }],
        chart: {
            type: 'area',
            height: 56,
            width: 140,
            sparkline: { enabled: true },
            animations: { enabled: true, easing: 'easeinout', speed: 800 },
            background: 'transparent',
            toolbar: { show: false },
        },
        stroke: {
            curve: 'smooth',
            width: 2,
            lineCap: 'round',
        },
        fill: {
            type: 'gradient',
            gradient: {
                type: 'vertical',
                shadeIntensity: 0,
                gradientToColors: ['#4f74f9'],
                inverseColors: false,
                opacityFrom: isDark ? 0.45 : 0.35,
                opacityTo: 0.02,
                stops: [0, 95, 100],
            },
        },
        colors: ['#4f74f9'],
        markers: { size: 0 },
        tooltip: { enabled: true },
        theme: { mode: isDark ? 'dark' : 'light' },
        grid: { padding: { top: 4, bottom: 4, left: 2, right: 2 } },
    };

    new ApexCharts(el, options).render();
});
</script>
@endpush
@endonce
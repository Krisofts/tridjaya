@props(['target'])

@php
    $actual       = (float) ($target['actual']            ?? 0);
    $targetVal    = (float) ($target['target']            ?? 0);
    $remaining    = (float) ($target['remaining_revenue'] ?? 0);
    $days         = (int)   ($target['remaining_days']    ?? 0);
    $neededPerDay = (float) ($target['needed_per_day']    ?? 0);
    $pct          = min((float) ($target['achievement_pct'] ?? 0), 100);
    $status       = $target['status'] ?? 'UNKNOWN';

    $fmtRp = function (float|int $v): string {
        if ($v >= 1_000_000_000) return 'Rp ' . number_format($v / 1_000_000_000, 2, ',', '.') . ' M';
        if ($v >= 1_000_000)     return 'Rp ' . number_format($v / 1_000_000, 1, ',', '.') . ' jt';
        return 'Rp ' . number_format((int) $v, 0, ',', '.');
    };

    // Ring & progress bar selalu biru
    $ringColor = '#465fff';

    // Badge warna-warni sesuai status
    $statusMap = [
        'ACHIEVED'  => ['label' => 'Tercapai',  'class' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500'],
        'ON_TRACK'  => ['label' => 'On Track',  'class' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400'],
        'AT_RISK'   => ['label' => 'At Risk',   'class' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400'],
        'BEHIND'    => ['label' => 'Terlambat', 'class' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400'],
        'NO_TARGET' => ['label' => 'No Target', 'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'],
        'UNKNOWN'   => ['label' => 'Unknown',   'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400'],
    ];

    $s = $statusMap[$status] ?? $statusMap['UNKNOWN'];
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

    {{-- Header --}}
    <div class="mb-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Target Bulanan</p>
            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ now()->translatedFormat('F Y') }}</p>
        </div>
        <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $s['class'] }}">
            {{ $s['label'] }}
        </span>
    </div>

    {{-- Ring + Stats --}}
    <div class="flex items-center gap-5">

        {{-- ApexCharts radial ring — selalu biru --}}
        <div class="relative flex-shrink-0" style="width:160px; height:160px;">
            <div id="monthlyTargetRing"
                 data-pct="{{ $pct }}"
                 style="width:160px; height:160px;">
            </div>
            <div class="pointer-events-none absolute inset-0 flex flex-col items-center justify-center">
                <span class="text-2xl font-bold tracking-tight text-gray-800 dark:text-white/90">
                    {{ number_format($pct, 1) }}%
                </span>
                <span class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">tercapai</span>
            </div>
        </div>

        {{-- Stats kanan --}}
        <div class="min-w-0 flex-1">

            {{-- Realisasi --}}
            <div class="mb-4">
                <p class="text-sm text-gray-500 dark:text-gray-400">Realisasi</p>
                <p class="mt-1 text-xl font-bold tracking-tight text-gray-800 dark:text-white/90">
                    {{ $fmtRp($actual) }}
                </p>
                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                    dari target {{ $fmtRp($targetVal) }}
                </p>
            </div>

            {{-- Progress bar — selalu biru --}}
            <div class="mb-4">
                <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-full rounded-full transition-all duration-700"
                         style="width: {{ $pct }}%; background: {{ $ringColor }};"></div>
                </div>
            </div>

            {{-- Sisa & kebutuhan/hari --}}
            <div class="grid grid-cols-2 divide-x divide-gray-100 dark:divide-gray-800">
                <div class="pr-4">
                    <p class="text-xs text-gray-400 dark:text-gray-500">Sisa target</p>
                    <p class="mt-1 text-sm font-bold tracking-tight text-gray-800 dark:text-white/90">
                        {{ $fmtRp($remaining) }}
                    </p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                        {{ $days }} hari lagi
                    </p>
                </div>
                <div class="pl-4">
                    <p class="text-xs text-gray-400 dark:text-gray-500">Kebutuhan/hari</p>
                    <p class="mt-1 text-sm font-bold tracking-tight text-gray-800 dark:text-white/90">
                        {{ $fmtRp($neededPerDay) }}
                    </p>
                    <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                        untuk capai target
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>

@once
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const el = document.getElementById('monthlyTargetRing');
    if (!el || typeof ApexCharts === 'undefined') return;

    const pct    = parseFloat(el.dataset.pct ?? 0);
    const isDark = document.documentElement.classList.contains('dark');

    new ApexCharts(el, {
        series: [parseFloat(pct.toFixed(1))],
        chart: {
            type: 'radialBar',
            height: 160,
            width: 160,
            sparkline: { enabled: true },
            animations: { enabled: true, easing: 'easeinout', speed: 900 },
            background: 'transparent',
            toolbar: { show: false },
        },
        plotOptions: {
            radialBar: {
                startAngle: -135,
                endAngle: 135,
                hollow: {
                    size: '68%',
                    background: 'transparent',
                },
                track: {
                    background: isDark ? '#1f2937' : '#f3f4f6',
                    strokeWidth: '100%',
                },
                dataLabels: { show: false },
            },
        },
        fill: { colors: ['#465fff'] },
        stroke: { lineCap: 'round' },
        theme: { mode: isDark ? 'dark' : 'light' },
    }).render();
});
</script>
@endpush
@endonce
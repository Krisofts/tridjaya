@props(['dealers'])

@php
    $fmtRp = function (float|int $v): string {
        if ($v >= 1_000_000_000) return 'Rp ' . number_format($v / 1_000_000_000, 2, ',', '.') . ' M';
        if ($v >= 1_000_000)     return 'Rp ' . number_format($v / 1_000_000, 1, ',', '.') . ' jt';
        return 'Rp ' . number_format((int) $v, 0, ',', '.');
    };

    $statusMap = [
        'ACHIEVED'  => ['label' => 'Achieved',   'class' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500', 'bar' => '#16a34a'],
        'ON_TRACK'  => ['label' => 'On Track',   'class' => 'bg-blue-50 text-blue-600 dark:bg-blue-500/15 dark:text-blue-400',             'bar' => '#2563eb'],
        'AT_RISK'   => ['label' => 'At Risk',    'class' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-warning-400', 'bar' => '#ca8a04'],
        'BEHIND'    => ['label' => 'Behind',     'class' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-400',         'bar' => '#dc2626'],
        'NO_TARGET' => ['label' => 'No Target',  'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',               'bar' => '#d1d5db'],
        'UNKNOWN'   => ['label' => 'Unknown',    'class' => 'bg-gray-100 text-gray-500 dark:bg-gray-800 dark:text-gray-400',               'bar' => '#d1d5db'],
    ];
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

    {{-- Header --}}
    <div class="mb-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Target Harian Cabang</p>
            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                {{ now()->translatedFormat('l, d F Y') }}
            </p>
        </div>
        <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
            Realtime
        </span>
    </div>

    {{-- Column headers --}}
    <div class="mb-1 grid grid-cols-12 gap-2 px-1 text-xs font-medium uppercase tracking-wide text-gray-400 dark:text-gray-500">
        <div class="col-span-4">Cabang</div>
        <div class="col-span-3 text-right">Hari ini</div>
        <div class="col-span-3 text-right">Target/hari</div>
        <div class="col-span-2 text-right">Capai</div>
    </div>

    {{-- Rows --}}
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($dealers as $row)
        @php
            $s   = $statusMap[$row['status']] ?? $statusMap['UNKNOWN'];
            $pct = min((int) round($row['achievement_pct_daily']), 100);
        @endphp

        <div class="grid grid-cols-12 items-center gap-2 py-3">

            {{-- Nama cabang + progress bar --}}
            <div class="col-span-4">
                <p class="truncate text-sm font-medium text-gray-800 dark:text-white/90">
                    {{ $row['dealer_name'] }}
                </p>
                <div class="mt-1.5 h-1 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                    <div class="h-full rounded-full transition-all duration-700"
                         style="width: {{ $pct }}%; background: {{ $s['bar'] }};"></div>
                </div>
            </div>

            {{-- Revenue hari ini --}}
            <div class="col-span-3 text-right">
                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                    {{ $fmtRp($row['today_amount']) }}
                </p>
            </div>

            {{-- Target per hari --}}
            <div class="col-span-3 text-right">
                <p class="text-sm text-gray-400 dark:text-gray-500">
                    {{ $fmtRp($row['target_amount_daily']) }}
                </p>
            </div>

            {{-- Achievement % + badge --}}
            <div class="col-span-2 text-right">
                <p class="text-sm font-bold" style="color: {{ $s['bar'] }};">
                    {{ number_format($row['achievement_pct_daily'], 1) }}%
                </p>
                <span class="mt-1 inline-block rounded-full px-1.5 py-0.5 text-xs font-medium {{ $s['class'] }}">
                    {{ $s['label'] }}
                </span>
            </div>

        </div>
        @empty
        <div class="py-10 text-center text-sm text-gray-400 dark:text-gray-500">
            Belum ada data penjualan hari ini.
        </div>
        @endforelse
    </div>

</div>
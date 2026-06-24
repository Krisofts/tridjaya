@props(['branches', 'month' => null, 'year' => null])

@php
    $month ??= now()->month;
    $year  ??= now()->year;

    $monthLabel = \Carbon\Carbon::create($year, $month)->translatedFormat('F Y');

    $total    = collect($branches)->sum('total_amount');
    $maxAmount = collect($branches)->max('total_amount') ?: 1;
    $medals   = ['🥇', '🥈', '🥉'];

    $fmtRp = function (float|int $v): string {
        if ($v >= 1_000_000_000) return 'Rp ' . number_format($v / 1_000_000_000, 2, ',', '.') . ' M';
        if ($v >= 1_000_000)     return 'Rp ' . number_format($v / 1_000_000, 1, ',', '.') . ' jt';
        return 'Rp ' . number_format((int) $v, 0, ',', '.');
    };

    $barColors = ['#3b82f6', '#10b981', '#8b5cf6', '#f97316', '#f43f5e'];
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

    {{-- Header --}}
    <div class="mb-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Ranking Cabang</p>
            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ $monthLabel }} · realtime</p>
        </div>
        <span class="rounded-lg bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
            Top {{ count($branches) }}
        </span>
    </div>

    {{-- Rows --}}
    <div class="space-y-3">
        @forelse($branches as $i => $row)
        @php
            $barPct  = round($row['total_amount'] / $maxAmount * 100);
            $sharePct= $total > 0 ? round($row['total_amount'] / $total * 100, 1) : 0;
        @endphp

        <div>
            {{-- Name row --}}
            <div class="mb-1.5 flex items-center justify-between gap-3">
                <div class="flex min-w-0 items-center gap-2">
                    <span class="w-5 flex-shrink-0 text-sm">
                        @if($i < 3) {{ $medals[$i] }} @else <span class="text-xs font-medium text-gray-400">{{ $row['ranking'] }}</span> @endif
                    </span>
                    <span class="truncate text-sm font-medium text-gray-700 dark:text-gray-300">
                        {{ $row['branch_name'] }}
                    </span>
                </div>
                <div class="flex flex-shrink-0 items-baseline gap-1.5">
                    <span class="text-sm font-semibold text-gray-800 dark:text-white/90">
                        {{ $fmtRp($row['total_amount']) }}
                    </span>
                    <span class="text-xs text-gray-400 dark:text-gray-500">
                        {{ $sharePct }}%
                    </span>
                </div>
            </div>

            {{-- Progress bar --}}
            <div class="h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                <div class="h-full rounded-full transition-all duration-700"
                     style="width: {{ $barPct }}%; background: {{ $barColors[$i % count($barColors)] }};"></div>
            </div>
        </div>

        @empty
        <p class="py-8 text-center text-sm text-gray-400 dark:text-gray-500">
            Belum ada data ranking cabang bulan ini.
        </p>
        @endforelse
    </div>

    {{-- Footer total --}}
    @if(count($branches) > 0)
    <div class="mt-5 border-t border-gray-100 pt-4 dark:border-gray-800">
        <div class="flex items-center justify-between">
            <p class="text-xs text-gray-400 dark:text-gray-500">Total semua cabang</p>
            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">{{ $fmtRp($total) }}</p>
        </div>
    </div>
    @endif

</div>
@props(['branches', 'month' => null, 'year' => null])

@php
$month ??= now()->month;
$year ??= now()->year;

$monthLabel = \Carbon\Carbon::create($year, $month)->translatedFormat('F Y');

$total = collect($branches)->sum('total_amount');

$medals = ['🥇', '🥈', '🥉'];

$fmtRp = function (float|int $v): string {
    if ($v >= 1_000_000_000) {
        return 'Rp ' . number_format($v / 1_000_000_000, 2, ',', '.') . ' M';
    }

    if ($v >= 1_000_000) {
        return 'Rp ' . number_format($v / 1_000_000, 1, ',', '.') . ' jt';
    }

    return 'Rp ' . number_format((int) $v, 0, ',', '.');
};

@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">

{{-- Header --}}
<div class="mb-5 flex justify-between">
    <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Ranking Cabang
        </h3>

        <p class="text-theme-sm mt-1 text-gray-500 dark:text-gray-400">
            Performa cabang bulan {{ $monthLabel }}
        </p>
    </div>

    <div class="rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-600 dark:bg-gray-800 dark:text-gray-400">
        {{ count($branches) }} Cabang
    </div>
</div>

{{-- Rows --}}
<div class="divide-y divide-gray-100 dark:divide-gray-800">

    @forelse($branches as $i => $row)

        @php
            $sharePct = $total > 0
                ? round(($row['total_amount'] / $total) * 100, 1)
                : 0;
        @endphp

        <div class="py-3">

            <div class="flex items-center justify-between gap-3">

                {{-- Left --}}
                <div class="flex min-w-0 items-center gap-3">

                    {{-- Ranking --}}
                    <div class="inline-flex h-10 w-10 shrink-0 items-center justify-center rounded-full border border-gray-200 shadow-xs dark:border-gray-800">

                        @if($i < 3)
                            <span class="text-lg">{{ $medals[$i] }}</span>
                        @else
                            <span class="text-sm font-semibold text-gray-600 dark:text-gray-400">
                                {{ $row['ranking'] }}
                            </span>
                        @endif

                    </div>

                    {{-- Branch Info --}}
                    <div class="min-w-0">
                        <h4 class="mb-0.5 truncate text-sm font-medium text-gray-800 dark:text-white/90">
                            {{ $row['branch_name'] }}
                        </h4>

                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            Kontribusi {{ $sharePct }}% dari total revenue
                        </p>
                    </div>

                </div>

                {{-- Right --}}
                <div class="text-right">

                    <p class="mb-0.5 text-sm font-semibold text-success-600">
                        {{ $fmtRp($row['total_amount']) }}
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        Rank #{{ $row['ranking'] }}
                    </p>

                </div>

            </div>

        </div>

    @empty

        <div class="py-10 text-center text-sm text-gray-400 dark:text-gray-500">
            Belum ada data ranking cabang bulan ini.
        </div>

    @endforelse

</div>

{{-- Footer --}}
@if(count($branches))
    <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">

        <div class="flex items-center justify-between">

            <div>
                <p class="text-xs text-gray-500 dark:text-gray-400">
                    Total Revenue
                </p>

                <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
                    {{ $fmtRp($total) }}
                </p>
            </div>

            <span class="rounded-lg bg-success-50 px-2.5 py-1 text-xs font-medium text-success-700 dark:bg-success-500/10 dark:text-success-400">
                {{ count($branches) }} Cabang
            </span>

        </div>

    </div>
@endif

</div>

@props(['rankings', 'allRankings' => [], 'month' => null, 'year' => null])

@php
    $month ??= now()->month;
    $year  ??= now()->year;

    $monthLabel = \Carbon\Carbon::create($year, $month)->translatedFormat('F Y');
    $total      = count($allRankings);
    $maxUnit    = collect($allRankings)->max('unit_current_month') ?: 1;
    $medals     = ['🥇', '🥈', '🥉'];
    $uid        = 'sr_' . uniqid();

    $fmtRp = function (float|int $v): string {
        if ($v >= 1_000_000_000) return 'Rp ' . number_format($v / 1_000_000_000, 2, ',', '.') . ' M';
        if ($v >= 1_000_000)     return 'Rp ' . number_format($v / 1_000_000, 1, ',', '.') . ' jt';
        return 'Rp ' . number_format((int) $v, 0, ',', '.');
    };

    $fmtNum = fn (float|int $v): string => number_format((int) $v, 0, ',', '.');

    $initials = fn (string $name): string => collect(explode(' ', $name))
        ->take(2)->map(fn ($w) => strtoupper($w[0] ?? ''))->implode('');

    $avatarColors = [
        ['bg' => 'bg-blue-100 dark:bg-blue-900/30',      'text' => 'text-blue-700 dark:text-blue-300'],
        ['bg' => 'bg-emerald-100 dark:bg-emerald-900/30', 'text' => 'text-emerald-700 dark:text-emerald-300'],
        ['bg' => 'bg-violet-100 dark:bg-violet-900/30',  'text' => 'text-violet-700 dark:text-violet-300'],
        ['bg' => 'bg-orange-100 dark:bg-orange-900/30',  'text' => 'text-orange-700 dark:text-orange-300'],
        ['bg' => 'bg-rose-100 dark:bg-rose-900/30',      'text' => 'text-rose-700 dark:text-rose-300'],
    ];
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6"
     x-data="{ open: false, showAll: false }"
     id="{{ $uid }}">

    {{-- Header --}}
    {{-- Header --}}
<div class="mb-5 flex justify-between">
    <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Ranking Sales
        </h3>

        <p class="text-theme-sm mt-1 text-gray-500 dark:text-gray-400">
            Performa sales bulan {{ $monthLabel }}
        </p>
    </div>

    <div class="relative h-fit" x-data="{ openDropDown: false }">

        <button
            @click="openDropDown = !openDropDown"
            :class="openDropDown
                ? 'text-gray-700 dark:text-white'
                : 'text-gray-400 hover:text-gray-700 dark:hover:text-white'">

            <svg class="fill-current" width="24" height="24"
                 viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">

                <path fill-rule="evenodd" clip-rule="evenodd"
                      d="M10.2441 6C10.2441 5.0335 11.0276 4.25 11.9941 4.25H12.0041C12.9706 4.25 13.7541 5.0335 13.7541 6C13.7541 6.9665 12.9706 7.75 12.0041 7.75H11.9941C11.0276 7.75 10.2441 6.9665 10.2441 6ZM10.2441 18C10.2441 17.0335 11.0276 16.25 11.9941 16.25H12.0041C12.9706 16.25 13.7541 17.0335 13.7541 18C13.7541 18.9665 12.9706 19.75 12.0041 19.75H11.9941C11.0276 19.75 10.2441 18.9665 10.2441 18ZM11.9941 10.25C11.0276 10.25 10.2441 11.0335 10.2441 12C10.2441 12.9665 11.0276 13.75 11.9941 13.75H12.0041C12.9706 13.75 13.7541 12.9665 13.7541 12C13.7541 11.0335 12.9706 10.25 12.0041 10.25H11.9941Z">
                </path>

            </svg>
        </button>

        <div
            x-show="openDropDown"
            @click.outside="openDropDown = false"
            x-transition
            class="shadow-theme-lg dark:bg-gray-dark absolute top-full right-0 z-40 mt-2 w-48 space-y-1 rounded-2xl border border-gray-200 bg-white p-2 dark:border-gray-800"
            style="display: none;">

            <button
                @click="showAll = true; openDropDown = false"
                class="text-theme-xs flex w-full rounded-lg px-3 py-2 text-left font-medium
                       text-gray-500 hover:bg-gray-100 hover:text-gray-700
                       dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                Lihat Semua {{ $total }} Sales
            </button>

            <button
                @click="showAll = false; openDropDown = false"
                class="text-theme-xs flex w-full rounded-lg px-3 py-2 text-left font-medium
                       text-gray-500 hover:bg-gray-100 hover:text-gray-700
                       dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                Tampilkan Top 10
            </button>

        </div>

    </div>
</div>

    

    {{-- Rows — semua dari Blade, Alpine hanya toggle visibility --}}
    {{-- Rows --}}
<div class="divide-y divide-gray-100 dark:divide-gray-800">
    @forelse($allRankings as $i => $row)

    @php
        $isTop = $i < 10;
    @endphp

    <div class="py-3"
         @if(!$isTop) x-show="showAll" style="display: none;" @endif>

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

                {{-- Sales Info --}}
                <div class="min-w-0">
                    <h4 class="mb-0.5 truncate text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $row['sales_name'] }}
                    </h4>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $fmtNum($row['transactions']) }} transaksi
                        •
                        {{ $fmtNum($row['unit_today']) }} unit hari ini
                    </p>
                </div>

            </div>

            {{-- Right --}}
            <div class="flex items-center justify-end gap-3 text-right">

                <div>
                    <p class="mb-0.5 text-sm font-semibold text-success-600">
                        {{ $fmtNum($row['unit_current_month']) }} Unit
                    </p>

                    <p class="text-xs text-gray-500 dark:text-gray-400">
                        {{ $fmtRp($row['amount_current_month']) }}
                    </p>
                </div>

            </div>

        </div>

    </div>

    @empty
    <div class="py-10 text-center text-sm text-gray-400 dark:text-gray-500">
        Belum ada data ranking bulan ini.
    </div>
    @endforelse
</div>

    {{-- View more / less button --}}
    @if($total > 10)
    <div class="mt-4 border-t border-gray-100 pt-4 dark:border-gray-800">
       @if($total > 10)
<button
    @click="showAll = !showAll"
    class="mt-3 flex h-11 w-full items-center justify-center gap-2 rounded-lg
           border border-gray-200 px-4 py-3 text-sm font-medium
           text-gray-700 shadow-xs transition hover:bg-gray-100
           dark:border-gray-700 dark:bg-gray-800
           dark:text-gray-400 dark:hover:bg-gray-900">

    <span x-text="showAll ? 'Sembunyikan Ranking' : 'Lihat Semua {{ $total }} Sales'"></span>

    <svg x-bind:class="showAll ? 'rotate-180' : ''"
         class="transition-transform duration-200"
         width="16" height="16" viewBox="0 0 24 24" fill="none"
         stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="6 9 12 15 18 9"/>
    </svg>

</button>
@endif
    </div>
    @endif

</div>
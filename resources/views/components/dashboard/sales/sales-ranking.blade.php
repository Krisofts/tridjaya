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
    <div class="mb-5 flex items-center justify-between">
        <div>
            <p class="text-sm font-semibold text-gray-800 dark:text-white/90">Ranking Sales</p>
            <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">{{ $monthLabel }}</p>
        </div>

        {{-- Titik tiga --}}
        <div class="relative">
            <button @click="open = !open" @click.outside="open = false"
                class="flex h-8 w-8 items-center justify-center rounded-lg text-gray-400
                       hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-800 dark:hover:text-gray-300">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="12" cy="5"  r="1.5"/>
                    <circle cx="12" cy="12" r="1.5"/>
                    <circle cx="12" cy="19" r="1.5"/>
                </svg>
            </button>

            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-75"
                 x-transition:leave-start="opacity-100 scale-100"
                 x-transition:leave-end="opacity-0 scale-95"
                 class="absolute right-0 top-9 z-10 w-48 overflow-hidden rounded-xl border border-gray-200
                        bg-white shadow-lg dark:border-gray-700 dark:bg-gray-900"
                 style="display: none;">
                <button @click="showAll = true; open = false"
                    class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-left text-sm
                           text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                    Lihat semua {{ $total }} sales
                </button>
                <div class="border-t border-gray-100 dark:border-gray-800"></div>
                <button @click="showAll = false; open = false"
                    class="flex w-full items-center gap-2.5 px-3.5 py-2.5 text-left text-sm
                           text-gray-700 hover:bg-gray-50 dark:text-gray-300 dark:hover:bg-gray-800">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="18 15 12 9 6 15"/>
                    </svg>
                    Top 10 saja
                </button>
            </div>
        </div>
    </div>

    {{-- Column headers --}}
    <div class="mb-2 grid grid-cols-12 gap-2 px-1 text-xs font-medium text-gray-400 dark:text-gray-500">
        <div class="col-span-1">#</div>
        <div class="col-span-4">Sales</div>
        <div class="col-span-2 text-center">Unit hari ini</div>
        <div class="col-span-2 text-center">Unit bulan ini</div>
        <div class="col-span-3 text-right">Revenue</div>
    </div>

    {{-- Rows — semua dari Blade, Alpine hanya toggle visibility --}}
    <div class="divide-y divide-gray-100 dark:divide-gray-800">
        @forelse($allRankings as $i => $row)
        @php
            $pct   = $maxUnit > 0 ? round($row['unit_current_month'] / $maxUnit * 100) : 0;
            $color = $avatarColors[$i % count($avatarColors)];
            $isTop = $i < 10;
        @endphp

        <div class="grid grid-cols-12 items-center gap-2 py-3"
             @if(!$isTop) x-show="showAll" style="display: none;" @endif>

            {{-- Rank --}}
            <div class="col-span-1 text-sm font-medium">
                @if($i < 3) {{ $medals[$i] }}
                @else <span class="text-gray-400 dark:text-gray-500">{{ $row['ranking'] }}</span>
                @endif
            </div>

            {{-- Avatar + Name --}}
            <div class="col-span-4 flex min-w-0 items-center gap-2.5">
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full
                            text-xs font-semibold {{ $color['bg'] }} {{ $color['text'] }}">
                    {{ $initials($row['sales_name']) }}
                </div>
                <div class="min-w-0">
                    <p class="truncate text-sm font-medium text-gray-800 dark:text-white/90">
                        {{ $row['sales_name'] }}
                    </p>
                    <div class="mt-1 h-1 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-800">
                        <div class="h-full rounded-full" style="width: {{ $pct }}%; background: #465fff;"></div>
                    </div>
                </div>
            </div>

            {{-- Unit hari ini --}}
            <div class="col-span-2 text-center">
                <span class="text-sm font-medium text-gray-800 dark:text-white/90">
                    {{ $fmtNum($row['unit_today']) }}
                </span>
            </div>

            {{-- Unit bulan ini --}}
            <div class="col-span-2 text-center">
                <span class="text-sm font-semibold text-gray-800 dark:text-white/90">
                    {{ $fmtNum($row['unit_current_month']) }}
                </span>
            </div>

            {{-- Revenue --}}
            <div class="col-span-3 text-right">
                <span class="text-sm font-semibold text-gray-800 dark:text-white/90">
                    {{ $fmtRp($row['amount_current_month']) }}
                </span>
                <p class="mt-0.5 text-xs text-gray-400 dark:text-gray-500">
                    {{ $fmtNum($row['transactions']) }} trx
                </p>
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
        <button @click="showAll = !showAll"
            class="flex w-full items-center justify-center gap-1.5 rounded-xl border border-gray-200 py-2
                   text-xs font-medium text-gray-500 transition hover:bg-gray-50
                   dark:border-gray-800 dark:text-gray-400 dark:hover:bg-gray-800">
            <span x-text="showAll ? 'Sembunyikan' : 'Lihat semua {{ $total }} sales'"></span>
            <svg x-bind:class="showAll ? 'rotate-180' : ''"
                 class="transition-transform duration-200"
                 width="13" height="13" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="6 9 12 15 18 9"/>
            </svg>
        </button>
    </div>
    @endif

</div>
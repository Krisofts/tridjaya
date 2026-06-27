@php
use Illuminate\Support\Str;

$salesData     = $salesData ?? collect([]);
$totalUnitNow  = $salesData->sum('unit_current_month');
$totalUnitLast = $salesData->sum('unit_last_month');
$totalAmt      = $salesData->sum('amount_current_month');
$totalInc      = $salesData->sum('incentive_current_month');
$todayUnit     = $salesData->sum('unit_today');
$unitDelta     = $totalUnitNow - $totalUnitLast;
@endphp

{{-- KPI --}}
<div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Unit Bln Ini</p>
        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalUnitNow }}</p>
        <p class="mt-0.5 text-xs {{ $unitDelta >= 0 ? 'text-success-500' : 'text-red-500' }}">
            {{ $unitDelta >= 0 ? '▲' : '▼' }} {{ abs($unitDelta) }} unit vs bln lalu
        </p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Omzet Bln Ini</p>
        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalAmt, 0, ',', '.') }}</p>
        <p class="mt-0.5 text-xs text-gray-400">Akumulasi semua sales</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Total Insentif</p>
        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalInc, 0, ',', '.') }}</p>
        <p class="mt-0.5 text-xs text-gray-400">Bulan berjalan</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Unit Hari Ini</p>
        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $todayUnit }}</p>
        <p class="mt-0.5 text-xs text-gray-400">{{ now()->translatedFormat('d M Y') }}</p>
    </div>
</div>

<div x-data="{ view: 'month' }" class="space-y-4">

    <div class="flex items-center gap-2">
        <button @click="view='month'"
            class="rounded-lg px-3 py-1.5 text-xs font-medium transition"
            :class="view==='month'?'bg-brand-500 text-white':'border border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400'">
            vs Bulan Lalu
        </button>
        <button @click="view='target'"
            class="rounded-lg px-3 py-1.5 text-xs font-medium transition"
            :class="view==='target'?'bg-brand-500 text-white':'border border-gray-300 text-gray-600 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400'">
            vs Target
        </button>
    </div>

    {{-- VS BULAN LALU --}}
    <div x-show="view==='month'" class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Sales</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Hari Ini</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Bln Lalu</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Bln Ini</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Growth Unit</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Omzet Bln Ini</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Growth Omzet</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Insentif</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($salesData as $row)
                        @php
                            $name    = $row['sales_name'];
                            $uToday  = $row['unit_today'];
                            $uLast   = $row['unit_last_month'];
                            $uNow    = $row['unit_current_month'];
                            $gUnit   = $row['growth_unit_percent'];
                            $amt     = $row['amount_current_month'];
                            $gAmt    = $row['growth_amount_percent'];
                            $inc     = $row['incentive_current_month'];
                            $slug    = Str::slug($name);
                            $ini     = collect(explode(' ', $name))->take(2)->map(fn($w) => $w[0])->implode('');
                            $nameFmt = collect(explode(' ', $name))->map(fn($w) => ucfirst(strtolower($w)))->implode(' ');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-600 dark:bg-brand-500/20 dark:text-brand-400">{{ $ini }}</div>
                                    <span class="font-medium text-gray-800 dark:text-white">{{ $nameFmt }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-medium {{ $uToday > 0 ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400' }}">{{ $uToday }}</td>
                            <td class="px-4 py-3 text-right text-gray-400">{{ $uLast }}</td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white">{{ $uNow }}</td>
                            <td class="px-4 py-3 text-right">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $gUnit >= 0 ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400' : 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400' }}">
                                    {{ $gUnit >= 0 ? '+' : '' }}{{ number_format($gUnit, 1) }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($amt, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $gAmt >= 0 ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400' : 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400' }}">
                                    {{ $gAmt >= 0 ? '+' : '' }}{{ number_format($gAmt, 1) }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($inc, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('sales.performance.show', $slug) }}"
                                    class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">
                                    Detail
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-10 text-center text-sm text-gray-400">
                                Tidak ada data sales.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if ($salesData->count() > 0)
                <tfoot class="border-t-2 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/50">
                    <tr>
                        <td class="px-4 py-3 text-sm font-bold text-gray-800 dark:text-white">Total</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">{{ $todayUnit }}</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-gray-400">{{ $totalUnitLast }}</td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">{{ $totalUnitNow }}</td>
                        <td></td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalAmt, 0, ',', '.') }}</td>
                        <td></td>
                        <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalInc, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

    {{-- VS TARGET --}}
    <div x-show="view==='target'" class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-900/50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Sales</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Unit Bln Ini</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Target Unit</th>
                        <th class="px-4 py-3 text-xs font-medium text-gray-500" style="min-width:140px">Pencapaian</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Omzet</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Target Omzet</th>
                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Selisih Target</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                    @forelse ($salesData as $row)
                        @php
                            $name      = $row['sales_name'];
                            $slug      = Str::slug($name);
                            $uNow      = $row['unit_current_month'];
                            $tUnit     = $row['target_unit'];
                            $pct       = $row['achievement_percent'];
                            $amt       = $row['amount_current_month'];
                            $tAmt      = $row['target_amount'];
                            $diff      = $row['difference_to_target'];
                            $ini       = collect(explode(' ', $name))->take(2)->map(fn($w) => $w[0])->implode('');
                            $nameFmt   = collect(explode(' ', $name))->map(fn($w) => ucfirst(strtolower($w)))->implode(' ');
                            $pctCapped = min($pct, 100);
                            $barColor  = $pct >= 80 ? '#1baf7a' : ($pct >= 50 ? '#eda100' : '#e34948');
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-600 dark:bg-brand-500/20 dark:text-brand-400">{{ $ini }}</div>
                                    <span class="font-medium text-gray-800 dark:text-white">{{ $nameFmt }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white">{{ $uNow }}</td>
                            <td class="px-4 py-3 text-right text-gray-400">{{ $tUnit > 0 ? $tUnit : '-' }}</td>
                            <td class="px-4 py-3">
                                @if ($tUnit > 0)
                                    <div class="flex items-center gap-2">
                                        <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                            <div class="h-full rounded-full" style="width:{{ $pctCapped }}%;background:{{ $barColor }}"></div>
                                        </div>
                                        <span class="text-xs font-medium" style="color:{{ $barColor }};min-width:40px;text-align:right">
                                            {{ number_format($pct, 1) }}%
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Belum ada target</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($amt, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-gray-400">{{ $tAmt > 0 ? 'Rp ' . number_format($tAmt, 0, ',', '.') : '-' }}</td>
                            <td class="px-4 py-3 text-right">
                                @if ($tUnit > 0)
                                    <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $diff >= 0 ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400' : 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400' }}">
                                        {{ $diff >= 0 ? '+' : '' }}{{ number_format($diff, 0) }} unit
                                    </span>
                                @else
                                    <span class="text-xs text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('sales.performance.show', $slug) }}"
                                    class="inline-flex items-center gap-1 rounded-lg border border-gray-200 px-2.5 py-1 text-xs font-medium text-gray-600 transition hover:bg-gray-50 dark:border-gray-700 dark:text-gray-400">
                                    Detail
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-400">Tidak ada data sales.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
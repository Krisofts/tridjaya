@php
$cabangData    = $cabangData ?? collect([]);
$totalUnit     = $cabangData->sum('unit_current_month');
$totalUnitLast = $cabangData->sum('unit_last_month');
$totalAmt      = $cabangData->sum('amount_current_month');
$todayUnit     = $cabangData->sum('today_unit');
$unitDelta     = $totalUnit - $totalUnitLast;
@endphp

{{-- KPI --}}
<div class="mb-5 grid grid-cols-2 gap-3 sm:grid-cols-4">
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Unit Hari Ini</p>
        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $todayUnit }}</p>
        <p class="mt-0.5 text-xs text-gray-400">{{ now()->translatedFormat('d M Y') }}</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Unit Bln Ini</p>
        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $totalUnit }}</p>
        <p class="mt-0.5 text-xs {{ $unitDelta >= 0 ? 'text-success-500' : 'text-red-500' }}">
            {{ $unitDelta >= 0 ? '▲' : '▼' }} {{ abs($unitDelta) }} vs bln lalu
        </p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Omzet Bln Ini</p>
        <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-white">Rp {{ number_format($totalAmt, 0, ',', '.') }}</p>
        <p class="mt-0.5 text-xs text-gray-400">Akumulasi semua cabang</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Total Cabang</p>
        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $cabangData->count() }}</p>
        <p class="mt-0.5 text-xs text-gray-400">Aktif bulan ini</p>
    </div>
</div>

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Cabang / Dealer</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Hari Ini</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Bln Lalu</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Bln Ini</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Growth Unit</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Omzet Bln Ini</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Growth Omzet</th>
                    <th class="px-4 py-3 text-xs font-medium text-gray-500" style="min-width:130px">Pencapaian</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($cabangData as $row)
                    @php
                        $name      = $row['dealer_name'];
                        $uToday    = $row['today_unit'];
                        $uLast     = $row['unit_last_month'];
                        $uNow      = $row['unit_current_month'];
                        $gUnit     = $row['growth_unit_percent'];
                        $amt       = $row['amount_current_month'];
                        $gAmt      = $row['growth_amount_percent'];
                        $pct       = $row['achievement_percent'];
                        $tUnit     = $row['target_unit'];
                        $pctCapped = min($pct, 100);
                        $barColor  = $pct >= 80 ? '#1baf7a' : ($pct >= 50 ? '#eda100' : '#e34948');
                        $ini       = collect(explode(' ', $name))->take(2)->map(fn($w) => $w[0])->implode('');
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-brand-100 text-xs font-semibold text-brand-600 dark:bg-brand-500/20 dark:text-brand-400">{{ $ini }}</div>
                                <span class="font-medium text-gray-800 dark:text-white">{{ $name }}</span>
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
                        <td class="px-4 py-3">
                            @if ($tUnit > 0)
                                <div class="flex items-center gap-2">
                                    <div class="h-1.5 flex-1 overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700">
                                        <div class="h-full rounded-full" style="width:{{ $pctCapped }}%;background:{{ $barColor }}"></div>
                                    </div>
                                    <span class="text-xs font-medium" style="color:{{ $barColor }};min-width:36px;text-align:right">
                                        {{ number_format($pct, 1) }}%
                                    </span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400">Belum ada target</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-10 text-center text-sm text-gray-400">
                            Tidak ada data cabang.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            @if ($cabangData->count() > 0)
            <tfoot class="border-t-2 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/50">
                <tr>
                    <td class="px-4 py-3 text-sm font-bold text-gray-800 dark:text-white">Total</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">{{ $todayUnit }}</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-400">{{ $totalUnitLast }}</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">{{ $totalUnit }}</td>
                    <td></td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalAmt, 0, ',', '.') }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>
</div>
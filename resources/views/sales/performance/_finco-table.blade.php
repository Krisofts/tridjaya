@php
$fincoData = $fincoData ?? collect([]);
$totalUnit = $fincoData->sum('unit_current_month');
$totalAmt  = $fincoData->sum('amount_current_month');
$todayUnit = $fincoData->sum('unit_today');
$unitLast  = $fincoData->sum('unit_last_month');
$unitDelta = $totalUnit - $unitLast;

$makeInitials = function(string $name): string {
    if ($name === '') return '?';
    return collect(explode(' ', $name))
        ->filter(fn($w) => strlen($w) > 0)
        ->take(2)
        ->map(fn($w) => $w[0])
        ->implode('');
};
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
        <p class="mt-0.5 text-xs text-gray-400">Akumulasi semua finco</p>
    </div>
    <div class="rounded-2xl border border-gray-200 bg-white p-4 dark:border-gray-800 dark:bg-gray-900">
        <p class="text-xs font-medium uppercase tracking-wide text-gray-400">Total Finco</p>
        <p class="mt-1 text-2xl font-semibold text-gray-900 dark:text-white">{{ $fincoData->count() }}</p>
        <p class="mt-0.5 text-xs text-gray-400">Aktif bulan ini</p>
    </div>
</div>

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 dark:bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500">Finco / Leasing</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Hari Ini</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Bln Lalu</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Bln Ini</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Omzet Bln Ini</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Omzet Bln Lalu</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500">Growth</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse ($fincoData as $row)
                    @php
                        $name    = (string) ($row['finco_name'] ?? '');
                        $uToday  = (int)   ($row['unit_today']           ?? 0);
                        $uLast   = (int)   ($row['unit_last_month']      ?? 0);
                        $uNow    = (int)   ($row['unit_current_month']   ?? 0);
                        $amt     = (float) ($row['amount_current_month'] ?? 0);
                        $amtLast = (float) ($row['amount_last_month']    ?? 0);
                        $growth  = (float) ($row['growth_percent']       ?? 0);
                        $ini     = $makeInitials($name);
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2.5">
                                <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-purple-100 text-xs font-semibold text-purple-600 dark:bg-purple-500/20 dark:text-purple-400">
                                    {{ $ini }}
                                </div>
                                <span class="font-medium text-gray-800 dark:text-white">{{ $name ?: '-' }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-right font-medium {{ $uToday > 0 ? 'text-brand-600 dark:text-brand-400' : 'text-gray-400' }}">{{ $uToday }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">{{ $uLast }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-gray-800 dark:text-white">{{ $uNow }}</td>
                        <td class="px-4 py-3 text-right text-gray-700 dark:text-gray-300">Rp {{ number_format($amt, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-gray-400">Rp {{ number_format($amtLast, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">
                            <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium
                                {{ $growth >= 0 ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-400' : 'bg-red-50 text-red-600 dark:bg-red-500/15 dark:text-red-400' }}">
                                {{ $growth >= 0 ? '+' : '' }}{{ number_format($growth, 1) }}%
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-400">
                            Tidak ada data finco.
                        </td>
                    </tr>
                @endforelse
            </tbody>

            @if ($fincoData->count() > 0)
            <tfoot class="border-t-2 border-gray-200 bg-gray-50 dark:border-gray-700 dark:bg-gray-900/50">
                <tr>
                    <td class="px-4 py-3 text-sm font-bold text-gray-800 dark:text-white">Total</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">{{ $todayUnit }}</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-400">{{ $unitLast }}</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">{{ $totalUnit }}</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-800 dark:text-white">Rp {{ number_format($totalAmt, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-sm font-bold text-gray-400">Rp {{ number_format($fincoData->sum('amount_last_month'), 0, ',', '.') }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif

        </table>
    </div>
</div>
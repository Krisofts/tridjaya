@props([
    'sales'
])

<div class="overflow-hidden rounded-2xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-white/[0.03]">

    {{-- HEADER --}}
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            Sales Overview
        </h3>

        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
            Today performance vs yesterday
        </p>
    </div>

    {{-- MAIN KPI --}}
    <div>
        <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
            Rp {{ number_format($sales['revenue']['today'], 0, ',', '.') }}
        </h2>

        <p class="mt-2 text-sm">
            <span class="font-semibold {{ $sales['revenue']['growth_pct'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                {{ $sales['revenue']['growth_pct'] >= 0 ? '▲' : '▼' }}
                {{ number_format(abs($sales['revenue']['growth_pct']), 2) }}%
            </span>

            <span class="text-gray-500 dark:text-gray-400">
                than yesterday
            </span>
        </p>

        <p class="mt-2 text-xs text-gray-400">
            Yesterday:
            <span class="text-gray-600 dark:text-gray-300">
                Rp {{ number_format($sales['revenue']['yesterday'], 0, ',', '.') }}
            </span>
        </p>
    </div>

    {{-- KPI GRID --}}
    <div class="mt-8 flex gap-4 overflow-x-auto sm:grid sm:grid-cols-3 sm:overflow-visible">

        {{-- Transactions --}}
        <div class="min-w-[160px] flex-1 rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/40">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Transactions
            </p>

            <h4 class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                {{ number_format($sales['transaction']['today']) }}
            </h4>

            <p class="mt-1 text-xs font-medium {{ $sales['transaction']['growth_pct'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                {{ $sales['transaction']['growth_pct'] >= 0 ? '▲' : '▼' }}
                {{ number_format(abs($sales['transaction']['growth_pct']), 2) }}%
            </p>
        </div>

        {{-- Units Sold --}}
        <div class="min-w-[160px] flex-1 rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/40">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Units Sold
            </p>

            <h4 class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                {{ number_format($sales['unit']['today']) }}
            </h4>

            <p class="mt-1 text-xs font-medium {{ $sales['unit']['growth_pct'] >= 0 ? 'text-green-500' : 'text-red-500' }}">
                {{ $sales['unit']['growth_pct'] >= 0 ? '▲' : '▼' }}
                {{ number_format(abs($sales['unit']['growth_pct']), 2) }}%
            </p>
        </div>

        {{-- Average Transaction --}}
        <div class="min-w-[160px] flex-1 rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-800/40">
            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">
                Avg / Trx
            </p>

            <h4 class="mt-2 text-xl font-bold text-gray-900 dark:text-white">
                {{ number_format($sales['avg_transaction'], 0, ',', '.') }}
            </h4>

            <p class="mt-1 text-xs text-gray-400">
                per transaction
            </p>
        </div>

    </div>
</div>
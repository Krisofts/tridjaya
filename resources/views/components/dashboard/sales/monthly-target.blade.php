@props([
    'target'
])

<div class="rounded-2xl border border-gray-200 bg-gray-100 dark:border-gray-800 dark:bg-white/[0.03]">
  <div class="shadow-default rounded-2xl bg-white px-5 pb-8 pt-5 dark:bg-gray-900 sm:px-6 sm:pt-6">

    {{-- HEADER --}}
    <div class="flex justify-between">
      <div>
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
          Monthly Target
        </h3>
        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
          Target you’ve set for this month
        </p>
      </div>
    </div>

    {{-- MAIN KPI --}}
    <div class="mt-6">
      <h2 class="text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
        {{ number_format($target['achievement_pct'], 2) }}%
      </h2>

      <p class="mt-2 text-sm">
        <span class="font-semibold
          @if($target['status'] === 'ACHIEVED') text-green-500
          @elseif($target['status'] === 'CRITICAL') text-red-500
          @elseif($target['status'] === 'WARNING') text-amber-500
          @else text-blue-500
          @endif">
          {{ $target['status'] }}
        </span>

        <span class="text-gray-500 dark:text-gray-400">
          achievement status
        </span>
      </p>
    </div>

    {{-- PROGRESS BAR --}}
    <div class="mt-5">
      <div class="h-3 w-full rounded-full bg-gray-200 dark:bg-gray-800 overflow-hidden">
        <div
          class="h-full rounded-full transition-all duration-500
          @if($target['status'] === 'ACHIEVED')
              bg-green-500
          @elseif($target['status'] === 'CRITICAL')
              bg-red-500
          @elseif($target['status'] === 'WARNING')
              bg-amber-500
          @else
              bg-blue-500
          @endif"
          style="width: {{ min($target['achievement_pct'], 100) }}%"
        ></div>
      </div>
    </div>

    {{-- SUB TEXT --}}
    <p class="mt-4 text-center text-sm text-gray-500">
      Rp {{ number_format($target['actual'], 0, ',', '.') }}
      / Rp {{ number_format($target['target'], 0, ',', '.') }}
    </p>

  </div>

  {{-- FOOTER --}}
  <div class="flex items-center justify-center gap-5 px-6 py-4 sm:gap-8">

    {{-- Remaining --}}
    <div class="text-center">
      <p class="text-xs text-gray-500">Remaining</p>
      <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
        Rp {{ number_format($target['remaining_revenue'], 0, ',', '.') }}
      </p>
    </div>

    <div class="h-6 w-px bg-gray-200 dark:bg-gray-800"></div>

    {{-- Days --}}
    <div class="text-center">
      <p class="text-xs text-gray-500">Days Left</p>
      <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
        {{ $target['remaining_days'] }}
      </p>
    </div>

    <div class="h-6 w-px bg-gray-200 dark:bg-gray-800"></div>

    {{-- Need / Day --}}
    <div class="text-center">
      <p class="text-xs text-gray-500">Need / Day</p>
      <p class="text-sm font-semibold text-gray-800 dark:text-white/90">
        Rp {{ number_format($target['needed_per_day'], 0, ',', '.') }}
      </p>
    </div>

  </div>
</div>
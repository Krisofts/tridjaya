@props([
    'title' => 'Filter',
    'width' => 'w-56',
])

<div
    class="relative"
    x-data="{ showFilter: false }"
>

    {{-- BUTTON --}}
    <button
        type="button"
        @click="showFilter = !showFilter"
        class="shadow-theme-xs flex h-11 w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 sm:w-auto sm:min-w-[100px] dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400"
    >
        <x-icons.filter />

        {{ $title }}
    </button>

    {{-- DROPDOWN --}}
    <div
        x-show="showFilter"
        x-transition
        @click.away="showFilter = false"
        class="absolute right-0 z-10 mt-2 {{ $width }} rounded-lg border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800"
        style="display:none"
    >
        {{ $slot }}
    </div>

</div>
@props([
    'paginator', // Laravel paginator instance
])

<div class="flex flex-col items-center justify-between border-t border-gray-200 px-5 py-4 sm:flex-row dark:border-gray-800">

    {{-- INFO --}}
    <div class="pb-3 sm:pb-0">
        <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
            Showing
            <span class="text-gray-800 dark:text-white/90">
                {{ $paginator->firstItem() ?? 0 }}
            </span>
            to
            <span class="text-gray-800 dark:text-white/90">
                {{ $paginator->lastItem() ?? 0 }}
            </span>
            of
            <span class="text-gray-800 dark:text-white/90">
                {{ $paginator->total() }}
            </span>
        </span>
    </div>

    {{-- PAGINATION --}}
    <div class="flex w-full items-center justify-between gap-2 rounded-lg bg-gray-50 p-4 sm:w-auto sm:justify-normal sm:rounded-none sm:bg-transparent sm:p-0 dark:bg-gray-900 dark:sm:bg-transparent">

        {{-- PREV --}}
        <a
            href="{{ $paginator->previousPageUrl() }}"
            class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 disabled:opacity-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">

            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M2.58 9.99L7.79 15.53c.29.29.76.29 1.06 0 .29-.29.29-.76 0-1.06L5.14 10.75H16.66c.41 0 .75-.33.75-.75s-.34-.75-.75-.75H5.14l3.71-3.72c.29-.29.29-.76 0-1.06a.75.75 0 00-1.06 0L2.58 9.99z"/>
            </svg>
        </a>

        {{-- MOBILE INFO --}}
        <span class="block text-sm font-medium text-gray-700 sm:hidden dark:text-gray-400">
            Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
        </span>

        {{-- PAGE NUMBERS --}}
        <ul class="hidden items-center gap-0.5 sm:flex">

            @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
                <li>
                    <a
                        href="{{ $url }}"
                        class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium
                        {{ $paginator->currentPage() == $page
                            ? 'bg-brand-500 text-white'
                            : 'text-gray-700 hover:bg-brand-500 hover:text-white dark:text-gray-400 dark:hover:text-white' }}">
                        {{ $page }}
                    </a>
                </li>
            @endforeach

        </ul>

        {{-- NEXT --}}
        <a
            href="{{ $paginator->nextPageUrl() }}"
            class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">

            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M17.41 10.0L12.2 4.47a.75.75 0 10-1.06 1.06l3.71 3.72H3.33a.75.75 0 000 1.5h11.52l-3.71 3.72a.75.75 0 001.06 1.06L17.41 10z"/>
            </svg>
        </a>

    </div>

</div>
@if ($paginator->hasPages())
    <div
        class="flex w-full flex-col items-center justify-between border-t border-gray-200 px-5 py-4 sm:flex-row dark:border-gray-800">

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
        <div
            class="flex w-full items-center justify-between gap-2 rounded-lg bg-gray-50 p-4 sm:w-auto sm:justify-normal sm:bg-transparent sm:p-0 dark:bg-white/[0.03] dark:sm:bg-transparent">

            {{-- PREVIOUS --}}
            @if ($paginator->onFirstPage())
                <span
                    class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-400 opacity-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                        <path
                            d="M8.85921 14.4699L5.13911 10.7472H16.6665C17.0807 10.7472 17.4165 10.4114 17.4165 9.99715C17.4165 9.58294 17.0807 9.24715 16.6665 9.24715H5.14456L8.85919 5.53016C9.15199 5.23717 9.15184 4.7623 8.85885 4.4695C8.56587 4.1767 8.09099 4.17685 7.79819 4.46984L2.84069 9.43049C2.68224 9.568 2.58203 9.77087 2.58203 9.99715C2.58203 10.1909 2.6549 10.3833 2.80152 10.53L7.79818 15.5301C8.09097 15.8231 8.56584 15.8233 8.85883 15.5305C9.15183 15.2377 9.152 14.7629 8.85921 14.4699Z" />
                    </svg>
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}"
                    class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                        <path
                            d="M8.85921 14.4699L5.13911 10.7472H16.6665C17.0807 10.7472 17.4165 10.4114 17.4165 9.99715C17.4165 9.58294 17.0807 9.24715 16.6665 9.24715H5.14456L8.85919 5.53016C9.15199 5.23717 9.15184 4.7623 8.85885 4.4695C8.56587 4.1767 8.09099 4.17685 7.79819 4.46984L2.84069 9.43049C2.68224 9.568 2.58203 9.77087 2.58203 9.99715C2.58203 10.1909 2.6549 10.3833 2.80152 10.53L7.79818 15.5301C8.09097 15.8231 8.56584 15.8233 8.85883 15.5305C9.15183 15.2377 9.152 14.7629 8.85921 14.4699Z" />
                    </svg>
                </a>
            @endif

            {{-- MOBILE PAGE INFO --}}
            <span class="block text-sm font-medium text-gray-700 sm:hidden dark:text-gray-400">
                Page {{ $paginator->currentPage() }} of {{ $paginator->lastPage() }}
            </span>

            {{-- PAGE NUMBERS --}}
            <ul class="hidden items-center gap-0.5 sm:flex">
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <li>
                            <span
                                class="flex h-10 w-10 items-center justify-center text-sm text-gray-400">
                                {{ $element }}
                            </span>
                        </li>
                    @endif

                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            <li>
                                <a href="{{ $url }}"
                                    class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium
                                    {{ $page == $paginator->currentPage()
                                        ? 'bg-brand-500 text-white'
                                        : 'text-gray-700 hover:bg-brand-500 hover:text-white dark:text-gray-400 dark:hover:text-white' }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                @endforeach
            </ul>

            {{-- NEXT --}}
            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}"
                    class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                        <path
                            d="M17.197 10.53L12.2004 15.5301C11.9076 15.8231 11.4327 15.8233 11.1397 15.5305C10.8467 15.2377 10.8465 14.7629 11.1393 14.4699L14.8594 10.7472H3.33203C2.91782 10.7472 2.58203 10.4114 2.58203 9.99715C2.58203 9.58294 2.91782 9.24715 3.33203 9.24715H14.854L11.1393 5.53016C10.8465 5.23717 10.8467 4.7623 11.1397 4.4695C11.4327 4.1767 11.9075 4.17685 12.2003 4.46984L17.1578 9.43049C17.3163 9.568 17.4165 9.77087 17.4165 9.99715C17.4165 10.1909 17.3437 10.3832 17.197 10.53Z" />
                    </svg>
                </a>
            @else
                <span
                    class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-400 opacity-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800">
                    <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                        <path
                            d="M17.197 10.53L12.2004 15.5301C11.9076 15.8231 11.4327 15.8233 11.1397 15.5305C10.8467 15.2377 10.8465 14.7629 11.1393 14.4699L14.8594 10.7472H3.33203C2.91782 10.7472 2.58203 10.4114 2.58203 9.99715C2.58203 9.58294 2.91782 9.24715 3.33203 9.24715H14.854L11.1393 5.53016C10.8465 5.23717 10.8467 4.7623 11.1397 4.4695C11.4327 4.1767 11.9075 4.17685 12.2003 4.46984L17.1578 9.43049C17.3163 9.568 17.4165 9.77087 17.4165 9.99715C17.4165 10.1909 17.3437 10.3832 17.197 10.53Z" />
                    </svg>
                </span>
            @endif

        </div>
    </div>
@endif
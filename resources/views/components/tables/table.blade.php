<div class="rounded-2xl border border-gray-200 bg-white pt-4 dark:border-gray-800 dark:bg-white/[0.03]">

    {{-- HEADER --}}
    @if($title || isset($header) || isset($actions))
        <div class="flex flex-col gap-2 px-5 mb-4 sm:flex-row sm:items-center sm:justify-between sm:px-6">

            <div>
                @if($title)
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                        {{ $title }}
                    </h3>
                @endif

                {{-- optional custom header content --}}
                {{ $header ?? '' }}
            </div>

            {{-- actions slot (button, filter, search, dll) --}}
            @isset($actions)
                <div class="flex items-center gap-2">
                    {{ $actions }}
                </div>
            @endisset

        </div>
    @endif

    {{-- TABLE --}}
    <div class="max-w-full px-5 overflow-x-auto">
        <table class="min-w-full">
            {{ $slot }}
        </table>
    </div>

    {{-- FOOTER (pagination, summary, dll) --}}
    @isset($footer)
        <div class="px-6 py-4 border-t border-gray-200 dark:border-white/[0.05]">
            {{ $footer }}
        </div>
    @endisset

</div>
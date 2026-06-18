@props([
    'title' => null,
    'description' => null,
])

<div class="rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    {{-- HEADER --}}
    <div class="flex flex-col justify-between gap-5 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center dark:border-gray-800">

        <div>
            @if($title)
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    {{ $title }}
                </h3>
            @endif

            @if($description)
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $description }}
                </p>
            @endif
        </div>

        @isset($header)
            <div class="flex gap-3">
                {{ $header }}
            </div>
        @endisset

    </div>

    {{-- TOOLBAR (SEARCH + FILTER) --}}
    @isset($toolbar)
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">
            {{ $toolbar }}
        </div>
    @endisset

    {{-- TABLE --}}
    <div class="custom-scrollbar overflow-x-auto">
        <table class="w-full table-auto">

            {{ $slot }}

        </table>
    </div>

    {{-- FOOTER / PAGINATION --}}
    @isset($footer)

            {{ $footer }}
        
    @endisset

</div>
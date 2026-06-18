@props(['title', 'desc' => null])

<div
    {{ $attributes->merge([
        'class' => 'rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]',
    ]) }}>

    {{-- HEADER --}}
    <div
        class="flex flex-col justify-between gap-5 border-b border-gray-200 px-5 py-4 sm:flex-row sm:items-center dark:border-gray-800">

        {{-- LEFT SIDE --}}
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                {{ $title }}
            </h3>

            @if ($desc)
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ $desc }}
                </p>
            @endif
        </div>

        {{-- RIGHT SIDE (ACTION SLOT) --}}
        @isset($actions)
            <div class="flex gap-3">
                {{ $actions }}
            </div>
        @endisset

    </div>

    {{-- BODY --}}
    
            {{ $slot }}
     

</div>

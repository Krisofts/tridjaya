@props([
    'name',
    'type' => 'text',
    'placeholder' => '',
    'value' => '',
    'icon' => null,
])

<div class="w-full px-2.5">
    <div class="relative">

        @if($icon)
            <span
                class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                {!! $icon !!}
            </span>
        @endif

        <input
            type="{{ $type }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $attributes->merge([
                'class' =>
                    'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 '
                    . ($icon ? 'pl-11' : '')
            ]) }}
        >

    </div>

    @error($name)
        <p class="mt-1 text-sm text-red-500">
            {{ $message }}
        </p>
    @enderror
</div>
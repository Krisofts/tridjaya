@props([
    'label' => null,
    'placeholder' => '',
    'type' => 'text',
    'model' => null,
])

<div class="w-full">

    {{-- LABEL --}}
    @if($label)
        <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    {{-- INPUT --}}
    <input
        type="{{ $type }}"
        placeholder="{{ $placeholder }}"
        @if($model) x-model="{{ $model }}" @endif
        {{ $attributes->merge([
            'class' => 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30'
        ]) }}
    >
</div>
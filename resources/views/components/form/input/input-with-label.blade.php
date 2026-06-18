@php
    $required ??= false;
    $readonly ??= false;
    $hint ??= null;
    $error ??= null;

    $hasError = $error || $errors->has($name);
@endphp

<div>
    @if ($label)
        <label
            for="{{ $name }}"
            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
        >
            {{ $label }}

            @if ($required)
                <span class="text-error-500">*</span>
            @endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ $value }}"
        placeholder="{{ $placeholder }}"
        @required($required)
        @readonly($readonly)
        {{ $attributes->merge([
            'class' =>
                'dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm placeholder:text-gray-400 focus:ring-3 focus:outline-hidden ' .
                ($hasError
                    ? 'border-error-500 focus:border-error-500 focus:ring-error-500/10 dark:border-error-500'
                    : 'border-gray-300 text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-800')
        ]) }}
    />

    @if ($hint && ! $hasError)
        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
            {{ $hint }}
        </p>
    @endif

    @if ($error)
        <p class="mt-1.5 text-xs text-error-500">
            {{ $error }}
        </p>
    @elseif ($errors->has($name))
        <p class="mt-1.5 text-xs text-error-500">
            {{ $errors->first($name) }}
        </p>
    @endif
</div>
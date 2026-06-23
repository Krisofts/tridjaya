@php
    $required ??= false;
    $readonly ??= false;
    $hint ??= null;
    $type ??= 'text';
    $value ??= old($name);

    $hasError = $errors->has($name);
    $errorMessage = $errors->first($name);

    $isPassword = $type === 'password';
@endphp

<div @if($isPassword) x-data="{ showPassword: false }" @endif>

    {{-- LABEL --}}
    @if (!empty($label))
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

    {{-- INPUT --}}
    <div class="relative">
        <input
            id="{{ $name }}"
            name="{{ $name }}"
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder ?? '' }}"
            @required($required)
            @readonly($readonly)

            @if ($isPassword)
                :type="showPassword ? 'text' : 'password'"
            @else
                type="{{ $type }}"
            @endif

            aria-invalid="{{ $hasError ? 'true' : 'false' }}"

            {{ $attributes->merge([
                'class' =>
                    'dark:bg-dark-900 shadow-theme-xs h-11 w-full rounded-lg border bg-transparent px-4 py-2.5 text-sm placeholder:text-gray-400 focus:outline-none focus:ring-2 pr-10 transition ' .
                    ($hasError
                        ? 'border-error-300 focus:border-error-300 focus:ring-error-500/10 dark:border-error-700 dark:focus:border-error-800'
                        : 'border-gray-300 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700')
            ]) }}
        />

        {{-- RIGHT ICON AREA (ONLY PASSWORD TOGGLE) --}}
        @if ($isPassword)
            <div class="absolute top-1/2 right-3.5 -translate-y-1/2 flex items-center">
                <button
                    type="button"
                    @click="showPassword = !showPassword"
                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400"
                >
                    <span x-show="!showPassword">
                        <x-icons.eye class="h-5 w-5" />
                    </span>

                    <span x-show="showPassword" x-cloak>
                        <x-icons.eye-off class="h-5 w-5" />
                    </span>
                </button>
            </div>
        @endif
    </div>

    {{-- ERROR / HINT --}}
    @if ($hasError)
        <p class="mt-1.5 text-xs text-error-500">
            {{ $errorMessage }}
        </p>
    @elseif ($hint)
        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">
            {{ $hint }}
        </p>
    @endif

</div>
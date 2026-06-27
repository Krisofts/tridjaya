@php
    $required ??= false;
    $disabled ??= false;
    $multiple ??= false;
    $hint     ??= null;
    $error    ??= null;
    $selected ??= null;
    $options  ??= [];

    // Normalise options ke format ['value' => 'label'] agar komponen
    // bisa terima dua format sekaligus:
    //   - Format lama : ['value' => 'label', ...]
    //   - Format baru : [['value' => ..., 'label' => ...], ...]
    $normalised = collect($options)->mapWithKeys(function ($item, $key) {
        if (is_array($item) && array_key_exists('value', $item) && array_key_exists('label', $item)) {
            return [$item['value'] => $item['label']];
        }
        return [$key => $item];
    })->toArray();

    $hasError = $error || $errors->has($name);
@endphp

<div>

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

    {{-- SELECT --}}
    <div
        x-data="{ isOptionSelected: @js(!empty($selected)) }"
        class="relative z-20 bg-transparent"
    >
        <select
            id="{{ $name }}"
            name="{{ $multiple ? $name . '[]' : $name }}"
            @required($required)
            @disabled($disabled)
            @if ($multiple) multiple @endif
            @change="isOptionSelected = true"
            {{ $attributes->merge([
                'class' =>
                    'dark:bg-dark-900 shadow-theme-xs h-11 w-full appearance-none rounded-lg bg-transparent px-4 py-2.5 pr-11 text-sm focus:ring-3 focus:outline-hidden ' .
                    ($hasError
                        ? 'border border-error-500 focus:border-error-500 focus:ring-error-500/10'
                        : 'border border-gray-300 text-gray-800 focus:border-brand-300 focus:ring-brand-500/10 dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-800'
                    )
            ]) }}
            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
        >
            @unless ($multiple)
                <option value="">{{ $placeholder ?? '' }}</option>
            @endunless

            @foreach ($normalised as $value => $text)
                <option
                    value="{{ $value }}"
                    class="text-gray-700 dark:bg-gray-900 dark:text-gray-400"
                    @selected(
                        $multiple
                            ? in_array((string) $value, array_map('strval', (array) $selected))
                            : (string) $selected === (string) $value
                    )
                >
                    {{ $text }}
                </option>
            @endforeach
        </select>

        {{-- CHEVRON --}}
        @unless ($multiple)
            <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        @endunless
    </div>

    {{-- HINT --}}
    @if ($hint && ! $hasError)
        <p class="mt-1.5 text-xs text-gray-500 dark:text-gray-400">{{ $hint }}</p>
    @endif

    {{-- ERROR --}}
    @if ($error)
        <p class="mt-1.5 text-xs text-error-500">{{ $error }}</p>
    @elseif ($errors->has($name))
        <p class="mt-1.5 text-xs text-error-500">{{ $errors->first($name) }}</p>
    @endif

</div>
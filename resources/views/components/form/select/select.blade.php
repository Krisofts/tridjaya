@props([
    'label' => null,
    'name',
    'options' => [],
    'valueField' => 'id',
    'labelField' => 'name',
    'selected' => null,
    'placeholder' => 'Select Option',
    'required' => false,
    'disabled' => false,
])

<div>
    @if($label)
        <label
            for="{{ $name }}"
            class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400"
        >
            {{ $label }}

            @if($required)
                <span class="text-error-500">*</span>
            @endif
        </label>
    @endif

    <div
        x-data="{ isOptionSelected: @js(!empty($selected)) }"
        class="relative z-20 bg-transparent"
    >
        <select
            id="{{ $name }}"
            name="{{ $name }}"
            @disabled($disabled)
            @required($required)
            @change="isOptionSelected = true"
            {{ $attributes->merge([
                'class' => 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90'
            ]) }}
            :class="isOptionSelected && 'text-gray-800 dark:text-white/90'"
        >
            <option value="">
                {{ $placeholder }}
            </option>

            @foreach($options as $option)
                @php
                    $value = is_array($option)
                        ? $option[$valueField]
                        : $option->{$valueField};

                    $text = is_array($option)
                        ? $option[$labelField]
                        : $option->{$labelField};
                @endphp

                <option
                    value="{{ $value }}"
                    @selected((string)$selected === (string)$value)
                >
                    {{ $text }}
                </option>
            @endforeach
        </select>

        <span
            class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400"
        >
            <svg
                class="stroke-current"
                width="20"
                height="20"
                viewBox="0 0 20 20"
                fill="none"
            >
                <path
                    d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </span>
    </div>

    @error($name)
        <p class="mt-1 text-sm text-error-500">
            {{ $message }}
        </p>
    @enderror
</div>
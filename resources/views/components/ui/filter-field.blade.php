@props([
    'label' => null,
    'type' => 'text', // text | select
    'placeholder' => '',
    'model' => null,
    'options' => [],
    'optionLabel' => 'label',
    'optionValue' => 'value',
    'selected' => null,
])

<div class="mb-5 w-full">

    {{-- LABEL --}}
    @if($label)
        <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
            {{ $label }}
        </label>
    @endif

    {{-- INPUT TEXT --}}
    @if($type === 'text')
        <input
            type="text"
            placeholder="{{ $placeholder }}"
            @if($model) x-model="{{ $model }}" @endif
            {{ $attributes->merge([
                'class' => 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30'
            ]) }}
        >
    @endif

    {{-- SELECT --}}
    @if($type === 'select')
        <div class="relative">

            <select
                @if($model) x-model="{{ $model }}" @endif
                {{ $attributes->merge([
                    'class' => 'dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-10 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90'
                ]) }}
            >

                {{-- placeholder --}}
                @if($placeholder)
                    <option value="">{{ $placeholder }}</option>
                @endif

                {{-- options --}}
                @foreach($options as $option)
                    @php
                        $value = $option[$optionValue] ?? null;
                        $labelText = $option[$optionLabel] ?? null;
                    @endphp

                    <option
                        value="{{ $value }}"
                        @selected((string) $selected === (string) $value)
                    >
                        {{ $labelText }}
                    </option>
                @endforeach

            </select>

            {{-- ICON DROPDOWN --}}
            <div class="pointer-events-none absolute inset-y-0 right-3 flex items-center text-gray-500 dark:text-gray-400">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none">
                    <path
                        d="M6 8l4 4 4-4"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                    />
                </svg>
            </div>

        </div>
    @endif

</div>
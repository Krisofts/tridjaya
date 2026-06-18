<div
    x-data="{
        selected: @json(old($name, $selected ?? null)),
        isOptionSelected: @json(!empty(old($name, $selected ?? null)))
    }"
>
    @if($label)
        <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
            {{ $label }}
        </label>
    @endif

    <div class="relative z-20 bg-transparent">
        <select
            name="{{ $name }}"
            @change="isOptionSelected = true"
            x-model="selected"
            class="h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
            :class="isOptionSelected ? 'text-gray-800 dark:text-white/90' : 'text-gray-400 dark:text-white/50'"
        >
            <option value="">
                {{ $placeholder }}
            </option>

            @foreach($options as $value => $text)
                <option value="{{ $value }}"
                    @selected(old($name, $selected) == $value)
                >
                    {{ $text }}
                </option>
            @endforeach
        </select>

        <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
            <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path
                    d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                    stroke="currentColor"
                    stroke-width="1.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                />
            </svg>
        </span>
    </div>
</div>
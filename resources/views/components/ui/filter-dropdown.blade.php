@props([
    'label' => 'Filter',
    'applyText' => 'Apply',
    'resetText' => 'Reset',
    'buttonClass' => null,
    'dropdownClass' => null,
    'applyClass' => null,
    'resetClass' => null,
])

<div x-data="{ show: false }" class="relative">

    {{-- TRIGGER --}}
    <button
        type="button"
        @click="show = !show"
        class="shadow-theme-xs flex h-11 w-full items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 sm:w-auto sm:min-w-[100px]
        dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 {{ $buttonClass }}">

        {{-- ICON --}}
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M14.6537 5.90414C14.6537 4.48433 13.5027 3.33331 12.0829 3.33331C10.6631 3.33331 9.51206 4.48433 9.51204 5.90415M14.6537 5.90414C14.6537 7.32398 13.5027 8.47498 12.0829 8.47498C10.663 8.47498 9.51204 7.32398 9.51204 5.90415M14.6537 5.90414L17.7087 5.90411M9.51204 5.90415L2.29199 5.90411M5.34694 14.0958C5.34694 12.676 6.49794 11.525 7.91777 11.525C9.33761 11.525 10.4886 12.676 10.4886 14.0958M5.34694 14.0958C5.34694 15.5156 6.49794 16.6666 7.91778 16.6666C9.33761 16.6666 10.4886 15.5156 10.4886 14.0958M5.34694 14.0958L2.29199 14.0958M10.4886 14.0958L17.7087 14.0958"
                stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>

        {{ $label }}
    </button>

    {{-- DROPDOWN --}}
    <div
        x-show="show"
        @click.away="show = false"
        x-transition
        class="absolute right-0 z-10 mt-2 w-56 rounded-lg border border-gray-200 bg-white p-4 shadow-lg
        dark:border-gray-700 dark:bg-gray-800 {{ $dropdownClass }}">

        {{-- WRAP SLOT AS FORM SUPPORT --}}
        <form method="GET" class="space-y-4">

            {{ $slot }}

            {{-- ACTION --}}
            <div class="flex gap-2 pt-2">

                {{-- APPLY --}}
                <button
                    type="submit"
                    class="h-10 w-full rounded-lg bg-brand-500 px-3 py-2 text-sm font-medium text-white hover:bg-brand-600
                    {{ $applyClass }}">
                    {{ $applyText }}
                </button>

                {{-- RESET --}}
                <a
                    href="{{ url()->current() }}"
                    class="flex h-10 w-full items-center justify-center rounded-lg border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300
                    {{ $resetClass }}">
                    {{ $resetText }}
                </a>

            </div>

        </form>

    </div>

</div>
@props([
    'submitText' => 'Save',
    'cancelText' => 'Cancel',
    'cancelUrl' => null,
])

<div class="w-full px-2.5">
    <div class="mt-1 flex items-center gap-3">

        <button
            type="submit"
            class="bg-brand-500 hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white"
        >
            {{ $submitText }}
        </button>

        @if($cancelUrl)
            <a
                href="{{ $cancelUrl }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
            >
                {{ $cancelText }}
            </a>
        @else
            <button
                type="button"
                onclick="history.back()"
                class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-3 text-sm font-medium text-gray-700 hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
            >
                {{ $cancelText }}
            </button>
        @endif

    </div>
</div>
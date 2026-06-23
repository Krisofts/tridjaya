@props([
    'type' => 'success', // success | error | info
    'message' => null,
    'closable' => true,
])

@php
    $styles = [
        'success' => 'border-success-500 text-success-600 bg-white dark:bg-[#1E2634] dark:text-success-500',
        'error'   => 'border-error-500 text-error-600 bg-white dark:bg-[#1E2634] dark:text-error-500',
        'info'    => 'border-blue-500 text-blue-600 bg-white dark:bg-[#1E2634] dark:text-blue-500',
    ];

    $bgIcon = [
        'success' => 'bg-success-50 dark:bg-success-500/15',
        'error'   => 'bg-error-50 dark:bg-error-500/15',
        'info'    => 'bg-blue-50 dark:bg-blue-500/15',
    ];

    $barColor = [
        'success' => 'bg-success-500',
        'error'   => 'bg-error-500',
        'info'    => 'bg-blue-500',
    ];

    $type = in_array($type, ['success', 'error', 'info']) ? $type : 'success';
@endphp

@if (filled($message))
<div
    x-data="{
        show: true,
        duration: 5000,
        progress: 100,
        interval: null,

        init() {
            const stepTime = 50;
            const step = 100 / (this.duration / stepTime);

            this.interval = setInterval(() => {
                this.progress -= step;

                if (this.progress <= 0) {
                    this.progress = 0;
                    clearInterval(this.interval);
                    this.show = false; // auto close saat progress selesai
                }
            }, stepTime);
        }
    }"
    x-init="init()"
    x-show="show"
    x-transition
    class="fixed top-10 right-5 z-50 w-80"
>

    {{-- CARD --}}
    <div class="relative overflow-hidden rounded-md shadow-lg {{ $styles[$type] }}">

        {{-- CONTENT --}}
        <div class="flex items-center justify-between gap-3 p-3">

            <div class="flex items-center gap-4">

                {{-- ICON --}}
                <div class="flex h-10 w-10 items-center justify-center rounded-lg {{ $bgIcon[$type] }}">
                    @switch($type)
                        @case('success')
                            <x-icons.success/>
                            @break

                        @case('error')
                            <x-icons.info/>
                            @break

                        @default
                            <x-icons.warning/>
                    @endswitch
                </div>

                {{-- MESSAGE --}}
                <div class="text-sm text-gray-800 dark:text-white/90">
                    {{ $message }}
                </div>

            </div>

            {{-- CLOSE BUTTON --}}
            @if ($closable)
                <button
                    type="button"
                    @click="show = false; clearInterval(interval)"
                    class="text-gray-400 hover:text-gray-800 dark:hover:text-white/90"
                >
                    <x-icons.close/>
                </button>
            @endif

        </div>

        {{-- PROGRESS BAR --}}
        <div class="absolute bottom-0 left-0 h-1 w-full bg-black/10 dark:bg-white/10">
            <div
                class="h-full transition-all duration-75 ease-linear {{ $barColor[$type] }}"
                :style="`width: ${progress}%`"
            ></div>
        </div>

    </div>
</div>
@endif
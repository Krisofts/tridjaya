@props([
    'type' => null,   // optional fallback kalau tidak pakai dynamic
])

@php
    $base = 'text-theme-xs rounded-full px-2 py-0.5 font-medium';

    $fallback = [
        'primary' => 'bg-brand-50 text-brand-500 dark:bg-brand-500/15 dark:text-brand-400',
        'success' => 'bg-success-50 text-success-700 dark:bg-success-500/15 dark:text-success-500',
        'error'   => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
        'warning' => 'bg-warning-50 text-warning-600 dark:bg-warning-500/15 dark:text-orange-400',
        'info'    => 'bg-blue-light-50 text-blue-light-500 dark:bg-blue-light-500/15 dark:text-blue-light-500',
        'light'   => 'bg-gray-100 text-gray-600 dark:bg-gray-500/15 dark:text-gray-400',
        'dark'    => 'bg-gray-500 text-white dark:bg-white/5 dark:text-white',
    ];
@endphp

<span
    {{ $attributes->merge([
        'class' => $base . ' ' . ($type ? ($fallback[$type] ?? $fallback['primary']) : '')
    ]) }}
>
    {{ $slot }}
</span>
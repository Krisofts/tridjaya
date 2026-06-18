@props([
    'pageTitle' => 'Page',
    'breadcrumbs' => [],
])

<div class="mb-6 flex flex-wrap items-center justify-between gap-3">
    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
        {{ $pageTitle }}
    </h2>

    <nav>
        <ol class="flex items-center gap-1.5">
            @foreach ($breadcrumbs as $breadcrumb)
                <li class="flex items-center gap-1.5">
                    @if (!empty($breadcrumb['url']))
                        <a
                            href="{{ $breadcrumb['url'] }}"
                            class="text-sm text-gray-500 hover:text-brand-500 dark:text-gray-400"
                        >
                            {{ $breadcrumb['label'] }}
                        </a>
                    @else
                        <span class="text-sm text-gray-800 dark:text-white/90">
                            {{ $breadcrumb['label'] }}
                        </span>
                    @endif

                    @unless ($loop->last)
                        <svg
                            class="stroke-current text-gray-400"
                            width="17"
                            height="16"
                            viewBox="0 0 17 16"
                            fill="none"
                        >
                            <path
                                d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366"
                                stroke-width="1.2"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                            />
                        </svg>
                    @endunless
                </li>
            @endforeach
        </ol>
    </nav>
</div>
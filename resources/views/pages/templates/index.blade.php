<x-ui.table
    title="Products List"
    description="Track your store's progress to boost your sales."
>

    {{-- HEADER ACTIONS --}}
    <x-slot:header>
        <button
            class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
            Export
            <x-icons.download class="fill-current" />
        </button>

        <a href="#"
            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
            <x-icons.add class="fill-current" />
            Add Product
        </a>
    </x-slot:header>

    {{-- TOOLBAR (SEARCH + FILTER) --}}
    <x-slot:toolbar>
        <div class="flex gap-3 sm:justify-between">

            {{-- SEARCH --}}
            <div class="relative flex-1 sm:flex-auto">
                <span class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                    <x-icons.search class="fill-current" />
                </span>

                <input type="text" placeholder="Search..."
                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent pl-11 pr-4 text-sm text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 sm:w-[300px]">
            </div>

            {{-- FILTER --}}
            <div x-data="{ showFilter: false }" class="relative">
                 
                <button @click="showFilter = !showFilter"
                    class="shadow-theme-xs flex h-11 items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    
                    <x-icons.filter class="fill-current" />
                    Filter
                </button>

                <div x-show="showFilter" @click.away="showFilter = false"
                    class="absolute right-0 z-10 mt-2 w-56 rounded-lg border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">

                    <input class="mb-3 w-full rounded border p-2 text-sm" placeholder="Category">
                    <input class="mb-3 w-full rounded border p-2 text-sm" placeholder="Company">

                    <button class="w-full rounded bg-brand-500 py-2 text-sm text-white">
                        Apply
                    </button>
                </div>
            </div>

        </div>
    </x-slot:toolbar>

    {{-- TABLE HEADER --}}
    <thead>
        <tr class="border-b border-gray-200 dark:border-gray-800">

            <th class="px-5 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                Created At
            </th>

            <th class="px-5 py-4 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                Action
            </th>

        </tr>
    </thead>

    {{-- TABLE BODY --}}
    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">

        <tr class="transition hover:bg-gray-50 dark:hover:bg-gray-900">

            <td class="px-5 py-4 whitespace-nowrap">
                <p class="text-sm text-gray-700 dark:text-gray-400">
                    {{ $item->created_at ?? '' }}
                </p>
            </td>

            <td class="px-5 py-4 whitespace-nowrap">

                <div x-data="{ open: false }" class="relative flex justify-center">

                    <button @click="open = !open" class="text-gray-500 dark:text-gray-400">
                        ⋮
                    </button>

                    <div x-show="open" @click.outside="open = false"
                        class="absolute right-0 mt-2 w-40 rounded-2xl border bg-white p-2 shadow-lg dark:border-gray-800 dark:bg-gray-900">

                        <button class="block w-full px-3 py-2 text-left text-sm hover:bg-gray-100">
                            View More
                        </button>

                        <button class="block w-full px-3 py-2 text-left text-sm text-red-500 hover:bg-gray-100">
                            Delete
                        </button>

                    </div>

                </div>

            </td>

        </tr>

    </tbody>

    {{-- FOOTER --}}
    <x-slot:footer>

        <div class="text-sm text-gray-500">
            Showing 1 to 7 of 20
        </div>

        <div class="flex gap-2">
            <button class="rounded border px-3 py-2 text-sm">Prev</button>
            <button class="rounded border px-3 py-2 text-sm">Next</button>
        </div>

    </x-slot:footer>

</x-ui.table>
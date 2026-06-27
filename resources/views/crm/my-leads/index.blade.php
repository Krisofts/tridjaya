@extends('layouts.app')

@section('content')

<x-ui.table
    title="Lead Saya"
    description="Daftar prospek yang ditugaskan kepada kamu."
>

    {{-- HEADER ACTIONS --}}
    <x-slot:header>
        <a
            href="{{ route('crm.leads.create') }}"
            class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition"
        >
            <x-icons.add class="fill-current" />
            Tambah Prospek
        </a>
    </x-slot:header>

    {{-- TOOLBAR --}}
    <x-slot:toolbar>
        <form method="GET">
            <div class="flex gap-3 sm:justify-between">

                {{-- SEARCH --}}
                <div class="relative flex-1 sm:flex-auto">
                    <span class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                        <x-icons.search class="fill-current" />
                    </span>
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari nama, telepon, atau email..."
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden sm:w-[300px] sm:min-w-[300px] dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                    >
                </div>

                {{-- FILTERS --}}
                <x-ui.filter-dropdown label="Filter">
                    <x-ui.filter-field
                        type="select"
                        label="Pipeline"
                        placeholder="Semua Pipeline"
                        :options="$pipelines"
                        :selected="request('pipeline_id')"
                        name="pipeline_id"
                    />
                    <x-ui.filter-field
                        type="select"
                        label="Temperature"
                        placeholder="Semua Temperature"
                        :options="$temperatures"
                        :selected="request('temperature')"
                        name="temperature"
                    />
                </x-ui.filter-dropdown>

            </div>
        </form>
    </x-slot:toolbar>

    {{-- TABLE HEADER --}}
    <thead class="border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
        <tr>
            <th class="px-6 py-3 text-left">
                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Nama</p>
            </th>
            <th class="px-6 py-3 text-left">
                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Telepon</p>
            </th>
            <th class="px-6 py-3 text-left">
                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Pipeline</p>
            </th>
            <th class="px-6 py-3 text-left">
                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Stage</p>
            </th>
            <th class="px-6 py-3 text-left">
                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Minat</p>
            </th>
            <th class="px-6 py-3 text-left">
                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Temperature</p>
            </th>
            <th class="px-6 py-3 text-right"></th>
        </tr>
    </thead>

    {{-- TABLE BODY --}}
    <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">

        @forelse ($leads as $lead)
            <tr class="cursor-pointer hover:bg-gray-50 dark:hover:bg-white/[0.02]"
                onclick="window.location='{{ route('crm.my-leads.show', $lead) }}'">

                {{-- NAMA --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center gap-3">
                        <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-brand-500 text-xs font-bold text-white">
                            {{ strtoupper(substr($lead->name, 0, 1)) }}
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $lead->name }}</p>
                            <p class="text-xs text-gray-400">{{ $lead->lead_code }}</p>
                        </div>
                    </div>
                </td>

                {{-- TELEPON --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $lead->phone ?: '-' }}</span>
                </td>

                {{-- PIPELINE --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $lead->pipeline->name ?? '-' }}</span>
                </td>

                {{-- STAGE --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($lead->stage)
                        <x-ui.badge :type="$lead->stage->badgeType()">{{ $lead->stage->name }}</x-ui.badge>
                    @else
                        <span class="text-sm text-gray-400">-</span>
                    @endif
                </td>

                {{-- MINAT --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $lead->interest->name ?? '-' }}</span>
                </td>

                {{-- TEMPERATURE --}}
                <td class="px-6 py-4 whitespace-nowrap">
                    @if ($lead->stage)
                        <x-ui.badge :type="$lead->stage->temperatureBadgeType()">{{ $lead->stage->temperatureLabel() }}</x-ui.badge>
                    @else
                        <span class="text-sm text-gray-400">-</span>
                    @endif
                </td>

                {{-- ACTIONS --}}
                <td class="px-6 py-4 text-right whitespace-nowrap" onclick="event.stopPropagation()">
                    <x-common.table-dropdown>
                        <x-slot:button>
                            <button type="button" class="text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300">
                                <x-icons.dots-horizontal class="fill-current" />
                            </button>
                        </x-slot:button>
                        <x-slot:content>
                            <a href="{{ route('crm.my-leads.show', $lead) }}"
                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                Lihat Detail
                            </a>
                            <a href="{{ route('crm.leads.edit', $lead) }}"
                                class="flex w-full rounded-lg px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300">
                                Edit
                            </a>
                        </x-slot:content>
                    </x-common.table-dropdown>
                </td>

            </tr>
        @empty
            <tr>
                <td colspan="7" class="px-6 py-12 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada lead yang ditugaskan ke kamu.</p>
                    <a href="{{ route('crm.leads.create') }}" class="mt-2 inline-block text-sm text-brand-500 hover:underline">
                        Tambah prospek baru
                    </a>
                </td>
            </tr>
        @endforelse

    </tbody>

    {{-- FOOTER --}}
    <x-slot:footer>
        <x-ui.pagination :paginator="$leads" />
    </x-slot:footer>

</x-ui.table>

@endsection
@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="CRM Leads" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'CRM Leads'],
    ]" />

    <x-ui.table
        title="CRM Leads"
        description="Manage and monitor all registered leads in your system."
    >

        {{-- HEADER ACTIONS --}}
        <x-slot:header>

            <button
                class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]"
            >
                Export
                <x-icons.download class="fill-current" />
            </button>

            <a
                href="{{ route('crm.leads.create') }}"
                class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition"
            >
                <x-icons.add class="fill-current" />
                Add Lead
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
                            placeholder="Search name, phone or email..."
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden sm:w-[300px] sm:min-w-[300px] dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30"
                        >

                    </div>

                    {{-- FILTER --}}
                    <x-ui.filter-dropdown label="Filter">

                        <x-ui.filter-field
                            type="select"
                            label="Source"
                            placeholder="All Sources"
                            :options="$sources"
                            :model="null"
                            :selected="request('source_id')"
                            name="source_id"
                        />

                        <x-ui.filter-field
                            type="select"
                            label="Pipeline"
                            placeholder="All Pipelines"
                            :options="$pipelines"
                            :model="null"
                            :selected="request('pipeline_id')"
                            name="pipeline_id"
                        />

                        <x-ui.filter-field
                            type="select"
                            label="Stage"
                            placeholder="All Stages"
                            :options="$stages ?? collect()"
                            :model="null"
                            :selected="request('stage_id')"
                            name="stage_id"
                        />

                    </x-ui.filter-dropdown>

                </div>
            </form>

        </x-slot:toolbar>

        {{-- TABLE HEADER --}}
        <thead class="border-gray-100 bg-gray-50 dark:border-gray-800 dark:bg-gray-900">
            <tr>

                <th class="px-6 py-3 text-left">
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                        Name
                    </p>
                </th>

                <th class="px-6 py-3 text-left">
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                        Phone
                    </p>
                </th>

                <th class="px-6 py-3 text-left">
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                        Pipeline
                    </p>
                </th>

                <th class="px-6 py-3 text-left">
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                        Stage
                    </p>
                </th>

                <th class="px-6 py-3 text-left">
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">
                        Source
                    </p>
                </th>

                <th class="px-6 py-3 text-right whitespace-nowrap"></th>

            </tr>
        </thead>

        {{-- TABLE BODY --}}
        <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">

            @forelse ($leads as $lead)

                <tr>

                    {{-- NAME --}}
                    <td class="px-6 py-4">
                        <div>
                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90">
                                {{ $lead->name }}
                            </p>

                            @if ($lead->email)
                                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                    {{ $lead->email }}
                                </p>
                            @endif
                        </div>
                    </td>

                    {{-- PHONE --}}
                    <td class="px-6 py-4">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                            {{ $lead->phone ?: '-' }}
                        </p>
                    </td>

                    {{-- PIPELINE --}}
                    <td class="px-6 py-4">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                            {{ $lead->pipeline->name ?? '-' }}
                        </p>
                    </td>

                    {{-- STAGE --}}
                    <td class="px-6 py-4">

                        @if ($lead->stage)
                            <x-ui.badge size="sm" color="primary">
                                {{ $lead->stage->name }}
                            </x-ui.badge>
                        @else
                            <x-ui.badge size="sm" color="light">
                                No Stage
                            </x-ui.badge>
                        @endif

                    </td>

                    {{-- SOURCE --}}
                    <td class="px-6 py-4">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                            {{ $lead->source->name ?? '-' }}
                        </p>
                    </td>

                    {{-- ACTION --}}
                    <td class="px-6 py-4 text-right whitespace-nowrap">

                        <x-common.table-dropdown>

                            <x-slot name="button">
                                <button
                                    type="button"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200"
                                >
                                    <svg class="fill-current" width="24" height="24" viewBox="0 0 24 24">
                                        <path
                                            fill-rule="evenodd"
                                            clip-rule="evenodd"
                                            d="M5.99902 10.245C6.96552 10.245 7.74902 11.0285 7.74902 11.995V12.005C7.74902 12.9715 6.96552 13.755 5.99902 13.755C5.03253 13.755 4.24902 12.9715 4.24902 12.005V11.995C4.24902 11.0285 5.03253 10.245 5.99902 10.245ZM17.999 10.245C18.9655 10.245 19.749 11.0285 19.749 11.995V12.005C19.749 12.9715 18.9655 13.755 17.999 13.755C17.0325 13.755 16.249 12.9715 16.249 12.005V11.995C16.249 11.0285 17.0325 10.245 17.999 10.245ZM13.749 11.995C13.749 11.0285 12.9655 10.245 11.999 10.245C11.0325 10.245 10.249 11.0285 10.249 11.995V12.005C10.249 12.9715 11.0325 13.755 11.999 13.755C12.9655 13.755 13.749 12.9715 13.749 12.005V11.995Z"
                                        />
                                    </svg>
                                </button>
                            </x-slot>

                            <x-slot name="content">

                                <a
                                    href="{{ route('crm.leads.show', $lead) }}"
                                    class="flex w-full px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300 rounded-lg"
                                >
                                    View
                                </a>

                                <a
                                    href="{{ route('crm.leads.edit', $lead) }}"
                                    class="flex w-full px-3 py-2 text-left text-theme-xs font-medium text-gray-500 hover:bg-gray-100 hover:text-gray-700 dark:text-gray-400 dark:hover:bg-white/5 dark:hover:text-gray-300 rounded-lg"
                                >
                                    Edit
                                </a>

                                <form
                                    action="{{ route('crm.leads.destroy', $lead) }}"
                                    method="POST"
                                    onsubmit="return confirm('Delete lead?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="flex w-full px-3 py-2 text-left text-theme-xs font-medium text-red-500 hover:bg-red-50 hover:text-red-600 rounded-lg"
                                    >
                                        Delete
                                    </button>
                                </form>

                            </x-slot>

                        </x-common.table-dropdown>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="px-6 py-10 text-center text-gray-500">
                        No leads found
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


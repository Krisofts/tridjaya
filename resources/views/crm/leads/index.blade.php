@extends('layouts.app')

@section('title', 'Leads')

@section('content')



    <x-common.page-breadcrumb pageTitle="Leads" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- HEADER --}}
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                        Leads
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Manage customer prospects and opportunities.
                    </p>
                </div>

                <a href="{{ route('crm.leads.create') }}"
                    class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">

                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 10.0002H15.0006M10.0002 5V15.0006" stroke="currentColor" stroke-width="1.5"
                            stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                    New Lead
                </a>

            </div>
        </div>

        {{-- SEARCH & FILTER --}}
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">

            <form method="GET">
                <div class="flex gap-3 sm:justify-between">

                    {{-- SEARCH --}}
                    <div class="relative flex-1 sm:flex-auto">

                        <span class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z"
                                    fill="">
                                </path>
                            </svg>
                        </span>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search lead name or phone..."
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 focus:ring-3 focus:outline-hidden sm:w-[320px] sm:min-w-[320px] dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30">
                    </div>

                    {{-- FILTER --}}
                    <div class="relative" x-data="{ showFilter: false }">

                        <button type="button" @click="showFilter = !showFilter"
                            class="shadow-theme-xs flex h-11 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">

                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                                fill="none">
                                <path
                                    d="M14.6537 5.90414C14.6537 4.48433 13.5027 3.33331 12.0829 3.33331C10.6631 3.33331 9.51206 4.48433 9.51204 5.90415M14.6537 5.90414C14.6537 7.32398 13.5027 8.47498 12.0829 8.47498C10.663 8.47498 9.51204 7.32398 9.51204 5.90415M14.6537 5.90414L17.7087 5.90411M9.51204 5.90415L2.29199 5.90411M5.34694 14.0958C5.34694 12.676 6.49794 11.525 7.91777 11.525C9.33761 11.525 10.4886 12.676 10.4886 14.0958M5.34694 14.0958C5.34694 15.5156 6.49794 16.6666 7.91778 16.6666C9.33761 16.6666 10.4886 15.5156 10.4886 14.0958M5.34694 14.0958L2.29199 14.0958M10.4886 14.0958L17.7087 14.0958"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                </path>
                            </svg>

                            Filter

                        </button>

                        <div x-show="showFilter" @click.away="showFilter = false" x-transition
                            class="absolute right-0 z-10 mt-2 w-56 rounded-lg border border-gray-200 bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">

                            {{-- Status --}}
                            <div class="mb-5">
                                <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Status
                                </label>

                                <select name="status"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                    <option value="">All Status</option>

                                    @foreach (App\CRM\Models\Lead::statuses() as $value => $label)
                                        <option value="{{ $value }}" @selected(request('status') == $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- Source --}}
                            <div class="mb-5">
                                <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Source
                                </label>

                                <select name="source"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                    <option value="">All Sources</option>

                                    @foreach (App\CRM\Models\Lead::sources() as $value => $label)
                                        <option value="{{ $value }}" @selected(request('source') == $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- Interest --}}
                            <div class="mb-5">
                                <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Interest
                                </label>

                                <select name="interest"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                    <option value="">All Interests</option>

                                    @foreach (App\CRM\Models\Lead::interests() as $value => $label)
                                        <option value="{{ $value }}" @selected(request('interest') == $value)>
                                            {{ $label }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            <div class="space-y-2">

                                <button type="submit"
                                    class="bg-brand-500 hover:bg-brand-600 h-10 w-full rounded-lg px-3 py-2 text-sm font-medium text-white">
                                    Apply
                                </button>

                                <a href="{{ route('crm.leads.index') }}"
                                    class="flex h-10 w-full items-center justify-center rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
                                    Reset
                                </a>

                            </div>

                        </div>

                    </div>

                </div>
            </form>

        </div>

        {{-- TABLE --}}
        <div class="max-w-full overflow-x-auto custom-scrollbar">

            <table class="min-w-full">

                {{-- TABLE HEADER --}}
                <thead>
                    <tr class="border-b border-gray-200 dark:divide-gray-800 dark:border-gray-800">

                        <th class="px-6 py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Lead
                                </p>
                            </div>
                        </th>

                        <th class="px-6 py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Phone
                                </p>
                            </div>
                        </th>

                        <th class="px-6 py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Status
                                </p>
                            </div>
                        </th>

                        <th class="px-6 py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Interest
                                </p>
                            </div>
                        </th>

                        <th class="px-6 py-3">
                            <div class="flex items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Assigned To
                                </p>
                            </div>
                        </th>

                        <th class="px-6 py-3">
                            <div class="flex justify-end items-center">
                                <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                                    Action
                                </p>
                            </div>
                        </th>

                    </tr>
                </thead>

                {{-- TABLE BODY --}}
                <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">

                    @forelse($leads as $lead)
                        <tr>

                            {{-- Lead --}}
                            <td class="px-6 py-3.5">
                                <div class="flex flex-col">
                                    <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90 whitespace-nowrap">
                                        {{ $lead->name }}
                                    </p>

                                    @if ($lead->email)
                                        <p class="text-gray-500 text-theme-xs dark:text-gray-400">
                                            {{ $lead->email }}
                                        </p>
                                    @endif
                                </div>
                            </td>

                            {{-- Phone --}}
                            <td class="px-6 py-3.5">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $lead->phone }}
                                    </p>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-3.5">

                                @php
                                    $statusColors = [
                                        'new' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',

                                        'contacted' =>
                                            'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',

                                        'qualified' =>
                                            'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400',

                                        'lost' => 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-400',
                                    ];
                                @endphp

                                <span
                                    class="inline-flex whitespace-nowrap rounded-full px-2.5 py-1 text-theme-xs font-medium {{ $statusColors[$lead->status] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-800 dark:text-gray-400 ' }}">
                                    {{ $lead->status_label }}
                                </span>

                            </td>

                            {{-- Interest --}}
                            <td class="px-6 py-3.5">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $lead->interest_label }}
                                    </p>
                                </div>
                            </td>

                            {{-- Assigned --}}
                            <td class="px-6 py-3.5">
                                <div class="flex items-center">
                                    <p class="text-gray-500 text-theme-sm dark:text-gray-400">
                                        {{ $lead->assignedTo?->name ?? '-' }}
                                    </p>
                                </div>
                            </td>

                            {{-- Action --}}
                            <td class="px-6 py-3.5">
                                <div class="flex items-center justify-end gap-3">

                                    {{-- View --}}
                                    <a href="{{ route('crm.leads.show', $lead) }}"
                                        class="inline-flex items-center gap-1 text-brand-500 text-theme-sm font-medium hover:text-brand-600">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 20 20" fill="none">
                                            <path
                                                d="M1.66699 10C1.66699 10 4.16699 4.16669 10.0003 4.16669C15.8337 4.16669 18.3337 10 18.3337 10C18.3337 10 15.8337 15.8334 10.0003 15.8334C4.16699 15.8334 1.66699 10 1.66699 10Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M10 12.5C11.3807 12.5 12.5 11.3807 12.5 10C12.5 8.61929 11.3807 7.5 10 7.5C8.61929 7.5 7.5 8.61929 7.5 10C7.5 11.3807 8.61929 12.5 10 12.5Z"
                                                stroke="currentColor" stroke-width="1.5" />
                                        </svg>

                                        View
                                    </a>

                                    {{-- Edit --}}
                                    <a href="{{ route('crm.leads.edit', $lead) }}"
                                        class="inline-flex items-center gap-1 text-warning-600 text-theme-sm font-medium hover:text-warning-700 dark:text-warning-400 dark:hover:text-warning-300">

                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            viewBox="0 0 20 20" fill="none">
                                            <path
                                                d="M11.6667 3.33331H5.83333C4.91286 3.33331 4.16667 4.0795 4.16667 4.99998V14.1666C4.16667 15.0871 4.91286 15.8333 5.83333 15.8333H15C15.9205 15.8333 16.6667 15.0871 16.6667 14.1666V8.33331"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M15.4167 2.08335C15.7482 1.75183 16.1978 1.56555 16.6667 1.56555C17.1355 1.56555 17.5851 1.75183 17.9167 2.08335C18.2482 2.41487 18.4345 2.86451 18.4345 3.33335C18.4345 3.80219 18.2482 4.25183 17.9167 4.58335L10.8333 11.6667L7.5 12.5L8.33333 9.16669L15.4167 2.08335Z"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>

                                        Edit
                                    </a>

                                </div>
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center">

                                <div class="flex flex-col items-center">

                                    <p class="font-medium text-gray-500 dark:text-gray-400">
                                        No leads found
                                    </p>

                                    <p class="mt-1 text-sm text-gray-400">
                                        Try changing your search or filters.
                                    </p>

                                </div>

                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

        @if ($leads->hasPages())

            <div
                class="flex flex-col items-center justify-between border-t border-gray-200 px-5 py-4 sm:flex-row dark:border-gray-800">

                {{-- INFO --}}
                <div class="pb-3 sm:pb-0">
                    <span class="block text-sm font-medium text-gray-500 dark:text-gray-400">
                        Showing
                        <span class="text-gray-800 dark:text-white/90">
                            {{ $leads->firstItem() }}
                        </span>
                        to
                        <span class="text-gray-800 dark:text-white/90">
                            {{ $leads->lastItem() }}
                        </span>
                        of
                        <span class="text-gray-800 dark:text-white/90">
                            {{ $leads->total() }}
                        </span>
                    </span>
                </div>

                {{-- PAGINATION --}}
                <div
                    class="flex w-full items-center justify-between gap-2 rounded-lg bg-gray-50 p-4 sm:w-auto sm:justify-normal sm:rounded-none sm:bg-transparent sm:p-0 dark:bg-gray-900 dark:sm:bg-transparent">

                    {{-- PREV --}}
                    @if ($leads->onFirstPage())
                        <span
                            class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-400 opacity-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M2.58203 9.99868C2.58174 10.1909 2.6549 10.3833 2.80152 10.53L7.79818 15.5301C8.09097 15.8231 8.56584 15.8233 8.85883 15.5305C9.15183 15.2377 9.152 14.7629 8.85921 14.4699L5.13911 10.7472L16.6665 10.7472C17.0807 10.7472 17.4165 10.4114 17.4165 9.99715C17.4165 9.58294 17.0807 9.24715 16.6665 9.24715L5.14456 9.24715L8.85919 5.53016C9.15199 5.23717 9.15184 4.7623 8.85885 4.4695C8.56587 4.1767 8.09099 4.17685 7.79819 4.46984L2.84069 9.43049C2.68224 9.568 2.58203 9.77087 2.58203 9.99715Z" />
                            </svg>
                        </span>
                    @else
                        <a href="{{ $leads->previousPageUrl() }}"
                            class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M2.58203 9.99868C2.58174 10.1909 2.6549 10.3833 2.80152 10.53L7.79818 15.5301C8.09097 15.8231 8.56584 15.8233 8.85883 15.5305C9.15183 15.2377 9.152 14.7629 8.85921 14.4699L5.13911 10.7472L16.6665 10.7472C17.0807 10.7472 17.4165 10.4114 17.4165 9.99715C17.4165 9.58294 17.0807 9.24715 16.6665 9.24715L5.14456 9.24715L8.85919 5.53016C9.15199 5.23717 9.15184 4.7623 8.85885 4.4695C8.56587 4.1767 8.09099 4.17685 7.79819 4.46984L2.84069 9.43049C2.68224 9.568 2.58203 9.77087 2.58203 9.99715Z" />
                            </svg>
                        </a>
                    @endif

                    {{-- MOBILE PAGE INFO --}}
                    <span class="block text-sm font-medium text-gray-700 sm:hidden dark:text-gray-400">
                        Page {{ $leads->currentPage() }}
                        of {{ $leads->lastPage() }}
                    </span>

                    {{-- DESKTOP PAGES --}}
                    <ul class="hidden items-center gap-0.5 sm:flex">

                        @foreach ($leads->getUrlRange(1, $leads->lastPage()) as $page => $url)
                            <li>
                                <a href="{{ $url }}"
                                    class="flex h-10 w-10 items-center justify-center rounded-lg text-sm font-medium
                       {{ $page == $leads->currentPage()
                           ? 'bg-brand-500 text-white'
                           : 'text-gray-700 hover:bg-brand-500 hover:text-white dark:text-gray-400 dark:hover:text-white' }}">
                                    {{ $page }}
                                </a>
                            </li>
                        @endforeach

                    </ul>

                    {{-- NEXT --}}
                    @if ($leads->hasMorePages())
                        <a href="{{ $leads->nextPageUrl() }}"
                            class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-700 hover:bg-gray-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.4165 9.9986C17.4168 10.1909 17.3437 10.3832 17.197 10.53L12.2004 15.5301C11.9076 15.8231 11.4327 15.8233 11.1397 15.5305C10.8467 15.2377 10.8465 14.7629 11.1393 14.4699L14.8594 10.7472L3.33203 10.7472C2.91782 10.7472 2.58203 10.4114 2.58203 9.99715C2.58203 9.58294 2.91782 9.24715 3.33203 9.24715L14.854 9.24715L11.1393 5.53016C10.8465 5.23717 10.8467 4.7623 11.1397 4.4695C11.4327 4.1767 11.9075 4.17685 12.2003 4.46984L17.1578 9.43049C17.3163 9.568 17.4165 9.77087 17.4165 9.99715Z" />
                            </svg>
                        </a>
                    @else
                        <span
                            class="shadow-theme-xs flex items-center gap-2 rounded-lg border border-gray-300 bg-white p-2 text-gray-400 opacity-50 sm:p-2.5 dark:border-gray-700 dark:bg-gray-800">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M17.4165 9.9986C17.4168 10.1909 17.3437 10.3832 17.197 10.53L12.2004 15.5301C11.9076 15.8231 11.4327 15.8233 11.1397 15.5305C10.8467 15.2377 10.8465 14.7629 11.1393 14.4699L14.8594 10.7472L3.33203 10.7472C2.91782 10.7472 2.58203 10.4114 2.58203 9.99715C2.58203 9.58294 2.91782 9.24715 3.33203 9.24715L14.854 9.24715L11.1393 5.53016C10.8465 5.23717 10.8467 4.7623 11.1397 4.4695C11.4327 4.1767 11.9075 4.17685 12.2003 4.46984L17.1578 9.43049C17.3163 9.568 17.4165 9.77087 17.4165 9.99715Z" />
                            </svg>
                        </span>
                    @endif

                </div>

            </div>

        @endif

    </div>

@endsection

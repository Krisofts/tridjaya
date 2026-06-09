@extends('layouts.app')

@section('title', 'User Report')

@section('content')

    <x-common.page-breadcrumb pageTitle="Report" />

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- HEADER --}}
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">

            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Report
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Detailed report of user leads, deal, won, and conversion activity
            </p>

        </div>

        {{-- SEARCH & FILTER --}}
        <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">

            <form method="GET">
                <div class="flex gap-3 sm:justify-between">

                    {{-- SEARCH --}}
                    <div class="relative flex-1 sm:flex-auto">

                        <span class="absolute top-1/2 left-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                            <svg class="fill-current" width="20" height="20" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.04199 9.37363C3.04199 5.87693 5.87735 3.04199 9.37533 3.04199C12.8733 3.04199 15.7087 5.87693 15.7087 9.37363C15.7087 12.8703 12.8733 15.7053 9.37533 15.7053C5.87735 15.7053 3.04199 12.8703 3.04199 9.37363ZM9.37533 1.54199C5.04926 1.54199 1.54199 5.04817 1.54199 9.37363C1.54199 13.6991 5.04926 17.2053 9.37533 17.2053C11.2676 17.2053 13.0032 16.5344 14.3572 15.4176L17.1773 18.238C17.4702 18.5309 17.945 18.5309 18.2379 18.238C18.5308 17.9451 18.5309 17.4703 18.238 17.1773L15.4182 14.3573C16.5367 13.0033 17.2087 11.2669 17.2087 9.37363C17.2087 5.04817 13.7014 1.54199 9.37533 1.54199Z">
                                </path>
                            </svg>
                        </span>

                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search user report..."
                            class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                              dark:focus:border-brand-800 h-11 w-full rounded-lg border border-gray-300 
                              bg-transparent py-2.5 pr-4 pl-11 text-sm text-gray-800 placeholder:text-gray-400 
                              focus:ring-3 focus:outline-hidden sm:w-[320px] sm:min-w-[320px] 
                              dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                    </div>

                    {{-- FILTER --}}
                    <div class="relative" x-data="{ showFilter: false }">

                        <button type="button" @click="showFilter = !showFilter"
                            class="shadow-theme-xs flex h-11 items-center justify-center gap-2 rounded-lg border 
                               border-gray-300 bg-white px-4 py-2.5 text-sm font-medium text-gray-700 
                               dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">

                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                                fill="none">
                                <path
                                    d="M14.6537 5.90414C14.6537 4.48433 13.5027 3.33331 12.0829 3.33331C10.6631 3.33331 9.51206 4.48433 9.51204 5.90415M14.6537 5.90414C14.6537 7.32398 13.5027 8.47498 12.0829 8.47498C10.663 8.47498 9.51204 7.32398 9.51204 5.90415M14.6537 5.90414L17.7087 5.90411M9.51204 5.90415L2.29199 5.90411M5.34694 14.0958C5.34694 12.676 6.49794 11.525 7.91777 11.525C9.33761 11.525 10.4886 12.676 10.4886 14.0958M5.34694 14.0958C5.34694 15.5156 6.49794 16.6666 7.91778 16.6666C9.33761 16.6666 10.4886 15.5156 10.4886 14.0958M5.34694 14.0958L2.29199 14.0958M10.4886 14.0958L17.7087 14.0958"
                                    stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>

                            Filter
                        </button>

                        {{-- DROPDOWN --}}
                        <div x-show="showFilter" @click.away="showFilter = false" x-transition
                            class="absolute right-0 z-10 mt-2 w-56 rounded-lg border border-gray-200 
                            bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">

                            {{-- GROUP --}}
                            <div class="mb-5">
                                <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    Group
                                </label>

                                <select name="group"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                                       dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 
                                       bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900 
                                       dark:text-white/90">

                                    <option value="">All Groups</option>

                                    @foreach (config('auth_groups.groups') as $key => $group)
                                        <option value="{{ $key }}" @selected(request('group') == $key)>
                                            {{ is_array($group) ? $group['label'] ?? $key : $group }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>

                            {{-- DATE FROM --}}
                            <div class="mb-5">
                                <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    From
                                </label>

                                <input type="date" name="from" value="{{ request('from') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                                      dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 
                                      bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            </div>

                            {{-- DATE TO --}}
                            <div class="mb-5">
                                <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                    To
                                </label>

                                <input type="date" name="to" value="{{ request('to') }}"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 
                                      dark:focus:border-brand-800 h-10 w-full rounded-lg border border-gray-300 
                                      bg-transparent px-4 text-sm dark:border-gray-700 dark:bg-gray-900">
                            </div>

                            {{-- ACTION --}}
                            <div class="space-y-2">

                                <button type="submit"
                                    class="bg-brand-500 hover:bg-brand-600 h-10 w-full rounded-lg px-3 py-2 
                                       text-sm font-medium text-white">
                                    Apply
                                </button>

                                <a href="{{ url()->current() }}"
                                    class="flex h-10 w-full items-center justify-center rounded-lg border 
                                  border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 
                                  dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-700">
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
                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                        User
                    </p>
                </th>

                <th class="px-6 py-3">
                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                        Total Leads
                    </p>
                </th>

                <th class="px-6 py-3">
                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                        Deal
                    </p>
                </th>

                <th class="px-6 py-3">
                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                        Won
                    </p>
                </th>

                <th class="px-6 py-3">
                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                        Today
                    </p>
                </th>

                <th class="px-6 py-3 text-right">
                    <p class="font-medium text-gray-500 text-theme-xs dark:text-gray-400">
                        Performance
                    </p>
                </th>

            </tr>
        </thead>

        {{-- TABLE BODY --}}
        <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">

            @forelse($users as $user)

                <tr>

                    {{-- USER --}}
                    <td class="px-6 py-3.5">
                        <div class="flex flex-col">
                            <p class="font-medium text-gray-800 text-theme-sm dark:text-white/90 whitespace-nowrap">
                                {{ $user->name }}
                            </p>
                        </div>
                    </td>

                    {{-- TOTAL LEADS --}}
                    <td class="px-6 py-3.5">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400 whitespace-nowrap">
                            {{ $user->total_leads }}
                        </p>
                    </td>

                    {{-- DEAL --}}
                    <td class="px-6 py-3.5">
                        <p class="text-blue-600 text-theme-sm whitespace-nowrap">
                            {{ $user->total_deal }}
                        </p>
                    </td>

                    {{-- WON --}}
                    <td class="px-6 py-3.5">
                        <p class="text-green-600 text-theme-sm whitespace-nowrap">
                            {{ $user->total_won }}
                        </p>
                    </td>

                    {{-- TODAY --}}
                    <td class="px-6 py-3.5">
                        <p class="text-gray-500 text-theme-sm dark:text-gray-400 whitespace-nowrap">
                            {{ $user->leads_today }}
                        </p>
                    </td>

                    {{-- PERFORMANCE --}}
                    <td class="px-6 py-3.5 text-right">

                        @php
                            $performance = $user->total_leads > 0
                                ? ($user->total_won / $user->total_leads) * 100
                                : 0;
                        @endphp

                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-theme-xs font-medium text-gray-700 dark:bg-gray-800 dark:text-gray-300 whitespace-nowrap">
                            {{ number_format($performance, 2) }}%
                        </span>

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="px-6 py-10 text-center">

                        <div class="flex flex-col items-center">

                            <p class="font-medium text-gray-500 dark:text-gray-400">
                                No report data found
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

    </div>

@endsection

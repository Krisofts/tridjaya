@extends('layouts.app')

@section('title', 'Tasks')

@section('content')

<x-common.page-breadcrumb pageTitle="Tasks" />

<div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

    {{-- HEADER --}}
    <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">

            <div>
                <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                    Task Management
                </h2>

                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Manage all CRM tasks efficiently.
                </p>
            </div>

            <a href="{{ route('crm.tasks.create') }}"
               class="inline-flex items-center justify-center rounded-lg bg-brand-500 px-4 py-2.5 text-sm font-medium text-white hover:bg-brand-600">

                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M5 10H15M10 5V15"
                          stroke="currentColor" stroke-width="1.5"
                          stroke-linecap="round" stroke-linejoin="round"/>
                </svg>

                New Task
            </a>

        </div>
    </div>

    {{-- SEARCH & FILTER --}}
    <div class="border-b border-gray-200 px-5 py-4 dark:border-gray-800">

        <form method="GET">
            <div class="flex gap-3 sm:justify-between">

                {{-- SEARCH --}}
                <div class="relative flex-1 sm:flex-auto">

                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search task title..."
                        class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10
                        h-11 w-full sm:w-[320px] rounded-lg border border-gray-300 bg-transparent
                        px-4 text-sm text-gray-800 placeholder:text-gray-400
                        dark:border-gray-700 dark:bg-gray-900 dark:text-white/90"
                    >
                </div>

                {{-- FILTER DROPDOWN --}}
                <div class="relative" x-data="{ showFilter: false }">

                    <button type="button"
                            @click="showFilter = !showFilter"
                            class="shadow-theme-xs flex h-11 items-center gap-2 rounded-lg border border-gray-300
                            bg-white px-4 py-2.5 text-sm font-medium text-gray-700
                            dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">

                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none">
                            <path d="M14 6H2M18 14H6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                        </svg>

                        Filter
                    </button>

                    <div x-show="showFilter"
                         @click.away="showFilter = false"
                         x-transition
                         class="absolute right-0 z-10 mt-2 w-56 rounded-lg border border-gray-200
                         bg-white p-4 shadow-lg dark:border-gray-700 dark:bg-gray-800">

                        {{-- STATUS --}}
                        <div class="mb-4">
                            <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Status
                            </label>

                            <select name="status"
                                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                <option value="">All</option>
                                <option value="open" @selected(request('status')=='open')>Open</option>
                                <option value="in_progress" @selected(request('status')=='in_progress')>In Progress</option>
                                <option value="done" @selected(request('status')=='done')>Done</option>
                                <option value="cancelled" @selected(request('status')=='cancelled')>Cancelled</option>
                            </select>
                        </div>

                        {{-- PRIORITY --}}
                        <div class="mb-4">
                            <label class="mb-2 block text-xs font-medium text-gray-700 dark:text-gray-300">
                                Priority
                            </label>

                            <select name="priority"
                                    class="h-10 w-full rounded-lg border border-gray-300 bg-transparent px-3 text-sm
                                    dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                                <option value="">All</option>
                                <option value="low" @selected(request('priority')=='low')>Low</option>
                                <option value="medium" @selected(request('priority')=='medium')>Medium</option>
                                <option value="high" @selected(request('priority')=='high')>High</option>
                            </select>
                        </div>

                        {{-- SUBMIT --}}
                        <button class="bg-brand-500 hover:bg-brand-600 h-10 w-full rounded-lg text-sm font-medium text-white">
                            Apply
                        </button>

                        <a href="{{ route('crm.tasks.index') }}"
                           class="mt-2 flex h-10 w-full items-center justify-center rounded-lg border
                           border-gray-300 text-sm text-gray-700 dark:border-gray-700 dark:text-gray-300">
                            Reset
                        </a>

                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="max-w-full overflow-x-auto custom-scrollbar">

        <table class="min-w-full">

            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-800">

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Task
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Lead
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Priority
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Status
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Assigned
                    </th>

                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400">
                        Due
                    </th>

                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400">
                        Action
                    </th>

                </tr>
            </thead>

            <tbody class="divide-y divide-gray-100 dark:divide-white/[0.05]">

                @forelse($tasks as $task)
                    <tr>

                        {{-- TASK --}}
                        <td class="px-6 py-3.5">
                            <div class="flex flex-col">
                                <p class="font-medium text-gray-800 dark:text-white/90">
                                    {{ $task->title }}
                                </p>

                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ Str::limit($task->description, 40) }}
                                </p>
                            </div>
                        </td>

                        {{-- LEAD --}}
                        <td class="px-6 py-3.5 text-gray-500 dark:text-gray-400">
                            {{ $task->lead?->name ?? '-' }}
                        </td>

                        {{-- PRIORITY --}}
                        <td class="px-6 py-3.5">
                            @php
                                $priorityColors = [
                                    'low' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400',
                                    'medium' => 'bg-yellow-50 text-yellow-700 dark:bg-yellow-500/15 dark:text-yellow-400',
                                    'high' => 'bg-red-50 text-red-700 dark:bg-red-500/15 dark:text-red-400',
                                ];
                            @endphp

                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                {{ $priorityColors[$task->priority] ?? 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($task->priority) }}
                            </span>
                        </td>

                        {{-- STATUS --}}
                        <td class="px-6 py-3.5">
                            @php
                                $statusColors = [
                                    'open' => 'bg-gray-100 text-gray-700',
                                    'in_progress' => 'bg-blue-50 text-blue-700 dark:bg-blue-500/15 dark:text-blue-400',
                                    'done' => 'bg-green-50 text-green-700 dark:bg-green-500/15 dark:text-green-400',
                                    'cancelled' => 'bg-gray-200 text-gray-700',
                                ];
                            @endphp

                            <span class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium
                                {{ $statusColors[$task->status] ?? '' }}">
                                {{ ucwords(str_replace('_',' ', $task->status)) }}
                            </span>
                        </td>

                        {{-- ASSIGNED --}}
                        <td class="px-6 py-3.5 text-gray-500 dark:text-gray-400">
                            {{ $task->assignedTo?->name ?? '-' }}
                        </td>

                        {{-- DUE --}}
                        <td class="px-6 py-3.5 text-gray-500 dark:text-gray-400">
                            {{ $task->due_date?->format('d M Y') ?? '-' }}
                        </td>

                        {{-- ACTION --}}
                        <td class="px-6 py-3.5">
                            <div class="flex justify-end gap-3">

                                <a href="{{ route('crm.tasks.edit', $task) }}"
                                   class="text-brand-500 text-sm font-medium hover:text-brand-600">
                                    Edit
                                </a>

                                @if($task->status !== 'done')
                                    <form method="POST" action="{{ route('crm.tasks.status', $task) }}">
                                        @csrf
                                        @method('PATCH')

                                        <input type="hidden" name="status" value="done">

                                        <button class="text-green-600 text-sm font-medium hover:text-green-700">
                                            Done
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-6 py-10 text-center text-gray-500">
                            No tasks found
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>
    </div>

    {{-- PAGINATION (STYLE LEADS) --}}
    @if ($tasks->hasPages())
        <div class="flex flex-col items-center justify-between border-t border-gray-200 px-5 py-4 sm:flex-row dark:border-gray-800">

            <div class="pb-3 sm:pb-0 text-sm text-gray-500">
                Showing
                <span class="text-gray-800 dark:text-white/90">{{ $tasks->firstItem() }}</span>
                to
                <span class="text-gray-800 dark:text-white/90">{{ $tasks->lastItem() }}</span>
                of
                <span class="text-gray-800 dark:text-white/90">{{ $tasks->total() }}</span>
            </div>

            <div class="flex items-center gap-2">
                {{ $tasks->links() }}
            </div>

        </div>
    @endif

</div>

@endsection
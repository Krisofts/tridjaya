@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')

<x-common.page-breadcrumb pageTitle="Edit Task" />

<div class="max-w-2xl mx-auto">

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">

        {{-- HEADER --}}
        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-800">

            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">
                Edit Task
            </h2>

            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Update task information below.
            </p>

        </div>

        {{-- FORM --}}
        <form method="POST"
              action="{{ route('crm.tasks.update', $task) }}">

            @csrf
            @method('PUT')

            <div class="p-6 space-y-5">

                {{-- TITLE --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Task Title
                    </label>

                    <input type="text"
                           name="title"
                           value="{{ $task->title }}"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                           focus:border-brand-300 focus:ring-brand-500/10">
                </div>

                {{-- DESCRIPTION --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Description
                    </label>

                    <textarea name="description"
                              rows="4"
                              class="w-full rounded-lg border border-gray-300 bg-transparent px-4 py-3 text-sm
                              text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                              focus:border-brand-300 focus:ring-brand-500/10">{{ $task->description }}</textarea>
                </div>

                {{-- LEAD --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Lead
                    </label>

                    <select name="lead_id"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                        <option value="">Select Lead</option>

                        @foreach(\App\CRM\Models\Lead::all() as $lead)
                            <option value="{{ $lead->id }}"
                                @selected($task->lead_id == $lead->id)>
                                {{ $lead->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- ASSIGNED --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Assign To
                    </label>

                    <select name="assigned_to"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                        <option value="">Select User</option>

                        @foreach(\App\User\Models\User::select('id','name')->get() as $user)
                            <option value="{{ $user->id }}"
                                @selected($task->assigned_to == $user->id)>
                                {{ $user->name }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- PRIORITY --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Priority
                    </label>

                    <select name="priority"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                            dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">

                        @foreach(['low','medium','high'] as $priority)
                            <option value="{{ $priority }}"
                                @selected($task->priority == $priority)>
                                {{ ucfirst($priority) }}
                            </option>
                        @endforeach

                    </select>
                </div>

                {{-- DUE DATE --}}
                <div>
                    <label class="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Due Date
                    </label>

                    <input type="datetime-local"
                           name="due_date"
                           value="{{ $task->due_date?->format('Y-m-d\TH:i') }}"
                           class="h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 text-sm
                           text-gray-800 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90
                           focus:border-brand-300 focus:ring-brand-500/10">
                </div>

            </div>

            {{-- FOOTER --}}
            <div class="flex items-center justify-end gap-3 border-t border-gray-200 px-6 py-4 dark:border-gray-800">

                <a href="{{ route('crm.tasks.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border border-gray-300
                          bg-white px-4 py-2.5 text-sm font-medium text-gray-700
                          hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800
                          dark:text-gray-300 dark:hover:bg-gray-700">

                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg
                               bg-brand-500 px-4 py-2.5 text-sm font-medium text-white
                               shadow-theme-xs hover:bg-brand-600 transition">

                    Update Task
                </button>

            </div>

        </form>

    </div>

</div>

@endsection
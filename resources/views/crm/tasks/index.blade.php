@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="p-6 space-y-6 max-w-7xl mx-auto">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Task Management
            </h1>
            <p class="text-sm text-gray-500">
                Manage all CRM tasks efficiently
            </p>
        </div>

        <a href="{{ route('crm.tasks.create') }}"
           class="px-4 py-2 rounded-lg bg-blue-600 text-white hover:bg-blue-700">
            + New Task
        </a>

    </div>

    {{-- FILTER --}}
    <form method="GET" class="grid md:grid-cols-5 gap-3">

        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            placeholder="Search title..."
            class="border rounded-lg px-3 py-2"
        >

        <select name="status" class="border rounded-lg px-3 py-2">
            <option value="">All Status</option>
            <option value="open" @selected(request('status')=='open')>Open</option>
            <option value="in_progress" @selected(request('status')=='in_progress')>In Progress</option>
            <option value="done" @selected(request('status')=='done')>Done</option>
            <option value="cancelled" @selected(request('status')=='cancelled')>Cancelled</option>
        </select>

        <select name="priority" class="border rounded-lg px-3 py-2">
            <option value="">All Priority</option>
            <option value="low" @selected(request('priority')=='low')>Low</option>
            <option value="medium" @selected(request('priority')=='medium')>Medium</option>
            <option value="high" @selected(request('priority')=='high')>High</option>
        </select>

        {{-- OVERDUE FILTER --}}
        <select name="overdue" class="border rounded-lg px-3 py-2">
            <option value="">All</option>
            <option value="1" @selected(request('overdue')=='1')>
                Overdue Only
            </option>
        </select>

        <button class="bg-gray-900 text-white rounded-lg px-4">
            Filter
        </button>

    </form>

    {{-- TABLE --}}
    <div class="bg-white rounded-xl shadow overflow-hidden">

        <table class="w-full text-sm">

            <thead class="bg-gray-100 text-gray-600">
                <tr>
                    <th class="p-3 text-left">Task</th>
                    <th class="p-3 text-left">Lead</th>
                    <th class="p-3 text-left">Priority</th>
                    <th class="p-3 text-left">Status</th>
                    <th class="p-3 text-left">Assigned</th>
                    <th class="p-3 text-left">Due</th>
                    <th class="p-3 text-right"></th>
                </tr>
            </thead>

            <tbody>

                @forelse($tasks as $task)

                    <tr class="border-t {{ $task->isOverdue() ? 'bg-red-50' : '' }}">

                        {{-- TITLE --}}
                        <td class="p-3">
                            <div class="font-medium text-gray-900 flex items-center gap-2">

                                {{ $task->title }}

                                @if($task->isOverdue())
                                    <span class="text-xs px-2 py-0.5 rounded bg-red-100 text-red-700">
                                        Overdue
                                    </span>
                                @endif

                            </div>

                            <div class="text-xs text-gray-500">
                                {{ \Illuminate\Support\Str::limit($task->description, 40) }}
                            </div>
                        </td>

                        {{-- LEAD --}}
                        <td class="p-3">
                            {{ $task->lead?->name ?? '-' }}
                        </td>

                        {{-- PRIORITY --}}
                        <td class="p-3">
                            @if($task->priority === 'high')
                                <span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">High</span>
                            @elseif($task->priority === 'medium')
                                <span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Medium</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Low</span>
                            @endif
                        </td>

                        {{-- STATUS --}}
                        <td class="p-3">
                            @if($task->status === 'done')
                                <span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Done</span>
                            @elseif($task->status === 'in_progress')
                                <span class="px-2 py-1 text-xs rounded bg-blue-100 text-blue-700">In Progress</span>
                            @elseif($task->status === 'cancelled')
                                <span class="px-2 py-1 text-xs rounded bg-gray-200 text-gray-700">Cancelled</span>
                            @else
                                <span class="px-2 py-1 text-xs rounded bg-gray-100 text-gray-700">Open</span>
                            @endif
                        </td>

                        {{-- ASSIGNED --}}
                        <td class="p-3">
                            {{ $task->assignedTo?->name ?? '-' }}
                        </td>

                        {{-- DUE --}}
                        <td class="p-3">
                            <div>
                                {{ $task->due_date?->format('d M Y') ?? '-' }}

                                @if($task->isOverdue())
                                    <div class="text-xs text-red-600 font-semibold">
                                        Overdue
                                    </div>
                                @endif
                            </div>
                        </td>

                        {{-- ACTION --}}
                        <td class="p-3 text-right space-x-2">

                            <a href="{{ route('crm.tasks.edit', $task) }}"
                               class="text-blue-600">
                                Edit
                            </a>

                            @if($task->status !== 'done')
                                <form method="POST"
                                      action="{{ route('crm.tasks.status', $task) }}"
                                      class="inline">

                                    @csrf
                                    @method('PATCH')

                                    <input type="hidden" name="status" value="done">

                                    <button class="text-green-600">
                                        Done
                                    </button>

                                </form>
                            @endif

                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">
                            No tasks found.
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    {{-- PAGINATION --}}
    <div>
        {{ $tasks->links() }}
    </div>

</div>
@endsection
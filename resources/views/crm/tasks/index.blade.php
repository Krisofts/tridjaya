@extends('layouts.app')

@section('title', 'Tasks')

@section('content')
<div class="p-6 space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">CRM Tasks</h1>
            <p class="text-sm text-gray-500">Manage all lead tasks</p>
        </div>

        <a href="{{ route('crm.tasks.create') }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
            + New Task
        </a>
    </div>

    {{-- FILTER FORM --}}
    <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3 bg-white p-4 rounded-lg shadow">

        <input type="text"
               name="search"
               value="{{ request('search') }}"
               placeholder="Search title..."
               class="border rounded px-3 py-2 w-full">

        <select name="status" class="border rounded px-3 py-2">
            <option value="">All Status</option>
            @foreach([\App\CRM\Models\LeadTask::STATUS_OPEN,
                      \App\CRM\Models\LeadTask::STATUS_IN_PROGRESS,
                      \App\CRM\Models\LeadTask::STATUS_DONE,
                      \App\CRM\Models\LeadTask::STATUS_CANCELLED] as $status)
                <option value="{{ $status }}" @selected(request('status') == $status)>
                    {{ ucfirst(str_replace('_',' ', $status)) }}
                </option>
            @endforeach
        </select>

        <select name="priority" class="border rounded px-3 py-2">
            <option value="">All Priority</option>
            @foreach(\App\CRM\Models\LeadTask::priorities() as $priority)
                <option value="{{ $priority }}" @selected(request('priority') == $priority)>
                    {{ ucfirst($priority) }}
                </option>
            @endforeach
        </select>

        <input type="date"
               name="due_date"
               value="{{ request('due_date') }}"
               class="border rounded px-3 py-2">

        <button class="bg-gray-800 text-white rounded px-4 py-2 hover:bg-gray-900">
            Filter
        </button>
    </form>

    {{-- TABLE --}}
    <div class="bg-white shadow rounded-lg overflow-hidden">

        <table class="w-full text-sm">
            <thead class="bg-gray-100 text-left">
                <tr>
                    <th class="p-3">Title</th>
                    <th class="p-3">Type</th>
                    <th class="p-3">Priority</th>
                    <th class="p-3">Status</th>
                    <th class="p-3">Assigned</th>
                    <th class="p-3">Due Date</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($tasks as $task)
                    <tr class="border-t hover:bg-gray-50">

                        {{-- TITLE --}}
                        <td class="p-3">
                            <div class="font-medium text-gray-900">
                                {{ $task->title }}
                            </div>
                            <div class="text-xs text-gray-500">
                                {{ $task->lead?->name ?? 'No Lead' }}
                            </div>
                        </td>

                        {{-- TYPE --}}
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded bg-gray-100">
                                {{ $task->type_label }}
                            </span>
                        </td>

                        {{-- PRIORITY --}}
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded
                                @if($task->priority === 'high') bg-red-100 text-red-700
                                @elseif($task->priority === 'medium') bg-yellow-100 text-yellow-700
                                @else bg-green-100 text-green-700
                                @endif">
                                {{ $task->priority_label }}
                            </span>
                        </td>

                        {{-- STATUS --}}
                        <td class="p-3">
                            <span class="text-xs px-2 py-1 rounded
                                @if($task->status_color === 'green') bg-green-100 text-green-700
                                @elseif($task->status_color === 'yellow') bg-yellow-100 text-yellow-700
                                @elseif($task->status_color === 'red') bg-red-100 text-red-700
                                @else bg-blue-100 text-blue-700
                                @endif">
                                {{ $task->status_label }}
                            </span>
                        </td>

                        {{-- ASSIGNED --}}
                        <td class="p-3">
                            {{ $task->assignedTo?->name ?? '-' }}
                        </td>

                        {{-- DUE DATE --}}
                        <td class="p-3">
                            @if($task->due_date)
                                <span class="{{ $task->isOverdue() ? 'text-red-600 font-semibold' : '' }}">
                                    {{ $task->due_date->format('d M Y') }}
                                </span>
                            @else
                                -
                            @endif
                        </td>

                        {{-- ACTION --}}
                        <td class="p-3 text-right space-x-1">

                            <a href="{{ route('crm.tasks.show', $task) }}"
                               class="text-blue-600 hover:underline">
                                View
                            </a>

                            <a href="{{ route('crm.tasks.edit', $task) }}"
                               class="text-yellow-600 hover:underline">
                                Edit
                            </a>

                            @if($task->status !== \App\CRM\Models\LeadTask::STATUS_DONE)
                                <form action="{{ route('crm.tasks.start', $task) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    <button class="text-green-600 hover:underline">
                                        Start
                                    </button>
                                </form>
                            @endif

                            @if($task->status !== \App\CRM\Models\LeadTask::STATUS_DONE)
                                <form action="{{ route('crm.tasks.cancel', $task) }}"
                                      method="POST" class="inline">
                                    @csrf
                                    <button class="text-red-600 hover:underline"
                                            onclick="return confirm('Cancel this task?')">
                                        Cancel
                                    </button>
                                </form>
                            @endif

                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="p-6 text-center text-gray-500">
                            No tasks found
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
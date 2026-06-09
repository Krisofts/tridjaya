@extends('layouts.app')

@section('title', $task->title)

@section('content')
<div class="p-6 max-w-5xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">

        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                {{ $task->title }}
            </h1>

            <p class="text-sm text-gray-500">
                Lead:
                <span class="font-medium">
                    {{ $task->lead?->name ?? '-' }}
                </span>
            </p>
        </div>

        {{-- ACTIONS --}}
        <div class="flex gap-2">

            <a href="{{ route('crm.tasks.edit', $task) }}"
               class="px-4 py-2 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                Edit
            </a>

            @if($task->status !== \App\CRM\Models\LeadTask::STATUS_DONE)
                <form action="{{ route('crm.tasks.start', $task) }}" method="POST">
                    @csrf
                    <button class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">
                        Start
                    </button>
                </form>

                <form action="{{ route('crm.tasks.complete', $task) }}" method="POST">
    @csrf

    <select name="outcome" class="border rounded px-2 py-1">
        <option value="">Select Outcome</option>

        @foreach(\App\CRM\Models\LeadTask::outcomes() as $outcome)
            <option value="{{ $outcome }}">
                {{ ucfirst(str_replace('_',' ', $outcome)) }}
            </option>
        @endforeach
    </select>

    <textarea name="notes" class="border rounded px-2 py-1"></textarea>

    <button class="bg-blue-600 text-white px-3 py-1 rounded">
        Complete
    </button>
</form>
            @endif

            @if($task->status !== \App\CRM\Models\LeadTask::STATUS_CANCELLED)
                <form action="{{ route('crm.tasks.cancel', $task) }}" method="POST">
                    @csrf
                    <button class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700"
                            onclick="return confirm('Cancel this task?')">
                        Cancel
                    </button>
                </form>
            @endif

        </div>
    </div>

    {{-- STATUS / PRIORITY / TYPE --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Status</div>
            <div class="font-semibold text-gray-900">
                {{ $task->status_label }}
            </div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Priority</div>
            <div class="font-semibold text-gray-900">
                {{ $task->priority_label }}
            </div>
        </div>

        <div class="bg-white p-4 rounded shadow">
            <div class="text-sm text-gray-500">Type</div>
            <div class="font-semibold text-gray-900">
                {{ $task->type_label }}
            </div>
        </div>

    </div>

    {{-- MAIN INFO --}}
    <div class="bg-white shadow rounded-lg p-6 space-y-4">

        <div>
            <div class="text-sm text-gray-500">Description</div>
            <div class="text-gray-800">
                {{ $task->description ?? '-' }}
            </div>
        </div>

        <div>
            <div class="text-sm text-gray-500">Notes</div>
            <div class="text-gray-800">
                {{ $task->notes ?? '-' }}
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <div class="text-sm text-gray-500">Assigned To</div>
                <div class="font-medium">
                    {{ $task->assignedTo?->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Created By</div>
                <div class="font-medium">
                    {{ $task->createdBy?->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Completed By</div>
                <div class="font-medium">
                    {{ $task->completedBy?->name ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Due Date</div>
                <div class="font-medium {{ $task->isOverdue() ? 'text-red-600' : '' }}">
                    {{ $task->due_date?->format('d M Y') ?? '-' }}
                </div>
            </div>

            <div>
                <div class="text-sm text-gray-500">Reminder</div>
                <div class="font-medium">
                    {{ $task->reminder_at?->format('d M Y H:i') ?? '-' }}
                </div>
            </div>

        </div>

    </div>

    {{-- OUTCOME --}}
    <div class="bg-white shadow rounded-lg p-6">

        <div class="text-sm text-gray-500">Outcome</div>

        <div class="mt-1">
            @if($task->outcome)
                <span class="px-3 py-1 rounded bg-purple-100 text-purple-700 text-sm">
                    {{ $task->outcome_label }}
                </span>
            @else
                <span class="text-gray-400 text-sm">No outcome yet</span>
            @endif
        </div>

    </div>

    {{-- META --}}
    @if($task->metadata)
    <div class="bg-white shadow rounded-lg p-6">

        <div class="text-sm text-gray-500 mb-2">Metadata</div>

        <pre class="text-xs bg-gray-100 p-3 rounded overflow-auto">
{{ json_encode($task->metadata, JSON_PRETTY_PRINT) }}
        </pre>

    </div>
    @endif

</div>
@endsection
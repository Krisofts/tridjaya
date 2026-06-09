@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="p-6 max-w-3xl mx-auto space-y-6">

    {{-- HEADER --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Create Task</h1>
        <p class="text-sm text-gray-500">Add a new CRM task for a lead</p>
    </div>

    {{-- FORM --}}
    <form action="{{ route('crm.tasks.store') }}" method="POST"
          class="bg-white shadow rounded-lg p-6 space-y-5">

        @csrf

        {{-- LEAD ID --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Lead ID</label>
            <input type="number"
                   name="lead_id"
                   value="{{ old('lead_id') }}"
                   class="w-full border rounded px-3 py-2 mt-1"
                   placeholder="Enter lead ID">

            @error('lead_id')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- TITLE --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Title</label>
            <input type="text"
                   name="title"
                   value="{{ old('title') }}"
                   class="w-full border rounded px-3 py-2 mt-1"
                   placeholder="Task title">

            @error('title')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- DESCRIPTION --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Description</label>
            <textarea name="description"
                      class="w-full border rounded px-3 py-2 mt-1"
                      rows="3">{{ old('description') }}</textarea>

            @error('description')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- TYPE --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Type</label>
            <select name="type" class="w-full border rounded px-3 py-2 mt-1">

                <option value="">Select Type</option>

                @foreach(\App\CRM\Models\LeadTask::types() as $type)
                    <option value="{{ $type }}" @selected(old('type') == $type)>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach

            </select>

            @error('type')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- PRIORITY --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Priority</label>
            <select name="priority" class="w-full border rounded px-3 py-2 mt-1">

                <option value="">Default (Medium)</option>

                @foreach(\App\CRM\Models\LeadTask::priorities() as $priority)
                    <option value="{{ $priority }}" @selected(old('priority') == $priority)>
                        {{ ucfirst($priority) }}
                    </option>
                @endforeach

            </select>

            @error('priority')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- ASSIGNED TO --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Assigned To (User ID)</label>
            <input type="number"
                   name="assigned_to"
                   value="{{ old('assigned_to') }}"
                   class="w-full border rounded px-3 py-2 mt-1"
                   placeholder="User ID (optional)">

            @error('assigned_to')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- DUE DATE --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Due Date</label>
            <input type="date"
                   name="due_date"
                   value="{{ old('due_date') }}"
                   class="w-full border rounded px-3 py-2 mt-1">

            @error('due_date')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- REMINDER --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Reminder At</label>
            <input type="datetime-local"
                   name="reminder_at"
                   value="{{ old('reminder_at') }}"
                   class="w-full border rounded px-3 py-2 mt-1">

            @error('reminder_at')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- NOTES --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Notes</label>
            <textarea name="notes"
                      class="w-full border rounded px-3 py-2 mt-1"
                      rows="3">{{ old('notes') }}</textarea>

            @error('notes')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- METADATA --}}
        <div>
            <label class="text-sm font-medium text-gray-700">Metadata (JSON)</label>
            <textarea name="metadata"
                      class="w-full border rounded px-3 py-2 mt-1 font-mono"
                      rows="3">{{ old('metadata') }}</textarea>

            @error('metadata')
                <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- ACTION --}}
        <div class="flex justify-end gap-2">
            <a href="{{ route('crm.tasks.index') }}"
               class="px-4 py-2 border rounded hover:bg-gray-100">
                Cancel
            </a>

            <button type="submit"
                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Create Task
            </button>
        </div>

    </form>

</div>
@endsection
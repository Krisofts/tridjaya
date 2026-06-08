@extends('layouts.app')

@section('title', 'Edit Task')

@section('content')
<div class="p-6 max-w-2xl">

    <h1 class="text-2xl font-bold mb-4">Edit Task</h1>

    <form method="POST"
          action="{{ route('crm.tasks.update', $task) }}"
          class="bg-white p-6 rounded-xl shadow space-y-4">

        @csrf
        @method('PUT')

        <input type="text"
               name="title"
               value="{{ $task->title }}"
               class="w-full border rounded-lg p-2">

        <textarea name="description"
                  class="w-full border rounded-lg p-2">{{ $task->description }}</textarea>

        <select name="lead_id" class="w-full border rounded-lg p-2">
            <option value="">Select Lead</option>
            @foreach(\App\CRM\Models\Lead::all() as $lead)
                <option value="{{ $lead->id }}"
                    @selected($task->lead_id == $lead->id)>
                    {{ $lead->name }}
                </option>
            @endforeach
        </select>

        <select name="assigned_to" class="w-full border rounded-lg p-2">
            <option value="">Assign User</option>
            @foreach(\App\User\Models\User::select('id','name')->get() as $user)
                <option value="{{ $user->id }}"
                    @selected($task->assigned_to == $user->id)>
                    {{ $user->name }}
                </option>
            @endforeach
        </select>

        <select name="priority" class="w-full border rounded-lg p-2">
            @foreach(['low','medium','high'] as $priority)
                <option value="{{ $priority }}"
                    @selected($task->priority == $priority)>
                    {{ ucfirst($priority) }}
                </option>
            @endforeach
        </select>

        <input type="datetime-local"
               name="due_date"
               value="{{ $task->due_date?->format('Y-m-d\TH:i') }}"
               class="w-full border rounded-lg p-2">

        <button class="w-full bg-yellow-600 text-white py-2 rounded-lg hover:bg-yellow-700">
            Update Task
        </button>

    </form>

</div>
@endsection
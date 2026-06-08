@extends('layouts.app')

@section('title', 'Create Task')

@section('content')
<div class="p-6 max-w-2xl">

    <h1 class="text-2xl font-bold mb-4">Create Task</h1>

    <form method="POST" action="{{ route('crm.tasks.store') }}"
          class="bg-white p-6 rounded-xl shadow space-y-4">

        @csrf

        <input type="text"
               name="title"
               placeholder="Task Title"
               class="w-full border rounded-lg p-2"
               required>

        <textarea name="description"
                  placeholder="Description"
                  class="w-full border rounded-lg p-2"></textarea>

        <select name="lead_id" class="w-full border rounded-lg p-2">
            <option value="">Select Lead</option>
            @foreach(\App\CRM\Models\Lead::all() as $lead)
                <option value="{{ $lead->id }}">{{ $lead->name }}</option>
            @endforeach
        </select>

        <select name="assigned_to" class="w-full border rounded-lg p-2">
            <option value="">Assign User</option>
            @foreach(\App\User\Models\User::select('id','name')->get() as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>

        <select name="priority" class="w-full border rounded-lg p-2">
            <option value="medium">Medium</option>
            <option value="low">Low</option>
            <option value="high">High</option>
        </select>

        <input type="datetime-local"
               name="due_date"
               class="w-full border rounded-lg p-2">

        <button class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">
            Save Task
        </button>

    </form>

</div>
@endsection
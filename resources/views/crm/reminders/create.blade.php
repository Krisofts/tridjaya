<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Reminder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100"><div class="max-w-3xl mx-auto px-4 py-6"><h1 class="text-2xl font-bold mb-1">
    Create Reminder
</h1>

<p class="text-gray-500 mb-6">
    Lead: {{ $lead->name }}
</p>

<form method="POST"
      action="{{ route('crm.leads.reminders.store', $lead) }}"
      class="bg-white p-6 rounded-xl shadow space-y-4">

    @csrf

    <div>
        <label class="block mb-1">Title</label>
        <input type="text"
               name="title"
               value="{{ old('title') }}"
               class="w-full border rounded-lg px-3 py-2">
    </div>

    <div>
        <label class="block mb-1">Description</label>
        <textarea name="description"
                  rows="4"
                  class="w-full border rounded-lg px-3 py-2">{{ old('description') }}</textarea>
    </div>

    <div>
        <label class="block mb-1">Type</label>
        <select name="type"
                class="w-full border rounded-lg px-3 py-2">

            <option value="follow_up">Follow Up</option>
            <option value="call">Call</option>
            <option value="meeting">Meeting</option>
            <option value="email">Email</option>

        </select>
    </div>

    <div>
        <label class="block mb-1">Reminder Date</label>
        <input type="datetime-local"
               name="remind_at"
               class="w-full border rounded-lg px-3 py-2">
    </div>

    <div>
        <label class="block mb-1">Assign To</label>

        <select name="assigned_to"
                class="w-full border rounded-lg px-3 py-2">

            <option value="">
                -- Select User --
            </option>

            @foreach($users as $user)
                <option value="{{ $user->id }}">
                    {{ $user->name }}
                </option>
            @endforeach

        </select>
    </div>

    <div class="flex gap-2">

        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg">
            Save
        </button>

        <a href="{{ route('crm.leads.show', $lead) }}"
           class="px-4 py-2 bg-gray-200 rounded-lg">
            Cancel
        </a>

    </div>

</form>

</div></body>
</html>
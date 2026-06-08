<!DOCTYPE html><html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Reminder</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100"><div class="max-w-3xl mx-auto px-4 py-6"><h1 class="text-2xl font-bold mb-6">
    Edit Reminder
</h1>

<form method="POST"
      action="{{ route('crm.reminders.update', $reminder) }}"
      class="bg-white p-6 rounded-xl shadow space-y-4">

    @csrf
    @method('PUT')

    <div>
        <label class="block mb-1">Title</label>
        <input type="text"
               name="title"
               value="{{ old('title', $reminder->title) }}"
               class="w-full border rounded-lg px-3 py-2">
    </div>

    <div>
        <label class="block mb-1">Description</label>
        <textarea name="description"
                  rows="4"
                  class="w-full border rounded-lg px-3 py-2">{{ old('description', $reminder->description) }}</textarea>
    </div>

    <div>
        <label class="block mb-1">Reminder Date</label>
        <input type="datetime-local"
               name="remind_at"
               value="{{ $reminder->remind_at?->format('Y-m-d\TH:i') }}"
               class="w-full border rounded-lg px-3 py-2">
    </div>

    <div>
        <label class="block mb-1">Status</label>

        <select name="status"
                class="w-full border rounded-lg px-3 py-2">

            <option value="pending" @selected($reminder->status === 'pending')>
                Pending
            </option>

            <option value="done" @selected($reminder->status === 'done')>
                Done
            </option>

            <option value="cancelled" @selected($reminder->status === 'cancelled')>
                Cancelled
            </option>

        </select>
    </div>

    <div class="flex gap-2">

        <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg">
            Update
        </button>

        <a href="{{ route('crm.reminders.index') }}"
           class="px-4 py-2 bg-gray-200 rounded-lg">
            Cancel
        </a>

    </div>

</form>

</div></body>
</html>
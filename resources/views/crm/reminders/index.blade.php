<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reminder Management</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-7xl mx-auto px-4 py-6">

    <h1 class="text-2xl font-bold mb-4">Reminder Management</h1>

    {{-- FILTER --}}
    <form class="bg-white p-4 rounded-xl shadow mb-4 flex gap-2 flex-wrap">

        <input type="text" name="search"
               value="{{ request('search') }}"
               placeholder="Search lead / phone"
               class="border px-3 py-2 rounded">

        <select name="status" class="border px-3 py-2 rounded">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="done">Done</option>
            <option value="cancelled">Cancelled</option>
        </select>

        <select name="type" class="border px-3 py-2 rounded">
            <option value="">All Type</option>
            <option value="auto_follow_up">Auto</option>
            <option value="follow_up">Manual</option>
        </select>

        <select name="user_id" class="border px-3 py-2 rounded">
            <option value="">All Users</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
            @endforeach
        </select>

        <button class="bg-blue-600 text-white px-4 py-2 rounded">
            Filter
        </button>

    </form>

    {{-- LIST --}}
    <div class="bg-white rounded-xl shadow divide-y">

        @forelse($reminders as $reminder)

            <div class="p-4 flex justify-between">

                <div>

                    <h3 class="font-semibold">
                        {{ $reminder->title ?? '-' }}
                    </h3>

                    <div class="text-xs text-gray-500">
                        Lead: {{ $reminder->lead?->name ?? '-' }}
                        ({{ $reminder->lead?->phone ?? '-' }})
                    </div>

                    <div class="text-xs text-gray-400">
                        Assigned: {{ $reminder->assignedTo?->name ?? 'Unassigned' }}
                    </div>

                </div>

                <div class="text-right">

                    <div>
                        {{ optional($reminder->remind_at)->format('d M Y H:i') }}
                    </div>

                    @if($reminder->status === 'pending' && $reminder->remind_at < now())
                        <div class="text-red-500 text-xs">OVERDUE</div>
                    @endif

                    <div class="flex gap-2 mt-2">

                        <form method="POST" action="{{ route('crm.reminders.done', $reminder) }}">
                            @csrf @method('PATCH')
                            <button class="text-xs bg-green-500 text-white px-2 py-1 rounded">Done</button>
                        </form>

                        <form method="POST" action="{{ route('crm.reminders.cancel', $reminder) }}">
                            @csrf @method('PATCH')
                            <button class="text-xs bg-red-500 text-white px-2 py-1 rounded">Cancel</button>
                        </form>

                    </div>

                </div>

            </div>

        @empty
            <div class="p-10 text-center text-gray-500">
                No reminders found
            </div>
        @endforelse

    </div>

</div>

</body>
</html>
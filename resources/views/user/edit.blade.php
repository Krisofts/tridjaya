<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-3xl mx-auto px-4 py-6">

    <div class="bg-white rounded-xl shadow-sm">

        {{-- HEADER --}}
        <div class="border-b px-6 py-4">

            <h1 class="text-2xl font-bold text-gray-900">
                Edit User
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Update user information, branch, and groups
            </p>

        </div>

        {{-- ERRORS --}}
        @if ($errors->any())
            <div class="m-6 rounded-lg border border-red-200 bg-red-50 p-4">

                <div class="font-medium text-red-700 mb-2">
                    Please fix the following errors:
                </div>

                <ul class="list-disc list-inside text-sm text-red-600">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>

            </div>
        @endif

        @php
            $userGroups = old(
                'groups',
                $user->groups->pluck('group')->toArray()
            );
        @endphp

        <form action="{{ route('users.update', $user) }}"
              method="POST"
              class="p-6">

            @csrf
            @method('PUT')

            {{-- NAME --}}
            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Name
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name', $user->name) }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring focus:ring-blue-200"
                       required>

            </div>

            {{-- EMAIL --}}
            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>

                <input type="email"
                       name="email"
                       value="{{ old('email', $user->email) }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring focus:ring-blue-200"
                       required>

            </div>

            {{-- BRANCH (🔥 NEW) --}}
            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Branch
                </label>

                <select name="branch_id"
                        class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring focus:ring-blue-200">

                    <option value="">-- Select Branch --</option>

                    @foreach($branches as $branch)
                        <option value="{{ $branch->id }}"
                            {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>

                            {{ $branch->code }} - {{ $branch->name }}

                        </option>
                    @endforeach

                </select>

                @error('branch_id')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror

            </div>

            {{-- PASSWORD --}}
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    New Password
                </label>

                <div class="relative">

                    <input id="password"
                           type="password"
                           name="password"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 pr-12"
                           placeholder="Leave blank if unchanged">

                    <button type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-2.5 text-gray-500">
                        👁
                    </button>

                </div>

                <p class="mt-1 text-sm text-gray-500">
                    Leave blank if you don't want to change the password.
                </p>

            </div>

            {{-- GROUPS --}}
            <div class="mb-6">

                <label class="block text-sm font-medium text-gray-700 mb-3">
                    User Groups
                </label>

                <div class="grid gap-3">

                    @foreach($groups as $key => $group)

                        <label class="flex items-start gap-3 rounded-lg border p-4 hover:bg-gray-50 cursor-pointer">

                            <input type="checkbox"
                                   name="groups[]"
                                   value="{{ $key }}"
                                   class="mt-1"
                                   {{ in_array($key, $userGroups) ? 'checked' : '' }}>

                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ $group['title'] }}
                                </div>

                                @if(!empty($group['description']))
                                    <div class="text-sm text-gray-500 mt-1">
                                        {{ $group['description'] }}
                                    </div>
                                @endif
                            </div>

                        </label>

                    @endforeach

                </div>

            </div>

            {{-- USER INFO --}}
            <div class="mb-6 rounded-lg bg-gray-50 p-4 text-sm">

                <div class="grid gap-2">

                    <div><b>User ID:</b> {{ $user->id }}</div>

                    <div><b>Created:</b> {{ $user->created_at?->format('d M Y H:i') }}</div>

                    @if($user->updated_at)
                        <div><b>Updated:</b> {{ $user->updated_at->format('d M Y H:i') }}</div>
                    @endif

                    <div>
                        <b>Branch:</b>
                        @if($user->branch)
                            {{ $user->branch->code }} - {{ $user->branch->name }}
                        @else
                            <span class="text-gray-400">No Branch</span>
                        @endif
                    </div>

                </div>

            </div>

            {{-- ACTIONS --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border px-4 py-2">
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    Update User
                </button>

            </div>

        </form>

    </div>

</div>

<script>
function togglePassword()
{
    const password = document.getElementById('password');
    password.type = password.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>
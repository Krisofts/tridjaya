<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User</title>

    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">

<div class="max-w-2xl mx-auto px-4 py-6">

    <div class="bg-white rounded-xl shadow-sm">

        {{-- HEADER --}}
        <div class="border-b px-6 py-4">

            <h1 class="text-2xl font-bold text-gray-900">
                Create User
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Create a new user account with branch access
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

        <form method="POST"
              action="{{ route('users.store') }}"
              class="p-6">

            @csrf

            {{-- NAME --}}
            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Name
                </label>

                <input type="text"
                       name="name"
                       value="{{ old('name') }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring focus:ring-blue-200"
                       placeholder="Enter full name"
                       required>

            </div>

            {{-- EMAIL --}}
            <div class="mb-5">

                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Email
                </label>

                <input type="email"
                       name="email"
                       value="{{ old('email') }}"
                       class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:ring focus:ring-blue-200"
                       placeholder="user@example.com"
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
                            {{ old('branch_id') == $branch->id ? 'selected' : '' }}>

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
                    Password
                </label>

                <div class="relative">

                    <input id="password"
                           type="password"
                           name="password"
                           class="w-full rounded-lg border border-gray-300 px-4 py-2 pr-12 focus:ring focus:ring-blue-200"
                           placeholder="Minimum 6 characters"
                           required>

                    <button type="button"
                            onclick="togglePassword()"
                            class="absolute right-3 top-2.5 text-gray-500">
                        👁
                    </button>

                </div>

            </div>

            {{-- BUTTONS --}}
            <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">

                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center justify-center rounded-lg border px-4 py-2">
                    Cancel
                </a>

                <button type="submit"
                        class="inline-flex items-center justify-center rounded-lg bg-blue-600 px-4 py-2 text-white hover:bg-blue-700">
                    Save User
                </button>

            </div>

        </form>

    </div>

</div>

<script>
function togglePassword()
{
    const input = document.getElementById('password');

    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>

</body>
</html>
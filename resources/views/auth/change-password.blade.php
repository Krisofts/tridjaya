<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password</title>

    {{-- Simple CDN (boleh diganti Vite nanti) --}}
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100 dark:bg-gray-950 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md">

        <div class="bg-white dark:bg-gray-900 shadow-lg rounded-2xl p-6">

            <h1 class="text-xl font-semibold text-gray-800 dark:text-white">
                Ganti Password
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1 mb-6">
                Untuk keamanan akun, silakan ganti password Anda sebelum melanjutkan.
            </p>

            {{-- SUCCESS MESSAGE --}}
            @if(session('success'))
                <div class="mb-4 p-3 rounded-lg bg-green-100 text-green-700 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            {{-- ERROR VALIDATION --}}
            @if ($errors->any())
                <div class="mb-4 p-3 rounded-lg bg-red-100 text-red-700 text-sm">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf

                {{-- CURRENT PASSWORD --}}
                <div>
                    <label class="text-sm text-gray-700 dark:text-gray-300">
                        Password Saat Ini
                    </label>
                    <input
                        type="password"
                        name="current_password"
                        required
                        class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                {{-- NEW PASSWORD --}}
                <div>
                    <label class="text-sm text-gray-700 dark:text-gray-300">
                        Password Baru
                    </label>
                    <input
                        type="password"
                        name="password"
                        required
                        class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                {{-- CONFIRM PASSWORD --}}
                <div>
                    <label class="text-sm text-gray-700 dark:text-gray-300">
                        Konfirmasi Password
                    </label>
                    <input
                        type="password"
                        name="password_confirmation"
                        required
                        class="mt-1 w-full px-3 py-2 rounded-lg border border-gray-300 dark:border-gray-700 dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                </div>

                {{-- BUTTON --}}
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 rounded-lg transition"
                >
                    Update Password
                </button>
            </form>

        </div>

    </div>

</body>
</html>
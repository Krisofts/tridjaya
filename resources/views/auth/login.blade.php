<x-guest-layout>
    {{-- Session Status --}}
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- NIK atau Email --}}
        <div>
            <label for="login" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                NIK atau Email
            </label>
            <input
                id="login"
                name="login"
                type="text"
                value="{{ old('login') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan NIK atau email"
                class="w-full px-4 py-2.5 text-sm rounded-lg border
                       bg-white dark:bg-gray-900
                       border-gray-300 dark:border-gray-700
                       text-gray-900 dark:text-white
                       placeholder-gray-400 dark:placeholder-gray-500
                       focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                       transition duration-150
                       {{ $errors->has('login') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : '' }}"
            >
            @error('login')
                <p class="mt-1.5 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mt-4">
            <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                Password
            </label>
            <div class="relative" x-data="{ show: false }">
                <input
                    id="password"
                    name="password"
                    :type="show ? 'text' : 'password'"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                    class="w-full px-4 py-2.5 pr-10 text-sm rounded-lg border
                           bg-white dark:bg-gray-900
                           border-gray-300 dark:border-gray-700
                           text-gray-900 dark:text-white
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
                           transition duration-150
                           {{ $errors->has('password') ? 'border-red-400 dark:border-red-500 focus:ring-red-500' : '' }}"
                >
                {{-- Toggle show/hide password --}}
                <button type="button" @click="show = !show"
                        class="absolute inset-y-0 right-0 flex items-center px-3 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors">
                    <svg x-show="!show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                    </svg>
                    <svg x-show="show" class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/>
                    </svg>
                </button>
            </div>
            @error('password')
                <p class="mt-1.5 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="mt-4 flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center gap-2 cursor-pointer">
                <input
                    id="remember_me"
                    type="checkbox"
                    name="remember"
                    class="w-4 h-4 rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900
                           text-blue-600 focus:ring-blue-500 dark:focus:ring-offset-gray-800"
                >
                <span class="text-sm text-gray-600 dark:text-gray-400">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}"
                   class="text-sm text-blue-600 dark:text-blue-400 hover:underline focus:outline-none focus:ring-2 focus:ring-blue-500 rounded">
                    Lupa password?
                </a>
            @endif
        </div>

        {{-- Submit --}}
        <div class="mt-6">
            <button type="submit"
                    class="w-full flex items-center justify-center gap-2 px-4 py-2.5
                           bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                           text-white text-sm font-semibold rounded-lg
                           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                           dark:focus:ring-offset-gray-900
                           transition duration-150 shadow-sm">
                Masuk
            </button>
        </div>

    </form>
</x-guest-layout>
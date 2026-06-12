@extends('layouts.guest')

@section('content')
<div class="w-full max-w-md mx-auto">

    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">
            Sign In
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            Login to your account
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}">
        @csrf

        {{-- Email --}}
        <div class="mb-4">
            <label
                for="email"
                class="block mb-2 text-sm font-medium text-gray-700"
            >
                Email
            </label>

            <input
                id="email"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('email')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Password --}}
        <div class="mb-4">
            <label
                for="password"
                class="block mb-2 text-sm font-medium text-gray-700"
            >
                Password
            </label>

            <input
                id="password"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                class="w-full rounded-lg border border-gray-300 px-4 py-2 focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('password')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Remember Me --}}
        <div class="mb-6 flex items-center">
            <input
                id="remember"
                type="checkbox"
                name="remember"
                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
            >

            <label
                for="remember"
                class="ml-2 text-sm text-gray-600"
            >
                Remember me
            </label>
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
        >
            Login
        </button>

        @if (Route::has('password.request'))
            <div class="mt-4 text-center">
                <a
                    href="{{ route('password.request') }}"
                    class="text-sm text-indigo-600 hover:underline"
                >
                    Forgot Password?
                </a>
            </div>
        @endif
    </form>
</div>
@endsection
@extends('layouts.guest')

@section('title', 'Register')

@section('content')

<div class="w-full">
    <div class="mb-6 text-center">
        <h1 class="text-2xl font-bold text-gray-900">
            Create Account
        </h1>

        <p class="mt-2 text-sm text-gray-600">
            Register a new account to continue
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        {{-- Name --}}
        <div class="mb-4">
            <label
                for="name"
                class="block mb-2 text-sm font-medium text-gray-700"
            >
                Name
            </label>

            <input
                id="name"
                name="name"
                type="text"
                value="{{ old('name') }}"
                required
                autofocus
                autocomplete="name"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('name')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

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
                name="email"
                type="email"
                value="{{ old('email') }}"
                required
                autocomplete="username"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
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
                name="password"
                type="password"
                required
                autocomplete="new-password"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >

            @error('password')
                <p class="mt-1 text-sm text-red-600">
                    {{ $message }}
                </p>
            @enderror
        </div>

        {{-- Confirm Password --}}
        <div class="mb-6">
            <label
                for="password_confirmation"
                class="block mb-2 text-sm font-medium text-gray-700"
            >
                Confirm Password
            </label>

            <input
                id="password_confirmation"
                name="password_confirmation"
                type="password"
                required
                autocomplete="new-password"
                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
            >
        </div>

        {{-- Submit --}}
        <button
            type="submit"
            class="w-full rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white transition hover:bg-indigo-700"
        >
            Register
        </button>

        {{-- Login Link --}}
        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                Already have an account?

                <a
                    href="{{ route('login') }}"
                    class="font-medium text-indigo-600 hover:text-indigo-500"
                >
                    Sign in
                </a>
            </p>
        </div>
    </form>
</div>

@endsection
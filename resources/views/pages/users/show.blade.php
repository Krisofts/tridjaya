@extends('layouts.app')

@section('content')
<div class="p-6 max-w-4xl mx-auto">

    {{-- HEADER --}}
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold">
            User Detail
        </h1>

        <div class="space-x-2">
            <a href="{{ route('users.edit', $user) }}"
               class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600">
                Edit
            </a>

            <a href="{{ route('users.index') }}"
               class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">
                Back
            </a>
        </div>
    </div>

    {{-- USER INFO --}}
    <div class="bg-white shadow rounded-lg p-6 mb-6">

        <div class="grid grid-cols-2 gap-4">

            <div>
                <p class="text-sm text-gray-500">Name</p>
                <p class="font-semibold">{{ $user->name }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-semibold">{{ $user->email }}</p>
            </div>

            <div>
                <p class="text-sm text-gray-500">Status</p>

                @if ($user->deleted_at)
                    <span class="px-2 py-1 text-xs bg-red-100 text-red-600 rounded">
                        Deleted
                    </span>
                @else
                    <span class="px-2 py-1 text-xs bg-green-100 text-green-600 rounded">
                        Active
                    </span>
                @endif
            </div>

            <div>
                <p class="text-sm text-gray-500">Created At</p>
                <p class="font-semibold">
                    {{ $user->created_at?->format('d M Y H:i') }}
                </p>
            </div>

        </div>

    </div>

    {{-- GROUPS --}}
    <div class="bg-white shadow rounded-lg p-6 mb-6">

        <h2 class="font-semibold mb-3">Groups</h2>

        @if (count($groups ?? []) > 0)

            <div class="flex flex-wrap gap-2">

                @foreach ($groups as $group)
                    <span class="px-3 py-1 text-sm bg-gray-200 rounded-full">
                        {{ $group }}
                    </span>
                @endforeach

            </div>

        @else
            <p class="text-gray-500 text-sm">No groups assigned</p>
        @endif

    </div>

    {{-- PERMISSIONS --}}
    <div class="bg-white shadow rounded-lg p-6">

        <h2 class="font-semibold mb-3">Permissions</h2>

        @if (count($permissions ?? []) > 0)

            <div class="grid grid-cols-2 gap-2">

                @foreach ($permissions as $permission)
                    <div class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded">
                        {{ $permission }}
                    </div>
                @endforeach

            </div>

        @else
            <p class="text-gray-500 text-sm">No permissions assigned</p>
        @endif

    </div>

</div>
@endsection
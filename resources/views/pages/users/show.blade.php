@extends('layouts.app')

@section('content')



<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

    {{-- MAIN --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- USER INFO (SINGLE CARD) --}}
        <x-common.component-card title="User Information">

            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ $user->email }}
                    </p>
                </div>

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

            <div class="grid grid-cols-2 gap-4 text-sm">

                <div>
                    <p class="text-gray-500">Created At</p>
                    <p class="font-medium">
                        {{ $user->created_at?->format('d M Y H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500">Updated At</p>
                    <p class="font-medium">
                        {{ $user->updated_at?->format('d M Y H:i') }}
                    </p>
                </div>

            </div>

        </x-common.component-card>

        {{-- GROUPS + PERMISSIONS (MERGED) --}}
        <x-common.component-card title="Access Control">

            <div class="space-y-4">

                {{-- Groups --}}
                <div>
                    <p class="text-sm text-gray-500 mb-2">Groups</p>

                    <div class="flex flex-wrap gap-2">
                        @forelse ($groups as $group)
                            <span class="px-3 py-1 text-sm bg-gray-200 rounded-full">
                                {{ $group }}
                            </span>
                        @empty
                            <p class="text-sm text-gray-500">No groups assigned</p>
                        @endforelse
                    </div>
                </div>

                {{-- Permissions --}}
                <div>
                    <p class="text-sm text-gray-500 mb-2">Permissions</p>

                    <div class="flex flex-wrap gap-2">
                        @forelse ($permissions as $permission)
                            <span class="px-3 py-1 text-sm bg-blue-50 text-blue-700 rounded">
                                {{ $permission }}
                            </span>
                        @empty
                            <p class="text-sm text-gray-500">No permissions assigned</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </x-common.component-card>

    </div>

    {{-- SIDEBAR (MINIMAL) --}}
    <div class="space-y-6">

        <x-common.component-card title="Summary">

            <div class="space-y-3 text-sm">

                <div>
                    <p class="text-gray-500">Name</p>
                    <p class="font-medium">{{ $user->name }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Email</p>
                    <p class="font-medium">{{ $user->email }}</p>
                </div>

                <div>
                    <p class="text-gray-500">Status</p>
                    <p class="font-medium">
                        {{ $user->deleted_at ? 'Deleted' : 'Active' }}
                    </p>
                </div>

            </div>

        </x-common.component-card>

    </div>

</div>

@endsection
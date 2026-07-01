@extends('layouts.app')

@section('content')

<div class="grid grid-cols-1 gap-6 xl:grid-cols-3">

    {{-- MAIN --}}
    <div class="xl:col-span-2 space-y-6">

        {{-- USER INFO --}}
        <x-common.component-card title="Informasi User">

            <div class="flex items-start justify-between mb-6">
                <div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                        {{ $user->name }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $user->email }}
                    </p>
                    @if($user->nik)
                        <p class="text-sm text-gray-400 dark:text-gray-500 mt-0.5">
                            NIK: {{ $user->nik }}
                        </p>
                    @endif
                </div>

                @if($user->deleted_at)
                    <span class="px-2.5 py-1 text-xs font-medium bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                        Dihapus
                    </span>
                @else
                    <span class="px-2.5 py-1 text-xs font-medium bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400 rounded-full">
                        Aktif
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">

                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">Branch</p>
                    <p class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $user->branch?->name ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">NIK</p>
                    <p class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $user->nik ?? '-' }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">Dibuat</p>
                    <p class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $user->created_at?->format('d M Y H:i') }}
                    </p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">Diperbarui</p>
                    <p class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $user->updated_at?->format('d M Y H:i') }}
                    </p>
                </div>

                @if($user->deleted_at)
                <div class="col-span-2">
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">Dihapus pada</p>
                    <p class="font-medium text-red-600 dark:text-red-400">
                        {{ $user->deleted_at->format('d M Y H:i') }}
                    </p>
                </div>
                @endif

            </div>

        </x-common.component-card>

        {{-- GROUPS + PERMISSIONS --}}
        <x-common.component-card title="Hak Akses">

            <div class="space-y-5">

                {{-- Groups --}}
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2.5">
                        Groups
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($groups as $group)
                            <span class="px-3 py-1 text-sm font-medium bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 rounded-full capitalize">
                                {{ $group }}
                            </span>
                        @empty
                            <p class="text-sm text-gray-400 dark:text-gray-500">Tidak ada group.</p>
                        @endforelse
                    </div>
                </div>

                <div class="border-t border-gray-100 dark:border-gray-700"></div>

                {{-- Permissions --}}
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2.5">
                        Permissions
                    </p>
                    <div class="flex flex-wrap gap-2">
                        @forelse($permissions as $permission)
                            <span class="px-3 py-1 text-xs font-medium bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400 rounded-md">
                                {{ $permission }}
                            </span>
                        @empty
                            <p class="text-sm text-gray-400 dark:text-gray-500">Tidak ada permission.</p>
                        @endforelse
                    </div>
                </div>

            </div>

        </x-common.component-card>

    </div>

    {{-- SIDEBAR --}}
    <div class="space-y-6">

        {{-- SUMMARY --}}
        <x-common.component-card title="Ringkasan">
            <div class="space-y-4 text-sm">

                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">Nama</p>
                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $user->name }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">NIK</p>
                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $user->nik ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">Email</p>
                    <p class="font-medium text-gray-800 dark:text-gray-200 break-all">{{ $user->email }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">Branch</p>
                    <p class="font-medium text-gray-800 dark:text-gray-200">{{ $user->branch?->name ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-gray-500 dark:text-gray-400 mb-0.5">Status</p>
                    @if($user->deleted_at)
                        <span class="inline-flex px-2.5 py-0.5 text-xs font-medium bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400 rounded-full">
                            Dihapus
                        </span>
                    @else
                        <span class="inline-flex px-2.5 py-0.5 text-xs font-medium bg-green-100 text-green-600 dark:bg-green-900/30 dark:text-green-400 rounded-full">
                            Aktif
                        </span>
                    @endif
                </div>

            </div>
        </x-common.component-card>

        {{-- ACTIONS --}}
        <x-common.component-card title="Aksi">
            <div class="space-y-2">

                <a href="{{ route('users.edit', $user) }}"
                   class="flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium
                          bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Edit User
                </a>

                @if($user->deleted_at)
                <form action="{{ route('users.restore', $user->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium
                                   bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                        Pulihkan User
                    </button>
                </form>
                @else
                <form action="{{ route('users.destroy', $user) }}" method="POST"
                      onsubmit="return confirm('Hapus user {{ $user->name }}?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium
                                   bg-red-50 hover:bg-red-100 text-red-600 dark:bg-red-900/20
                                   dark:hover:bg-red-900/30 dark:text-red-400 rounded-lg transition-colors">
                        Hapus User
                    </button>
                </form>
                @endif

                <a href="{{ route('users.index') }}"
                   class="flex items-center justify-center w-full px-4 py-2.5 text-sm font-medium
                          bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600
                          text-gray-700 dark:text-gray-300 rounded-lg transition-colors">
                    Kembali ke Daftar
                </a>

            </div>
        </x-common.component-card>

    </div>

</div>

@endsection
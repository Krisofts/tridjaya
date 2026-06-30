{{-- resources/views/pages/crm/tasks/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Tambah Task')

@section('content')

<nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-5">
    <a href="{{ route('crm.tasks.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Tasks</a>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
    <span class="text-gray-800 dark:text-white font-medium">Tambah</span>
</nav>

<div class="flex items-center justify-between mb-6">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Tambah Task</h1>
    <a href="{{ route('crm.tasks.index') }}"
       class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m19 12H5M12 5l-7 7 7 7"/></svg>
        Kembali
    </a>
</div>

<form method="POST" action="{{ route('crm.tasks.store') }}">
    @csrf

    @include('pages.crm.tasks._form')

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('crm.tasks.index') }}"
           class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Batal
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan Task
        </button>
    </div>
</form>

@endsection
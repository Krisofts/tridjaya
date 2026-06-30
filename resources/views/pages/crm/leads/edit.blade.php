{{-- resources/views/pages/crm/leads/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Lead — ' . $lead->name)

@section('content')

<nav class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-5">
    <a href="{{ route('crm.leads.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">Leads</a>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
    <a href="{{ route('crm.leads.show', $lead) }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">{{ $lead->name }}</a>
    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m9 18 6-6-6-6"/></svg>
    <span class="text-gray-800 dark:text-white font-medium">Edit</span>
</nav>

<div class="flex items-center justify-between mb-5">
    <h1 class="text-xl font-bold text-gray-900 dark:text-white">Edit Lead</h1>
    <a href="{{ route('crm.leads.show', $lead) }}"
       class="inline-flex items-center gap-1.5 px-3 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m19 12H5M12 5l-7 7 7 7"/></svg>
        Kembali
    </a>
</div>

@if($lead->isClosed())
<div class="flex items-start gap-3 px-4 py-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-xl mb-5 text-sm text-amber-800 dark:text-amber-300">
    <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
    <div>Lead ini sudah berstatus <strong>{{ $lead->statusLabel() }}</strong>. Gunakan <em>Reopen</em> di halaman detail untuk membuka kembali.</div>
</div>
@endif

<form method="POST" action="{{ route('crm.leads.update', $lead) }}">
    @csrf
    @method('PUT')

    @include('pages.crm.leads._form')

    <div class="flex items-center justify-end gap-3">
        <a href="{{ route('crm.leads.show', $lead) }}"
           class="px-4 py-2 text-sm text-gray-600 dark:text-gray-300 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
            Batal
        </a>
        <button type="submit"
                class="inline-flex items-center gap-2 px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
            Simpan Perubahan
        </button>
    </div>
</form>

@endsection
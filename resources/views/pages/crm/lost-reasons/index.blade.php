{{-- resources/views/pages/crm/lost-reasons/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Master Alasan Lost')

@section('content')

{{-- Header --}}
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Alasan Lost</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">Master data alasan lead tidak jadi / lost</p>
    </div>
    <a href="{{ route('crm.lost-reasons.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Alasan
    </a>
</div>

{{-- Filter pipeline --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-5">
    <form method="GET" action="{{ route('crm.lost-reasons.index') }}" class="flex items-end gap-3">
        <div class="min-w-[200px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Filter Pipeline</label>
            <select name="pipeline_id"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <option value="">Semua pipeline</option>
                @foreach($pipelines as $p)
                    <option value="{{ $p->id }}" @selected($pipelineId == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit"
                class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
            Filter
        </button>
        <a href="{{ route('crm.lost-reasons.index') }}"
           class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
            Reset
        </a>
    </form>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider w-8">#</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pipeline</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Urutan</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($reasons as $reason)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">

                    <td class="px-4 py-3 text-xs text-gray-400 dark:text-gray-500">{{ $reason->id }}</td>

                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800 dark:text-gray-200">{{ $reason->name }}</div>
                        @if($reason->description)
                            <div class="text-xs text-gray-400 dark:text-gray-500 mt-0.5">{{ Str::limit($reason->description, 60) }}</div>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        @if($reason->pipeline)
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400">
                                {{ $reason->pipeline->name }}
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-medium bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400">
                                Global
                            </span>
                        @endif
                    </td>

                    <td class="px-4 py-3 text-gray-600 dark:text-gray-300">{{ $reason->sort_order }}</td>

                    <td class="px-4 py-3">
                        <form method="POST" action="{{ route('crm.lost-reasons.toggle-active', $reason) }}">
                            @csrf @method('PATCH')
                            <button type="submit"
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold transition-colors
                                           {{ $reason->is_active
                                               ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 hover:bg-green-200'
                                               : 'bg-gray-100 text-gray-500 dark:bg-gray-700 dark:text-gray-400 hover:bg-gray-200' }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $reason->is_active ? 'bg-green-500' : 'bg-gray-400' }}"></span>
                                {{ $reason->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1 justify-end">
                            <a href="{{ route('crm.lost-reasons.edit', $reason) }}"
                               class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded-lg transition-colors"
                               title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                            </a>
                            <form method="POST" action="{{ route('crm.lost-reasons.destroy', $reason) }}"
                                  onsubmit="return confirm('Hapus alasan lost {{ addslashes($reason->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                        class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/30 rounded-lg transition-colors"
                                        title="Hapus">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6M14 11v6M9 6V4h6v2"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-500">
                            <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <p class="text-sm">Belum ada alasan lost.</p>
                            <a href="{{ route('crm.lost-reasons.create') }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                                + Tambah pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($reasons->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end">
        {{ $reasons->appends(request()->query())->links() }}
    </div>
    @endif
</div>

@endsection
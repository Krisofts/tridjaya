{{-- resources/views/pages/crm/leads/my-leads.blade.php --}}
@extends('layouts.app')

@section('title', 'Lead Saya')

@section('content')

<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-bold text-gray-900 dark:text-white">Lead Saya</h1>
        <p class="text-sm text-gray-500 dark:text-gray-400 mt-0.5">
            Lead yang ditugaskan kepadamu · {{ $leads->total() }} lead
        </p>
    </div>
    <a href="{{ route('crm.leads.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
        Tambah Lead
    </a>
</div>

{{-- Filter --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-5">
    <form method="GET" action="{{ route('crm.leads.my-leads') }}" class="flex flex-wrap gap-3 items-end">

        <div class="flex-1 min-w-[180px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Cari</label>
            <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                   placeholder="Nama atau nomor HP…"
                   class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Pipeline</label>
            <select name="pipeline_id"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua pipeline</option>
                @foreach($pipelines as $p)
                    <option value="{{ $p->id }}" @selected(($filters['pipeline_id'] ?? '') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="min-w-[130px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Status</label>
            <select name="status"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua status</option>
                <option value="open"  @selected(($filters['status'] ?? '') === 'open')>Open</option>
                <option value="won"   @selected(($filters['status'] ?? '') === 'won')>Won</option>
                <option value="lost"  @selected(($filters['status'] ?? '') === 'lost')>Lost</option>
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Filter
            </button>
            <a href="{{ route('crm.leads.my-leads') }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                Reset
            </a>
        </div>

    </form>
</div>

{{-- Table --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-700">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Lead</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pipeline / Stage</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Sumber</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nilai</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Prob.</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Follow-up</th>
                    <th class="px-4 py-3"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($leads as $lead)
                @php
                    $badgeClass = match($lead->status) {
                        'won'   => 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-400',
                        'lost'  => 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-400',
                        default => 'bg-blue-100 text-blue-700 dark:bg-blue-900/40 dark:text-blue-400',
                    };
                @endphp
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">

                    <td class="px-4 py-3">
                        <a href="{{ route('crm.leads.show', $lead) }}"
                           class="font-semibold text-blue-600 dark:text-blue-400 hover:underline">
                            {{ $lead->name }}
                        </a>
                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ $lead->phone }}</div>
                    </td>

                    <td class="px-4 py-3">
                        <div class="font-medium text-gray-800 dark:text-gray-200 text-xs">{{ $lead->pipeline->name ?? '—' }}</div>
                        <div class="text-xs text-gray-400 dark:text-gray-500">{{ $lead->stage->name ?? '—' }}</div>
                    </td>

                    <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">{{ $lead->source->name ?? '—' }}</td>

                    <td class="px-4 py-3 text-xs font-medium text-gray-800 dark:text-gray-200 whitespace-nowrap">
                        Rp {{ number_format($lead->estimated_value, 0, ',', '.') }}
                    </td>

                    <td class="px-4 py-3 min-w-[80px]">
                        <div class="flex items-center gap-1.5">
                            <div class="flex-1 h-1.5 bg-gray-200 dark:bg-gray-700 rounded-full overflow-hidden">
                                <div class="h-full bg-blue-500 rounded-full" style="width:{{ $lead->probability }}%"></div>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $lead->probability }}%</span>
                        </div>
                    </td>

                    <td class="px-4 py-3">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold {{ $badgeClass }}">
                            {{ $lead->statusLabel() }}
                        </span>
                    </td>

                    <td class="px-4 py-3 text-xs whitespace-nowrap">
                        @if($lead->next_follow_up_at)
                            <span class="{{ $lead->next_follow_up_at->isPast() ? 'text-red-500' : 'text-gray-600 dark:text-gray-300' }}">
                                {{ $lead->next_follow_up_at->format('d M Y') }}
                                @if($lead->next_follow_up_at->isPast()) ⚠ @endif
                            </span>
                        @else
                            <span class="text-gray-400">—</span>
                        @endif
                    </td>

                    <td class="px-4 py-3">
                        <div class="flex items-center gap-1">
                            <a href="{{ route('crm.leads.show', $lead) }}"
                               class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/30 rounded-lg transition-colors" title="Detail">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </a>
                            <a href="{{ route('crm.leads.edit', $lead) }}"
                               class="p-1.5 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50 dark:hover:bg-yellow-900/30 rounded-lg transition-colors" title="Edit">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4Z"/></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-4 py-16 text-center">
                        <div class="flex flex-col items-center gap-3 text-gray-400 dark:text-gray-500">
                            <svg class="w-10 h-10 opacity-40" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                            <p class="text-sm">Belum ada lead yang ditugaskan kepadamu.</p>
                            <a href="{{ route('crm.leads.create') }}"
                               class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                                + Tambah lead pertama
                            </a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($leads->hasPages())
    <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700 flex justify-end">
        {{ $leads->appends($filters)->links() }}
    </div>
    @endif
</div>

@endsection
{{-- resources/views/pages/crm/reports/_filter.blade.php --}}
{{-- Partial filter bar — dipakai semua halaman laporan --}}

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 mb-5">
    <form method="GET" action="{{ $action }}" class="flex flex-wrap gap-3 items-end">

        {{-- Date range --}}
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Dari Tanggal <span class="text-red-500">*</span></label>
            <input type="date" name="date_from" value="{{ $filters['date_from'] ?? now()->startOfMonth()->format('Y-m-d') }}"
                   class="px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Sampai Tanggal <span class="text-red-500">*</span></label>
            <input type="date" name="date_to" value="{{ $filters['date_to'] ?? now()->format('Y-m-d') }}"
                   class="px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>

        {{-- Pipeline (opsional) --}}
        @if(isset($pipelines))
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
        @endif

        {{-- Source (opsional) --}}
        @if(isset($sources))
        <div class="min-w-[140px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Sumber</label>
            <select name="source_id"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua sumber</option>
                @foreach($sources as $s)
                    <option value="{{ $s->id }}" @selected(($filters['source_id'] ?? '') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- Status (opsional) --}}
        @if(isset($withStatus) && $withStatus)
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
        @endif

        {{-- Sales (opsional) --}}
        @if(isset($users))
        <div class="min-w-[150px]">
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Sales</label>
            <select name="assigned_to"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">Semua sales</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(($filters['assigned_to'] ?? '') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>
        @endif

        <div class="flex gap-2">
            <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                Tampilkan
            </button>
            <a href="{{ $action }}"
               class="px-4 py-2 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:hover:bg-gray-600 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg transition-colors">
                Reset
            </a>
            @if($data)
            <a href="{{ $exportUrl . '?' . http_build_query(array_filter($filters)) }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Excel
            </a>
            @endif
        </div>

    </form>
</div>
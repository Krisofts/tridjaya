{{-- resources/views/pages/crm/tasks/_form.blade.php --}}
@php $isEdit = isset($task); @endphp

<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl mb-5">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Detail Task</h2>
    </div>
    <div class="p-5 space-y-4">

        {{-- Judul --}}
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Judul <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title"
                   value="{{ old('title', $task->title ?? '') }}"
                   placeholder="Contoh: Kirim brosur via WhatsApp"
                   class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition
                          {{ $errors->has('title') ? 'border-red-400' : 'border-gray-200 dark:border-gray-700' }}">
            @error('title')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        {{-- Priority & Due at --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                    Prioritas <span class="text-red-500">*</span>
                </label>
                <select name="priority"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="high"   @selected(old('priority', $task->priority ?? '') === 'high')>🔴 Tinggi</option>
                    <option value="medium" @selected(old('priority', $task->priority ?? 'medium') === 'medium')>🟡 Sedang</option>
                    <option value="low"    @selected(old('priority', $task->priority ?? '') === 'low')>⚪ Rendah</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                    Deadline <span class="text-red-500">*</span>
                </label>
                <input type="datetime-local" name="due_at"
                       value="{{ old('due_at', isset($task->due_at) ? $task->due_at->format('Y-m-d\TH:i') : '') }}"
                       class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                @error('due_at')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

        </div>

        {{-- Assigned to --}}
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Ditugaskan ke</label>
            <select name="assigned_to"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <option value="">— Saya sendiri —</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(old('assigned_to', $task->assigned_to ?? '') == $u->id)>
                        {{ $u->name }}
                    </option>
                @endforeach
            </select>
        </div>

        {{-- Lead terkait --}}
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Lead Terkait
                <span class="ml-1 font-normal text-gray-400">(opsional)</span>
            </label>
            @if(isset($lead) && $lead)
                {{-- Dari halaman show lead — locked --}}
                <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                <div class="flex items-center gap-2 px-3 py-2 text-sm bg-gray-100 dark:bg-gray-700 border border-gray-200 dark:border-gray-600 rounded-lg text-gray-600 dark:text-gray-300">
                    <svg class="w-4 h-4 flex-shrink-0 text-gray-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    {{ $lead->name }} — {{ $lead->phone }}
                </div>
            @else
                {{-- Dari halaman tasks — bisa pilih lead --}}
                <select name="lead_id"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">— Tanpa lead (task umum) —</option>
                    @foreach($leads ?? [] as $l)
                        <option value="{{ $l->id }}" @selected(old('lead_id', $task->lead_id ?? '') == $l->id)>
                            {{ $l->name }} — {{ $l->phone }}
                        </option>
                    @endforeach
                </select>
            @endif
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Deskripsi</label>
            <textarea name="description" rows="3"
                      placeholder="Detail task, instruksi, catatan penting…"
                      class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none">{{ old('description', $task->description ?? '') }}</textarea>
        </div>

    </div>
</div>
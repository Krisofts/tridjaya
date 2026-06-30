{{-- resources/views/pages/crm/leads/_form.blade.php --}}
@php $isEdit = isset($lead); @endphp

{{-- SECTION: Informasi Lead --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl mb-5">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Informasi Lead</h2>
    </div>
    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Nama <span class="text-red-500">*</span>
            </label>
            <input type="text" name="name"
                   value="{{ old('name', $lead->name ?? '') }}"
                   placeholder="Nama lengkap lead"
                   class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition
                          {{ $errors->has('name') ? 'border-red-400' : 'border-gray-200 dark:border-gray-700' }}">
            @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Nomor HP <span class="text-red-500">*</span>
            </label>
            <input type="text" name="phone" id="phone_input"
                   value="{{ old('phone', $lead->phone ?? '') }}"
                   placeholder="08xx xxxx xxxx"
                   autocomplete="off"
                   class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition
                          {{ $errors->has('phone') ? 'border-red-400' : 'border-gray-200 dark:border-gray-700' }}">
            @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror

            {{-- Warning duplikat real-time --}}
            <div id="phone_duplicate_warning" class="hidden mt-2 p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 rounded-lg">
                <div class="flex items-start gap-2">
                    <svg class="w-4 h-4 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-amber-800 dark:text-amber-300">Nomor ini sudah terdaftar sebagai lead</p>
                        <div id="phone_duplicate_info" class="mt-1 space-y-0.5"></div>
                        <a id="phone_duplicate_link" href="#" target="_blank"
                           class="inline-flex items-center gap-1 mt-1.5 text-xs text-blue-600 dark:text-blue-400 hover:underline font-medium">
                            Lihat lead →
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">
                Pipeline <span class="text-red-500">*</span>
            </label>
            <select name="pipeline_id"
                    {{ $isEdit ? 'disabled' : '' }}
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition
                           {{ $errors->has('pipeline_id') ? 'border-red-400' : 'border-gray-200 dark:border-gray-700' }}
                           {{ $isEdit ? 'opacity-60 cursor-not-allowed' : '' }}">
                <option value="">— Pilih Pipeline —</option>
                @foreach($pipelines as $p)
                    <option value="{{ $p->id }}" @selected(old('pipeline_id', $lead->pipeline_id ?? '') == $p->id)>
                        {{ $p->name }}
                    </option>
                @endforeach
            </select>
            @if($isEdit)
                <input type="hidden" name="pipeline_id" value="{{ $lead->pipeline_id }}">
            @endif
            @error('pipeline_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Sumber Lead</label>
            <select name="source_id"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <option value="">— Pilih Sumber —</option>
                @foreach($sources as $s)
                    <option value="{{ $s->id }}" @selected(old('source_id', $lead->source_id ?? '') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Produk</label>
            <select name="product_id"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <option value="">— Pilih Produk —</option>
                @foreach($products as $p)
                    <option value="{{ $p->id }}" @selected(old('product_id', $lead->product_id ?? '') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Ditugaskan ke</label>
            <select name="assigned_to"
                    class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                <option value="">— Otomatis (saya) —</option>
                @foreach($users as $u)
                    <option value="{{ $u->id }}" @selected(old('assigned_to', $lead->assigned_to ?? '') == $u->id)>{{ $u->name }}</option>
                @endforeach
            </select>
        </div>

    </div>
</div>

{{-- SECTION: Nilai Bisnis --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl mb-5">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">Nilai Bisnis</h2>
    </div>
    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Estimasi Nilai (Rp)</label>
            <input type="number" name="estimated_value" min="0" step="1000"
                   value="{{ old('estimated_value', $lead->estimated_value ?? 0) }}"
                   placeholder="0"
                   class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            @error('estimated_value')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Follow-up Berikutnya</label>
            <input type="datetime-local" name="next_follow_up_at"
                   value="{{ old('next_follow_up_at', isset($lead->next_follow_up_at) ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '') }}"
                   class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
            @error('next_follow_up_at')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

    </div>
</div>

{{-- SECTION: Lokasi --}}
<div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl mb-5">
    <div class="px-5 py-4 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-sm font-semibold text-gray-800 dark:text-white">
            Lokasi
            <span class="ml-1 text-xs font-normal text-gray-400">(opsional)</span>
        </h2>
    </div>
    <div class="p-5">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Provinsi</label>
                <select id="province_id" name="province_id"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">— Pilih Provinsi —</option>
                    @foreach($provinces as $prov)
                        <option value="{{ $prov->id }}" @selected(old('province_id', $lead->province_id ?? '') == $prov->id)>{{ $prov->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Kota / Kabupaten</label>
                <select id="regency_id" name="regency_id"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">— Pilih Kota —</option>
                    @foreach($regencies ?? [] as $reg)
                        <option value="{{ $reg->id }}" @selected(old('regency_id', $lead->regency_id ?? '') == $reg->id)>{{ $reg->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Kecamatan</label>
                <select id="district_id" name="district_id"
                        class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 transition">
                    <option value="">— Pilih Kecamatan —</option>
                    @foreach($districts ?? [] as $dist)
                        <option value="{{ $dist->id }}" @selected(old('district_id', $lead->district_id ?? '') == $dist->id)>{{ $dist->name }}</option>
                    @endforeach
                </select>
            </div>

        </div>

        <div>
            <label class="block text-xs font-medium text-gray-600 dark:text-gray-400 mb-1.5">Alamat Lengkap</label>
            <textarea name="address" rows="3"
                      placeholder="Jl. Merdeka No. 1…"
                      class="w-full px-3 py-2 text-sm bg-gray-50 dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded-lg text-gray-900 dark:text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none">{{ old('address', $lead->address ?? '') }}</textarea>
        </div>
    </div>
</div>

@push('scripts')
<script>
(function () {
    // ----------------------------------------------------------------
    // Cascade dropdown lokasi
    // ----------------------------------------------------------------
    const provinceEl = document.getElementById('province_id');
    const regencyEl  = document.getElementById('regency_id');
    const districtEl = document.getElementById('district_id');

    function resetSelect(el, placeholder) {
        el.innerHTML = `<option value="">${placeholder}</option>`;
    }

    async function fetchOptions(url, targetEl, placeholder) {
        resetSelect(targetEl, 'Memuat…');
        try {
            const res  = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
            const data = await res.json();
            resetSelect(targetEl, placeholder);
            data.forEach(item => {
                const opt       = document.createElement('option');
                opt.value       = item.id;
                opt.textContent = item.name;
                targetEl.appendChild(opt);
            });
        } catch {
            resetSelect(targetEl, placeholder);
        }
    }

    provinceEl?.addEventListener('change', function () {
        resetSelect(regencyEl, '— Pilih Kota —');
        resetSelect(districtEl, '— Pilih Kecamatan —');
        if (this.value) {
            fetchOptions(`{{ url('/crm/provinces') }}/${this.value}/regencies`, regencyEl, '— Pilih Kota —');
        }
    });

    regencyEl?.addEventListener('change', function () {
        resetSelect(districtEl, '— Pilih Kecamatan —');
        if (this.value) {
            fetchOptions(`{{ url('/crm/regencies') }}/${this.value}/districts`, districtEl, '— Pilih Kecamatan —');
        }
    });

    // ----------------------------------------------------------------
    // Cek duplikat nomor HP — real-time
    // ----------------------------------------------------------------
    const phoneInput   = document.getElementById('phone_input');
    const warningBox   = document.getElementById('phone_duplicate_warning');
    const infoBox      = document.getElementById('phone_duplicate_info');
    const duplicateLink= document.getElementById('phone_duplicate_link');
    const checkUrl     = '{{ route('crm.leads.check-phone') }}';
    const excludeId    = '{{ $lead->id ?? '' }}'; // untuk edit — exclude lead ini

    let phoneTimer = null;

    phoneInput?.addEventListener('input', function () {
        clearTimeout(phoneTimer);
        const phone = this.value.trim();

        if (phone.length < 8) {
            warningBox.classList.add('hidden');
            return;
        }

        phoneTimer = setTimeout(async () => {
            try {
                const params = new URLSearchParams({ phone });
                if (excludeId) params.append('exclude_id', excludeId);

                const res  = await fetch(`${checkUrl}?${params}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                const data = await res.json();

                if (data.exists) {
                    const lead = data.lead;
                    infoBox.innerHTML = `
                        <p class="text-xs text-amber-700 dark:text-amber-400">
                            <span class="font-medium">${lead.name}</span>
                            &nbsp;·&nbsp; ${lead.pipeline} / ${lead.stage}
                            &nbsp;·&nbsp; <span class="font-medium">${lead.status}</span>
                        </p>
                        <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">
                            Sales: ${lead.assigned_to}
                        </p>
                    `;
                    duplicateLink.href = lead.url;
                    warningBox.classList.remove('hidden');

                    // Warnai border field merah
                    phoneInput.classList.add('border-amber-400');
                    phoneInput.classList.remove('border-gray-200', 'dark:border-gray-700');
                } else {
                    warningBox.classList.add('hidden');
                    phoneInput.classList.remove('border-amber-400');
                    phoneInput.classList.add('border-gray-200', 'dark:border-gray-700');
                }
            } catch (e) {
                // Silent fail — jangan blok user
            }
        }, 500); // debounce 500ms
    });
})();
</script>
@endpush
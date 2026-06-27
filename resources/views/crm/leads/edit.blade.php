@extends('layouts.app')

@section('content')

<div class="mx-auto max-w-(--breakpoint-2xl) p-4 pb-20 md:p-6 md:pb-6">

    {{-- BREADCRUMB --}}
    <div class="flex flex-wrap items-center justify-between gap-3 pb-6">
        <div class="flex items-center gap-3">
            <h2 class="text-xl font-semibold text-gray-800 dark:text-white/90">Edit Prospek</h2>
            <span class="rounded-md bg-gray-100 px-2 py-0.5 text-xs font-medium text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                {{ $lead->lead_code }}
            </span>
        </div>
        <nav>
            <ol class="flex items-center gap-1.5">
                <li>
                    <a href="{{ route('crm.leads.index') }}" class="inline-flex items-center gap-1.5 text-sm text-gray-500 dark:text-gray-400">
                        Prospek
                        <svg class="stroke-current" width="17" height="16" viewBox="0 0 17 16" fill="none">
                            <path d="M6.0765 12.667L10.2432 8.50033L6.0765 4.33366" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                </li>
                <li class="text-sm text-gray-800 dark:text-white/90">Edit Prospek</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('crm.leads.update', $lead) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="space-y-6">

            {{-- INFORMASI PRIBADI --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white">Informasi Pribadi</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">

                        <div class="md:col-span-2">
                            <x-form.input.input-with-label
                                name="name"
                                label="Nama Lengkap"
                                placeholder="Masukkan nama lengkap"
                                :value="old('name', $lead->name)"
                                :required="true"
                            />
                        </div>

                        <x-form.input.input-with-label
                            name="phone"
                            label="No. Telepon"
                            placeholder="08xxxxxxxxxx"
                            :value="old('phone', $lead->phone)"
                        />

                        <x-form.input.input-with-label
                            name="email"
                            type="email"
                            label="Email"
                            placeholder="contoh@email.com"
                            :value="old('email', $lead->email)"
                        />

                    </div>
                </div>
            </div>

            {{-- LOKASI --}}
            <div
                class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]"
                x-data="{
                    provinceCode:      @js(old('province_code', $lead->province_code)),
                    cityCode:          @js(old('city_code', $lead->city_code)),
                    districtCode:      @js(old('district_code', $lead->district_code)),
                    savedCityCode:     @js(old('city_code', $lead->city_code)),
                    savedDistrictCode: @js(old('district_code', $lead->district_code)),
                    cities:            {},
                    districts:         {},

                    async loadCities(restore = false) {
                        this.cities = {}; this.districts = {};
                        if (!restore) { this.cityCode = ''; this.districtCode = ''; }
                        if (!this.provinceCode) return;
                        this.cities = await fetch('{{ route('crm.regions.cities', ':code') }}'.replace(':code', this.provinceCode)).then(r => r.json());
                        if (restore && this.savedCityCode) {
                            await this.$nextTick();
                            this.cityCode = this.savedCityCode;
                            await this.loadDistricts(true);
                        }
                    },

                    async loadDistricts(restore = false) {
                        this.districts = {};
                        if (!restore) this.districtCode = '';
                        if (!this.cityCode) return;
                        this.districts = await fetch('{{ route('crm.regions.districts', ':code') }}'.replace(':code', this.cityCode)).then(r => r.json());
                        if (restore && this.savedDistrictCode) {
                            await this.$nextTick();
                            this.districtCode = this.savedDistrictCode;
                        }
                    },
                }"
                x-init="
                    if (provinceCode) loadCities(true);
                    $watch('provinceCode', () => loadCities(false));
                    $watch('cityCode', (val) => { if (val && val !== savedCityCode) loadDistricts(false); });
                "
            >
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white">Lokasi</h2>
                </div>
                <div class="p-4 sm:p-6">

                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-3">

                        {{-- PROVINSI --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Provinsi</label>
                            <div class="relative">
                                <select name="province_code" x-model="provinceCode" @change="loadCities(false)"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90">
                                    <option value="">-- Pilih Provinsi --</option>
                                    @foreach ($provinces as $code => $name)
                                        <option value="{{ $code }}" {{ old('province_code', $lead->province_code) == $code ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                            @if ($lead->province_name)
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Tersimpan: {{ $lead->province_name }}</p>
                            @endif
                            @error('province_code') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- KOTA --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kota / Kabupaten</label>
                            <div class="relative">
                                <select name="city_code" x-model="cityCode" @change="loadDistricts(false)" :disabled="Object.keys(cities).length === 0"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="">-- Pilih Kota --</option>
                                    <template x-for="(name, code) in cities" :key="code">
                                        <option :value="code" x-text="name" :selected="code == cityCode"></option>
                                    </template>
                                </select>
                                <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                            @if ($lead->city_name)
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Tersimpan: {{ $lead->city_name }}</p>
                            @endif
                            @error('city_code') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                        {{-- KECAMATAN --}}
                        <div>
                            <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">Kecamatan</label>
                            <div class="relative">
                                <select name="district_code" x-model="districtCode" :disabled="Object.keys(districts).length === 0"
                                    class="dark:bg-dark-900 shadow-theme-xs focus:border-brand-300 focus:ring-brand-500/10 dark:focus:border-brand-800 h-11 w-full appearance-none rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 pr-11 text-sm text-gray-800 focus:ring-3 focus:outline-hidden dark:border-gray-700 dark:text-white/90 disabled:cursor-not-allowed disabled:opacity-50">
                                    <option value="">-- Pilih Kecamatan --</option>
                                    <template x-for="(name, code) in districts" :key="code">
                                        <option :value="code" x-text="name" :selected="code == districtCode"></option>
                                    </template>
                                </select>
                                <span class="pointer-events-none absolute top-1/2 right-4 -translate-y-1/2 text-gray-500 dark:text-gray-400">
                                    <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                                </span>
                            </div>
                            @if ($lead->district_name)
                                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">Tersimpan: {{ $lead->district_name }}</p>
                            @endif
                            @error('district_code') <p class="mt-1.5 text-xs text-error-500">{{ $message }}</p> @enderror
                        </div>

                    </div>

                    <div class="mt-5">
                        <x-form.textarea.textarea-with-label
                            name="address"
                            label="Alamat Lengkap"
                            placeholder="Jl. Contoh No. 1, RT/RW..."
                            :value="old('address', $lead->address)"
                            :rows="3"
                        />
                    </div>

                </div>
            </div>

            {{-- KEBUTUHAN --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white">Kebutuhan</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <x-form.select.select-with-label
                        name="interest_id"
                        label="Kategori yang Diminati"
                        placeholder="-- Pilih Kategori --"
                        :options="$interests"
                        :selected="old('interest_id', $lead->interest_id)"
                    />
                </div>
            </div>

            {{-- PIPELINE & PENUGASAN --}}
            <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
                <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-800">
                    <h2 class="text-lg font-medium text-gray-800 dark:text-white">Pipeline & Penugasan</h2>
                </div>
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-3">

                        <x-form.select.select-with-label
                            name="pipeline_id"
                            label="Tipe Pipeline"
                            placeholder="-- Pilih Pipeline --"
                            :required="true"
                            hint="Mengubah pipeline akan mereset stage ke awal."
                            :options="$pipelines"
                            :selected="old('pipeline_id', $lead->pipeline_id)"
                        />

                        <x-form.select.select-with-label
                            name="lead_source_id"
                            label="Sumber Prospek"
                            placeholder="-- Pilih Sumber --"
                            :options="$sources"
                            :selected="old('lead_source_id', $lead->lead_source_id)"
                        />

                        <x-form.select.select-with-label
                            name="assigned_to"
                            label="Ditugaskan ke"
                            placeholder="-- Pilih Sales --"
                            :options="$users"
                            :selected="old('assigned_to', $lead->assigned_to)"
                        />

                    </div>
                </div>
            </div>

            {{-- INFO LEAD --}}
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-5 dark:border-gray-800 dark:bg-gray-800/50">
                <dl class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Kode Lead</dt>
                        <dd class="mt-0.5 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $lead->lead_code }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Dibuat oleh</dt>
                        <dd class="mt-0.5 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $lead->creator->name ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 dark:text-gray-400">Tanggal Dibuat</dt>
                        <dd class="mt-0.5 text-sm font-medium text-gray-700 dark:text-gray-300">
                            {{ $lead->created_at->translatedFormat('d F Y, H:i') }}
                        </dd>
                    </div>
                </dl>
            </div>

            {{-- TOMBOL AKSI --}}
            <div class="flex flex-col gap-3 sm:flex-row sm:justify-end">
                <a href="{{ route('crm.leads.index') }}"
                    class="shadow-theme-xs inline-flex items-center justify-center gap-2 rounded-lg bg-white px-4 py-3 text-sm font-medium text-gray-700 ring-1 ring-gray-300 transition hover:bg-gray-50 dark:bg-gray-800 dark:text-gray-400 dark:ring-gray-700 dark:hover:bg-white/[0.03]">
                    Batal
                </a>
                <button type="submit"
                    class="bg-brand-500 shadow-theme-xs hover:bg-brand-600 inline-flex items-center justify-center gap-2 rounded-lg px-4 py-3 text-sm font-medium text-white transition">
                    Simpan Perubahan
                </button>
            </div>

        </div>
    </form>
</div>

@endsection
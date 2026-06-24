@extends('layouts.app')

@section('content')


<form action="{{ route('crm.leads.store') }}" method="POST" class="space-y-6">
    @csrf

    {{-- CUSTOMER INFORMATION --}}
    <x-common.component-card title="Customer Information" desc="Basic information about the customer.">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <x-form.input.input-with-label name="name" label="Name" required placeholder="Enter customer name" />

            <x-form.input.input-with-label name="phone" label="Phone" placeholder="Enter phone number" />

            <x-form.input.input-with-label name="email" label="Email" type="email" placeholder="Enter email address" />

            <x-form.input.input-with-label name="interest" label="Interest" placeholder="TV, AC, Kulkas..." />

            <div class="lg:col-span-2">
                <x-form.textarea.textarea-with-label name="address" label="Address" rows="3" />
            </div>

        </div>
    </x-common.component-card>

    {{-- REGION INFORMATION --}}
    <x-common.component-card title="Region Information" desc="Customer location information.">
        <div
            x-data="regionSelector()"
            class="grid grid-cols-1 gap-6 lg:grid-cols-3"
            >

            {{-- PROVINCE --}}
            <x-form.select.select-with-label
                name="province_code"
                label="Province"
                placeholder="Select Province"
                :options="$provinces"
                x-model="province"
                @change="onProvinceChange"
                />

            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    City / Regency
                </label>

                <div class="relative z-20 bg-transparent">
                    <select
                        name="city_code"
                        x-model="city"
                        @change="onCityChange"
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full appearance-none rounded-lg bg-transparent px-4 py-2.5 pr-11 text-sm
                        border border-gray-300 text-gray-800
                        focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden
                        dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-800"
                        >
                        <option value="">Select City</option>

                        <template x-for="(name, code) in cityOptions" :key="code">
                            <option :value="code" x-text="name"></option>
                        </template>
                    </select>

                    {{-- dropdown icon --}}
                    <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                />
                        </svg>
                    </span>
                </div>
            </div>
            <div>
                <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                    District
                </label>

                <div class="relative z-20 bg-transparent">
                    <select
                        name="district_code"
                        x-model="district"
                        class="dark:bg-dark-900 shadow-theme-xs h-11 w-full appearance-none rounded-lg bg-transparent px-4 py-2.5 pr-11 text-sm
                        border border-gray-300 text-gray-800
                        focus:border-brand-300 focus:ring-brand-500/10 focus:ring-3 focus:outline-hidden
                        dark:border-gray-700 dark:text-white/90 dark:focus:border-brand-800"
                        >
                        <option value="">Select District</option>

                        <template x-for="(name, code) in districtOptions" :key="code">
                            <option :value="code" x-text="name"></option>
                        </template>
                    </select>

                    {{-- dropdown icon --}}
                    <span class="pointer-events-none absolute top-1/2 right-4 z-30 -translate-y-1/2 text-gray-700 dark:text-gray-400">
                        <svg class="stroke-current" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <path
                                d="M4.79175 7.396L10.0001 12.6043L15.2084 7.396"
                                stroke-width="1.5"
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                />
                        </svg>
                    </span>
                </div>
            </div>

        </div>
    </x-common.component-card>

    {{-- LEAD INFORMATION --}}
    <x-common.component-card title="Lead Information" desc="CRM settings and lead management information.">
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

            <x-form.select.select-with-label
                name="lead_source_id"
                label="Lead Source"
                :options="$sources->pluck('name', 'id')->toArray()"
                />

            <x-form.select.select-with-label
                name="pipeline_id"
                label="Pipeline"
                :options="$pipelines->pluck('name', 'id')->toArray()"
                required
                />

            <x-form.select.select-with-label
                name="sale_type"
                label="Sale Type"
                :options="[
                'cash' => 'Cash',
                'credit' => 'Credit',
                ]"
                />

            <x-form.select.select-with-label
                name="branch_id"
                label="Branch"
                :options="$branches->pluck('name', 'id')->toArray()"
                />

            <x-form.select.select-with-label
                name="assigned_to"
                label="Assigned To"
                :options="$users->pluck('name', 'id')->toArray()"
                />

            <div class="lg:col-span-2">
                <x-form.textarea.textarea-with-label name="notes" label="Notes" rows="4" />
            </div>

        </div>
    </x-common.component-card>

    {{-- ACTIONS --}}
    <div class="flex justify-end gap-3">
        <a href="{{ route('crm.leads.index') }}"
            class="px-5 py-2.5 border rounded-lg text-sm">
            Cancel
        </a>

        <button type="submit"
            class="px-5 py-2.5 bg-brand-500 text-white rounded-lg">
            Save Lead
        </button>
    </div>
</form>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('regionSelector', () => ({
            province: '',
            city: '',
            district: '',

            cityOptions: {},
            districtOptions: {},

            async onProvinceChange() {
                this.city = '';
                this.district = '';
                this.cityOptions = {};
                this.districtOptions = {};

                if (!this.province) return;

                const res = await fetch(`/crm/regions/cities/${this.province}`);
                this.cityOptions = await res.json();
            },

            async onCityChange() {
                this.district = '';
                this.districtOptions = {};

                if (!this.city) return;

                const res = await fetch(`/crm/regions/districts/${this.city}`);
                this.districtOptions = await res.json();
            }
        }));
    });
</script>

@endsection
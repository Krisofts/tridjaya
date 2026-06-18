@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Edit Lead" :breadcrumbs="[
        ['label' => 'Home', 'url' => route('dashboard')],
        ['label' => 'CRM Leads', 'url' => route('crm.leads.index')],
        ['label' => 'Edit'],
    ]" />

    <form action="{{ route('crm.leads.update', $lead) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- CUSTOMER INFORMATION --}}
        <x-common.component-card
            title="Customer Information"
            desc="Basic information about the customer."
        >
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <x-form.input.input-with-label
                    name="name"
                    label="Name"
                    :value="$lead->name"
                    required
                    placeholder="Enter customer name"
                />

                <x-form.input.input-with-label
                    name="phone"
                    label="Phone"
                    :value="$lead->phone"
                    placeholder="Enter phone number"
                />

                <x-form.input.input-with-label
                    name="email"
                    label="Email"
                    type="email"
                    :value="$lead->email"
                    placeholder="Enter email address"
                />

                <x-form.input.input-with-label
                    name="interest"
                    label="Interest"
                    :value="$lead->interest"
                    placeholder="TV, AC, Kulkas..."
                    hint="Products that interest the customer."
                />

                <div class="lg:col-span-2">
                    <x-form.textarea.textarea-with-label
                        name="address"
                        label="Address"
                        :value="$lead->address"
                        rows="3"
                        placeholder="Enter customer address"
                    />
                </div>

            </div>
        </x-common.component-card>

        {{-- LEAD INFORMATION --}}
        <x-common.component-card
            title="Lead Information"
            desc="CRM settings and lead management information."
        >
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

                <x-form.select.select-with-label
                    name="lead_source_id"
                    label="Lead Source"
                    placeholder="Select Source"
                    :options="$sources->pluck('name', 'id')->toArray()"
                    :selected="$lead->lead_source_id"
                />

                <x-form.select.select-with-label
                    name="pipeline_id"
                    label="Pipeline"
                    placeholder="Select Pipeline"
                    :options="$pipelines->pluck('name', 'id')->toArray()"
                    :selected="$lead->pipeline_id"
                    required
                    hint="Choose the current pipeline stage."
                />

                <x-form.select.select-with-label
                    name="assigned_to"
                    label="Assigned To"
                    placeholder="Unassigned"
                    :options="$users->pluck('name', 'id')->toArray()"
                    :selected="$lead->assigned_to"
                />

                <div class="lg:col-span-2">
                    <x-form.textarea.textarea-with-label
                        name="notes"
                        label="Notes"
                        :value="$lead->notes"
                        rows="4"
                        placeholder="Additional notes about this lead"
                    />
                </div>

            </div>
        </x-common.component-card>

        {{-- ACTIONS --}}
        <div class="flex items-center justify-end gap-3">

            <a href="{{ route('crm.leads.index') }}"
                class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800">
                Cancel
            </a>

            <button type="submit"
                class="rounded-lg bg-brand-500 px-5 py-2.5 text-sm font-medium text-white hover:bg-brand-600">
                Update Lead
            </button>

        </div>
    </form>
@endsection
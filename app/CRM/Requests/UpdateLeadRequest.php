<?php

namespace App\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Core
            'name'           => ['required', 'string', 'max:255'],
            'phone'          => ['nullable', 'string', 'max:30'],
            'email'          => ['nullable', 'email', 'max:255'],
            'address'        => ['nullable', 'string'],
            'interest_id'    => ['nullable', 'integer', 'exists:crm_interests,id'],
            'notes'          => ['nullable', 'string'],

            // Pipeline
            'pipeline_id'    => ['required', 'integer', 'exists:crm_pipelines,id'],
            'lead_source_id' => ['nullable', 'integer', 'exists:crm_lead_sources,id'],

            // Region
            'province_code'  => ['nullable', 'string', 'max:20'],
            'city_code'      => ['nullable', 'string', 'max:20'],
            'district_code'  => ['nullable', 'string', 'max:20'],

            // Relations
            'assigned_to'    => ['nullable', 'integer', 'exists:users,id'],
            'branch_id'      => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'        => 'Nama lengkap harus diisi.',
            'email.email'          => 'Format email tidak valid.',
            'pipeline_id.required' => 'Pipeline harus dipilih.',
            'pipeline_id.exists'   => 'Pipeline tidak ditemukan.',
            'assigned_to.exists'   => 'Sales yang dipilih tidak ditemukan.',
            'branch_id.exists'     => 'Cabang tidak ditemukan.',
        ];
    }
}
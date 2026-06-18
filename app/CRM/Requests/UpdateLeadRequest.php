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
            'lead_source_id' => ['nullable', 'exists:crm_lead_sources,id'],

            'pipeline_id' => ['required', 'exists:crm_pipelines,id'],

            'name' => ['required', 'string', 'max:255'],

            'phone' => ['nullable', 'string', 'max:30'],

            'email' => ['nullable', 'email'],

            'address' => ['nullable', 'string'],

            'interest' => ['nullable', 'string', 'max:255'],

            'notes' => ['nullable', 'string'],

            'assigned_to' => ['nullable', 'exists:users,id'],

            'branch_id' => ['nullable', 'exists:branches,id'],
        ];
    }
}
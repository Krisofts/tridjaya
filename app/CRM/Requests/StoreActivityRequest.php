<?php

namespace App\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'lead_id'          => ['required', 'exists:crm_leads,id'],
            'type'             => ['required', 'string', Rule::in([
                'whatsapp', 'call', 'visit', 'survey', 'note', 'task_completed',
            ])],
            'title'            => ['nullable', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'result_id'        => ['nullable', 'exists:crm_results,id'],
            'stage_id'         => ['nullable', 'exists:crm_pipeline_stages,id'],
            'next_follow_up_at'=> ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'lead_id.required'   => 'Lead harus dipilih.',
            'lead_id.exists'     => 'Lead tidak ditemukan.',
            'type.required'      => 'Tipe aktivitas harus diisi.',
            'type.in'            => 'Tipe aktivitas tidak valid.',
            'result_id.exists'   => 'Result tidak ditemukan.',
            'next_follow_up_at.after' => 'Jadwal follow up harus di masa mendatang.',
        ];
    }
}
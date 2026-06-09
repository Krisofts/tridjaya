<?php

namespace App\Http\Requests\CRM;

use App\CRM\Models\LeadTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            /*
            |--------------------------------------------------------------------------
            | CORE RELATION
            |--------------------------------------------------------------------------
            */
            'lead_id' => ['required', 'integer', 'exists:leads,id'],

            /*
            |--------------------------------------------------------------------------
            | CONTENT
            |--------------------------------------------------------------------------
            */
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],

            /*
            |--------------------------------------------------------------------------
            | CLASSIFICATION
            |--------------------------------------------------------------------------
            */
            'type' => ['required', 'string', Rule::in(LeadTask::types())],

            // optional tapi kalau ada harus valid
            'priority' => [
                'nullable',
                'string',
                Rule::in(LeadTask::priorities()),
            ],

            /*
            |--------------------------------------------------------------------------
            | ASSIGNMENT
            |--------------------------------------------------------------------------
            */
            'assigned_to' => ['nullable', 'integer', 'exists:users,id'],

            /*
            |--------------------------------------------------------------------------
            | SCHEDULING
            |--------------------------------------------------------------------------
            */
            'due_date' => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date', 'after_or_equal:due_date'],

            /*
            |--------------------------------------------------------------------------
            | EXTRA DATA
            |--------------------------------------------------------------------------
            */
            'metadata' => ['nullable', 'array'],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | DEFAULT VALUE HANDLING (IMPORTANT)
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([
            'priority' => $this->priority ?? LeadTask::PRIORITY_MEDIUM,
        ]);
    }
}
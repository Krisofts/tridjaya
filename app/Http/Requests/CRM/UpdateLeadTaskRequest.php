<?php

namespace App\Http\Requests\CRM;

use App\CRM\Models\LeadTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLeadTaskRequest extends FormRequest
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
            'priority' => ['nullable', 'string', Rule::in(LeadTask::priorities())],

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
    | AUTO NORMALIZATION
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([
            'priority' => $this->priority ?? null,
        ]);
    }
}
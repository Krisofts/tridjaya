<?php

namespace App\Http\Requests\CRM;

use App\CRM\Models\LeadTask;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CompleteLeadTaskRequest extends FormRequest
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
            | OUTCOME
            |--------------------------------------------------------------------------
            */
            'outcome' => [
                'nullable',
                'string',
                Rule::in(LeadTask::outcomes()),
            ],

            /*
            |--------------------------------------------------------------------------
            | NOTES
            |--------------------------------------------------------------------------
            */
            'notes' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALIZATION
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([
            'outcome' => $this->outcome ?: null,
            'notes' => $this->notes ?: null,
        ]);
    }
}
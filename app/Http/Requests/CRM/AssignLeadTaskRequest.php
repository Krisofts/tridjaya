<?php

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\User\Models\User;

class AssignLeadTaskRequest extends FormRequest
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
            | ASSIGNEE
            |--------------------------------------------------------------------------
            */
            'assigned_to' => [
                'required',
                'integer',
                Rule::exists('users', 'id'),
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONAL: NORMALIZATION / SAFETY
    |--------------------------------------------------------------------------
    */

    protected function prepareForValidation(): void
    {
        $this->merge([
            'assigned_to' => $this->assigned_to ? (int) $this->assigned_to : null,
        ]);
    }
}
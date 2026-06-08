<?php

namespace App\Http\Requests\CRM;

use App\CRM\Models\Lead;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LeadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:50',
            ],

            'address' => [
                'nullable',
                'string',
                'max:255',
            ],

            'source' => [
                'nullable',
                'string',
                Rule::in(array_keys(Lead::sources())),
            ],

            'status' => [
                'nullable',
                'string',
                Rule::in(array_keys(Lead::statuses())),
            ],

            'interest' => [
                'nullable',
                'string',
                Rule::in(array_keys(Lead::interests())),
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'assigned_to' => [
                'nullable',
                'exists:users,id',
            ],
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | CLEAN DATA OUTPUT
    |--------------------------------------------------------------------------
    */

    public function toArrayData(): array
    {
        return [
            'name' => $this->input('name'),
            'phone' => $this->input('phone'),
            'address' => $this->input('address'),

            'source' => $this->input('source'),

            'status' => $this->input('status')
                ?: Lead::defaultStatus(),

            'interest' => $this->input('interest'),

            'notes' => $this->input('notes'),

            'assigned_to' => $this->input('assigned_to'),
        ];
    }
}
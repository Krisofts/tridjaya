<?php

namespace App\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => [
                'required',
                'string',
                'max:50'
            ],

            'title' => [
                'nullable',
                'string',
                'max:255'
            ],

            'description' => [
                'nullable',
                'string'
            ],
        ];
    }
}
<?php

namespace App\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'email',
                'max:150',
                'unique:users,email',
            ],

            'password' => [
                'required',
                'string',
                'min:6',
            ],

            'branch_id' => [
                'nullable',
                'exists:branches,id',
            ],

            'groups' => [
                'nullable',
                'array',
            ],

            'groups.*' => [
                'string',
                'max:100',
            ],

            'permissions' => [
                'nullable',
                'array',
            ],

            'permissions.*' => [
                'string',
                'max:100',
            ],
        ];
    }
}

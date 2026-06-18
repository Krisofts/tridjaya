<?php

namespace App\User\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user')->id;

        return [
            'name' => ['required', 'string', 'max:100'],

            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('users', 'email')->ignore($userId),
            ],

            'password' => [
                'nullable',
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

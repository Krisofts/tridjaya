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

            'nik' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('users', 'nik')->ignore($userId),
            ],

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

    public function messages(): array
    {
        return [
            'name.required'    => 'Nama wajib diisi.',
            'name.max'         => 'Nama maksimal 100 karakter.',
            'nik.unique'       => 'NIK sudah digunakan oleh user lain.',
            'nik.max'          => 'NIK maksimal 20 karakter.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'email.unique'     => 'Email sudah digunakan oleh user lain.',
            'password.min'     => 'Password minimal 6 karakter.',
            'branch_id.exists' => 'Branch tidak ditemukan.',
        ];
    }
}
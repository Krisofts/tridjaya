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

            'nik' => [
                'nullable',
                'string',
                'max:20',
                'unique:users,nik',
            ],

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

    public function messages(): array
    {
        return [
            'name.required'    => 'Nama wajib diisi.',
            'name.max'         => 'Nama maksimal 100 karakter.',
            'nik.unique'       => 'NIK sudah digunakan.',
            'nik.max'          => 'NIK maksimal 20 karakter.',
            'email.required'   => 'Email wajib diisi.',
            'email.email'      => 'Format email tidak valid.',
            'email.unique'     => 'Email sudah digunakan.',
            'password.required'=> 'Password wajib diisi.',
            'password.min'     => 'Password minimal 6 karakter.',
            'branch_id.exists' => 'Branch tidak ditemukan.',
        ];
    }
}
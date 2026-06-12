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
            /*
            |--------------------------------------------------------------------------
            | BASIC USER DATA
            |--------------------------------------------------------------------------
            */
            'name'     => ['required', 'string'],
            'email'    => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],

            /*
            |--------------------------------------------------------------------------
            | RBAC GROUPS
            |--------------------------------------------------------------------------
            */
            'groups'   => ['nullable', 'array'],

            /*
            |--------------------------------------------------------------------------
            | BRANCH ASSIGNMENT
            |--------------------------------------------------------------------------
            */
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }
}
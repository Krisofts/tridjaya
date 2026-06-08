<?php

namespace App\Http\Requests\User;

use App\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('users.create', User::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique((new User)->getTable(), 'email'),
            ],

            'password' => ['required', 'min:6'],

            // 👇 TAMBAHAN BRANCH SUPPORT
            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }
}
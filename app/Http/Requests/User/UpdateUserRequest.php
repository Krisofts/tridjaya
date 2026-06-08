<?php

namespace App\Http\Requests\User;

use App\User\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('users.edit', $this->route('user'));
    }

    public function rules(): array
    {
        /** @var User $user */
        $user = $this->route('user');

        return [
            'name' => ['required', 'string', 'max:255'],

            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique((new User)->getTable(), 'email')->ignore($user->id),
            ],

            'password' => ['nullable', 'min:6'],

            'groups' => ['nullable', 'array'],

            'branch_id' => ['nullable', 'integer', 'exists:branches,id'],
        ];
    }
}
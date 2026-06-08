<?php

namespace App\Http\Requests\CRM;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreLeadReminderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [

            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'type' => [
                'required',
                Rule::in([
                    'follow_up',
                    'call',
                    'meeting',
                    'email',
                ]),
            ],

            'remind_at' => [
                'required',
                'date',
            ],

            'assigned_to' => [
                'nullable',
                'exists:users,id',
            ],

        ];
    }

    public function attributes(): array
    {
        return [
            'title' => 'judul',
            'description' => 'deskripsi',
            'type' => 'tipe reminder',
            'remind_at' => 'tanggal reminder',
            'assigned_to' => 'petugas',
        ];
    }
}
<?php

namespace App\CRM\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLeadRequest extends FormRequest
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
            | LEAD CORE
            |--------------------------------------------------------------------------
            */
            'lead_source_id' => [
                'nullable',
                'integer',
                'exists:crm_lead_sources,id',
            ],

            'pipeline_id' => [
                'required',
                'integer',
                'exists:crm_pipelines,id',
            ],

            'name' => [
                'required',
                'string',
                'max:255',
            ],

            'phone' => [
                'nullable',
                'string',
                'max:30',
            ],

            'email' => [
                'nullable',
                'email',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | REGION (STORE ONLY CODE)
            |--------------------------------------------------------------------------
            */
            'province_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'city_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            'district_code' => [
                'nullable',
                'string',
                'max:20',
            ],

            /*
            |--------------------------------------------------------------------------
            | SALES INFO
            |--------------------------------------------------------------------------
            */
            'sale_type' => [
                'nullable',
                'in:cash,credit',
            ],

            'interest' => [
                'nullable',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            /*
            |--------------------------------------------------------------------------
            | RELATIONS
            |--------------------------------------------------------------------------
            */
            'assigned_to' => [
                'nullable',
                'integer',
                'exists:users,id',
            ],

            'branch_id' => [
                'nullable',
                'integer',
                'exists:branches,id',
            ],
        ];
    }
}
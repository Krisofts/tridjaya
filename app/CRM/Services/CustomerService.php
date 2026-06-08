<?php

namespace App\CRM\Services;

use App\CRM\Models\Customer;
use App\CRM\Models\Lead;
use Illuminate\Support\Facades\Auth;

class CustomerService
{
    /*
    |--------------------------------------------------------------------------
    | GET OR CREATE FROM LEAD
    |--------------------------------------------------------------------------
    */
    public function createFromLead(Lead $lead): Customer
    {
        return Customer::updateOrCreate(
            [
                'lead_id' => $lead->id,
            ],
            [
                'name'   => $lead->name,
                'phone'  => $lead->phone,
                'address'=> $lead->address,

                // default jadi active customer
                'type' => 'active',

                'converted_at' => now(),
                'converted_by' => Auth::id(),
                'created_by'   => Auth::id(),
            ]
        );
    }

    /*
    |--------------------------------------------------------------------------
    | MANUAL CONVERT LEAD → CUSTOMER
    |--------------------------------------------------------------------------
    */
    public function convert(Lead $lead, string $type = 'active'): Customer
    {
        $customer = Customer::firstOrNew([
            'lead_id' => $lead->id,
        ]);

        $customer->fill([
            'name'   => $lead->name,
            'phone'  => $lead->phone,
            'address'=> $lead->address,

            'type' => $type,

            'converted_at' => now(),
            'converted_by' => Auth::id(),
            'created_by'   => Auth::id(),
        ]);

        $customer->save();

        return $customer;
    }

    /*
    |--------------------------------------------------------------------------
    | FIND BY LEAD
    |--------------------------------------------------------------------------
    */
    public function findByLead(Lead $lead): ?Customer
    {
        return Customer::where('lead_id', $lead->id)->first();
    }

    /*
    |--------------------------------------------------------------------------
    | CHECK EXISTS
    |--------------------------------------------------------------------------
    */
    public function exists(Lead $lead): bool
    {
        return Customer::where('lead_id', $lead->id)->exists();
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE TYPE (VIP / ACTIVE / INACTIVE)
    |--------------------------------------------------------------------------
    */
    public function updateType(Customer $customer, string $type): Customer
    {
        $customer->update([
            'type' => $type,
        ]);

        return $customer;
    }

    /*
    |--------------------------------------------------------------------------
    | AUTO CONVERT LOGIC (CRM FLOW)
    |--------------------------------------------------------------------------
    | Dipakai dari Transaction Service
    |--------------------------------------------------------------------------
    */
    public function autoConvertFromTransaction(Lead $lead, string $transactionType): Customer
    {
        // CASH = langsung jadi customer aktif
        if ($transactionType === 'cash') {
            return $this->convert($lead, 'active');
        }

        // CREDIT = tetap customer tapi status bisa pending/active
        if ($transactionType === 'credit') {
            return $this->convert($lead, 'active');
        }

        // default fallback
        return $this->convert($lead, 'active');
    }
}
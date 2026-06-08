<?php

namespace App\CRM\Services;

use App\CRM\Models\Lead;
use App\CRM\Models\LeadTransaction;
use Illuminate\Support\Facades\Auth;
use App\CRM\Services\CustomerService;

class LeadTransactionService
{
    public function __construct(
        protected CustomerService $customerService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | LIST TRANSACTIONS
    |--------------------------------------------------------------------------
    */
    public function paginate(int $perPage = 15)
    {
        return LeadTransaction::query()
            ->with(['lead', 'createdBy'])
            ->latest()
            ->paginate($perPage);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE TRANSACTION
    |--------------------------------------------------------------------------
    */
    public function create(Lead $lead, array $data): LeadTransaction
    {
        $data['lead_id'] = $lead->id;
        $data['created_by'] = $data['created_by'] ?? Auth::id();

        $type = $data['type'] ?? null;

        /*
        |--------------------------------------------------------------------------
        | AUTO CALC CREDIT
        |--------------------------------------------------------------------------
        */
        if ($type === 'credit') {
            $data['monthly_payment'] = $this->calculateMonthlyPayment(
                $data['amount'] ?? 0,
                $data['down_payment'] ?? 0,
                $data['tenor_months'] ?? 1
            );
        }

        /*
        |--------------------------------------------------------------------------
        | DEFAULT STATUS
        |--------------------------------------------------------------------------
        */
        $data['status'] = $data['status']
            ?? config('crm.transaction_status.pending');

        $transaction = LeadTransaction::create($data);

        /*
        |--------------------------------------------------------------------------
        | 🔥 CORE CRM FLOW
        |--------------------------------------------------------------------------
        | 1. Update lead status
        | 2. Auto convert to customer
        |--------------------------------------------------------------------------
        */
        $this->applyLeadStatusFromTransaction($lead, $type);

        $this->autoConvertCustomer($lead, $type);

        return $transaction;
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE TRANSACTION
    |--------------------------------------------------------------------------
    */
    public function update(LeadTransaction $transaction, array $data): LeadTransaction
    {
        $type = $data['type'] ?? $transaction->type;

        if ($type === 'credit') {
            $data['monthly_payment'] = $this->calculateMonthlyPayment(
                $data['amount'] ?? $transaction->amount,
                $data['down_payment'] ?? $transaction->down_payment,
                $data['tenor_months'] ?? $transaction->tenor_months
            );
        }

        $transaction->update($data);

        return $transaction;
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE
    |--------------------------------------------------------------------------
    */
    public function delete(LeadTransaction $transaction): bool
    {
        return $transaction->delete();
    }

    /*
    |--------------------------------------------------------------------------
    | STATUS FLOW
    |--------------------------------------------------------------------------
    */
    public function approve(LeadTransaction $transaction): LeadTransaction
    {
        $transaction->update([
            'status' => config('crm.transaction_status.approved'),
        ]);

        return $transaction;
    }

    public function reject(LeadTransaction $transaction): LeadTransaction
    {
        $transaction->update([
            'status' => config('crm.transaction_status.rejected'),
        ]);

        return $transaction;
    }

    public function complete(LeadTransaction $transaction): LeadTransaction
    {
        $transaction->update([
            'status' => config('crm.transaction_status.completed'),
        ]);

        return $transaction;
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 LEAD STATUS FLOW
    |--------------------------------------------------------------------------
    */
    private function applyLeadStatusFromTransaction(Lead $lead, ?string $type): void
    {
        if ($type === 'cash') {
            $lead->update([
                'status' => 'won',
            ]);
        }

        if ($type === 'credit') {
            $lead->update([
                'status' => 'deal',
            ]);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | 🔥 AUTO CONVERT CUSTOMER
    |--------------------------------------------------------------------------
    */
    private function autoConvertCustomer(Lead $lead, ?string $type): void
    {
        if (!$type) {
            return;
        }

        // CASH → langsung customer aktif
        if ($type === 'cash') {
            $this->customerService->convert($lead, 'active');
            return;
        }

        // CREDIT → customer tetap dibuat (bisa kamu ubah nanti ke pending)
        if ($type === 'credit') {
            $this->customerService->convert($lead, 'active');
            return;
        }
    }

    /*
    |--------------------------------------------------------------------------
    | HELPER
    |--------------------------------------------------------------------------
    */
    private function calculateMonthlyPayment(
        float $amount,
        float $downPayment,
        int $tenor
    ): float {
        $principal = max(0, $amount - $downPayment);

        return $tenor > 0
            ? $principal / $tenor
            : $principal;
    }
}
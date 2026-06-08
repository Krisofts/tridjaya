<?php

namespace App\CRM\Observers;

use App\CRM\Models\LeadTransaction;
use App\CRM\Services\LeadActivityService;

class LeadTransactionObserver
{
    public function __construct(
        protected LeadActivityService $activityService
    ) {}

    /*
    |--------------------------------------------------------------------------
    | CREATED
    |--------------------------------------------------------------------------
    */
    public function created(LeadTransaction $transaction): void
    {
        $lead = $transaction->lead;

        $this->activityService->custom(
            $lead,
            'transaction_created',
            'Transaksi ' . strtoupper($transaction->type) .
            ' dibuat sebesar Rp ' .
            number_format($transaction->amount, 0, ',', '.')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATED
    |--------------------------------------------------------------------------
    */
    public function updated(LeadTransaction $transaction): void
    {
        $lead = $transaction->lead;

        /*
        |--------------------------------------------------------------------------
        | STATUS CHANGE
        |--------------------------------------------------------------------------
        */
        if ($transaction->wasChanged('status')) {

            $this->activityService->custom(
                $lead,
                'transaction_status_changed',
                'Status transaksi diubah dari ' .
                $transaction->getOriginal('status') .
                ' menjadi ' .
                $transaction->status
            );
        }

        /*
        |--------------------------------------------------------------------------
        | TYPE CHANGE
        |--------------------------------------------------------------------------
        */
        if ($transaction->wasChanged('type')) {

            $this->activityService->custom(
                $lead,
                'transaction_type_changed',
                'Tipe transaksi diubah ke ' . strtoupper($transaction->type)
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AMOUNT CHANGE
        |--------------------------------------------------------------------------
        */
        if ($transaction->wasChanged('amount')) {

            $this->activityService->custom(
                $lead,
                'transaction_amount_updated',
                'Nominal transaksi diperbarui menjadi Rp ' .
                number_format($transaction->amount, 0, ',', '.')
            );
        }

        /*
        |--------------------------------------------------------------------------
        | PAYMENT STRUCTURE CHANGE (CREDIT FLOW)
        |--------------------------------------------------------------------------
        */
        if ($transaction->wasChanged('down_payment') || $transaction->wasChanged('tenor_months')) {

            $this->activityService->custom(
                $lead,
                'transaction_structure_updated',
                'Skema pembayaran diperbarui (DP / Tenor)'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DELETED
    |--------------------------------------------------------------------------
    */
    public function deleted(LeadTransaction $transaction): void
    {
        $this->activityService->custom(
            $transaction->lead,
            'transaction_deleted',
            'Transaksi dihapus (ID: ' . $transaction->id . ')'
        );
    }
}
<?php

namespace App\CRM\Services;

use App\CRM\Models\Lead;
use App\CRM\Models\Task;

class AutoTaskService
{
    /*
    |--------------------------------------------------------------------------
    | CREATE INITIAL TASK
    |--------------------------------------------------------------------------
    */
    public function createForLead(Lead $lead): Task
    {
        return Task::create([
            'lead_id'      => $lead->id,
            'assigned_to'  => $lead->assigned_to,
            'created_by'   => $lead->created_by,

            'title'        => 'Follow Up Lead Baru',
            'description'  => 'Hubungi lead maksimal 15 menit setelah lead dibuat.',

            'priority'     => 'high',
            'status'       => 'open',

            'due_date'     => now()->addMinutes(15),
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE TASK BASED ON STATUS
    |--------------------------------------------------------------------------
    */
    public function replaceForLead(Lead $lead): void
    {
        match ($lead->status) {

            'contacted' => $this->createTask(
                $lead,
                'Follow Up Customer',
                'Pastikan customer mendapatkan informasi produk.',
                'medium',
                now()->addDay()
            ),

            'qualified' => $this->createTask(
                $lead,
                'Survey / Presentasi Produk',
                'Jadwalkan survey atau presentasi produk.',
                'high',
                now()->addDays(3)
            ),

            'proposal' => $this->createTask(
                $lead,
                'Follow Up Proposal',
                'Pastikan proposal sudah diterima customer.',
                'high',
                now()->addDays(2)
            ),

            'won' => $this->createTask(
                $lead,
                'Persiapan Pengiriman',
                'Koordinasikan pengiriman dan instalasi.',
                'medium',
                now()->addDay()
            ),

            default => null,
        };
    }

    /*
    |--------------------------------------------------------------------------
    | CREATE TASK
    |--------------------------------------------------------------------------
    */
    protected function createTask(
        Lead $lead,
        string $title,
        string $description,
        string $priority,
        $dueDate
    ): Task {

        return Task::create([
            'lead_id'      => $lead->id,
            'assigned_to'  => $lead->assigned_to,
            'created_by'   => $lead->created_by,

            'title'        => $title,
            'description'  => $description,

            'priority'     => $priority,
            'status'       => 'open',

            'due_date'     => $dueDate,
        ]);
    }
}
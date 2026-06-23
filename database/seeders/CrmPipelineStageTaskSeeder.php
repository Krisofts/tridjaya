<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;
use App\CRM\Models\CrmPipelineStageTask;

class CrmPipelineStageTaskSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | CASH PIPELINE
        |--------------------------------------------------------------------------
        */
        $cashPipeline = CrmPipeline::where('name', 'Cash')->first();

        if ($cashPipeline) {

            $cashTasks = [

                'New' => [
                    'title' => 'Follow Up Lead Baru',
                    'description' => 'Hubungi customer secepatnya.',
                    'type' => 'follow_up',
                    'priority' => 'urgent',
                    'due_after_minutes' => 15,
                ],

                'Contacted' => [
                    'title' => 'Kirim Katalog Produk',
                    'description' => 'Kirim katalog dan daftar harga.',
                    'type' => 'follow_up',
                    'priority' => 'high',
                    'due_after_minutes' => 60,
                ],

                'Qualified' => [
                    'title' => 'Buat Penawaran',
                    'description' => 'Siapkan penawaran untuk customer.',
                    'type' => 'proposal',
                    'priority' => 'high',
                    'due_after_minutes' => 1440,
                ],

                'Prospek' => [
                    'title' => 'Follow Up Keputusan Customer',
                    'description' => 'Tanyakan keputusan customer.',
                    'type' => 'follow_up',
                    'priority' => 'medium',
                    'due_after_minutes' => 4320,
                ],

                'Deal' => [
                    'title' => 'Siapkan Barang dan Invoice',
                    'description' => 'Persiapkan pengiriman dan invoice.',
                    'type' => 'delivery',
                    'priority' => 'urgent',
                    'due_after_minutes' => 60,
                ],

                'Won' => [
                    'title' => 'Minta Testimoni',
                    'description' => 'Hubungi customer untuk testimoni.',
                    'type' => 'customer_care',
                    'priority' => 'low',
                    'due_after_minutes' => 10080, // 7 hari
                ],

                'Lost' => [
                    'title' => 'Re-Engagement Customer',
                    'description' => 'Coba follow up kembali.',
                    'type' => 'follow_up',
                    'priority' => 'low',
                    'due_after_minutes' => 43200, // 30 hari
                ],
            ];

            $this->seedTasks($cashPipeline, $cashTasks);
        }

        /*
        |--------------------------------------------------------------------------
        | CREDIT PIPELINE
        |--------------------------------------------------------------------------
        */
        $creditPipeline = CrmPipeline::where('name', 'Credit')->first();

        if ($creditPipeline) {

            $creditTasks = [

                'New' => [
                    'title' => 'Hubungi Customer',
                    'description' => 'Lakukan kontak pertama.',
                    'type' => 'follow_up',
                    'priority' => 'urgent',
                    'due_after_minutes' => 15,
                ],

                'Contacted' => [
                    'title' => 'Jelaskan Simulasi Kredit',
                    'description' => 'Berikan simulasi kredit.',
                    'type' => 'follow_up',
                    'priority' => 'high',
                    'due_after_minutes' => 60,
                ],

                'Qualified' => [
                    'title' => 'Kumpulkan Dokumen',
                    'description' => 'KTP, KK, Slip Gaji dan dokumen lainnya.',
                    'type' => 'document',
                    'priority' => 'high',
                    'due_after_minutes' => 1440,
                ],

                'Prospek' => [
                    'title' => 'Pastikan Dokumen Lengkap',
                    'description' => 'Verifikasi kelengkapan dokumen.',
                    'type' => 'document',
                    'priority' => 'high',
                    'due_after_minutes' => 1440,
                ],

                'Poling' => [
                    'title' => 'Kirim Data ke Leasing',
                    'description' => 'Ajukan data customer ke leasing.',
                    'type' => 'submission',
                    'priority' => 'urgent',
                    'due_after_minutes' => 60,
                ],

                'Survey' => [
                    'title' => 'Monitor Hasil Survey',
                    'description' => 'Pantau hasil survey leasing.',
                    'type' => 'survey',
                    'priority' => 'high',
                    'due_after_minutes' => 2880,
                ],

                'Acc' => [
                    'title' => 'Persiapkan Pengiriman Barang',
                    'description' => 'Customer telah ACC.',
                    'type' => 'delivery',
                    'priority' => 'urgent',
                    'due_after_minutes' => 60,
                ],

                'Reject' => [
                    'title' => 'Tawarkan Leasing Alternatif',
                    'description' => 'Ajukan ke leasing lain.',
                    'type' => 'follow_up',
                    'priority' => 'medium',
                    'due_after_minutes' => 1440,
                ],

                'Won' => [
                    'title' => 'Minta Testimoni',
                    'description' => 'Hubungi customer untuk testimoni.',
                    'type' => 'customer_care',
                    'priority' => 'low',
                    'due_after_minutes' => 10080,
                ],

                'Lost' => [
                    'title' => 'Re-Engagement Customer',
                    'description' => 'Hubungi kembali di masa mendatang.',
                    'type' => 'follow_up',
                    'priority' => 'low',
                    'due_after_minutes' => 43200,
                ],
            ];

            $this->seedTasks($creditPipeline, $creditTasks);
        }
    }

    private function seedTasks(
        CrmPipeline $pipeline,
        array $tasks
    ): void {
        foreach ($tasks as $stageName => $task) {

            $stage = CrmPipelineStage::where('pipeline_id', $pipeline->id)
                ->where('name', $stageName)
                ->first();

            if (! $stage) {
                continue;
            }

            CrmPipelineStageTask::updateOrCreate(
                [
                    'pipeline_stage_id' => $stage->id,
                    'title' => $task['title'],
                ],
                [
                    'description' => $task['description'],
                    'type' => $task['type'],
                    'priority' => $task['priority'],
                    'due_after_minutes' => $task['due_after_minutes'],
                    'reminder_before_minutes' => 15,
                    'is_active' => true,
                ]
            );
        }
    }
}
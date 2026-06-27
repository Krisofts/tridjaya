<?php

namespace Database\Seeders;

use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;
use Illuminate\Database\Seeder;

class CrmPipelineStageSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // ================================================================
            // CASH — Penjualan tunai langsung
            // ================================================================
            'Cash' => [
                ['name' => 'Lead Baru',          'sort_order' => 1,  'temperature' => CrmPipelineStage::TEMP_COLD,     'is_default' => true,  'is_won' => false, 'is_lost' => false],
                ['name' => 'Dihubungi',          'sort_order' => 2,  'temperature' => CrmPipelineStage::TEMP_COLD,     'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Tertarik',           'sort_order' => 3,  'temperature' => CrmPipelineStage::TEMP_WARM,     'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Negosiasi',          'sort_order' => 4,  'temperature' => CrmPipelineStage::TEMP_WARM,     'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Penawaran Dikirim',  'sort_order' => 5,  'temperature' => CrmPipelineStage::TEMP_HOT,      'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Deal / Sepakat',     'sort_order' => 6,  'temperature' => CrmPipelineStage::TEMP_HOT,      'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Proses Pembayaran',  'sort_order' => 7,  'temperature' => CrmPipelineStage::TEMP_HOT,      'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Closing / Selesai',  'sort_order' => 8,  'temperature' => CrmPipelineStage::TEMP_CUSTOMER, 'is_default' => false, 'is_won' => true,  'is_lost' => false],
                ['name' => 'Tidak Jadi',         'sort_order' => 9,  'temperature' => CrmPipelineStage::TEMP_LOST,     'is_default' => false, 'is_won' => false, 'is_lost' => true],
            ],

            // ================================================================
            // KREDIT — Penjualan via leasing / cicilan
            // ================================================================
            'Kredit' => [
                ['name' => 'Lead Baru',          'sort_order' => 1,  'temperature' => CrmPipelineStage::TEMP_COLD,     'is_default' => true,  'is_won' => false, 'is_lost' => false],
                ['name' => 'Dihubungi',          'sort_order' => 2,  'temperature' => CrmPipelineStage::TEMP_COLD,     'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Tertarik',           'sort_order' => 3,  'temperature' => CrmPipelineStage::TEMP_WARM,     'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Simulasi Kredit',    'sort_order' => 4,  'temperature' => CrmPipelineStage::TEMP_WARM,     'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Kumpul Dokumen',     'sort_order' => 5,  'temperature' => CrmPipelineStage::TEMP_WARM,     'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Pengajuan Leasing',  'sort_order' => 6,  'temperature' => CrmPipelineStage::TEMP_HOT,      'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Proses Survey',      'sort_order' => 7,  'temperature' => CrmPipelineStage::TEMP_HOT,      'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Disetujui',          'sort_order' => 8,  'temperature' => CrmPipelineStage::TEMP_HOT,      'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Tanda Tangan SPK',   'sort_order' => 9,  'temperature' => CrmPipelineStage::TEMP_HOT,      'is_default' => false, 'is_won' => false, 'is_lost' => false],
                ['name' => 'Closing / Selesai',  'sort_order' => 10, 'temperature' => CrmPipelineStage::TEMP_CUSTOMER, 'is_default' => false, 'is_won' => true,  'is_lost' => false],
                ['name' => 'Ditolak Leasing',    'sort_order' => 11, 'temperature' => CrmPipelineStage::TEMP_LOST,     'is_default' => false, 'is_won' => false, 'is_lost' => true],
                ['name' => 'Tidak Jadi',         'sort_order' => 12, 'temperature' => CrmPipelineStage::TEMP_LOST,     'is_default' => false, 'is_won' => false, 'is_lost' => true],
            ],

        ];

        foreach ($data as $pipelineName => $stages) {
            $pipeline = CrmPipeline::where('name', $pipelineName)->first();

            if (! $pipeline) {
                $this->command->warn("Pipeline '{$pipelineName}' tidak ditemukan, skip.");
                continue;
            }

            foreach ($stages as $stage) {
                CrmPipelineStage::updateOrCreate(
                    ['pipeline_id' => $pipeline->id, 'name' => $stage['name']],
                    array_merge($stage, ['pipeline_id' => $pipeline->id]),
                );
            }

            $this->command->line("  ✓ {$pipelineName}: " . count($stages) . " stages");
        }

        $this->command->info('✓ Pipeline stages berhasil di-seed.');
    }
}
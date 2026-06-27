<?php

namespace Database\Seeders;

use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;
use App\CRM\Models\CrmResult;
use App\CRM\Models\CrmResultStageMapping;
use Illuminate\Database\Seeder;

class CrmResultStageMappingSeeder extends Seeder
{
    public function run(): void
    {
        $mappings = [

            // ================================================================
            // CASH — result code => nama stage tujuan
            // ================================================================
            'Cash' => [
                'no_response'       => 'Dihubungi',          // tidak merespon → tetap di dihubungi
                'unreachable'       => 'Dihubungi',          // tidak bisa dihubungi → coba lagi
                'follow_up'         => 'Dihubungi',          // follow up kembali → dihubungi
                'not_ready'         => 'Dihubungi',          // belum siap beli → dihubungi
                'interested'        => 'Tertarik',           // tertarik → masuk tertarik
                'need_info'         => 'Tertarik',           // minta info → tertarik
                'offer_sent'        => 'Penawaran Dikirim',  // penawaran dikirim → stage penawaran
                'negotiating'       => 'Negosiasi',          // negosiasi harga → stage negosiasi
                'dp'                => 'Proses Pembayaran',  // DP masuk → proses pembayaran
                'paid'              => 'Closing / Selesai',  // lunas → closing
                'success'           => 'Closing / Selesai',  // berhasil closing → selesai
                'not_interested'    => 'Tidak Jadi',         // tidak tertarik → tidak jadi
                'cancelled'         => 'Tidak Jadi',         // batal → tidak jadi
                'competitor'        => 'Tidak Jadi',         // ke kompetitor → tidak jadi
            ],

            // ================================================================
            // KREDIT — result code => nama stage tujuan
            // ================================================================
            'Kredit' => [
                'no_response'       => 'Dihubungi',          // tidak merespon → dihubungi
                'unreachable'       => 'Dihubungi',          // tidak bisa dihubungi → coba lagi
                'follow_up'         => 'Dihubungi',          // follow up kembali → dihubungi
                'not_ready'         => 'Dihubungi',          // belum siap → dihubungi
                'interested'        => 'Tertarik',           // tertarik → tertarik
                'need_info'         => 'Tertarik',           // minta info → tertarik
                'simulation_sent'   => 'Simulasi Kredit',    // simulasi dikirim → simulasi kredit
                'docs_complete'     => 'Pengajuan Leasing',  // dokumen lengkap → pengajuan
                'docs_incomplete'   => 'Kumpul Dokumen',     // dokumen kurang → kumpul dokumen
                'submitted'         => 'Pengajuan Leasing',  // pengajuan dikirim → pengajuan leasing
                'survey'            => 'Proses Survey',      // sedang disurvey → proses survey
                'approved'          => 'Disetujui',          // disetujui → disetujui
                'spk_signed'        => 'Tanda Tangan SPK',   // SPK ditandatangani → tanda tangan SPK
                'dp'                => 'Closing / Selesai',  // DP masuk → closing
                'success'           => 'Closing / Selesai',  // berhasil closing → selesai
                'rejected'          => 'Ditolak Leasing',    // ditolak leasing → ditolak leasing
                'not_interested'    => 'Tidak Jadi',         // tidak tertarik → tidak jadi
                'cancelled'         => 'Tidak Jadi',         // batal → tidak jadi
                'competitor'        => 'Tidak Jadi',         // ke kompetitor → tidak jadi
            ],

        ];

        foreach ($mappings as $pipelineName => $map) {
            $pipeline = CrmPipeline::where('name', $pipelineName)->first();

            if (! $pipeline) {
                $this->command->warn("Pipeline '{$pipelineName}' tidak ditemukan, skip.");
                continue;
            }

            $count = 0;
            foreach ($map as $resultCode => $stageName) {
                $result = CrmResult::where('code', $resultCode)->first();
                $stage  = CrmPipelineStage::where('pipeline_id', $pipeline->id)
                    ->where('name', $stageName)
                    ->first();

                if (! $result) {
                    $this->command->warn("  ⚠ Result '{$resultCode}' tidak ditemukan, skip.");
                    continue;
                }

                if (! $stage) {
                    $this->command->warn("  ⚠ Stage '{$stageName}' pada pipeline '{$pipelineName}' tidak ditemukan, skip.");
                    continue;
                }

                CrmResultStageMapping::updateOrCreate(
                    ['pipeline_id' => $pipeline->id, 'result_id' => $result->id],
                    ['target_stage_id' => $stage->id, 'is_active' => true, 'priority' => 0],
                );

                $count++;
            }

            $this->command->line("  ✓ {$pipelineName}: {$count} mappings");
        }

        $this->command->info('✓ Result stage mappings berhasil di-seed.');
    }
}
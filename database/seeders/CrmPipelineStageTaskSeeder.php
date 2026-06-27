<?php

namespace Database\Seeders;

use App\CRM\Models\CrmPipeline;
use App\CRM\Models\CrmPipelineStage;
use App\CRM\Models\CrmPipelineStageTask;
use Illuminate\Database\Seeder;

class CrmPipelineStageTaskSeeder extends Seeder
{
    public function run(): void
    {
        $data = [

            // ================================================================
            // CASH
            // ================================================================
            'Cash' => [
                'Lead Baru' => [
                    'title'             => 'Hubungi Customer Secepatnya',
                    'description'       => 'Telepon atau WhatsApp customer dalam 15 menit setelah lead masuk.',
                    'type'              => 'follow_up',
                    'priority'          => 'urgent',
                    'due_after_minutes' => 15,
                ],
                'Dihubungi' => [
                    'title'             => 'Kirim Katalog & Harga',
                    'description'       => 'Kirimkan katalog produk dan daftar harga via WhatsApp.',
                    'type'              => 'follow_up',
                    'priority'          => 'high',
                    'due_after_minutes' => 60,
                ],
                'Tertarik' => [
                    'title'             => 'Follow Up Minat Customer',
                    'description'       => 'Tanyakan produk mana yang paling diminati, tawarkan demo jika perlu.',
                    'type'              => 'follow_up',
                    'priority'          => 'high',
                    'due_after_minutes' => 1440, // 1 hari
                ],
                'Negosiasi' => [
                    'title'             => 'Buat & Kirim Penawaran',
                    'description'       => 'Siapkan penawaran harga terbaik dan kirimkan ke customer.',
                    'type'              => 'proposal',
                    'priority'          => 'high',
                    'due_after_minutes' => 480, // 8 jam
                ],
                'Penawaran Dikirim' => [
                    'title'             => 'Follow Up Keputusan Customer',
                    'description'       => 'Tanyakan apakah customer sudah mempertimbangkan penawaran yang dikirim.',
                    'type'              => 'follow_up',
                    'priority'          => 'medium',
                    'due_after_minutes' => 2880, // 2 hari
                ],
                'Deal / Sepakat' => [
                    'title'             => 'Konfirmasi Pembayaran',
                    'description'       => 'Konfirmasi metode dan jadwal pembayaran dengan customer.',
                    'type'              => 'follow_up',
                    'priority'          => 'urgent',
                    'due_after_minutes' => 60,
                ],
                'Proses Pembayaran' => [
                    'title'             => 'Siapkan Barang & Invoice',
                    'description'       => 'Persiapkan barang, buat invoice, dan jadwalkan pengiriman.',
                    'type'              => 'delivery',
                    'priority'          => 'urgent',
                    'due_after_minutes' => 120,
                ],
                'Closing / Selesai' => [
                    'title'             => 'After Sales — Minta Testimoni',
                    'description'       => 'Hubungi customer H+7 untuk memastikan kepuasan dan minta testimoni.',
                    'type'              => 'customer_care',
                    'priority'          => 'low',
                    'due_after_minutes' => 10080, // 7 hari
                ],
                'Tidak Jadi' => [
                    'title'             => 'Follow Up Kembali (30 Hari)',
                    'description'       => 'Coba hubungi kembali setelah 30 hari, mungkin situasi sudah berubah.',
                    'type'              => 'follow_up',
                    'priority'          => 'low',
                    'due_after_minutes' => 43200, // 30 hari
                ],
            ],

            // ================================================================
            // KREDIT
            // ================================================================
            'Kredit' => [
                'Lead Baru' => [
                    'title'             => 'Hubungi Customer Secepatnya',
                    'description'       => 'Telepon atau WhatsApp customer dalam 15 menit setelah lead masuk.',
                    'type'              => 'follow_up',
                    'priority'          => 'urgent',
                    'due_after_minutes' => 15,
                ],
                'Dihubungi' => [
                    'title'             => 'Jelaskan Simulasi Kredit',
                    'description'       => 'Berikan simulasi cicilan, DP, dan tenor yang sesuai kebutuhan customer.',
                    'type'              => 'follow_up',
                    'priority'          => 'high',
                    'due_after_minutes' => 60,
                ],
                'Tertarik' => [
                    'title'             => 'Follow Up Pilihan Produk & Leasing',
                    'description'       => 'Pastikan customer sudah memilih produk dan leasing yang diinginkan.',
                    'type'              => 'follow_up',
                    'priority'          => 'high',
                    'due_after_minutes' => 1440, // 1 hari
                ],
                'Simulasi Kredit' => [
                    'title'             => 'Kirim Detail Simulasi Kredit',
                    'description'       => 'Kirimkan simulasi kredit lengkap (DP, cicilan, tenor, asuransi).',
                    'type'              => 'follow_up',
                    'priority'          => 'high',
                    'due_after_minutes' => 120,
                ],
                'Kumpul Dokumen' => [
                    'title'             => 'Minta & Verifikasi Dokumen',
                    'description'       => 'Kumpulkan KTP, KK, Slip Gaji, NPWP, dan dokumen pendukung lainnya.',
                    'type'              => 'document',
                    'priority'          => 'high',
                    'due_after_minutes' => 1440, // 1 hari
                ],
                'Pengajuan Leasing' => [
                    'title'             => 'Submit Dokumen ke Leasing',
                    'description'       => 'Ajukan dokumen lengkap ke pihak leasing untuk diproses.',
                    'type'              => 'submission',
                    'priority'          => 'urgent',
                    'due_after_minutes' => 60,
                ],
                'Proses Survey' => [
                    'title'             => 'Monitor Status Survey Leasing',
                    'description'       => 'Pantau perkembangan survey dari leasing, bantu customer jika ada pertanyaan.',
                    'type'              => 'survey',
                    'priority'          => 'high',
                    'due_after_minutes' => 2880, // 2 hari
                ],
                'Disetujui' => [
                    'title'             => 'Informasikan Persetujuan ke Customer',
                    'description'       => 'Hubungi customer bahwa pengajuan disetujui, jadwalkan tanda tangan SPK.',
                    'type'              => 'follow_up',
                    'priority'          => 'urgent',
                    'due_after_minutes' => 60,
                ],
                'Tanda Tangan SPK' => [
                    'title'             => 'Jadwalkan & Proses Tanda Tangan SPK',
                    'description'       => 'Atur jadwal tanda tangan SPK dan pastikan semua dokumen siap.',
                    'type'              => 'document',
                    'priority'          => 'urgent',
                    'due_after_minutes' => 480, // 8 jam
                ],
                'Closing / Selesai' => [
                    'title'             => 'After Sales — Minta Testimoni',
                    'description'       => 'Hubungi customer H+7 untuk memastikan kepuasan dan minta testimoni.',
                    'type'              => 'customer_care',
                    'priority'          => 'low',
                    'due_after_minutes' => 10080, // 7 hari
                ],
                'Ditolak Leasing' => [
                    'title'             => 'Tawarkan Leasing Alternatif',
                    'description'       => 'Coba ajukan ke leasing lain yang lebih sesuai profil customer.',
                    'type'              => 'follow_up',
                    'priority'          => 'medium',
                    'due_after_minutes' => 1440, // 1 hari
                ],
                'Tidak Jadi' => [
                    'title'             => 'Follow Up Kembali (30 Hari)',
                    'description'       => 'Coba hubungi kembali setelah 30 hari, mungkin situasi sudah berubah.',
                    'type'              => 'follow_up',
                    'priority'          => 'low',
                    'due_after_minutes' => 43200, // 30 hari
                ],
            ],
        ];

        foreach ($data as $pipelineName => $stageTasks) {
            $pipeline = CrmPipeline::where('name', $pipelineName)->first();

            if (! $pipeline) {
                $this->command->warn("Pipeline '{$pipelineName}' tidak ditemukan, skip.");
                continue;
            }

            foreach ($stageTasks as $stageName => $task) {
                $stage = CrmPipelineStage::where('pipeline_id', $pipeline->id)
                    ->where('name', $stageName)
                    ->first();

                if (! $stage) {
                    $this->command->warn("  ⚠ Stage '{$stageName}' tidak ditemukan, skip.");
                    continue;
                }

                CrmPipelineStageTask::updateOrCreate(
                    ['pipeline_stage_id' => $stage->id, 'title' => $task['title']],
                    [
                        'description'             => $task['description'],
                        'type'                    => $task['type'],
                        'priority'                => $task['priority'],
                        'due_after_minutes'       => $task['due_after_minutes'],
                        'reminder_before_minutes' => 15,
                        'is_active'               => true,
                    ],
                );
            }

            $this->command->line("  ✓ {$pipelineName}: " . count($stageTasks) . " task templates");
        }

        $this->command->info('✓ Pipeline stage tasks berhasil di-seed.');
    }
}
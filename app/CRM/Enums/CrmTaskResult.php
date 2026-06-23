<?php

namespace App\CRM\Enums;

enum CrmTaskResult: string
{
    case NO_RESPONSE     = 'Tidak Merespon';
    case INTERESTED      = 'Tertarik';
    case NOT_INTERESTED  = 'Tidak Tertarik';
    case FOLLOW_UP       = 'Follow Up Kembali';

    case SUBMITTED       = 'Poling/Pengajuan Kredit';
    case SURVEY          = 'Sedang Survey';
    case APPROVED        = 'Acc/Disetujui';
    case REJECT          = 'Ditolak';

    case DEAL            = 'Deal Berjalan';
    case DP              = 'DP Masuk';
    case SUCCESS         = 'Berhasil Closing';

    /**
     * ambil semua value (buat dropdown)
     */
    public static function values(): array
    {
        return array_map(
            fn($case) => $case->value,
            self::cases()
        );
    }
}
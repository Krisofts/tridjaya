<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            // Hapus index terlebih dahulu jika ada
            $table->dropIndex(['sale_type']);
        });

        Schema::table('crm_leads', function (Blueprint $table) {
            $table->dropColumn('sale_type');
        });
    }

    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {
            $table->string('sale_type')
                ->nullable()
                ->after('address');

            $table->index('sale_type');
        });
    }
};
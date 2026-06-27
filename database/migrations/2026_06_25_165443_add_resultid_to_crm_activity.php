<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tambah result_id ke crm_activities (ganti kolom result string lama)
        Schema::table('crm_activities', function (Blueprint $table) {
            // Hapus kolom result string kalau ada
            if (Schema::hasColumn('crm_activities', 'result')) {
                $table->dropColumn('result');
            }

            $table->foreignId('result_id')
                ->nullable()
                ->after('description')
                ->constrained('crm_results')
                ->nullOnDelete();
        });

        // Tambah result_id ke crm_tasks (ganti kolom result string lama)
        Schema::table('crm_tasks', function (Blueprint $table) {
            if (Schema::hasColumn('crm_tasks', 'result')) {
                $table->dropColumn('result');
            }

            $table->foreignId('result_id')
                ->nullable()
                ->after('description')
                ->constrained('crm_results')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('crm_activities', function (Blueprint $table) {
            $table->dropForeign(['result_id']);
            $table->dropColumn('result_id');
            $table->string('result')->nullable()->after('description');
        });

        Schema::table('crm_tasks', function (Blueprint $table) {
            $table->dropForeign(['result_id']);
            $table->dropColumn('result_id');
            $table->string('result')->nullable()->after('description');
        });
    }
};
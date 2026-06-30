<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crm_lead_activities', function (Blueprint $table) {
            $table->id();

            // Lead
            $table->foreignId('lead_id')
                ->constrained('crm_leads')
                ->cascadeOnDelete();

            // Jenis aktivitas
            $table->foreignId('activity_type_id')
                ->constrained('crm_activity_types')
                ->restrictOnDelete();

            // Hasil aktivitas (nullable jika belum diisi)
            $table->foreignId('activity_result_id')
                ->nullable()
                ->constrained('crm_activity_results')
                ->nullOnDelete();

            // User yang melakukan aktivitas
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            // Waktu aktivitas
            $table->dateTime('activity_at');

            // Ringkasan
            $table->string('title');

            // Catatan
            $table->text('notes')->nullable();

            // Lokasi (opsional)
            $table->string('location')->nullable();

            // Stage lead saat aktivitas dilakukan
            $table->foreignId('stage_id')
                ->nullable()
                ->constrained('crm_pipeline_stages')
                ->nullOnDelete();

            // Apakah berhasil melakukan kontak
            $table->boolean('is_contacted')
                ->default(false);

            $table->timestamps();

            $table->index(['lead_id', 'activity_at']);
            $table->index(['user_id', 'activity_at']);
            $table->index('activity_type_id');
            $table->index('activity_result_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_lead_activities');
    }
};
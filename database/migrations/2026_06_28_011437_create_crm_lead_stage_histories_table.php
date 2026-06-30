<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lead_stage_histories', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained('crm_leads')
                ->cascadeOnDelete();

            // null = entry pertama (baru masuk pipeline)
            $table->foreignId('from_stage_id')
                ->nullable()
                ->constrained('crm_pipeline_stages')
                ->nullOnDelete();

            $table->foreignId('to_stage_id')
                ->constrained('crm_pipeline_stages')
                ->cascadeOnDelete();

            // User yang melakukan perpindahan stage
            $table->foreignId('changed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // Catatan opsional saat pindah stage
            $table->text('note')->nullable();

            // Waktu perpindahan — default now()
            $table->timestamp('changed_at')->useCurrent();

            $table->timestamps();

            // Indexes
            $table->index(['lead_id', 'changed_at']);
            $table->index('to_stage_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lead_stage_histories');
    }
};
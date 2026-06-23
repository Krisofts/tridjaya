<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('crm_result_stage_mappings', function (Blueprint $table) {

            $table->id();

            // pipeline scope (Cash / Credit)
            $table->foreignId('pipeline_id')
                ->constrained('crm_pipelines')
                ->cascadeOnDelete();

            // result yang dipilih user
            $table->foreignId('result_id')
                ->constrained('crm_results')
                ->cascadeOnDelete();

            // stage tujuan
            $table->foreignId('target_stage_id')
                ->constrained('crm_pipeline_stages')
                ->cascadeOnDelete();

            // optional: untuk override prioritas mapping
            $table->unsignedInteger('priority')->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            // 1 result hanya boleh punya 1 mapping per pipeline
            $table->unique(
                ['pipeline_id', 'result_id'],
                'crm_result_pipeline_unique'
            );

            $table->index(['pipeline_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_result_stage_mappings');
    }
};
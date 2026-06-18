<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {

            $table->foreignId('pipeline_id')
                ->nullable()
                ->after('lead_source_id')
                ->constrained('crm_pipelines')
                ->nullOnDelete();

            $table->foreignId('pipeline_stage_id')
                ->nullable()
                ->after('pipeline_id')
                ->constrained('crm_pipeline_stages')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::table('crm_leads', function (Blueprint $table) {

            $table->dropForeign(['pipeline_id']);
            $table->dropForeign(['pipeline_stage_id']);

            $table->dropColumn([
                'pipeline_id',
                'pipeline_stage_id'
            ]);
        });
    }
};
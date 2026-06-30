<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_pipeline_stages', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pipeline_id')
                ->constrained('crm_pipelines')
                ->cascadeOnDelete();

            $table->string('name');

            $table->string('slug')->nullable();

            $table->unsignedInteger('sort_order')->default(0);

            // probability lebih penting dari temperature
            $table->unsignedTinyInteger('probability')->default(0);

            // status logic
            $table->boolean('is_default')->default(false);
            $table->boolean('is_won')->default(false);
            $table->boolean('is_lost')->default(false);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->index(['pipeline_id', 'sort_order']);
            $table->index('pipeline_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_pipeline_stages');
    }
};
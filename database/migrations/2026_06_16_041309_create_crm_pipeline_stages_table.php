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

            $table->unsignedInteger('sort_order')
                ->default(0);

            $table->string('color')
                ->nullable();

            $table->boolean('is_default')
                ->default(false);

            $table->boolean('is_won')
                ->default(false);

            $table->boolean('is_lost')
                ->default(false);

            $table->timestamps();

            $table->index([
                'pipeline_id',
                'sort_order'
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_pipeline_stages');
    }
};
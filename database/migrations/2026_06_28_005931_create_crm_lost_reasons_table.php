<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_lost_reasons', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pipeline_id')
                ->nullable()
                ->constrained('crm_pipelines')
                ->nullOnDelete();

            $table->string('name');
            $table->string('slug');

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['pipeline_id', 'slug']);
            $table->index(['pipeline_id', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_lost_reasons');
    }
};
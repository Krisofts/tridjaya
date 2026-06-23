<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_pipeline_stage_tasks', function (Blueprint $table) {

            $table->id();

            $table->foreignId('pipeline_stage_id')
                ->constrained('crm_pipeline_stages')
                ->cascadeOnDelete();

            $table->string('title');

            $table->text('description')
                ->nullable();

            $table->string('type')
                ->default('follow_up');

            $table->string('priority')
                ->default('medium');

            /*
            |--------------------------------------------------------------------------
            | DUE DATE RULE
            |--------------------------------------------------------------------------
            */
            $table->integer('due_after_minutes')
                ->default(60);

            $table->integer('reminder_before_minutes')
                ->default(15);

            /*
            |--------------------------------------------------------------------------
            | SETTINGS
            |--------------------------------------------------------------------------
            */
            $table->boolean('is_active')
                ->default(true);

            $table->timestamps();

            $table->index('pipeline_stage_id');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_pipeline_stage_tasks');
    }
};
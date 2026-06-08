<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();

            /*
            |--------------------------------------------------------------------------
            | RELATION
            |--------------------------------------------------------------------------
            */
            $table->foreignId('lead_id')
                ->nullable()
                ->constrained('leads')
                ->nullOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |--------------------------------------------------------------------------
            | TASK CORE
            |--------------------------------------------------------------------------
            */
            $table->string('title');
            $table->text('description')->nullable();

            /*
            |--------------------------------------------------------------------------
            | PRIORITY & STATUS
            |--------------------------------------------------------------------------
            */
            $table->enum('priority', ['low', 'medium', 'high'])
                ->default('medium');

            $table->enum('status', ['open', 'in_progress', 'done', 'cancelled'])
                ->default('open');

            /*
            |--------------------------------------------------------------------------
            | SCHEDULE
            |--------------------------------------------------------------------------
            */
            $table->dateTime('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();

            /*
            |--------------------------------------------------------------------------
            | TIMESTAMPS
            |--------------------------------------------------------------------------
            */
            $table->timestamps();

            /*
            |--------------------------------------------------------------------------
            | INDEX (FOR CRM PERFORMANCE)
            |--------------------------------------------------------------------------
            */
            $table->index(['lead_id']);
            $table->index(['assigned_to']);
            $table->index(['status']);
            $table->index(['due_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
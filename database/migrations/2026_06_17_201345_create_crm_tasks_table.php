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
        Schema::create('crm_tasks', function (Blueprint $table) {
    $table->id();

    $table->foreignId('lead_id')
        ->constrained('crm_leads')
        ->cascadeOnDelete();

    $table->foreignId('user_id')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->foreignId('created_by')
        ->nullable()
        ->constrained('users')
        ->nullOnDelete();

    $table->string('title');
    $table->text('description')->nullable();

    $table->string('type')->default('follow_up');

    $table->string('status')->default('pending');
    $table->string('priority')->default('medium');

    $table->timestamp('due_at')->nullable();
    $table->timestamp('reminder_at')->nullable();
    $table->timestamp('completed_at')->nullable();

    $table->text('result')->nullable();

    $table->timestamps();

    $table->index(['lead_id', 'status']);
    $table->index(['user_id', 'status']);
    $table->index(['type', 'status']);
    $table->index(['status', 'due_at']);
    $table->index('due_at');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crm_tasks');
    }
};
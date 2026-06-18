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

            // relation
            $table->foreignId('lead_id')
                ->constrained('crm_leads')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // content
            $table->string('title');
            $table->text('description')->nullable();

            // flexible fields (NO ENUM)
            $table->string('status')->default('pending'); 
            $table->string('priority')->default('medium');

            // scheduling
            $table->timestamp('due_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // indexes
            $table->index(['lead_id', 'status']);
            $table->index(['user_id', 'status']);
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
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->string('phone', 30)->nullable();
            $table->text('address')->nullable();

            $table->string('source')->nullable();
            $table->string('status')->default('new');

            $table->decimal('estimated_value', 15, 2)->nullable();

            $table->timestamp('next_follow_up_at')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            $table->timestamps();

            $table->index('name');
            $table->index('phone');
            $table->index('status');
            $table->index('source');
            $table->index('assigned_to');
            $table->index('created_by');
            $table->index('next_follow_up_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
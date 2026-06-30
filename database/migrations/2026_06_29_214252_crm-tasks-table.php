<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_tasks', function (Blueprint $table) {
            $table->id();

            // ----------------------------------------------------------------
            // Relasi ke lead — nullable (task bisa standalone)
            // ----------------------------------------------------------------
            $table->foreignId('lead_id')
                ->nullable()
                ->constrained('crm_leads')
                ->cascadeOnDelete();

            // ----------------------------------------------------------------
            // Assignment
            // ----------------------------------------------------------------
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ----------------------------------------------------------------
            // Konten task
            // ----------------------------------------------------------------
            $table->string('title');
            $table->text('description')->nullable();

            // ----------------------------------------------------------------
            // Priority: low | medium | high
            // ----------------------------------------------------------------
            $table->string('priority', 20)->default('medium');

            // ----------------------------------------------------------------
            // Status: open | done | cancelled
            // ----------------------------------------------------------------
            $table->string('status', 20)->default('open');

            // ----------------------------------------------------------------
            // Waktu
            // ----------------------------------------------------------------
            $table->dateTime('due_at');           // deadline task
            $table->dateTime('done_at')->nullable(); // diisi saat selesai

            // ----------------------------------------------------------------
            // Reminder — apakah sudah diingatkan
            // ----------------------------------------------------------------
            $table->boolean('is_reminded')->default(false);

            $table->timestamps();
            $table->softDeletes();

            // ----------------------------------------------------------------
            // Indexes
            // ----------------------------------------------------------------
            $table->index(['assigned_to', 'status']);
            $table->index(['lead_id', 'status']);
            $table->index('due_at');
            $table->index('status');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_tasks');
    }
};
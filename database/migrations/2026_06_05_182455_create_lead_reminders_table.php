<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_reminders', function (Blueprint $table) {

            $table->id();

            /*
            |---------------------------------------------------
            | RELATION
            |---------------------------------------------------
            */
            $table->foreignId('lead_id')
                ->constrained('leads')
                ->cascadeOnDelete();

            /*
            |---------------------------------------------------
            | CONTENT
            |---------------------------------------------------
            */
            $table->string('title');
            $table->text('description')->nullable();

            /*
            |---------------------------------------------------
            | TYPE
            |---------------------------------------------------
            */
            $table->string('type')->default('follow_up');

            /*
            |---------------------------------------------------
            | SCHEDULE
            |---------------------------------------------------
            */
            $table->timestamp('remind_at'); // ❌ HAPUS index di sini

            /*
            |---------------------------------------------------
            | STATUS
            |---------------------------------------------------
            */
            $table->string('status')->default('pending');

            /*
            |---------------------------------------------------
            | ASSIGNMENT
            |---------------------------------------------------
            */
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            /*
            |---------------------------------------------------
            | TIMESTAMPS
            |---------------------------------------------------
            */
            $table->timestamps();

            /*
            |---------------------------------------------------
            | INDEXING (SAFE & CLEAN)
            |---------------------------------------------------
            */
            $table->index(['lead_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('remind_at'); // ✔ cukup ini saja
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_reminders');
    }
};
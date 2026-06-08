<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();

            // RELASI KE LEAD (opsional tapi penting untuk migrasi)
            $table->foreignId('lead_id')
                ->nullable()
                ->constrained('leads')
                ->nullOnDelete();

            // DATA CUSTOMER
            $table->string('name');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();

            // STATUS CUSTOMER
            $table->string('type')->default('new');
            // new | active | vip | inactive

            // TRACKING
            $table->timestamp('converted_at')->nullable();
            $table->foreignId('converted_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['lead_id']);
            $table->index(['phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
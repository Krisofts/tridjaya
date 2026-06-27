<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained('crm_leads')
                ->cascadeOnDelete();

            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // Tipe: cash / credit
            $table->enum('type', ['cash', 'credit'])->default('cash');

            // Nominal
            $table->unsignedBigInteger('amount');

            // Untuk kredit
            $table->unsignedBigInteger('dp_amount')->nullable();
            $table->string('leasing')->nullable();
            $table->unsignedTinyInteger('tenor')->nullable()->comment('dalam bulan');

            // Status: pending, paid, cancelled
            $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');

            $table->text('notes')->nullable();
            $table->date('transaction_date');
            $table->timestamp('paid_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_transactions');
    }
};
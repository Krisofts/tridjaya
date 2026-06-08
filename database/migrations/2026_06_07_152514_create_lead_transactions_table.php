<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lead_transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('lead_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('type'); 
            // credit | cash | installment | dp | refund

            $table->decimal('amount', 15, 2)->default(0);

            $table->decimal('down_payment', 15, 2)->nullable();

            $table->integer('tenor_months')->nullable();

            $table->decimal('monthly_payment', 15, 2)->nullable();

            $table->string('status')->default('pending');
            // pending | approved | rejected | active | completed

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index(['lead_id', 'type']);
            $table->index(['status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lead_transactions');
    }
};
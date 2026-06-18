<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_leads', function (Blueprint $table) {
            $table->id();

            $table->string('lead_code')->unique();

            $table->foreignId('lead_source_id')
                ->nullable()
                ->constrained('crm_lead_sources')
                ->nullOnDelete();

            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();

            $table->enum('sale_type', [
                'cash',
                'credit',
            ])->default('cash');

            $table->string('interest')->nullable();

            $table->text('notes')->nullable();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();

            $table->index('lead_code');
            $table->index('phone');
            $table->index('sale_type');
            $table->index('branch_id');
            $table->index('assigned_to');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};

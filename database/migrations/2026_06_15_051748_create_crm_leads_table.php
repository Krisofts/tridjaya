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

            // 🏠 ADDRESS DETAIL
            $table->text('address')->nullable();

            // 🌍 WILAYAH.ID STRUCTURE
            $table->string('province_code')->nullable();
            $table->string('province_name')->nullable();

            $table->string('city_code')->nullable();
            $table->string('city_name')->nullable();

            $table->string('district_code')->nullable();
            $table->string('district_name')->nullable();

            // 💰 SALE TYPE
            $table->enum('sale_type', [
                'cash',
                'credit',
            ])->default('cash');

            // 🛒 INTEREST PRODUCT
            $table->string('interest')->nullable();

            $table->text('notes')->nullable();

            // 👤 RELATIONS
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

            // INDEXES
            $table->index('lead_code');
            $table->index('phone');
            $table->index('sale_type');
            $table->index('branch_id');
            $table->index('assigned_to');

            // 🔥 penting untuk CRM filtering
            $table->index(['province_code', 'city_code']);
            $table->index('district_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};
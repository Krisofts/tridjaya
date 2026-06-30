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

            // ----------------------------------------------------------------
            // CORE RELATION
            // ----------------------------------------------------------------
            $table->foreignId('pipeline_id')
                ->constrained('crm_pipelines')
                ->cascadeOnDelete();

            $table->foreignId('stage_id')
                ->constrained('crm_pipeline_stages')
                ->cascadeOnDelete();

            // ----------------------------------------------------------------
            // ORGANIZATION
            // ----------------------------------------------------------------
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // ----------------------------------------------------------------
            // LEAD INFO
            // ----------------------------------------------------------------
            $table->string('name');
            $table->string('phone', 25);

            // ----------------------------------------------------------------
            // SOURCE & PRODUCT
            // ----------------------------------------------------------------
            $table->foreignId('source_id')
                ->nullable()
                ->constrained('crm_sources')
                ->nullOnDelete();

            $table->foreignId('product_id')
                ->nullable()
                ->constrained('products')
                ->nullOnDelete();

            // ----------------------------------------------------------------
            // LOCATION
            // ----------------------------------------------------------------
            $table->foreignId('province_id')
                ->nullable()
                ->constrained('provinces')
                ->nullOnDelete();

            $table->foreignId('regency_id')
                ->nullable()
                ->constrained('regencies')
                ->nullOnDelete();

            $table->foreignId('district_id')
                ->nullable()
                ->constrained('districts')
                ->nullOnDelete();

            $table->text('address')->nullable();

            // ----------------------------------------------------------------
            // BUSINESS VALUE
            // ----------------------------------------------------------------
            $table->decimal('estimated_value', 18, 2)->default(0);
            $table->unsignedTinyInteger('probability')->default(0);

            // ----------------------------------------------------------------
            // STATUS CONTROL
            // open | won | lost
            // ----------------------------------------------------------------
            $table->string('status', 20)->default('open');

            // Lost detail — diisi saat status berubah ke 'lost'
            $table->foreignId('lost_reason_id')
                ->nullable()
                ->constrained('crm_lost_reasons')
                ->nullOnDelete();

            $table->text('lost_note')->nullable();

            // ----------------------------------------------------------------
            // TIMELINE
            // ----------------------------------------------------------------
            $table->timestamp('last_activity_at')->nullable();   // update otomatis setiap ada aktivitas
            $table->timestamp('next_follow_up_at')->nullable();  // jadwal follow-up berikutnya
            $table->timestamp('closed_at')->nullable();          // diisi saat won / lost

            $table->timestamps();
            $table->softDeletes();

            // ----------------------------------------------------------------
            // INDEXES
            // ----------------------------------------------------------------
            $table->index(['pipeline_id', 'stage_id']);
            $table->index('assigned_to');
            $table->index('branch_id');
            $table->index('phone');
            $table->index('status');
            $table->index('next_follow_up_at');
            $table->index('last_activity_at');
            $table->index('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_leads');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_activities', function (Blueprint $table) {
            $table->id();

            // RELATION
            $table->foreignId('lead_id')
                ->constrained('crm_leads')
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // TYPE ACTIVITY (WA / CALL / VISIT / NOTE / SURVEY)
            $table->string('type');

            // OPTIONAL TITLE (ringkasan cepat)
            $table->string('title')->nullable();

            // DETAIL CATATAN
            $table->text('description')->nullable();

            // ⭐ RESULT ACTIVITY (PENTING UNTUK CRM)
            $table->string('result')->nullable();

            // ⭐ FOLLOW UP DATE
            $table->date('next_follow_up_at')->nullable();

            // ⭐ AUTO STAGE UPDATE (optional kalau kamu mau automation)
            $table->foreignId('stage_id')
    ->nullable();

            // TIMESTAMP
            $table->timestamps();

            // INDEXES
            $table->index('lead_id');
            $table->index('type');
            $table->index('result');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
    }
};
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

            // ACTIVITY TYPE
            $table->string('type'); 
            // call, whatsapp, note, meeting, visit, stage_changed

            // CONTENT
            $table->string('title')->nullable();
            $table->text('description')->nullable();

            // OPTIONAL FLEXIBLE DATA (future proof)
            $table->json('meta')->nullable();

            $table->timestamps();

            // INDEXES
            $table->index('lead_id');
            $table->index('type');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activities');
    }
};
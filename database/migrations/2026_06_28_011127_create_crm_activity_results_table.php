<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('crm_activity_results', function (Blueprint $table) {
            $table->id();

            $table->foreignId('activity_type_id')
                ->constrained('crm_activity_types')
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('slug');

            $table->unsignedInteger('sort_order')->default(0);

            $table->boolean('is_default')->default(false);

            // Menandakan hasil aktivitas dianggap berhasil
            $table->boolean('is_success')->default(false);

            // Untuk mengaktifkan/nonaktifkan master data tanpa menghapusnya
            $table->boolean('is_active')->default(true);

            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['activity_type_id', 'slug']);

            $table->index(['activity_type_id', 'sort_order']);
            $table->index(['activity_type_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_activity_results');
    }
};
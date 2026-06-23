<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('crm_results', function (Blueprint $table) {

            $table->id();

            // Optional: kalau result beda tiap pipeline
            $table->foreignId('pipeline_id')
                ->nullable()
                ->constrained('crm_pipelines')
                ->cascadeOnDelete();

            // Nama yang tampil di UI
            $table->string('name');

            // Key internal (lebih aman untuk logic & mapping)
            $table->string('code')->unique();

            // UI helper (opsional)
            $table->string('color')->nullable();

            // aktif / non aktif (biar bisa disable tanpa hapus data)
            $table->boolean('is_active')->default(true);

            // untuk urutan dropdown
            $table->unsignedInteger('sort_order')->default(0);

            // apakah result ini final (won/lost)
            $table->boolean('is_terminal')->default(false);

            $table->timestamps();

            // index untuk query cepat per pipeline
            $table->index(['pipeline_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_results');
    }
};
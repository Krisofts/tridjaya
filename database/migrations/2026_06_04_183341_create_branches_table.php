<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('branches', function (Blueprint $table) {
            $table->id();

            $table->string('code', 50)->unique();      // kode cabang
            $table->string('name', 150);               // nama cabang
            $table->text('address')->nullable();       // alamat
            $table->string('phone', 30)->nullable();   // telepon
            $table->string('manager_name', 150)->nullable(); // kepala cabang
            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['code']);
            $table->index(['is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('branches');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wilayah_provinsi', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('name', 100);
        });

        Schema::create('wilayah_kota', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('province_code', 10);
            $table->string('name', 100);

            $table->index('province_code');
        });

        Schema::create('wilayah_kecamatan', function (Blueprint $table) {
            $table->string('code', 10)->primary();
            $table->string('city_code', 10);
            $table->string('name', 100);

            $table->index('city_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wilayah_kecamatan');
        Schema::dropIfExists('wilayah_kota');
        Schema::dropIfExists('wilayah_provinsi');
    }
};
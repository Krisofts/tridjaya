<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();

            $table->string('sku')->nullable()->unique();
            $table->string('name');

            $table->string('brand')->nullable();
            $table->string('category')->nullable();

            $table->decimal('price', 18, 2)->default(0);

            $table->boolean('is_active')->default(true);

            $table->timestamps();

            $table->index(['name', 'brand']);
            $table->index('category');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
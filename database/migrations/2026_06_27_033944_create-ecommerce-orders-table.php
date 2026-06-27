<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ecommerce_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->nullable()->constrained('ecommerce_customers')->nullOnDelete();
            $table->string('order_number', 30)->unique();
            $table->enum('status', ['pending','processing','shipped','delivered','cancelled','refunded'])->default('pending');
            $table->enum('payment_status', ['unpaid','paid','refunded'])->default('unpaid');
            $table->string('payment_method', 50)->nullable();
            $table->unsignedBigInteger('subtotal')->default(0);
            $table->unsignedBigInteger('shipping_cost')->default(0);
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('total')->default(0);
            $table->string('shipping_name', 100)->nullable();
            $table->string('shipping_phone', 20)->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_province', 100)->nullable();
            $table->string('shipping_postal', 10)->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });

        Schema::create('ecommerce_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('ecommerce_orders')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('ecommerce_products')->nullOnDelete();
            $table->string('name', 200);
            $table->string('sku', 50)->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedInteger('qty')->default(1);
            $table->unsignedBigInteger('subtotal');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ecommerce_order_items');
        Schema::dropIfExists('ecommerce_orders');
    }
};
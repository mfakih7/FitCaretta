<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->foreignId('variant_id')->nullable()->constrained('product_variants')->nullOnDelete();
            $table->string('product_name', 180);
            $table->string('product_slug', 200)->nullable();
            $table->string('variant_sku', 100)->nullable();
            $table->string('size_name', 60)->nullable();
            $table->string('color_name', 80)->nullable();
            $table->unsignedInteger('quantity');
            $table->decimal('base_price', 10, 2);
            $table->decimal('discounted_price', 10, 2);
            $table->decimal('line_subtotal', 10, 2);
            $table->decimal('line_total', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};

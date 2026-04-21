<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['product_id', 'size_id', 'stock_qty'], 'pv_product_size_stock_idx');
            $table->index(['product_id', 'color_id', 'stock_qty'], 'pv_product_color_stock_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['is_active', 'base_price', 'id'], 'products_active_price_id_idx');
            $table->index(['is_active', 'gender_target', 'id'], 'products_active_gender_id_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->index(['order_status', 'placed_at'], 'orders_status_placedat_idx');
            $table->index(['order_status', 'created_at'], 'orders_status_createdat_idx');
        });

        Schema::table('homepage_slides', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order', 'id'], 'homepage_slides_active_sort_id_idx');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->index(['is_active', 'sort_order', 'name'], 'categories_active_sort_name_idx');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_active_sort_name_idx');
        });

        Schema::table('homepage_slides', function (Blueprint $table) {
            $table->dropIndex('homepage_slides_active_sort_id_idx');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex('orders_status_createdat_idx');
            $table->dropIndex('orders_status_placedat_idx');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('products_active_gender_id_idx');
            $table->dropIndex('products_active_price_id_idx');
        });

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('pv_product_color_stock_idx');
            $table->dropIndex('pv_product_size_stock_idx');
        });
    }
};


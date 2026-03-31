<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table
                ->foreignId('color_id')
                ->nullable()
                ->after('product_id')
                ->constrained('colors')
                ->nullOnDelete()
                ->index();

            $table->index(['product_id', 'color_id']);
        });
    }

    public function down(): void
    {
        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'color_id']);
            $table->dropConstrainedForeignId('color_id');
        });
    }
};


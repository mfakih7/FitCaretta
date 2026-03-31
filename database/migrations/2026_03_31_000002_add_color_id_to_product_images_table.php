<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Production safety: this migration may run on databases where `color_id`
        // already exists (manual hotfix or partial deploy). In that case, no-op.
        if (! Schema::hasColumn('product_images', 'color_id')) {
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
        } else {
            // Ensure expected type/nullability without requiring doctrine/dbal.
            try {
                DB::statement("ALTER TABLE `product_images` MODIFY `color_id` BIGINT UNSIGNED NULL");
            } catch (\Throwable) {
                // ignore
            }
        }
    }

    public function down(): void
    {
        if (! Schema::hasColumn('product_images', 'color_id')) {
            return;
        }

        // Best-effort rollback (may not exist / may have different names).
        try {
            Schema::table('product_images', function (Blueprint $table) {
                $table->dropIndex(['product_id', 'color_id']);
            });
        } catch (\Throwable) {
            // ignore
        }

        try {
            Schema::table('product_images', function (Blueprint $table) {
                $table->dropConstrainedForeignId('color_id');
            });
        } catch (\Throwable) {
            try {
                DB::statement("ALTER TABLE `product_images` DROP COLUMN `color_id`");
            } catch (\Throwable) {
                // ignore
            }
        }
    }
};


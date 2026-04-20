<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('main_image_thumb_path')->nullable()->after('main_image_path');
            $table->string('main_image_medium_path')->nullable()->after('main_image_thumb_path');
            $table->string('main_image_original_path')->nullable()->after('main_image_medium_path');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['main_image_thumb_path', 'main_image_medium_path', 'main_image_original_path']);
        });
    }
};


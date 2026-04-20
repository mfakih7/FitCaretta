<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->string('image_thumb_path')->nullable()->after('image_path');
            $table->string('image_medium_path')->nullable()->after('image_thumb_path');
            $table->string('image_original_path')->nullable()->after('image_medium_path');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['image_thumb_path', 'image_medium_path', 'image_original_path']);
        });
    }
};


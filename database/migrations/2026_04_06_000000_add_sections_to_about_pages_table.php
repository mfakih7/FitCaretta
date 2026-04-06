<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('about_pages', function (Blueprint $table) {
            $table->string('page_title')->nullable()->after('id');
            $table->boolean('show_about_page')->default(false)->index()->after('is_enabled');

            $table->string('section1_title')->nullable()->after('show_about_page');
            $table->longText('section1_description')->nullable()->after('section1_title');
            $table->string('section1_image_path')->nullable()->after('section1_description');

            $table->string('section2_title')->nullable()->after('section1_image_path');
            $table->longText('section2_description')->nullable()->after('section2_title');
            $table->string('section2_image_path')->nullable()->after('section2_description');

            $table->string('section3_title')->nullable()->after('section2_image_path');
            $table->longText('section3_description')->nullable()->after('section3_title');
            $table->string('section3_image_path')->nullable()->after('section3_description');
        });
    }

    public function down(): void
    {
        Schema::table('about_pages', function (Blueprint $table) {
            $table->dropColumn([
                'page_title',
                'show_about_page',
                'section1_title',
                'section1_description',
                'section1_image_path',
                'section2_title',
                'section2_description',
                'section2_image_path',
                'section3_title',
                'section3_description',
                'section3_image_path',
            ]);
        });
    }
};


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('about_pages', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->longText('content')->nullable();
            $table->string('image_path')->nullable();
            $table->boolean('is_enabled')->default(false)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('about_pages');
    }
};


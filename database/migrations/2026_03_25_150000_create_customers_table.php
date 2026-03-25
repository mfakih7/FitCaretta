<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('full_name', 150);
            $table->string('email', 190)->nullable()->index();
            $table->string('phone', 30)->unique();
            $table->string('city_area', 120)->nullable();
            $table->text('address')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['full_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};


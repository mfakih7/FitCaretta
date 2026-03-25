<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('code', 60)->nullable()->unique();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('value', 10, 2);
            $table->enum('scope', ['product', 'category', 'global'])->default('global')->index();
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('priority')->default(0)->index();
            $table->timestamps();

            $table->index(['scope', 'is_active', 'start_date', 'end_date'], 'discount_scope_active_dates_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};

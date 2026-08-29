<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('child_categories', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('category_id')
            ->constrained('categories')
            ->onDelete('CASCADE');
            $table->foreignId('sub_category_id')
            ->constrained('sub_categories')
            ->onDelete('CASCADE');
            $table->string('name');
            $table->string('slug')->unique()->nullable();
            $table->string('meta_title')->nullable();
            $table->longText('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_categories');
    }
};

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
        Schema::create('product_collections', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('title'); 
            $table->longText('description')->nullable();
            $table->string('image');
            $table->enum('collection_type', ['manual', 'smart'])->default('manual'); // Added a default value
            $table->timestamps(); // Adds created_at and updated_at columns
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_collections');
    }
};

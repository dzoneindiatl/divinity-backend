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
        Schema::create('simple_variant_values', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('product_id')->unsigned(); // Ensure it's unsigned
            $table->bigInteger('variant_id');
            $table->integer('total_units')->default(0);
            $table->string('variant_values')->nullable();
            $table->timestamps();

            // Add foreign key constraint
            //$table->foreign('product_id')->references('id')->on('products')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('simple_veriant_values');
    }
};

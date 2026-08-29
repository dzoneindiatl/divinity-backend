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
        Schema::create('option_value_product_variants', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('value_id')
                ->constrained('product_values')
                ->onDelete('CASCADE');
            $table->foreignId('variant_id')
                ->constrained('product_variants')
                ->onDelete('CASCADE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('option_value_product_variants');
    }
};

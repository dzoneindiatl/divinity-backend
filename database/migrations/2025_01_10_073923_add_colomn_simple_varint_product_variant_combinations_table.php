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
        Schema::table('product_variant_combinations', function (Blueprint $table) {
             $table->integer('simple_varint_value')->nullable()->after('size_variant_value_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variant_combinations', function (Blueprint $table) {
             $table->dropColumn('simple_varint_value');
        });
    }
};

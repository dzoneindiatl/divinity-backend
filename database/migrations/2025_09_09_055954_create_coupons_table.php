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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('name', 20);
            $table->string('coupon_code', 20)->nullable();
            $table->enum('coupon_type', ['private', 'public']);
            $table->enum('user_type', ['all', 'existing', 'new'])->nullable();
            $table->enum('discount_type', ['flat', 'percentage'])->nullable();
            $table->float('discount_value', 10, 2);
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->float('min_cart_value', 10, 2)->nullable()->default(0.00);
            $table->integer('max_discount')->nullable()->default(0);
            $table->float('min_discount', 10, 2)->nullable()->default(0.00);
            $table->text('description')->nullable();
            $table->boolean('is_unlimited')->default(0);
            $table->integer('available_coupons')->nullable()->default(0);
            $table->integer('category_id')->nullable()->comment('0=All');
            $table->longText('sub_categories')->nullable(); // serialized or JSON format expected
            $table->boolean('is_active')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};

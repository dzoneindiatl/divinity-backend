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
        Schema::create('invoice_settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->string('pincode', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('prefix', 50)->nullable();
            $table->string('name')->nullable();
            $table->string('nature_spilly')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('packet_id')->nullable();
            $table->string('website_name')->nullable();
            $table->string('signature')->nullable();
            $table->string('designation')->nullable();
            $table->text('note')->nullable();
            $table->enum('is_active', ['1', '0'])->nullable();
            $table->string('invoice_setting')->nullable();
            $table->timestamps(); // created_at and updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_settings');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleMenuPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_menu_permissions', function (Blueprint $table) {
            $table->id(); // Primary key
            $table->unsignedBigInteger('role_id'); // Foreign key to roles table
            $table->integer('acl_id'); // Foreign key to acls table (integer type to match acls table)
            $table->text('permissions'); // JSON or serialized permissions for this menu
            
            // Foreign key constraints
            //$table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
            //$table->foreign('acl_id')->references('id')->on('acls')->onDelete('cascade');

            // Timestamps (optional)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('role_menu_permissions');
    }
}

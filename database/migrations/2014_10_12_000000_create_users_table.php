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
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('id_prefix');
            $table->string('id_no');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('contact_details')->nullable();
            $table->enum("sex", ["male", "female"]);
            $table->string('company');
            $table->string('business_unit');
            $table->string('department');
            $table->string('unit');
            $table->string('sub_unit');
            $table->string('location');
            $table->string('username')->unique();
            $table->string('password');
            $table->unsignedInteger("role_id")->index();
            $table->boolean('is_active')->default(true);

            $table->foreign("role_id")
            ->references("id")
            ->on("roles")
            ->onDelete('cascade');

        
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

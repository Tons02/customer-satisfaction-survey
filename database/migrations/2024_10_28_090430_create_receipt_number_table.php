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
        Schema::create('receipt_numbers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('receipt_number');
            $table->string('contact_details');
            $table->unsignedInteger("store_id")->index();
            $table->boolean('is_valid')->default(false);
            $table->boolean('is_used')->default(false);
            $table->boolean('is_done')->default(false);
            $table->timestamps('expiration_date');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipt_number');
    }
};

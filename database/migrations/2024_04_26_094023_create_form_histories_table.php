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
        Schema::create('form_histories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('survey_id');
            $table->string('mobile_number');
            $table->string('title')->nullable();
            $table->string('description')->nullable();
            $table->json('sections')->nullable(); 
            $table->boolean('is_active')->default(true);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('form_histories');
    }
};

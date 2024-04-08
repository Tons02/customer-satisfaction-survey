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
        Schema::create('section_question', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('section_id');
            $table->unsignedInteger('question_id');
            $table->timestamps();
        
            $table->foreign('section_id')->references('id')->on('sections')->onDelete('cascade');
            $table->foreign('question_id')->references('id')->on('questions')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_question');
    }
};

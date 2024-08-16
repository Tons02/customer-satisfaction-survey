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
        Schema::create('survey_answers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('entry_code'); 
            $table->unsignedInteger("store_id")->index()->nullable();
            $table->foreign("store_id")
            ->references("id")
            ->on("store_names");
            
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('mobile_number');
            $table->boolean('mobile_number_verified');
            $table->enum("gender", ["male", "female"]);
            $table->string('age');

            $table->json('questionnaire_answer')->nullable();
            $table->string('voucher_code')->nullable();
            $table->timestamp('valid_until')->nullable();
            $table->timestamp('next_voucher_date')->nullable();
            $table->string('claim')->nullable();
            $table->unsignedInteger("claim_by_user_id")->index()->nullable();
            $table->foreign("claim_by_user_id")
            ->references("id")
            ->on("users")
            ->onDelete('cascade');


            $table->boolean('is_active');
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('survey_answers');
    }
};

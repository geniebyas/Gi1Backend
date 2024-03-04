<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('feedback_questions', function (Blueprint $table) {
            $table->id('question_id');
            $table->foreignId('category_id')->constrained('feedback_question_categories','category_id');
            $table->text('question_text');
            $table->string('answer_type');
            $table->json('answers')->nullable(); // Use JSON to store an array of choices
            // Add other fields based on the answer type
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('feedback_questions');
    }
};

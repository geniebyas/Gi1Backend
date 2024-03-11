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
            Schema::create('feedback_users_response', function (Blueprint $table) {
                $table->id();
                $table->foreignId('question_id')->constrained('feedback_questions','question_id');
                $table->foreignId('uid')->constrained('users','uid');
                $table->text('response_text')->nullable();
                $table->boolean('response_boolean')->nullable();
                $table->integer('response_range')->nullable();
                $table->string('response_choice')->nullable();
                $table->string('response_date')->nullable();
                $table->timestamps();
            });
        }
    
        public function down()
        {
            Schema::dropIfExists('feedback_users_response');
        }
};

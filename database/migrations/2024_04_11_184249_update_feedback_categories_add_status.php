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
        Schema::table("feedback_question_categories",function ( Blueprint $table){
            $table->boolean('status')->default(true)->after("category_desc");        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

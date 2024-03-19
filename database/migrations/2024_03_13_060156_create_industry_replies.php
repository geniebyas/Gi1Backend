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
        Schema::create('industry_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industry_id')->constrained("industries");
            $table->foreignId('discussion_id')->constrained("industry_discussions");
            $table->string("uid"); // Define the data type and length of the column
            $table->foreign("uid")->references("uid")->on("users");
            $table->string('msg');
            $table->timestamps();
        });

        // reply_likes table migration
        Schema::create('reply_likes', function (Blueprint $table) {
            $table->id();
            $table->string("uid"); // Define the data type and length of the column
            $table->foreign("uid")->references("uid")->on("users");
            $table->foreignId('reply_id')->constrained("industry_replies");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industry_replies');
    }
};

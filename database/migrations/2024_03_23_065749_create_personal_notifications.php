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
        Schema::create('personal_notifications', function (Blueprint $table) {
            $table->id();
            $table->string("sender_uid"); // Define the data type and length of the column
            $table->foreign("sender_uid")->references("uid")->on("users");
            $table->string("reciever_uid"); // Define the data type and length of the column
            $table->foreign("reciever_uid")->references("uid")->on("users");
            $table->string("title");
            $table->string("body");
            $table->string("img_url")->nullable();
            $table->string("android_route")->nullable();
            $table->boolean("is_read")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('personal_notifications');
    }
};

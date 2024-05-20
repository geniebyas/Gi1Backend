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
        Schema::create('users_links', function (Blueprint $table) {
            $table->id();
            $table->string("uid"); // Define the data type and length of the column
            $table->foreign("uid")->references("uid")->on("users");
            $table->string("link");
            $table->string("title");
            $table->integer("clicks");
            $table->timestamps();
        });
        Schema::table("public_notifications",function ( Blueprint $table){
            $table->boolean('is_announcement')->default(false)->after("body");
            $table->integer("views")->default(500)->after("is_announcement");
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('update_links_notifications');
    }
};

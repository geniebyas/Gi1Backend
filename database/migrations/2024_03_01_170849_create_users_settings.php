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
        Schema::create('users_settings', function (Blueprint $table) {
            $table->id();
            $table->string("uid"); // Define the data type and length of the column
            $table->foreign("uid")->references("uid")->on("users")->nullable();
            $table->boolean("is_private")->default(false);
            $table->string('refer_code')->unique();
            $table->string("referred_by"); // Define the data type and length of the column
            $table->foreign("referred_by")->references("uid")->on("users");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_settings');
    }
};

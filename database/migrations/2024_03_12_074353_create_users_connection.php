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
        Schema::create('users_connection', function (Blueprint $table) {
            $table->id();
            $table->string("source_uid"); // Define the data type and length of the column
            $table->foreign("source_uid")->references("uid")->on("users");
            $table->string("dest_uid"); // Define the data type and length of the column
            $table->foreign("dest_uid")->references("uid")->on("users");
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_connection');
    }
};

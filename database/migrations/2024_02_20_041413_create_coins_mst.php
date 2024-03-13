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
        // Schema::create('coins_mst', function (Blueprint $table) {
        //     $table->id();
        //     $table->string("uid"); // Define the data type and length of the column
        //     $table->foreign("uid")->references("uid")->on("users");
        //     $table->string("type");
        //     $table->foreignId("action_id")->constrained("coins_actions_mst");
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coins_mst');
    }
};

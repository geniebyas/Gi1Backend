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
        //
        Schema::table("personal_notifications" ,function(Blueprint $table){
            $table->string("type")->nullable()->after("body");
            $table->json("data")->nullable()->after("type");
        });

        Schema::table("coins_actions_mst",function(Blueprint $table){
            $table->boolean('status')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

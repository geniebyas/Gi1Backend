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
        Schema::create('cdsm_post', function (Blueprint $table) {
            $table->id();
            $table->string("img")->nullable();
            $table->string("category")->nullable();
            $table->string("caption")->nullable();
            $table->string("location")->nullable();
            $table->string("description")->nullable();
            $table->string("tags")->nullable();
            $table->integer("views")->nullable();
            $table->boolean("is_active")->default(true);
            $table->string("uid");
            $table->foreign("uid")->references("uid")->on("users")->onDelete("cascade");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdsm_post');
    }
};

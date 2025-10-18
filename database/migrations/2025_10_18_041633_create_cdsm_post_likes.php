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
        Schema::create('cdsm_post_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId("post_id")->constrained("cdsm_post")->onDelete("cascade");
            $table->foreignId("uid")->constrained("users")->onDelete("cascade"); // Assuming you have ar
            $table->boolean("is_liked")->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cdsm_post_likes');
    }
};

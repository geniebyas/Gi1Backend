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
        Schema::create('cdsm_post_interested', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("post_id");
            $table->foreign("post_id")->references("id")->on("cdsm_post")->onDelete("cascade");
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
        Schema::dropIfExists('cdsm_post_interested');
    }
};

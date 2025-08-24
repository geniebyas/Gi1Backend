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
        Schema::create('public_notifications', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("body");
            $table->string("img_url")->nullable();
            $table->string("android_route")->nullable();
            $table->string("topic")->default("all");
            $table->boolean("is_announcement")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('public_notifications');
    }
};

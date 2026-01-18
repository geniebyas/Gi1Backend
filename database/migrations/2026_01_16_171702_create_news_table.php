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
        Schema::create('news', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->text("content");
            $table->string("uid");
            $table->foreign("uid")->references("uid")->on("users")->onDelete("cascade");
            $table->string("img_url");
            $table->boolean("is_active")->default(true);
            $table->boolean("is_featured")->default(false);
            $table->string("category");
            $table->integer("likes")->default(0);
            $table->string("slug")->unique();
            $table->string("tags")->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->json('content_json')->nullable();
            $table->longText('content_html')->nullable();
            $table->integer('reading_time')->default(0);
            $table->integer('shares_count')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news');
    }
};

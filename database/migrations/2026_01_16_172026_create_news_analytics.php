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
        Schema::create('news_analytics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("news_id");
            $table->foreign("news_id")->references("id")->on("news")->onDelete("cascade");
            $table->string("ip_address");
            $table->string("user_agent")->nullable();
            $table->string("country")->nullable();
            $table->string("city")->nullable();
            $table->string("region")->nullable();
            $table->string("latitude")->nullable();
            $table->string("longitude")->nullable();
            $table->string("device_type")->nullable();
            $table->string("browser")->nullable();
            $table->string("operating_system")->nullable();
            $table->string("session_id")->nullable();
            $table->boolean("is_unique")->default(false);
            $table->string("utm_source")->nullable();
            $table->string("utm_medium")->nullable();
            $table->string("utm_campaign")->nullable();
            $table->timestamps();
        });

        // Add index for faster lookups
        Schema::table('news_analytics', function (Blueprint $table) {
            $table->index(['news_id', 'ip_address', 'created_at']);
            $table->index(['news_id', 'created_at']);
            $table->index(['news_id', 'device_type', 'created_at']);
            $table->index(['news_id', 'country', 'created_at']);
            $table->index(['news_id', 'city', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_analytics');
    }
};

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
        Schema::create('jobs_mst', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->string('type')->nullable();
            $table->string('location')->nullable();
            $table->string('salary')->nullable();
            $table->string('experience')->nullable();
            $table->string('skills')->nullable();
            $table->string('company')->nullable();
            $table->string('website')->nullable();
            $table->string('banner')->nullable();
            $table->string('status')->default('active');
            $table->string('created_by')->nullable();
            $table->foreign('created_by')->references('uid')->on('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs_mst');
    }
};

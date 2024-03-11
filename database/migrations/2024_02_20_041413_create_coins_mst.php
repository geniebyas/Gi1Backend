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
        Schema::create('coins_mst', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('uid')->constrained('users','uid');
            $table->string("type");
            $table->integer("action_id");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coins_mst');
    }
};

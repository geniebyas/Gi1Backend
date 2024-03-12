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
        Schema::create('users_settings', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->boolean("is_private")->default(false);
            $table->string('refer_code')->unique()->default(random_int(100000,999999));
            $table->string('refered_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_settings');
    }
};

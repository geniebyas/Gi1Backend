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
        Schema::table('users', function (Blueprint $table) {
            $table->string('token')->nullable()->after('bio');
        });
        Schema::table('industries', function (Blueprint $table) {
            $table->boolean('is_discussion_allowed')->default(true)->nullable()->after('status');
        });

        Schema::table('coins_mst',function (Blueprint $table) {
            $table->string('description')->nullable()->after('amount');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    
        Schema::table('industries', function (Blueprint $table) {
            $table->dropColumn('is_discussion_allowed');
        });
    
        Schema::table('coins_mst', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};

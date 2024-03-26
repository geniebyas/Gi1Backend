<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        $tables = [
            // List all tables with foreign key constraints referencing the users table
            'coins_mst',
            'user_wallet',
            'users_settings',
            'feedback_users_response',
            'industry_discussions',
            'industry_replies',
            'industry_views',
            'discussion_likes',
            'reply_likes'
            // Add more table names as needed
        ];

        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropForeign(['uid']);
                $table->foreign('uid')
                      ->references('uid')
                      ->on('users')
                      ->onDelete('cascade');
            });
        }
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};

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
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the existing foreign key
            $table->dropForeign(['user_id']);
            
            // Add the correct foreign key
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['user_id']);
            
            // Restore the original foreign key
            $table->foreign('user_id')->references('id')->on('Users');
        });
    }
}; 
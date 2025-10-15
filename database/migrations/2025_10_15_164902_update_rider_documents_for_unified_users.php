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
        Schema::table('rider_documents', function (Blueprint $table) {
           $table->dropForeign(['driver_id']);

            // Rename column driver_id -> user_id
            $table->renameColumn('driver_id', 'user_id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rider_documents', function (Blueprint $table) {
           $table->dropForeign(['user_id']);
            $table->renameColumn('user_id', 'driver_id');
            $table->foreign('driver_id')->references('id')->on('reg_riders')->onDelete('cascade');
        });
    }
};

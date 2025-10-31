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
         Schema::table('vehicle_details', function (Blueprint $table) {
            // Drop the old foreign key
            $table->dropForeign(['driver_id']);

            // Add the new foreign key referencing users
            $table->foreign('driver_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade'); });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
     Schema::table('vehicle_details', function (Blueprint $table) {
            // Revert to reg_riders if needed
            $table->dropForeign(['driver_id']);
            $table->foreign('driver_id')
                ->references('id')
                ->on('reg_riders')
                ->onDelete('cascade');
        });
    }
};

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
        Schema::table('users', function (Blueprint $table) {
          

            $table->boolean('is_verified')->default(false)->after('profile_image');
            $table->boolean('is_available')->default(false)->after('is_verified');
            $table->decimal('latitude', 10, 7)->nullable()->after('is_available');
            $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
           $table->dropColumn([
                'phone',
                'gender',
                'profile_image',
                'is_verified',
                'is_available',
                'latitude',
                'longitude',
            ]);
        });
    }
};

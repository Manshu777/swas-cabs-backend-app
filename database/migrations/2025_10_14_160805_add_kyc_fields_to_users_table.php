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
              $table->string('adhar_number')->unique()->nullable();
            $table->enum('kyc_status', ['pending', 'verified', 'failed'])->default('pending');
            $table->timestamp('kyc_verified_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
                        $table->dropColumn(['adhar_number', 'kyc_status', 'kyc_verified_at']);

        });
    }
};

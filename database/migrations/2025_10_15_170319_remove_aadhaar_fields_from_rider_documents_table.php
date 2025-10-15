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
                       $table->dropColumn(['aadhaar_number', 'aadhaar_front_image', 'aadhaar_back_image']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rider_documents', function (Blueprint $table) {
              $table->string('aadhaar_number')->nullable()->after('license_image');
            $table->string('aadhaar_front_image')->nullable()->after('aadhaar_number');
            $table->string('aadhaar_back_image')->nullable()->after('aadhaar_front_image');
        });
    }
};

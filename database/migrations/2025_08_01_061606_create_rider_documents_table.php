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
        Schema::create('rider_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('reg_riders')->onDelete('cascade');
            $table->string('license_number'); 
            $table->string('license_image');
            $table->string('aadhaar_number');
            $table->string('aadhaar_front_image');
            $table->string('aadhaar_back_image')->nullable();
            $table->string('vehicle_rc_number');
            $table->string('vehicle_rc_image');
            $table->string('insurance_number');
            $table->string('insurance_image');
            $table->string('police_verification_image')->nullable();
            $table->string('status')->default('pending'); 
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rider_documents');
    }
};

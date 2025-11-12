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
        Schema::create('driverdocuments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('reg_riders')->onDelete('cascade');

            // License
            $table->string('license_number');
            $table->string('license_image');
            $table->enum('license_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('license_rejection_reason')->nullable();

            // Aadhaar
            $table->string('aadhaar_number');
            $table->string('aadhaar_front_image');
            $table->string('aadhaar_back_image')->nullable();
            $table->enum('aadhaar_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('aadhaar_rejection_reason')->nullable();

            // Vehicle RC
            $table->string('vehicle_rc_number');
            $table->string('vehicle_rc_image');
            $table->enum('vehicle_rc_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('vehicle_rc_rejection_reason')->nullable();

            // Insurance
            $table->string('insurance_number');
            $table->string('insurance_image');
            $table->enum('insurance_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('insurance_rejection_reason')->nullable();

            // Police Verification (optional)
            $table->string('police_verification_image')->nullable();
            $table->enum('police_verification_status', ['pending', 'verified', 'rejected'])->default('pending');
            $table->text('police_verification_rejection_reason')->nullable();
     

            $table->enum('document', ['pending', 'verified', 'rejected'])->default('pending');

             
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driverdocuments');
    }
};

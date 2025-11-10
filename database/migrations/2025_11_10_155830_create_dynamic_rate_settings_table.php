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
        Schema::create('dynamic_rate_settings', function (Blueprint $table) {
            $table->id();
            $table->string('area_name')->default('All');
            $table->string('vehicle_type')->default('Any');
            $table->time('day_start_time')->default('06:00:00');
            $table->time('night_start_time')->default('22:00:00');
            $table->decimal('min_rate_per_km_day', 8, 2)->default(8.00);
            $table->decimal('max_rate_per_km_day', 8, 2)->default(12.00);
            $table->decimal('min_rate_per_km_night', 8, 2)->default(10.00);
            $table->decimal('max_rate_per_km_night', 8, 2)->default(15.00);
            $table->decimal('default_rate_per_km_day', 8, 2)->default(10.00);
            $table->decimal('default_rate_per_km_night', 8, 2)->default(13.00);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dynamic_rate_settings');
    }
};
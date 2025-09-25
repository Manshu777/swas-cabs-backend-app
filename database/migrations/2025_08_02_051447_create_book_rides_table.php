<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('book_rides', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->string('pickup_location');
            $table->string('drop_location');
            $table->decimal('fare', 10, 2)->nullable();
            $table->enum('status', ['pending', 'accepted', 'in_progress', 'completed', 'canceled'])->default('pending');
            $table->timestamp('scheduled_at')->nullable();
            $table->string('package_name')->nullable();
            $table->decimal('pickup_latitude', 10, 6)->nullable();
            $table->decimal('pickup_longitude', 10, 6)->nullable();
            $table->decimal('drop_latitude', 10, 6)->nullable();
            $table->decimal('drop_longitude', 10, 6)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('reg_riders')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_rides');
    }
};

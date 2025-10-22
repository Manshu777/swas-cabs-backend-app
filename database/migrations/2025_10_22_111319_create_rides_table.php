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
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
           
            $table->string('pickup_location');
            $table->string('drop_location');
            $table->decimal('distance', 8, 2)->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->enum('status', [
                'pending', 'accepted', 'ongoing', 'completed', 'cancelled'
            ])->default('pending');
             $table->timestamp('pickup_time')->nullable();
            $table->timestamp('drop_time')->nullable();
                        $table->enum('payment_status', ['pending', 'paid'])->default('pending');
  $table->string('payment_method')->nullable();

 $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('driver_id')->nullable();
  $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('set null');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};

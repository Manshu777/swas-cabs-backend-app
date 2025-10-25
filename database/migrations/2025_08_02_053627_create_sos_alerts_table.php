<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sos_alerts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ride_id');
            $table->float('latitude');
            $table->float('longitude');
            $table->string('location');
            $table->string('status')->default('pending');
            $table->string('geohash');
            $table->timestamps();
            $table->foreign('ride_id')->references('id')->on('rides')->onDelete('cascade');
            $table->index('geohash');
        });
    }

    public function down(): void
    {
        Schema::table('sos_alerts', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
            $table->dropColumn('status');
            $table->enum('status', ['active', 'resolved'])->after('location')->default('active');
        });
    }
};
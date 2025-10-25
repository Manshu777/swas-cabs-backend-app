<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->decimal('pickup_latitude', 10, 6)->after('pickup_location')->nullable();
            $table->decimal('pickup_longitude', 10, 6)->after('pickup_latitude')->nullable();
            $table->decimal('dropoff_latitude', 10, 6)->after('drop_location')->nullable();
            $table->decimal('dropoff_longitude', 10, 6)->after('dropoff_latitude')->nullable();
            $table->decimal('current_latitude', 10, 6)->after('dropoff_longitude')->nullable();
            $table->decimal('current_longitude', 10, 6)->after('current_latitude')->nullable();
            $table->string('package_name')->after('dropoff_longitude')->nullable();
            $table->timestamp('scheduled_at')->after('package_name')->nullable();
            $table->decimal('fare', 10, 2)->after('price')->nullable();
            $table->index(['pickup_latitude', 'pickup_longitude'], 'pickup_location_index');
        });
    }

    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropColumn([
                'pickup_latitude',
                'pickup_longitude',
                'dropoff_latitude',
                'dropoff_longitude',
                'current_latitude',
                'current_longitude',
                'package_name',
                'scheduled_at',
                'fare'
            ]);
            $table->dropIndex('pickup_location_index');
        });
    }
};
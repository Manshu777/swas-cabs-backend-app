<?php
namespace App\Services;
use App\Events\DriverLocationUpdate;
use Illuminate\Support\Facades\Redis;

class LocationService
{
    public function updateDriverLocation($driverId, $lat, $lng, $bearing, $rideId = null)
    {
        // Cache in Redis
        Redis::set("driver:{$driverId}:location", json_encode([
            'lat' => $lat,
            'lng' => $lng,
            'bearing' => $bearing,
            'updated_at' => now()->timestamp,
        ]), 'EX', 3600);

        // Broadcast via Pusher
        event(new DriverLocationUpdate($driverId, $lat, $lng, $bearing, $rideId));
    }

    public function getNearbyDrivers($lat, $lng, $radius)
    {
        $drivers = \App\Models\User::where('is_available', true)
            ->whereRaw('
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                ) <= ?
            ', [$lat, $lng, $lat, $radius])
            ->get();
        return $drivers->pluck('id')->toArray();
    }
}
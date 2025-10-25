<?php
namespace App\Http\Controllers\Drivers;
use App\Http\Controllers\Controller;
use App\Events\DriverLocationUpdate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class LocationController extends Controller
{
    public function update(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'lng' => 'required|numeric',
            'bearing' => 'nullable|numeric',
            'ride_id' => 'nullable|exists:rides,id',
        ]);

        $driverId = auth()->id();
        $lat = $request->lat;
        $lng = $request->lng;
        $bearing = $request->bearing;
        $rideId = $request->ride_id;

        // Cache in Redis
        Redis::set("driver:{$driverId}:location", json_encode([
            'lat' => $lat,
            'lng' => $lng,
            'bearing' => $bearing,
            'updated_at' => now()->timestamp,
        ]), 'EX', 3600); // Expire after 1 hour

        // Broadcast via Pusher
        event(new DriverLocationUpdate($driverId, $lat, $lng, $bearing, $rideId));

        return response()->json(['status' => 'location_updated']);
    }
}
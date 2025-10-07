<?php

namespace App\Http\Controllers\Users;

use App\Models\BookRide as Ride;
use Illuminate\Http\Request;
use App\Events\RideCreated;
use App\Events\FareEstimated;
use App\Http\Controllers\Controller;

class BookRideController extends Controller
{
    public function estimateFare(Request $request)
    {
        $request->validate([
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'drop_latitude' => 'required|numeric',
            'drop_longitude' => 'required|numeric'
        ]);

        $distance = $this->calculateDistance(
            $request->pickup_latitude,
            $request->pickup_longitude,
            $request->drop_latitude,
            $request->drop_longitude
        );

        $fare = $distance * 10; // ₹10/km

        // Broadcast fare estimation to the user's private channel
        event(new FareEstimated(round($fare, 2), auth()->id()));

        return response()->json(['fare' => round($fare, 2)]);
    }

    public function createRide(Request $request)
    {
        $request->validate([
            'pickup_location' => 'required|string',
            'drop_location' => 'required|string',
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'drop_latitude' => 'required|numeric',
            'drop_longitude' => 'required|numeric',
            'scheduled_at' => 'nullable|date',
            'package_name' => 'nullable|string'
        ]);

        $ride = Ride::create([
            'user_id' => auth()->id(),
            'pickup_location' => $request->pickup_location,
            'drop_location' => $request->drop_location,
            'pickup_latitude' => $request->pickup_latitude,
            'pickup_longitude' => $request->pickup_longitude,
            'drop_latitude' => $request->drop_latitude,
            'drop_longitude' => $request->drop_longitude,
            'scheduled_at' => $request->scheduled_at,
            'package_name' => $request->package_name,
            'status' => 'pending'
        ]);

        // Broadcast the new ride to all drivers/admin
        event(new RideCreated($ride));

        return response()->json([
            'message' => 'Ride booked successfully',
            'ride' => $ride
        ], 201);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2 +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}

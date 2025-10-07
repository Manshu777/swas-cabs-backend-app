<?php

namespace App\Http\Controllers\Drivers;

use App\Models\Rides as Ride;
use App\Models\RegRiders as Driver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;
use App\Http\Controllers\Controller;


class RideController extends Controller
{
    protected $pusher;

    public function __construct()
    {
        $this->pusher = new Pusher(
            env('PUSHER_APP_KEY'),
            env('PUSHER_APP_SECRET'),
            env('PUSHER_APP_ID'),
            ['cluster' => env('PUSHER_APP_CLUSTER'), 'useTLS' => true]
        );
    }

    public function searchRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'dropoff_latitude' => 'required|numeric',
            'dropoff_longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Find available drivers within 5km radius
        $drivers = Driver::where('is_available', true)
            ->where('is_verified', true)
            ->whereRaw('
                6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                ) <= 5
            ', [
                $request->pickup_latitude,
                $request->pickup_longitude,
                $request->pickup_latitude
            ])
            ->with('documents')
            ->get();

        return response()->json(['drivers' => $drivers], 200);
    }

    public function bookRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_location' => 'required|string',
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'dropoff_location' => 'required|string',
            'dropoff_latitude' => 'required|numeric',
            'dropoff_longitude' => 'required|numeric',
            'scheduled_at' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Calculate fare using Google Maps API
        $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
            'origin' => $request->pickup_latitude . ',' . $request->pickup_longitude,
            'destination' => $request->dropoff_latitude . ',' . $request->dropoff_longitude,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to calculate fare'], 500);
        }

        $distance = $response->json()['routes'][0]['legs'][0]['distance']['value'] / 1000; // km
        $fare = $distance * 10; // ₹10/km (example rate)

        $ride = Ride::create([
            'user_id' => auth()->id(),
            'pickup_location' => $request->pickup_location,
            'pickup_latitude' => $request->pickup_latitude,
            'pickup_longitude' => $request->pickup_longitude,
            'dropoff_location' => $request->dropoff_location,
            'dropoff_latitude' => $request->dropoff_latitude,
            'dropoff_longitude' => $request->dropoff_longitude,
            'fare' => $fare,
            'scheduled_at' => $request->scheduled_at,
        ]);

        // Notify nearby drivers
        $this->pusher->trigger('ride-requests', 'new-ride', [
            'ride_id' => $ride->id,
            'pickup_location' => $ride->pickup_location,
            'dropoff_location' => $ride->dropoff_location,
            'fare' => $ride->fare
        ]);

        return response()->json(['message' => 'Ride booked successfully', 'ride' => $ride], 201);
    }

    public function acceptRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ride = Ride::find($request->ride_id);

        if ($ride->status !== 'pending') {
            return response()->json(['message' => 'Ride already assigned or completed'], 400);
        }

        $ride->update([
            'driver_id' => auth()->id(),
            'status' => 'accepted'
        ]);

        // Notify user
        $this->pusher->trigger('user-' . $ride->user_id, 'ride-accepted', [
            'ride_id' => $ride->id,
            'driver' => auth()->user()
        ]);

        return response()->json(['message' => 'Ride accepted successfully', 'ride' => $ride], 200);
    }

    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ride = Ride::find($request->ride_id);

        if ($ride->driver_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $ride->update([
            'current_latitude' => $request->latitude,
            'current_longitude' => $request->longitude
        ]);

        // Update driver location
        auth()->user()->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        // Broadcast location update
        $this->pusher->trigger('user-' . $ride->user_id, 'location-update', [
            'ride_id' => $ride->id,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude
        ]);

        return response()->json(['message' => 'Location updated successfully'], 200);
    }

    public function trackRide(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ride = Ride::find($request->ride_id);

        if ($ride->user_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json([
            'ride' => $ride,
            'current_location' => [
                'latitude' => $ride->current_latitude,
                'longitude' => $ride->current_longitude
            ]
        ], 200);
    }

    public function assignDriver(Request $request, $rideId)
    {
        $request->validate(['driver_id' => 'required|exists:reg_riders,id']);

        $ride = Ride::find($rideId);
        if (!$ride) {
            return response()->json(['message' => 'Ride not found'], 404);
        }

        $ride->driver_id = $request->driver_id;
        $ride->status = 'accepted';
        $ride->save();

        return response()->json([
            'message' => 'Driver assigned successfully',
            'ride' => $ride
        ]);
    }

}
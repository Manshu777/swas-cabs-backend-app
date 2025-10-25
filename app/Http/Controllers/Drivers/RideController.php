<?php
namespace App\Http\Controllers\Drivers;
use App\Http\Controllers\Controller;
use App\Models\Rides as Ride;
use App\Models\RegRiders as Driver;
use App\Events\RideRequestCreated;
use App\Events\DriverLocationUpdate;
use App\Events\DriverCounterOffer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class RideController extends Controller
{
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

        $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
            'origin' => $request->pickup_latitude . ',' . $request->pickup_longitude,
            'destination' => $request->dropoff_latitude . ',' . $request->dropoff_longitude,
            'key' => env('GOOGLE_MAPS_API_KEY'),
        ]);

        if ($response->failed()) {
            return response()->json(['message' => 'Failed to calculate fare'], 500);
        }

        $distance = $response->json()['routes'][0]['legs'][0]['distance']['value'] / 1000;
        $fare = $distance * 10;

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
            'status' => 'pending',
        ]);

        event(new RideRequestCreated($ride));

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

        return response()->json(['message' => 'Ride accepted successfully', 'ride' => $ride], 200);
    }

    public function driverAction(Request $request, $rideId)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:accept,counter,decline',
            'driver_price' => 'required_if:action,counter|numeric',
            'eta_seconds' => 'required_if:action,counter|integer',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $ride = Ride::findOrFail($rideId);

        if ($request->action === 'counter') {
            event(new DriverCounterOffer($rideId, auth()->id(), $request->driver_price, $request->eta_seconds));
            \App\Models\RideOffer::create([
                'ride_id' => $ride->id,
                'driver_id' => auth()->id(),
                'offered_price' => $request->driver_price,
            ]);
        } elseif ($request->action === 'accept') {
            $ride->update(['driver_id' => auth()->id(), 'status' => 'accepted']);
        } elseif ($request->action === 'decline') {
            // Optionally log decline action
        }

        return response()->json(['message' => 'Action processed', 'ride' => $ride]);
    }

    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ride_id' => 'required|exists:rides,id',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'bearing' => 'nullable|numeric',
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
            'current_longitude' => $request->longitude,
        ]);

        event(new DriverLocationUpdate(auth()->id(), $request->latitude, $request->longitude, $request->bearing, $ride->id));

        return response()->json(['message' => 'Location updated successfully'], 200);
    }

    // Keep trackRide and assignDriver methods unchanged
}
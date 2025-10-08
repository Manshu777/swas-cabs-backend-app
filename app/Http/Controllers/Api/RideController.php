<?php

namespace App\Http\Controllers\API;
use App\Http\Controllers\Controller;

   use App\Models\Ride;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class RideController extends Controller
   {


    public function available(Request $request)
{
    $validated = $request->validate([
        'latitude' => 'required|numeric',
        'longitude' => 'required|numeric',
    ]);

    $rides = Ride::where('status', 'pending')
        ->whereRaw("
            6371 * acos(
                cos(radians(?)) * cos(radians(pickup_latitude)) * cos(radians(pickup_longitude) - radians(?)) +
                sin(radians(?)) * sin(radians(pickup_latitude))
            ) < 5", [$validated['latitude'], $validated['longitude'], $validated['latitude']])
        ->with('passenger')
        ->get();

    return response()->json($rides);
}

public function accept(Request $request, Ride $ride)
{
    if ($ride->status !== 'pending' || !Auth::user()->isDriver()) {
        return response()->json(['message' => 'Ride is not available'], 403);
    }

    $ride->update([
        'driver_id' => Auth::id(),
        'status' => 'accepted',
    ]);

    event(new \App\Events\RideUpdated($ride));

    return response()->json(['message' => 'Ride accepted']);
}

public function updateStatus(Request $request, Ride $ride)
{
    if ($ride->driver_id !== Auth::id()) {
        return response()->json(['message' => 'Unauthorized'], 403);
    }

    $validated = $request->validate([
        'status' => 'required|in:in_progress,completed,cancelled',
        'current_latitude' => 'nullable|numeric',
        'current_longitude' => 'nullable|numeric',
        'fare' => 'nullable|numeric',
    ]);

    $ride->update($validated);
    event(new \App\Events\RideUpdated($ride));

    return response()->json(['message' => 'Ride status updated']);
}


public function driverRides()
{
    $rides = Auth::user()->ridesAsDriver()->with(['passenger', 'ratings'])->get();
    $earnings = $rides->where('status', 'completed')->sum('fare');
    return response()->json(['rides' => $rides, 'earnings' => $earnings]);
}

public function driverRatings()
{
    $ratings = Auth::user()->ratingsReceived()->with(['ride', 'passenger'])->get();
    $averageRating = $ratings->avg('rating');
    return response()->json(['ratings' => $ratings, 'average_rating' => $averageRating]);
}





       public function store(Request $request)
       {
           $validated = $request->validate([
               'pickup_location' => 'required|string',
               'pickup_latitude' => 'required|numeric',
               'pickup_longitude' => 'required|numeric',
               'dropoff_location' => 'required|string',
               'dropoff_latitude' => 'required|numeric',
               'dropoff_longitude' => 'required|numeric',
               'package_name' => 'nullable|string',
               'scheduled_at' => 'nullable|date|after:now',
           ]);

           $fare = $this->calculateFare($validated['pickup_latitude'], $validated['pickup_longitude'], $validated['dropoff_latitude'], $validated['dropoff_longitude']);

           $ride = Ride::create([
               'user_id' => Auth::id(),
               'pickup_location' => $validated['pickup_location'],
               'pickup_latitude' => $validated['pickup_latitude'],
               'pickup_longitude' => $validated['pickup_longitude'],
               'dropoff_location' => $validated['dropoff_location'],
               'dropoff_latitude' => $validated['dropoff_latitude'],
               'dropoff_longitude' => $validated['dropoff_longitude'],
               'package_name' => $validated['package_name'],
               'scheduled_at' => $validated['scheduled_at'],
               'fare' => $fare,
               'status' => 'pending',
           ]);

           // Notify nearby drivers (using Pusher or Laravel WebSockets)
           event(new \App\Events\RideRequested($ride));

           return response()->json(['message' => 'Ride booked successfully', 'ride' => $ride], 201);
       }

       private function calculateFare($pickupLat, $pickupLng, $dropoffLat, $dropoffLng)
       {
           // Use Google Maps API or a simple distance formula
           $distance = $this->calculateDistance($pickupLat, $pickupLng, $dropoffLat, $dropoffLng);
           return $distance * config('app.fare_per_km', 10); // Example: $10 per km
       }

       private function calculateDistance($lat1, $lng1, $lat2, $lng2)
       {
           // Haversine formula to calculate distance
           $earthRadius = 6371; // km
           $dLat = deg2rad($lat2 - $lat1);
           $dLng = deg2rad($lng2 - $lng1);
           $a = sin($dLat / 2) * sin($dLat / 2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) * sin($dLng / 2);
           $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
           return $earthRadius * $c;
       }

       public function passengerRides()
{
    $rides = Auth::user()->ridesAsPassenger()->with(['driver', 'ratings'])->get();
    return response()->json($rides);
}
   }
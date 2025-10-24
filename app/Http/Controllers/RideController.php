<?php

namespace App\Http\Controllers;

use App\Models\Driverwallet;
use App\Models\Ride;
use App\Models\RideOffer;
use Illuminate\Http\Request;

class RideController extends Controller
{
    // 1️⃣ User books ride
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'pickup_location' => 'required|string',
            'drop_location' => 'required|string',
        ]);

        $ride = Ride::create($data);

        broadcast(new \App\Events\RideRequested($ride))->toOthers();

        return response()->json([
            'message' => 'Ride requested successfully',
            'ride' => $ride
        ]);
    }

    // 2️⃣ Driver sends offer
    public function offer(Request $request, $rideId)
    {
        $data = $request->validate([
            'driver_id' => 'required|exists:reg_riders,id',
            'offer_price' => 'required|numeric|min:10',
        ]);

        $offer = RideOffer::create([
            'ride_id' => $rideId,
            'driver_id' => $data['driver_id'],
            'offer_price' => $data['offer_price']
        ]);

        broadcast(new \App\Events\RideOfferCreated($offer))->toOthers();

        return response()->json(['message' => 'Offer sent', 'offer' => $offer]);
    }

    // 3️⃣ Get all offers for a ride
    public function offers($rideId)
    {
        $offers = RideOffer::with('driver')->where('ride_id', $rideId)->get();
        return response()->json($offers);
    }

    // 4️⃣ User confirms one driver
    public function confirm(Request $request, $rideId)
    {
        $data = $request->validate([
            'driver_id' => 'required|exists:reg_riders,id',
        ]);

        $ride = Ride::findOrFail($rideId);

        $ride->update([
            'driver_id' => $data['driver_id'],
            'status' => 'confirmed'
        ]);

        RideOffer::where('ride_id', $rideId)->update(['status' => 'rejected']);
        RideOffer::where('ride_id', $rideId)
            ->where('driver_id', $data['driver_id'])
            ->update(['status' => 'accepted']);

        broadcast(new \App\Events\RideConfirmed($ride))->toOthers();

        return response()->json(['message' => 'Ride confirmed', 'ride' => $ride]);
    }

    // 5️⃣ Cancel ride
    public function cancel($id)
    {
        $ride = Ride::find($id);
        if (!$ride) return response()->json(['message' => 'Ride not found'], 404);

        $ride->update(['status' => 'cancelled']);
        return response()->json(['message' => 'Ride cancelled']);
    }
}

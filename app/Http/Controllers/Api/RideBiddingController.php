<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\Bid;
use App\Events\RideRequested;
use App\Events\BidReceived;
use Illuminate\Http\Request;

class RideBiddingController extends Controller
{
    // Step 1: Passenger creates a request
    public function storeRide(Request $request)
    {
        $ride = Ride::create([
            'user_id' => auth()->id(),
            'pickup_location' => $request->pickup_location,
            'drop_location' => $request->drop_location,
            'initial_fare' => $request->initial_fare,
            'status' => 'searching'
        ]);

        broadcast(new RideRequested($ride))->toOthers();

        return response()->json(['message' => 'Ride requested!', 'ride' => $ride]);
    }

    // Step 2: Driver sends an offer
    public function submitBid(Request $request, $rideId)
    {
        $bid = Bid::create([
            'ride_id' => $rideId,
            'driver_id' => auth()->id(),
            'offered_fare' => $request->offered_fare,
        ]);

        broadcast(new BidReceived($bid))->toOthers();

        return response()->json(['message' => 'Bid sent!', 'bid' => $bid]);
    }

    // Step 3: Passenger accepts one offer
    public function acceptBid($bidId)
    {
        $bid = Bid::findOrFail($bidId);
        $ride = $bid->ride;

        $ride->update([
            'driver_id' => $bid->driver_id,
            'final_fare' => $bid->offered_fare,
            'status' => 'accepted'
        ]);

        $bid->update(['status' => 'accepted']);
        
        return response()->json(['message' => 'Driver confirmed!', 'ride' => $ride]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Driverwallet;
use App\Models\Ride;
use Illuminate\Http\Request;

class RideController extends Controller
{
      public function index()
    {
        $rides = Ride::with(['user', 'driver'])->latest()->get();
        return response()->json($rides);
    }

  
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'pickup_location' => 'required|string',
            'drop_location' => 'required|string',
            'distance' => 'nullable|numeric',
            'price' => 'nullable|numeric',
            'payment_method' => 'nullable|string',
        ]);

        $ride = Ride::create($validated);

        return response()->json([
            'message' => 'Ride booked successfully!',
            'ride' => $ride,
        ]);
    }


     public function show($id)
    {
        $ride = Ride::with(['user', 'driver'])->find($id);

        if (!$ride) {
            return response()->json(['message' => 'Ride not found'], 404);
        }

        return response()->json($ride);
    }


    public function update(Request $request, $id)
    {
        $ride = Ride::find($id);

        if (!$ride) {
            return response()->json(['message' => 'Ride not found'], 404);
        }


  $DriverWallet= Driverwallet::find($request->driver_id);
  if($DriverWallet->amount <100){
            return response()->json(['message' => 'Recharge your Wallet'], 404);

  }



        $ride->update($request->only([
            'driver_id',
            'status',
            'price',
            'payment_status',
            'payment_method',
            'pickup_time',
            'drop_time',
        ]));

        return response()->json([
            'message' => 'Ride updated successfully',
            'ride' => $ride
        ]);
    }



      public function cancel($id)
    {
        $ride = Ride::find($id);

        if (!$ride) {
            return response()->json(['message' => 'Ride not found'], 404);
        }

        $ride->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Ride cancelled successfully']);
    }

     public function destroy($id)
    {
        $ride = Ride::find($id);

        if (!$ride) {
            return response()->json(['message' => 'Ride not found'], 404);
        }

        $ride->delete();

        return response()->json(['message' => 'Ride deleted successfully']);
    }





}

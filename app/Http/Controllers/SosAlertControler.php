<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SosAlert;

class SosAlertControler extends Controller
{
    public function triggerSOS(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|exists:rides,id',
            'location' => 'required|string'
        ]);

        $alert = SosAlert::create([
            'ride_id' => $request->ride_id,
            'user_id' => auth()->id(),
            'location' => $request->location,
            'status' => 'active'
        ]);

        // Notify admin or send SMS/Email here

        return response()->json([
            'message' => 'SOS alert triggered',
            'alert' => $alert
        ]);
    }
}

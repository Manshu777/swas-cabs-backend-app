<?php

   namespace App\Http\Controllers;

   use App\Models\SosAlert;
   use App\Models\Ride;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;
   use Illuminate\Support\Facades\Notification;
   use App\Notifications\SosAlertNotification;

   class SosAlertController extends Controller
   {
       public function store(Request $request)
       {
           $validated = $request->validate([
               'ride_id' => 'required|exists:rides,id',
               'location' => 'required|string',
               'latitude' => 'required|numeric',
               'longitude' => 'required|numeric',
           ]);

           $ride = Ride::findOrFail($validated['ride_id']);
           if ($ride->user_id !== Auth::id()) {
               return response()->json(['message' => 'Unauthorized'], 403);
           }

           $alert = SosAlert::create([
               'ride_id' => $ride->id,
               'user_id' => Auth::id(),
               'location' => $validated['location'],
               'latitude' => $validated['latitude'],
               'longitude' => $validated['longitude'],
               'status' => 'pending',
           ]);

           // Notify emergency contacts and admins
           $emergencyContacts = json_decode(Auth::user()->emergency_contacts, true) ?? [];
           Notification::send(User::where('role', 'admin')->get(), new SosAlertNotification($alert));
           // Add logic to notify emergency contacts via email/SMS

           return response()->json(['message' => 'SOS alert sent', 'alert' => $alert], 201);
       }
   }
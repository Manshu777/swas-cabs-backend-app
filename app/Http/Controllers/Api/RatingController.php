<?php

   namespace App\Http\Controllers;

   use App\Models\Rating;
   use App\Models\Ride;
   use Illuminate\Http\Request;
   use Illuminate\Support\Facades\Auth;

   class RatingController extends Controller
   {
       public function store(Request $request, Ride $ride)
       {
           if ($ride->status !== 'completed' || $ride->user_id !== Auth::id()) {
               return response()->json(['message' => 'Cannot rate this ride'], 403);
           }

           $validated = $request->validate([
               'rating' => 'required|integer|min:1|max:5',
               'review' => 'nullable|string|max:1000',
           ]);

           $rating = Rating::create([
               'ride_id' => $ride->id,
               'user_id' => Auth::id(),
               'driver_id' => $ride->driver_id,
               'rating' => $validated['rating'],
               'review' => $validated['review'],
           ]);

           return response()->json(['message' => 'Rating submitted', 'rating' => $rating], 201);
       }
   }
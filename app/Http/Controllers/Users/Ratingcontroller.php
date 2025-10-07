<?php

namespace App\Http\Controllers\Users;

use App\Models\Rating;
use App\Models\BookRide;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'ride_id' => 'required|exists:book_rides,id',
            'driver_id' => 'required|exists:reg_riders,id',
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string'
        ]);

        $rating = Rating::create([
            'ride_id' => $request->ride_id,
            'user_id' => auth()->id(),
            'driver_id' => $request->driver_id,
            'rating' => $request->rating,
            'review' => $request->review,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Rating submitted successfully',
            'rating' => $rating
        ]);
    }

    public function getDriverRatings($driverId)
    {
        $ratings = Rating::where('driver_id', $driverId)->get();
        return response()->json([
            'status' => true,
            'ratings' => $ratings
        ]);
    }
}

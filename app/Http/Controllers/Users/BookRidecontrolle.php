<?php

namespace App\Http\Controllers\Users;

use App\Events\FareEstimated;
use App\Events\RideCreated;
use App\Http\Controllers\Controller;
use App\Models\BookRide as Ride;  // Use BookRide model
use App\Models\DynamicRateSetting;  // Assume model for dynamic_rate_settings
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class BookRideControlle extends Controller
{
    public function estimateFare(Request $request)
    {
        $request->validate([
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'drop_latitude' => 'required|numeric',
            'drop_longitude' => 'required|numeric',
            'area_name' => 'nullable|string',  // Optional area
            'vehicle_type' => 'nullable|string',  // Optional vehicle type
        ]);

        // Calculate distance using Mapbox Directions API
        $origin = "{$request->pickup_longitude},{$request->pickup_latitude}";
        $destination = "{$request->drop_longitude},{$request->drop_latitude}";
        $response = Http::get("https://api.mapbox.com/directions/v5/mapbox/driving/{$origin};{$destination}", [
            'access_token' => config('services.mapbox.access_token'),
        ]);

        $data = $response->json();
        if (empty($data['routes'])) {
            return response()->json(['error' => 'Unable to calculate distance'], 400);
        }

        $distance = $data['routes'][0]['distance'] / 1000;  // km

        // Get dynamic rate based on time, area, vehicle
        $now = Carbon::now();
        $currentTime = $now->format('H:i:s');
        $isNight = $currentTime >= '22:00:00' || $currentTime < '06:00:00';

        $rateSetting = DynamicRateSetting::where('area_name', $request->area_name ?? 'All')
            ->where('vehicle_type', $request->vehicle_type ?? 'Any')
            ->where('is_active', true)
            ->first();

        if (!$rateSetting) {
            return response()->json(['error' => 'No rate settings found'], 404);
        }

        $ratePerKm = $isNight
            ? $rateSetting->default_rate_per_km_night
            : $rateSetting->default_rate_per_km_day;

        // Ensure within min/max
        $minRate = $isNight ? $rateSetting->min_rate_per_km_night : $rateSetting->min_rate_per_km_day;
        $maxRate = $isNight ? $rateSetting->max_rate_per_km_night : $rateSetting->max_rate_per_km_day;
        $ratePerKm = max($minRate, min($ratePerKm, $maxRate));

        $fare = $distance * $ratePerKm;

        // Broadcast fare estimation
        event(new FareEstimated(round($fare, 2), auth()->id()));

        return response()->json(['fare' => round($fare, 2)]);
    }

    public function createRide(Request $request)
    {
        $request->validate([
            'pickup_location' => 'required|string',
            'drop_location' => 'required|string',
            'pickup_latitude' => 'required|numeric',
            'pickup_longitude' => 'required|numeric',
            'drop_latitude' => 'required|numeric',
            'drop_longitude' => 'required|numeric',
            'scheduled_at' => 'nullable|date',
            'package_name' => 'nullable|string',
            'area_name' => 'nullable|string',
            'vehicle_type' => 'nullable|string',
            'payment_method' => 'required|in:razorpay,wallet,cash',  // NEW
            'razorpay_payment_id' => 'nullable|string',             // NEW
        ]);

        // Estimate fare
        $fareResponse = $this->estimateFare($request);
        if ($fareResponse->status() !== 200) {
            return $fareResponse;
        }
        $fare = $fareResponse->original['fare'];

        $ride = Ride::create([
            'user_id' => auth()->id(),
            'pickup_location' => $request->pickup_location,
            'drop_location' => $request->drop_location,
            'pickup_latitude' => $request->pickup_latitude,
            'pickup_longitude' => $request->pickup_longitude,
            'drop_latitude' => $request->drop_latitude,
            'drop_longitude' => $request->drop_longitude,
            'scheduled_at' => $request->scheduled_at,
            'package_name' => $request->package_name,
            'status' => 'pending',
            'fare' => $fare,
            'payment_method' => $request->payment_method,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'payment_status' => $request->payment_method === 'cash' ? 'pending' : 'paid',
        ]);

        event(new RideCreated($ride));

        return response()->json([
            'message' => 'Ride booked successfully',
            'ride' => $ride
        ], 201);
}
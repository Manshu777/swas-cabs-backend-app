<?php
use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('ride.{rideId}', function ($user, $rideId) {
    $ride = \App\Models\Ride::find($rideId);
    return (int) $user->id === (int) $ride->user_id || $user->role === 'driver';
});

Broadcast::channel('chat.{rideId}', function ($user, $rideId) {
    $ride = \App\Models\Ride::find($rideId);
    return $ride && ($user->id === $ride->user_id || $user->id === $ride->driver_id);
});
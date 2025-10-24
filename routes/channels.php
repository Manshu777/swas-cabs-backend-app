<?php

use Illuminate\Support\Facades\Broadcast;


Broadcast::channel('chat', function ($user) {
    return true; // ya authentication check
});

Broadcast::channel('ride.{rideId}', function ($user, $rideId) {
    return true; 
});

Broadcast::channel('rides', function ($user) {
    return $user->is_admin ?? false;
});

Broadcast::channel('fare.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});


Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

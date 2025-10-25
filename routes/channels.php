<?php
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('private-user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('private-driver.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id && $user->role === 'driver';
});

Broadcast::channel('admin.sos', function ($user) {
    return $user->role === 'admin';
});

Broadcast::channel('public-nearby.{geohash}', function ($user) {
    return $user->role === 'driver' && $user->is_available;
});
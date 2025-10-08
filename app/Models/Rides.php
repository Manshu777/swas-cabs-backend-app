<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', // Passenger
        'driver_id', // Driver
        'pickup_location',
        'pickup_latitude',
        'pickup_longitude',
        'dropoff_location',
        'dropoff_latitude',
        'dropoff_longitude',
        'fare',
        'status',
        'scheduled_at',
        'current_latitude',
        'current_longitude',
        'package_name',
    ];

    protected $casts = [
        'fare' => 'decimal:2',
        'scheduled_at' => 'datetime',
        'pickup_latitude' => 'decimal:8',
        'pickup_longitude' => 'decimal:8',
        'dropoff_latitude' => 'decimal:8',
        'dropoff_longitude' => 'decimal:8',
        'current_latitude' => 'decimal:8',
        'current_longitude' => 'decimal:8',
    ];

    public function passenger()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'ride_id');
    }

    public function sosAlerts()
    {
        return $this->hasMany(SosAlert::class, 'ride_id');
    }
}
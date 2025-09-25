<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rides extends Model
{
    protected $fillable = [
        'user_id', 'driver_id', 'pickup_location', 'pickup_latitude', 'pickup_longitude',
        'dropoff_location', 'dropoff_latitude', 'dropoff_longitude', 'fare', 'status',
        'scheduled_at', 'current_latitude', 'current_longitude'
    ];

    public function user()
    {
        return $this->belongsTo(RegUsers::class);
    }

    public function driver()
    {
        return $this->belongsTo(RegRiders::class);
    }
}

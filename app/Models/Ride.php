<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
     protected $fillable = [
        'user_id',
        'driver_id',
        'pickup_location',
        'drop_location',
        'distance',
        'price',
        'status',
        'pickup_time',
        'drop_time',
        'payment_status',
        'payment_method',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(RegUsers::class);
    }

    public function driver()
    {
        return $this->belongsTo(RegRiders::class);
    }

}

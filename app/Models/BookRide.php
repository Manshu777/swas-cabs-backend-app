<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookRide extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'driver_id',
        'pickup_location',
        'drop_location',
        'fare',
        'status',
        'scheduled_at',
        'package_name',
        'pickup_latitude',
        'pickup_longitude',
        'drop_latitude',
        'drop_longitude',
    ];

    protected $casts = [
        'fare' => 'decimal:2',
        'scheduled_at' => 'datetime',
        'pickup_latitude' => 'decimal:6',
        'pickup_longitude' => 'decimal:6',
        'drop_latitude' => 'decimal:6',
        'drop_longitude' => 'decimal:6',
    ];

    /**
     * Get the user who requested the ride.
     */
    public function user()
    {
        return $this->belongsTo(RegUsers::class);
    }

    /**
     * Get the driver assigned to the ride.
     */
    public function driver()
    {
        return $this->belongsTo(RegRiders::class, 'driver_id');
    }
}

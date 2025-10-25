<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Ride extends Model
{
    protected $fillable = [
        'user_id',
        'driver_id',
        'pickup_location',
        'pickup_latitude',
        'pickup_longitude',
        'dropoff_location',
        'dropoff_latitude',
        'dropoff_longitude',
        'current_latitude',
        'current_longitude',
        'distance',
        'price',
        'fare',
        'status',
        'pickup_time',
        'drop_time',
        'payment_status',
        'payment_method',
        'package_name',
        'scheduled_at',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    public function offers()
    {
        return $this->hasMany(RideOffer::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class RegRiders extends Authenticatable
{
    use HasApiTokens, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        "profile_image",
        'is_verified',
        'is_available',
        'latitude',
        'longitude'
    ];

    protected $hidden = ['password'];

    public function rides()
    {
        return $this->hasMany(Rides::class, 'driver_id');
    }

    public function vehicleDetails()
    {
        return $this->hasOne(VehicleDetails::class, 'driver_id');
    }

    public function documents()
    {
        return $this->hasOne(RiderDocuments::class, 'driver_id');
    }
}

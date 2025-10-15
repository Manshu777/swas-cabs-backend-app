<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'gender',
        'profile_image',
        'emergency_contacts',
        'language',
        'is_active',
        'role', // 'passenger' or 'driver'
          'adhar_number',
          'is_verified',
          'is_available',
          'latitude',
          'longitude',
        'kyc_status',
        'kyc_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Rides requested by the user (as a passenger)
    public function ridesAsPassenger()
    {
        return $this->hasMany(Ride::class, 'user_id');
    }

    // Rides driven by the user (as a driver)
    public function ridesAsDriver()
    {
        return $this->hasMany(Ride::class, 'user_id');
    }

    // Documents (for drivers only)
    public function documents()
    {
        return $this->hasMany(RiderDocument::class, 'user_id');
    }

    // Vehicles (for drivers only)
    public function vehicles()
    {
        return $this->hasMany(VehicleDetail::class, 'user_id');
    }

    // Ratings given by the user (as a passenger)
    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    // Ratings received by the user (as a driver)
    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    // SOS alerts created by the user
    public function sosAlerts()
    {
        return $this->hasMany(SosAlert::class, 'user_id');
    }

    // Check if the user is a driver
    public function isDriver()
    {
        return $this->role === 'driver';
    }

    // Check if the user is a passenger
    public function isPassenger()
    {
        return $this->role === 'passenger';
    }
}
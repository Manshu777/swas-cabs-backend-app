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
        'role', // 'passenger', 'driver', 'admin', 'super_admin'
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

    // Scopes for role-based queries
    public function scopeAdmins($query)
    {
        return $query->whereIn('role', ['admin', 'super_admin']);
    }

    public function scopeDrivers($query)
    {
        return $query->where('role', 'driver');
    }

    public function scopePassengers($query)
    {
        return $query->where('role', 'passenger');
    }

    // Existing relationships...
    public function ridesAsPassenger()
    {
        return $this->hasMany(Ride::class, 'user_id');
    }

    public function ridesAsDriver()
    {
        return $this->hasMany(Ride::class, 'user_id');
    }

    public function documents()
    {
        return $this->hasMany(RiderDocuments::class, 'user_id');
    }

    public function vehicles()
    {
        return $this->hasMany(VehicleDetails::class, 'user_id');
    }

    public function ratingsGiven()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    public function ratingsReceived()
    {
        return $this->hasMany(Rating::class, 'user_id');
    }

    public function sosAlerts()
    {
        return $this->hasMany(SosAlert::class, 'user_id');
    }

    public function isDriver()
    {
        return $this->role === 'driver';
    }

    public function isPassenger()
    {
        return $this->role === 'passenger';
    }

    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    public function isSuperAdmin()
    {
        return $this->role === 'super_admin';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class RegUsers extends Model
{
    use HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'gender',
        'profile_image',
        'emergency_contacts',
        'language',
        'is_active'
    ];

    protected $hidden = ['password',];

    public function rides()
    {
        return $this->hasMany(Rides::class);
    }
}

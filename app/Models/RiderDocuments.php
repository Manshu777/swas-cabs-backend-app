<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RiderDocuments extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'license_number',
        'license_image',
        'aadhaar_number',
        'aadhaar_front_image',
        'aadhaar_back_image',
        'vehicle_rc_number',
        'vehicle_rc_image',
        'insurance_number',
        'insurance_image',
        'police_verification_image',
        'status',
        'rejection_reason',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
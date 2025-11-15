<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driverdocument extends Model
{
       protected $fillable = [
        'driver_id',

        // License 
        'license_number',
        'license_image',
        'license_status',
        'license_rejection_reason',

        // Aadhaar
        'aadhaar_number',
        'aadhaar_front_image',
        'aadhaar_back_image',
        'aadhaar_status',
        'aadhaar_rejection_reason',

        // Vehicle RC
        'vehicle_rc_number',
        'vehicle_rc_image',
        'vehicle_rc_status',
        'vehicle_rc_rejection_reason',

        // Insurance
        'insurance_number',
        'insurance_image',
        'insurance_status',
        'insurance_rejection_reason',

        // Police verification
        'police_verification_image',
        'police_verification_status',
        'police_verification_rejection_reason',

        "document"
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleDetails extends Model
{
    protected $fillable = [
        'driver_id',
        'brand',
        'model',
        'license_plate',
        'vehicle_type',
        'year',
        'color',
    ];

    public function vehicleDetails()
    {
        return $this->belongsTo(Rides::class);
    }
}

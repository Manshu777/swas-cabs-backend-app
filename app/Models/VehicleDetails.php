<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'driver_id',
        'brand',
        'model',
        'license_plate',
        'vehicle_type',
        'year',
        'color',
    ];

    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }
}
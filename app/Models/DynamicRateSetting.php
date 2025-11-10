<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DynamicRateSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'area_name',
        'vehicle_type',
        'day_start_time',
        'night_start_time',
        'min_rate_per_km_day',
        'max_rate_per_km_day',
        'min_rate_per_km_night',
        'max_rate_per_km_night',
        'default_rate_per_km_day',
        'default_rate_per_km_night',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
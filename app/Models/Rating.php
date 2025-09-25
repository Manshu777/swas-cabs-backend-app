<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rating extends Model
{
    protected $fillable = ['ride_id', 'user_id', 'driver_id', 'rating', 'review'];
}

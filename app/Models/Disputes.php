<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Disputes extends Model
{
    protected $fillable = ['ride_id', 'user_id', 'driver_id', 'issue', 'status'];
}

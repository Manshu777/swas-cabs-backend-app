<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
   protected $fillable = [
        'type',
        'title',
        'description',
        'price',
        'image',
        'is_active',
        'status',
        'address',
        'latitude',
        'longitude',
        'contact_person',
        'phone',
        'email',
    ];
}

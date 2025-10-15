<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Driverwallet extends Model
{
    //
    protected $fillable=[
        "amount",
        "driver_id"
    ];


    public function driver(){
        return $this->belongsTo(RegRiders::class);
    }
}

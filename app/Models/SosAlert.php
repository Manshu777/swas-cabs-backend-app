<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SosAlert extends Model
{
    protected $table = 'sos_alerts';

    protected $fillable = [
        'ride_id',
        'user_id',
        'location',
        'status',
    ];

    /**
     * Relationships
     */

    public function ride()
    {
        return $this->belongsTo(BookRide::class);
    }

    public function user()
    {
        return $this->belongsTo(RegUsers::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'driver_id',
        'offered_fare',
        'status',
        'comment'
    ];

    protected $casts = [
        'offered_fare' => 'decimal:2',
    ];

    /**
     * The ride this bid belongs to.
     */
    public function ride()
    {
        return $this->belongsTo(Ride::class);
    }

    /**
     * The driver who made the bid.
     */
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // Helper scope to get only active bids
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SosAlert extends Model
{
    use HasFactory;

    protected $table = 'sos_alerts';

    protected $fillable = [
        'ride_id',
        'user_id',
        'location',
        'latitude',
        'longitude',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:10',
        'longitude' => 'decimal:10',
    ];

    public function ride()
    {
        return $this->belongsTo(Ride::class, 'ride_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
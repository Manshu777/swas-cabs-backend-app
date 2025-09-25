<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class RideLocationUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $rideId;
    public $latitude;
    public $longitude;

    public function __construct($rideId, $latitude, $longitude)
    {
        $this->rideId = $rideId;
        $this->latitude = $latitude;
        $this->longitude = $longitude;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('ride.' . $this->rideId);
    }

    public function broadcastAs()
    {
        return 'RideLocationUpdated';
    }
}

<?php 

namespace App\Events;

use App\Models\Ride;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RideRequested implements ShouldBroadcast
{
    use InteractsWithSockets;

    public $ride;

    public function __construct(Ride $ride)
    {
        $this->ride = $ride;
    }

    public function broadcastOn()
    {
        return ['rides-channel'];
    }

    public function broadcastAs()
    {
        return 'ride.requested';
    }
}

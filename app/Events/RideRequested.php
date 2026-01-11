<?php 
namespace App\Events;

use App\Models\Ride;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class RideRequested implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public Ride $ride) {}

    public function broadcastOn() {
        return new Channel('available-drivers');
    }

    public function broadcastAs() {
        return 'ride.requested';
    }
}
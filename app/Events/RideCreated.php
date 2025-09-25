<?php

namespace App\Events;

use App\Models\BookRide;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class RideCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $ride;

    public function __construct(BookRide $ride)
    {
        $this->ride = $ride;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('rides');
    }

    public function broadcastAs()
    {
        return 'RideCreated';
    }
}

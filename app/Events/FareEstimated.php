<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class FareEstimated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $fare;
    public $userId;

    public function __construct($fare, $userId)
    {
        $this->fare = $fare;
        $this->userId = $userId;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('fare.' . $this->userId);
    }

    public function broadcastAs()
    {
        return 'FareEstimated';
    }
}

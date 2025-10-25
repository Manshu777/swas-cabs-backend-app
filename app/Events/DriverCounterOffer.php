<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class DriverCounterOffer implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $requestId;
    public $driverId;
    public $driverPrice;
    public $etaSeconds;

    public function __construct($requestId, $driverId, $driverPrice, $etaSeconds)
    {
        $this->requestId = $requestId;
        $this->driverId = $driverId;
        $this->driverPrice = $driverPrice;
        $this->etaSeconds = $etaSeconds;
    }

    public function broadcastOn()
    {
        $userId = \App\Models\Ride::find($this->requestId)->user_id;
        return new Channel('private-user.' . $userId);
    }

    public function broadcastAs()
    {
        return 'driver.counter';
    }
}
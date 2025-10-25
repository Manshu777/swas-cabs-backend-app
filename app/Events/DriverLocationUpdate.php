<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class DriverLocationUpdate implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $driverId;
    public $lat;
    public $lng;
    public $bearing;
    public $rideId;

    public function __construct($driverId, $lat, $lng, $bearing, $rideId = null)
    {
        $this->driverId = $driverId;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->bearing = $bearing;
        $this->rideId = $rideId;
    }

    public function broadcastOn()
    {
        $channels = [new Channel('private-driver.' . $this->driverId)];
        if ($this->rideId) {
            $userId = \App\Models\Ride::find($this->rideId)->user_id;
            $channels[] = new Channel('private-user.' . $userId);
        }
        return $channels;
    }

    public function broadcastAs()
    {
        return 'driver.location';
    }
}
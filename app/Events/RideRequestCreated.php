<?php
namespace App\Events;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class RideRequestCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    public $ride;

    public function __construct($ride)
    {
        $this->ride = $ride;
    }

    public function broadcastOn()
    {
        return new Channel('public-nearby.' . $this->getGeoHash());
    }

    public function broadcastAs()
    {
        return 'ride.request';
    }

    private function getGeoHash()
    {
        $lat = $this->ride->pickup_latitude;
        $lng = $this->ride->pickup_longitude;
        return $this->encodeGeoHash($lat, $lng, 6);
    }

    private function encodeGeoHash($latitude, $longitude, $precision)
    {
        $chars = '0123456789bcdefghjkmnpqrstuvwxyz';
        $hash = '';
        $minLat = -90;
        $maxLat = 90;
        $minLng = -180;
        $maxLng = 180;

        for ($i = 0; $i < $precision; $i++) {
            $charIndex = 0;
            $midLat = ($minLat + $maxLat) / 2;
            $midLng = ($minLng + $maxLng) / 2;

            if ($latitude > $midLat) {
                $charIndex |= 16;
                $minLat = $midLat;
            } else {
                $maxLat = $midLat;
            }

            if ($longitude > $midLng) {
                $charIndex |= 8;
                $minLng = $midLng;
            } else {
                $maxLng = $midLng;
            }

            for ($j = 2; $j >= 0; $j--) {
                $midLat = ($minLat + $maxLat) / 2;
                $midLng = ($minLng + $maxLng) / 2;
                if ($latitude > $midLat) {
                    $charIndex |= (1 << (2 * $j + 1));
                    $minLat = $midLat;
                } else {
                    $maxLat = $midLat;
                }
                if ($longitude > $midLng) {
                    $charIndex |= (1 << (2 * $j));
                    $minLng = $midLng;
                } else {
                    $maxLng = $midLng;
                }
            }

            $hash .= $chars[$charIndex];
        }
        return $hash;
    }
}
<?php

namespace App\Events;

use App\Models\Bid;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class BidReceived implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public Bid $bid) {
        $this->bid->load('driver'); // Send driver details with the bid
    }

    public function broadcastOn() {
        return new PrivateChannel('ride.' . $this->bid->ride_id);
    }

    public function broadcastAs() {
        return 'bid.received';
    }
}
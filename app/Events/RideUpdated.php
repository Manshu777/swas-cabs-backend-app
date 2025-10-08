<?php

   namespace App\Events;

   use App\Models\Ride;
   use Illuminate\Broadcasting\Channel;
   use Illuminate\Broadcasting\InteractsWithSockets;
   use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
   use Illuminate\Foundation\Events\Dispatchable;

   class RideUpdated implements ShouldBroadcast
   {
       use Dispatchable, InteractsWithSockets;

       public $ride;

       public function __construct(Ride $ride)
       {
           $this->ride = $ride;
       }

       public function broadcastOn()
       {
           return new Channel('ride.' . $this->ride->id);
       }
   }
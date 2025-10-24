<?php

namespace App\Events;

use App\Models\Message; // ✅ model import
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message; // ✅ public property for model

    public function __construct(Message $message) // ✅ model type hint
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        return new PrivateChannel('chat');
    }
}
<?php
namespace App\Events;

use App\Models\ChatMessage;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;

    public function __construct(public ChatMessage $chatMessage) {
        $this->chatMessage->load('sender:id,name,profile_image');
    }

    public function broadcastOn() {
        return new PrivateChannel('chat.' . $this->chatMessage->ride_id);
    }

    public function broadcastAs() {
        return 'message.new';
    }
}
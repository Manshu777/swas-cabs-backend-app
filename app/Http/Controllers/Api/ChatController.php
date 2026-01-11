<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ride;
use App\Models\ChatMessage;
use App\Events\MessageSent;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function sendMessage(Request $request, $rideId)
    {
        $ride = Ride::findOrFail($rideId);

        // Security check
        if (auth()->id() !== $ride->user_id && auth()->id() !== $ride->driver_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $message = ChatMessage::create([
            'ride_id' => $rideId,
            'sender_id' => auth()->id(),
            'message' => $request->message
        ]);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    public function getHistory($rideId)
    {
        return ChatMessage::where('ride_id', $rideId)->with('sender')->get();
    }
}
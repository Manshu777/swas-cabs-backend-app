<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Events\MessageSent;


class ChatController extends Controller
{

    public function index() {
    $messages = Message::with('user')->get();
    return view('chat', compact('messages'));
}

    public function fetchMessages() {
    return Message::with('user')->get(); // fetch all messages with user info
}

public function sendMessage(Request $request) {
    $message = Message::create([
        'username' => $request->username ?? 'Guest',
        'message' => $request->message,
    ]);

    broadcast(new MessageSent($message))->toOthers();

    return response()->json($message, 201);
}
}

<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;

use Auth;

class ChatController extends Controller
{
    public function list()
    {
        $chats = Chat::where('sender_id', Auth::id())
            ->orWhere('receiver_id', Auth::id())
            ->latest()
            ->get();

        return response()->json([
            'status' => true,
            'data' => $chats,
        ]);
    }

    public function send(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string',
        ]);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Message sent successfully.',
            'data' => $chat,
        ]);
    }
}

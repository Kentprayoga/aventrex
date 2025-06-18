<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Message; // Assuming you have a Message model
use App\Models\Profile;
use App\Models\User; // Assuming you have a User model

class MessageController extends Controller
{
    public function getUserProfile(Request $request)
{
    $user = $request->user(); // User yang sedang login

    $profile = Profile::where('user_id', $user->id)->first();

    if (!$profile) {
        return response()->json(['error' => 'Profile not found'], 404);
    }

    return response()->json([
        'nip' => $profile->nip,
        'name' => $profile->name,
    ], 200);
}
        public function getMessagesWithUser($userId)
    {
        $userIdLogin = Auth::id();

        $messages = Message::where(function ($query) use ($userIdLogin, $userId) {
                $query->where('sender_id', $userIdLogin)
                      ->where('receiver_id', $userId);
            })->orWhere(function ($query) use ($userIdLogin, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $userIdLogin);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json($messages, 200);
    }

    // Simpan pesan baru
    public function store(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => $message,
        ], 201);
    }

}
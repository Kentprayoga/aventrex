<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{

public function index(Request $request)
{
    $adminId = Auth::id();

    if ($request->isMethod('post')) {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $selectedUser = User::findOrFail($request->user_id);

        Message::create([
            'sender_id' => $adminId,
            'receiver_id' => $selectedUser->id,
            'message' => $request->message,
        ]);
        return redirect()->route('admin.chat', ['user_id' => $selectedUser->id])->with('success', 'Pesan berhasil dikirim.');
    }

    // Cari semua user yang chat dengan admin
    $userIds = Message::where('receiver_id', $adminId)
        ->orWhere('sender_id', $adminId)
        ->pluck('sender_id')
        ->merge(
            Message::where('receiver_id', $adminId)->pluck('receiver_id')
        )
        ->unique()
        ->reject(fn($id) => $id == $adminId);

    // Eager load profile supaya bisa tampil nama
$usersQuery = User::with('profile')->whereIn('id', $userIds);

// Filter berdasarkan input pencarian nama
if ($request->filled('search')) {
    $search = $request->input('search');
    $usersQuery->whereHas('profile', function ($query) use ($search) {
        $query->where('name', 'like', '%' . $search . '%');
    });
}

$users = $usersQuery->get();

    $selectedUser = null;
    $messages = collect();

    if ($request->has('user_id')) {
        $selectedUser = User::with('profile')->findOrFail($request->user_id);

        $messages = Message::where(function ($q) use ($adminId, $selectedUser) {
            $q->where('sender_id', $adminId)->where('receiver_id', $selectedUser->id);
        })->orWhere(function ($q) use ($adminId, $selectedUser) {
            $q->where('sender_id', $selectedUser->id)->where('receiver_id', $adminId);
        })->orderBy('created_at')->get();
    }

    return view('chat.index', compact('users', 'selectedUser', 'messages'));
}

public function deleteMessage($id)
{
    $message = Message::findOrFail($id);

    $userId = ($message->sender_id == Auth::id()) ? $message->receiver_id : $message->sender_id;

    $message->delete();

    return redirect()->route('admin.chat', ['user_id' => $userId])
                     ->with('success', 'Pesan berhasil dihapus!');
}


    // Fungsi untuk menghapus semua pesan antara admin dan user tertentu
    public function clearMessages(Request $request, $userId)
    {
        $adminId = Auth::id();

        Message::where(function ($query) use ($adminId, $userId) {
            $query->where('sender_id', $adminId)
                ->where('receiver_id', $userId);
        })
        ->orWhere(function ($query) use ($adminId, $userId) {
            $query->where('sender_id', $userId)
                ->where('receiver_id', $adminId);
        })
        ->delete();

        return redirect()->route('admin.chat', ['user_id' => $userId])
                        ->with('success', 'Semua pesan berhasil dihapus!');
    }





}
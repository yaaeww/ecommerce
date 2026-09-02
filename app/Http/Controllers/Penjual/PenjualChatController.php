<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use App\Events\NewChatMessage;

class PenjualChatController extends Controller
{
    /**
     * 📜 Menampilkan daftar pembeli yang pernah chat
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $chats = Chat::where('sender_id', $userId)
            ->orWhere('receiver_id', $userId)
            ->orderBy('created_at', 'asc')
            ->get();

        $customers = User::whereIn('id', $chats->pluck('sender_id')
            ->merge($chats->pluck('receiver_id'))
            ->unique())
            ->where('id', '!=', $userId)
            ->get();

        return view('penjual.chat.index', compact('customers'));
    }

    /**
     * 🕓 Ambil riwayat chat dengan pembeli
     */
    public function history($receiverId)
    {
        $userId = Auth::id();

        // Tandai pesan dari pembeli ini sebagai sudah dibaca
        Chat::where('sender_id', $receiverId)
            ->where('receiver_id', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $chats = Chat::where(function ($q) use ($userId, $receiverId) {
            $q->where('sender_id', $userId)
                ->where('receiver_id', $receiverId);
        })->orWhere(function ($q) use ($userId, $receiverId) {
            $q->where('sender_id', $receiverId)
                ->where('receiver_id', $userId);
        })->orderBy('created_at', 'asc')->get();

        $totalUnread = Chat::where('receiver_id', $userId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'chats' => $chats,
            'total_unread' => $totalUnread,
        ]);
    }

    /**
     * 💬 Kirim pesan ke pembeli
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $chat = Chat::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
            'is_ai' => 0,
        ]);

        // 🔔 Broadcast real-time (Pusher + Echo)
        event(new NewChatMessage($chat));

        return response()->json(['success' => true]);
    }
}

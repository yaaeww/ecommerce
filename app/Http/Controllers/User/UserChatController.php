<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Chat;
use App\Models\User;
use App\Models\Umkm;
use Illuminate\Support\Facades\Auth;
use App\Services\GeminiService;
use App\Events\NewChatMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

class UserChatController extends Controller
{
    /**
     * 🏠 Halaman utama chat (AI & Penjual)
     */
    public function index(Request $request, $id = null)
    {
        $authId = Auth::id();
        $id = $id ?? $request->query('seller_id') ?? $request->query('user_id');

        // 🔹 Ambil semua penjual (yang punya UMKM)
        $penjualIds = Umkm::pluck('user_id')->toArray();
        $penjuals = User::whereIn('id', $penjualIds)
            ->where('id', '!=', $authId)
            ->get();

        // 🔹 Hitung unread chat per penjual
        foreach ($penjuals as $penjual) {
            $penjual->unread_count = Chat::where('sender_id', $penjual->id)
                ->where('receiver_id', $authId)
                ->where('is_read', false)
                ->count();
        }

        // 🔹 Jika ada activeUserId, tandai pesan dari user tersebut sebagai sudah dibaca
        if ($id && $id != 0) {
            Chat::where('sender_id', $id)
                ->where('receiver_id', $authId)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        } elseif ($id === 0) {
            Chat::where('receiver_id', $authId)
                ->where('is_ai', true)
                ->where('is_read', false)
                ->update(['is_read' => true]);
        }

        // 🔹 Tambahkan AI Asisten
        $ai = (object) [
            'id' => 0,
            'name' => 'AI Asisten 🤖',
            'email' => 'ai@chat.local',
        ];

        return view('pembeli.chat.index', [
            'users' => $penjuals,
            'ai' => $ai,
            'activeUserId' => $id,
        ]);
    }

    /**
     * 💬 Kirim pesan ke AI atau penjual
     */
    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
            'receiver_id' => 'nullable|integer',
        ]);

        $sender = Auth::user();
        $receiverId = $request->input('receiver_id');
        $message = $request->input('message');

        // 🧠 Jika ke AI
        if ($receiverId == 0 || $receiverId === null) {
            return $this->handleAiChat($sender, $message);
        }

        // 👥 Jika ke penjual
        return $this->handleUserChat($sender, $receiverId, $message);
    }

    /**
     * 🧠 Chat ke AI (Asisten Virtual)
     */
    private function handleAiChat($sender, $message)
    {
        try {
            $chatUser = Chat::create([
                'sender_id' => $sender->id,
                'receiver_id' => null,
                'message' => $message, // Otomatis dienkripsi oleh mutator
                'is_ai' => false,
                'is_read' => true,
            ]);

            $prompt = "Kamu adalah AI Asisten UMKM Indramayu.\n\nUser: {$message}";
            $aiReply = '⚠️ Maaf, saya tidak dapat memproses pesan ini.';
            try {
                $aiReply = GeminiService::askOnce($prompt)
                    ?? '⚠️ Terjadi kesalahan saat memproses permintaan.';
            } catch (\Throwable $err) {
                Log::error("Gemini Error: " . $err->getMessage());
            }

            $chatAI = Chat::create([
                'sender_id' => $sender->id,
                'receiver_id' => $sender->id,
                'message' => $aiReply, // Otomatis dienkripsi oleh mutator
                'is_ai' => true,
                'is_read' => true,
            ]);

            broadcast(new NewChatMessage($chatUser))->toOthers();
            broadcast(new NewChatMessage($chatAI))->toOthers();

            return response()->json([
                'status' => 'ok',
                'user_message' => $chatUser,
                'ai_reply' => $chatAI,
            ]);
        } catch (\Throwable $e) {
            Log::error("AI Chat Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal kirim ke AI.'], 500);
        }
    }

    /**
     * 👥 Chat antar user (pembeli ↔ penjual)
     */
    private function handleUserChat($sender, $receiverId, $message)
    {
        $receiver = User::find($receiverId);
        if (!$receiver) {
            return response()->json(['status' => 'error', 'message' => 'Penjual tidak ditemukan.'], 404);
        }

        try {
            $umkm = Umkm::where('user_id', $receiverId)->first();

            $chat = Chat::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiverId,
                'umkm_id' => $umkm ? $umkm->id : null,
                'message' => $message, // Otomatis dienkripsi oleh mutator
                'is_ai' => false,
                'is_read' => false,
            ]);

            // Broadcast realtime ke lawan chat
            event(new NewChatMessage($chat));

            return response()->json([
                'status' => 'sent',
                'chat' => $chat->load('sender', 'receiver'),
            ]);
        } catch (\Throwable $e) {
            Log::error("User Chat Error: " . $e->getMessage());
            return response()->json(['status' => 'error', 'message' => 'Gagal mengirim pesan.'], 500);
        }
    }

    /**
     * 🕘 Ambil riwayat chat & tandai sudah dibaca
     */
    public function history($userId = null)
    {
        $authId = Auth::id();

        if ($userId == 0 || $userId === null) {
            // Chat AI: tandai pesan AI sebagai terbaca
            Chat::where('receiver_id', $authId)
                ->where('is_ai', true)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $query = Chat::with(['sender', 'receiver'])
                ->where(function ($q) use ($authId) {
                    $q->where('sender_id', $authId)
                        ->whereNull('receiver_id')
                        ->orWhere(function ($subq) use ($authId) {
                            $subq->where('receiver_id', $authId)->where('is_ai', true);
                        });
                })
                ->orderBy('created_at', 'asc');
        } else {
            // Chat pembeli ↔ penjual: tandai pesan dari penjual ini sebagai terbaca
            Chat::where('sender_id', $userId)
                ->where('receiver_id', $authId)
                ->where('is_read', false)
                ->update(['is_read' => true]);

            $query = Chat::with(['sender', 'receiver'])
                ->where(function ($q) use ($authId, $userId) {
                    $q->where('sender_id', $authId)->where('receiver_id', $userId);
                })
                ->orWhere(function ($q) use ($authId, $userId) {
                    $q->where('sender_id', $userId)->where('receiver_id', $authId);
                })
                ->orderBy('created_at', 'asc');
        }

        $chats = $query->get();

        // Hitung total sisa pesan yang belum dibaca untuk user ini
        $totalUnread = Chat::where('receiver_id', $authId)
            ->where('is_read', false)
            ->count();

        return response()->json([
            'status' => 'ok', 
            'chats' => $chats,
            'total_unread' => $totalUnread
        ]);
    }

    /**
     * 🧹 Hapus riwayat chat
     */
    public function clear($userId = null)
    {
        $authId = Auth::id();

        if ($userId == 0 || $userId === null) {
            Chat::where(function ($q) use ($authId) {
                $q->where('sender_id', $authId)
                    ->whereNull('receiver_id')
                    ->orWhere(function ($subq) use ($authId) {
                        $subq->where('receiver_id', $authId)->where('is_ai', true);
                    });
            })->delete();
        } else {
            Chat::where(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $authId)->where('receiver_id', $userId);
            })->orWhere(function ($q) use ($authId, $userId) {
                $q->where('sender_id', $userId)->where('receiver_id', $authId);
            })->delete();
        }

        return response()->json(['message' => 'Chat berhasil dihapus.']);
    }

    /**
     * 👥 Daftar penjual aktif
     */
    public function listUsers()
    {
        $authId = Auth::id();
        $penjualIds = Umkm::pluck('user_id')->toArray();

        $users = User::whereIn('id', $penjualIds)
            ->where('id', '!=', $authId)
            ->get(['id', 'name', 'email']);

        return response()->json(['status' => 'ok', 'users' => $users]);
    }

    /**
     * 🔓 Method untuk decrypt manual (jika diperlukan)
     */
    public function decryptMessage($encryptedMessage)
    {
        try {
            return Crypt::decryptString($encryptedMessage);
        } catch (\Exception $e) {
            Log::error("Decryption error: " . $e->getMessage());
            return "Pesan tidak dapat dibaca";
        }
    }
}
<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use App\Models\Umkm;
use App\Services\LocalAiBotService;
use App\Events\NewChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ChatApiController extends Controller
{
    /**
     * Get chat contacts list
     */
    public function getContacts(Request $request)
    {
        try {
            $user = $request->user();
            $authId = $user->id;

            if ($user->role === 'penjual') {
                // Get all buyers who have sent messages to this seller or received from seller
                $chatUserIds = Chat::where('sender_id', $authId)
                    ->orWhere('receiver_id', $authId)
                    ->pluck('sender_id')
                    ->merge(Chat::where('sender_id', $authId)->orWhere('receiver_id', $authId)->pluck('receiver_id'))
                    ->unique()
                    ->reject(fn($id) => $id == $authId || is_null($id))
                    ->values();

                $contacts = User::whereIn('id', $chatUserIds)->get();

                foreach ($contacts as $contact) {
                    $contact->unread_count = Chat::where('sender_id', $contact->id)
                        ->where('receiver_id', $authId)
                        ->where('is_read', false)
                        ->count();
                    
                    $lastMessage = Chat::where(function($q) use ($authId, $contact) {
                        $q->where('sender_id', $authId)->where('receiver_id', $contact->id);
                    })->orWhere(function($q) use ($authId, $contact) {
                        $q->where('sender_id', $contact->id)->where('receiver_id', $authId);
                    })->latest()->first();

                    $contact->last_message = $lastMessage ? $lastMessage->message : null;
                    $contact->last_message_time = $lastMessage ? $lastMessage->created_at : null;
                }

                return response()->json([
                    'success' => true,
                    'data' => [
                        'ai' => null,
                        'contacts' => $contacts
                    ]
                ]);
            } else {
                // Buyer contacts: All sellers + AI Assistant
                $penjualIds = Umkm::pluck('user_id')->toArray();
                $penjuals = User::with('umkm')->whereIn('id', $penjualIds)
                    ->where('id', '!=', $authId)
                    ->get();

                foreach ($penjuals as $penjual) {
                    $penjual->unread_count = Chat::where('sender_id', $penjual->id)
                        ->where('receiver_id', $authId)
                        ->where('is_read', false)
                        ->count();

                    $lastMessage = Chat::where(function($q) use ($authId, $penjual) {
                        $q->where('sender_id', $authId)->where('receiver_id', $penjual->id);
                    })->orWhere(function($q) use ($authId, $penjual) {
                        $q->where('sender_id', $penjual->id)->where('receiver_id', $authId);
                    })->latest()->first();

                    $penjual->last_message = $lastMessage ? $lastMessage->message : null;
                    $penjual->last_message_time = $lastMessage ? $lastMessage->created_at : null;
                }

                $aiLastMessage = Chat::where('receiver_id', $authId)->where('is_ai', true)->latest()->first();

                $ai = [
                    'id' => 0,
                    'name' => 'AI Asisten Juragan Pelem 🤖',
                    'email' => 'ai@chat.local',
                    'last_message' => $aiLastMessage ? $aiLastMessage->message : 'Halo! Ada yang bisa saya bantu terkait produk buah mangga segar?',
                    'last_message_time' => $aiLastMessage ? $aiLastMessage->created_at : null,
                ];

                return response()->json([
                    'success' => true,
                    'data' => [
                        'ai' => $ai,
                        'contacts' => $penjuals
                    ]
                ]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil daftar kontak: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get chat history with specified user or AI
     */
    public function getHistory(Request $request, $userId)
    {
        try {
            $user = $request->user();
            $authId = $user->id;

            if ($userId == 0 || $userId === 'ai') {
                // Mark AI messages read
                Chat::where('receiver_id', $authId)
                    ->where('is_ai', true)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $chats = Chat::with(['sender'])
                    ->where(function ($q) use ($authId) {
                        $q->where('sender_id', $authId)
                            ->whereNull('receiver_id')
                            ->orWhere(function ($subq) use ($authId) {
                                $subq->where('receiver_id', $authId)->where('is_ai', true);
                            });
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                // Mark messages from this user read
                Chat::where('sender_id', $userId)
                    ->where('receiver_id', $authId)
                    ->where('is_read', false)
                    ->update(['is_read' => true]);

                $chats = Chat::with(['sender', 'receiver'])
                    ->where(function ($q) use ($authId, $userId) {
                        $q->where('sender_id', $authId)->where('receiver_id', $userId);
                    })
                    ->orWhere(function ($q) use ($authId, $userId) {
                        $q->where('sender_id', $userId)->where('receiver_id', $authId);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $chats
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil riwayat chat: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Send chat message
     */
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string|max:1000',
            'receiver_id' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $sender = $request->user();
            $receiverId = $request->input('receiver_id');
            $message = $request->input('message');

            // If to AI
            if ($receiverId == 0 || $receiverId === null) {
                $chatUser = Chat::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => null,
                    'message' => $message,
                    'is_ai' => false,
                    'is_read' => true,
                ]);

                $aiReply = LocalAiBotService::reply($sender, $message);

                $chatAI = Chat::create([
                    'sender_id' => $sender->id,
                    'receiver_id' => $sender->id,
                    'message' => $aiReply,
                    'is_ai' => true,
                    'is_read' => true,
                ]);

                return response()->json([
                    'success' => true,
                    'data' => [
                        'user_message' => $chatUser,
                        'ai_reply' => $chatAI,
                    ]
                ]);
            }

            // If to user (buyer <-> seller)
            $receiver = User::find($receiverId);
            if (!$receiver) {
                return response()->json(['success' => false, 'message' => 'Penerima tidak ditemukan'], 404);
            }

            $umkm = Umkm::where('user_id', $receiverId)->first();

            $chat = Chat::create([
                'sender_id' => $sender->id,
                'receiver_id' => $receiverId,
                'umkm_id' => $umkm ? $umkm->id : null,
                'message' => $message,
                'is_ai' => false,
                'is_read' => false,
            ]);

            return response()->json([
                'success' => true,
                'data' => $chat->load('sender', 'receiver')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengirim pesan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Clear chat
     */
    public function clearChat(Request $request, $userId)
    {
        try {
            $authId = $request->user()->id;

            if ($userId == 0 || $userId === 'ai') {
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

            return response()->json([
                'success' => true,
                'message' => 'Riwayat chat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus chat: ' . $e->getMessage()], 500);
        }
    }
}

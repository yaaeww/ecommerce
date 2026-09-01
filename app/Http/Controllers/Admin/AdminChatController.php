<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminChatController extends Controller
{
    /**
     * Kata kunci berisiko tinggi transaksi di luar platform (Anti-Fraud / Anti-Bypass).
     */
    protected $flaggedKeywords = [
        'wa', 'whatsapp', 'transfer', 'rekening', 'bca', 'bri', 'mandiri', 'bni',
        '081', '082', '083', '085', '087', '088', '089', 'luar aplikasi',
        'direct', 'tf langsung', 'rek', 'hubungi no', 'japri'
    ];

    /**
     * Tampilkan daftar seluruh room percakapan aktif & deteksi anti-fraud.
     */
    public function index(Request $request)
    {
        $filterRisk = $request->get('risk_only', false);
        $search = $request->get('search');

        // Ambil pasangan user yang pernah bertukar pesan
        $chatPairs = Chat::select(
                DB::raw('LEAST(sender_id, receiver_id) as user_a'),
                DB::raw('GREATEST(sender_id, receiver_id) as user_b'),
                DB::raw('MAX(created_at) as last_chat_at'),
                DB::raw('COUNT(id) as total_messages')
            )
            ->groupBy('user_a', 'user_b')
            ->orderByDesc('last_chat_at')
            ->get();

        $conversations = [];
        $totalFlaggedConversations = 0;

        foreach ($chatPairs as $pair) {
            $userA = User::with('umkm')->find($pair->user_a);
            $userB = User::with('umkm')->find($pair->user_b);

            if (!$userA || !$userB) continue;

            // Cari pesan terakhir
            $lastMessage = Chat::where(function ($q) use ($pair) {
                    $q->where('sender_id', $pair->user_a)->where('receiver_id', $pair->user_b);
                })
                ->orWhere(function ($q) use ($pair) {
                    $q->where('sender_id', $pair->user_b)->where('receiver_id', $pair->user_a);
                })
                ->latest()
                ->first();

            // Hitung pesan yang memuat kata kunci berisiko
            $flaggedCount = 0;
            $allMessages = Chat::where(function ($q) use ($pair) {
                    $q->where('sender_id', $pair->user_a)->where('receiver_id', $pair->user_b);
                })
                ->orWhere(function ($q) use ($pair) {
                    $q->where('sender_id', $pair->user_b)->where('receiver_id', $pair->user_a);
                })
                ->get();

            foreach ($allMessages as $msg) {
                $lowered = strtolower($msg->message);
                foreach ($this->flaggedKeywords as $kw) {
                    if (str_contains($lowered, $kw)) {
                        $flaggedCount++;
                        break;
                    }
                }
            }

            if ($flaggedCount > 0) {
                $totalFlaggedConversations++;
            }

            // Filter risk only
            if ($filterRisk && $flaggedCount === 0) {
                continue;
            }

            // Tentukan peran
            $seller = $userA->role === 'penjual' ? $userA : ($userB->role === 'penjual' ? $userB : $userA);
            $buyer = $userA->role === 'pembeli' ? $userA : ($userB->role === 'pembeli' ? $userB : $userB);

            // Filter search
            if ($search) {
                $s = strtolower($search);
                $matchesSeller = str_contains(strtolower($seller->name), $s) || ($seller->umkm && str_contains(strtolower($seller->umkm->nama_toko), $s));
                $matchesBuyer = str_contains(strtolower($buyer->name), $s) || str_contains(strtolower($buyer->email), $s);
                $matchesMsg = $lastMessage && str_contains(strtolower($lastMessage->message), $s);

                if (!$matchesSeller && !$matchesBuyer && !$matchesMsg) {
                    continue;
                }
            }

            $conversations[] = (object) [
                'user_a' => $userA,
                'user_b' => $userB,
                'seller' => $seller,
                'buyer' => $buyer,
                'last_message' => $lastMessage,
                'total_messages' => $pair->total_messages,
                'flagged_count' => $flaggedCount,
                'has_risk' => $flaggedCount > 0,
            ];
        }

        $totalRoom = count($chatPairs);
        $totalAllChat = Chat::count();

        return view('admin.chat.index', compact(
            'conversations',
            'totalRoom',
            'totalAllChat',
            'totalFlaggedConversations',
            'filterRisk',
            'search'
        ));
    }

    /**
     * Ambil transkrip percakapan lengkap antar 2 user (JSON untuk modal / transcript reader).
     */
    public function show($userAId, $userBId)
    {
        $userA = User::with('umkm')->findOrFail($userAId);
        $userB = User::with('umkm')->findOrFail($userBId);

        $messages = Chat::where(function ($q) use ($userAId, $userBId) {
                $q->where('sender_id', $userAId)->where('receiver_id', $userBId);
            })
            ->orWhere(function ($q) use ($userAId, $userBId) {
                $q->where('sender_id', $userBId)->where('receiver_id', $userAId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $formatted = $messages->map(function ($msg) {
            $isFlagged = false;
            $lowered = strtolower($msg->message);
            $matchedKeywords = [];

            foreach ($this->flaggedKeywords as $kw) {
                if (str_contains($lowered, $kw)) {
                    $isFlagged = true;
                    $matchedKeywords[] = $kw;
                }
            }

            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'receiver_id' => $msg->receiver_id,
                'message' => $msg->message,
                'is_read' => $msg->is_read,
                'created_at' => $msg->created_at->translatedFormat('d M Y, H:i'),
                'is_flagged' => $isFlagged,
                'matched_keywords' => $matchedKeywords,
            ];
        });

        // Catat di log audit jika admin menginspeksi transkrip
        ActivityLog::record(
            'INSPECT_CHAT',
            "Superadmin menginspeksi transkrip percakapan antara {$userA->name} dan {$userB->name}."
        );

        return response()->json([
            'success' => true,
            'user_a' => [
                'id' => $userA->id,
                'name' => $userA->name,
                'role' => $userA->role,
                'toko' => $userA->umkm->nama_toko ?? null,
            ],
            'user_b' => [
                'id' => $userB->id,
                'name' => $userB->name,
                'role' => $userB->role,
                'toko' => $userB->umkm->nama_toko ?? null,
            ],
            'messages' => $formatted,
        ]);
    }
}

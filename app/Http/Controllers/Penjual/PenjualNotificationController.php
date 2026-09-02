<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Produk;
use App\Models\Umkm;
use App\Models\Ulasan;
use App\Models\PenarikanSaldo;
use App\Models\Chat;
use App\Models\Message;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenjualNotificationController extends Controller
{
    /**
     * Mengambil daftar notifikasi terisolasi khusus untuk akun Penjual yang sedang login.
     */
    public static function getSellerNotifications()
    {
        $notifications = collect();
        $user = Auth::user();
        if (!$user) return $notifications;

        $lastReadTimestamp = session('penjual_notif_read_timestamp_' . $user->id, 0);
        $umkm = Umkm::where('user_id', $user->id)->first();

        if (!$umkm) {
            // Notifikasi Toko Belum Didaftarkan
            $notifications->push([
                'id' => 'umkm_empty',
                'type' => 'umkm_empty',
                'category' => 'action_required',
                'title' => 'Daftarkan Toko Anda',
                'description' => 'Toko belum didaftarkan. Daftarkan toko untuk mulai berjualan.',
                'time' => 'Sekarang',
                'timestamp' => time(),
                'is_unread' => true,
                'url' => route('penjual.umkm.create'),
                'icon' => 'fas fa-store-slash',
                'badge_bg' => 'bg-amber-500',
                'badge_text' => 'text-amber-500',
                'bg_light' => 'bg-amber-50',
                'border' => 'border-amber-200',
                'is_critical' => true,
            ]);
            return $notifications;
        }

        $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');

        // 1. Pesanan Baru Lunas yang Menunggu Diproses / Dikemas
        try {
            $pendingOrders = Order::with(['user', 'produk'])
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->where(function ($q) {
                    $q->whereNull('status_pesanan')
                      ->orWhere('status_pesanan', 'menunggu_diproses')
                      ->orWhere('status_pesanan', 'dikemas');
                })
                ->latest()
                ->take(5)
                ->get();

            $now = Carbon::now();
            foreach ($pendingOrders as $order) {
                $isOverdue = $order->created_at && $order->created_at->diffInHours($now) >= 24;
                $ts = $order->created_at ? $order->created_at->timestamp : time();

                $notifications->push([
                    'id' => 'order_' . $order->id,
                    'type' => $isOverdue ? 'sla_overdue' : 'new_order',
                    'category' => $isOverdue ? 'action_required' : 'order',
                    'title' => $isOverdue ? '⚠️ SLA Keterlambatan Pengiriman' : '🛒 Pesanan Baru Masuk',
                    'description' => "Pesanan #ORD-" . str_pad($order->id, 5, '0', STR_PAD_LEFT) . " dari " . ($order->name ?: 'Pembeli') . " (" . ($order->produk->nama ?? 'Produk') . " - {$order->jumlah}x)",
                    'time' => $order->created_at ? $order->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('penjual.pesanan.create', $order->id),
                    'icon' => $isOverdue ? 'fas fa-truck-ramp-box' : 'fas fa-box-open',
                    'badge_bg' => $isOverdue ? 'bg-rose-500' : 'bg-emerald-500',
                    'badge_text' => $isOverdue ? 'text-rose-500' : 'text-emerald-500',
                    'bg_light' => $isOverdue ? 'bg-rose-50' : 'bg-emerald-50',
                    'border' => $isOverdue ? 'border-rose-200' : 'border-emerald-200',
                    'is_critical' => $isOverdue,
                ]);
            }
        } catch (\Throwable $e) {}

        // 2. Status Penarikan Saldo (Payout) Toko
        try {
            $payouts = PenarikanSaldo::where('umkm_id', $umkm->id)
                ->latest()
                ->take(3)
                ->get();

            foreach ($payouts as $payout) {
                $ts = $payout->updated_at ? $payout->updated_at->timestamp : time();
                $isApproved = $payout->status === 'approved';
                $isPending = $payout->status === 'pending';

                $notifications->push([
                    'id' => 'payout_' . $payout->id,
                    'type' => 'payout',
                    'category' => 'finance',
                    'title' => $isApproved ? '💰 Pencairan Saldo Berhasil' : ($isPending ? '⏳ Pencairan Saldo Diproses' : '❌ Pencairan Saldo Ditolak'),
                    'description' => "Penarikan Rp " . number_format($payout->jumlah, 0, ',', '.') . " ke " . $payout->bank_tujuan . " (" . ucfirst($payout->status) . ")",
                    'time' => $payout->updated_at ? $payout->updated_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('penjual.penarikan.index'),
                    'icon' => 'fas fa-wallet',
                    'badge_bg' => $isApproved ? 'bg-emerald-500' : ($isPending ? 'bg-amber-500' : 'bg-rose-500'),
                    'badge_text' => $isApproved ? 'text-emerald-500' : ($isPending ? 'text-amber-500' : 'text-rose-500'),
                    'bg_light' => $isApproved ? 'bg-emerald-50' : ($isPending ? 'bg-amber-50' : 'bg-rose-50'),
                    'border' => $isApproved ? 'border-emerald-200' : ($isPending ? 'border-amber-200' : 'border-rose-200'),
                    'is_critical' => false,
                ]);
            }
        } catch (\Throwable $e) {}

        // 3. Ulasan Baru dari Pembeli
        try {
            $recentUlasans = Ulasan::with(['user', 'produk'])
                ->whereIn('produks_id', $produkIds)
                ->where(function ($q) {
                    $q->whereNull('status_moderasi')
                      ->orWhere('status_moderasi', '!=', 'hidden');
                })
                ->latest()
                ->take(3)
                ->get();

            foreach ($recentUlasans as $review) {
                $ts = $review->created_at ? $review->created_at->timestamp : time();
                $isLowRating = $review->bintang <= 2;

                $notifications->push([
                    'id' => 'review_' . $review->id,
                    'type' => 'review',
                    'category' => $isLowRating ? 'action_required' : 'review',
                    'title' => "⭐ Ulasan Baru: " . $review->bintang . " Bintang",
                    'description' => "Pembeli " . ($review->user->name ?? 'Pelanggan') . " mengulas " . ($review->produk->nama ?? 'Produk') . ": \"" . substr($review->ulasan, 0, 45) . "...\"",
                    'time' => $review->created_at ? $review->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('penjual.dashboard'),
                    'icon' => 'fas fa-star',
                    'badge_bg' => $isLowRating ? 'bg-amber-500' : 'bg-emerald-500',
                    'badge_text' => $isLowRating ? 'text-amber-500' : 'text-emerald-500',
                    'bg_light' => $isLowRating ? 'bg-amber-50' : 'bg-emerald-50',
                    'border' => $isLowRating ? 'border-amber-200' : 'border-emerald-200',
                    'is_critical' => $isLowRating,
                ]);
            }
        } catch (\Throwable $e) {}

        // 4. Pesan Chat Masuk dari Pembeli
        try {
            $unreadMessages = Message::where('receiver_id', $user->id)
                ->with('sender')
                ->latest()
                ->take(3)
                ->get();

            foreach ($unreadMessages as $msg) {
                $ts = $msg->created_at ? $msg->created_at->timestamp : time();

                $notifications->push([
                    'id' => 'chat_' . $msg->id,
                    'type' => 'chat',
                    'category' => 'chat',
                    'title' => "💬 Pesan Chat dari " . ($msg->sender->name ?? 'Pembeli'),
                    'description' => substr($msg->pesan, 0, 50) . "...",
                    'time' => $msg->created_at ? $msg->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('penjual.chat.index'),
                    'icon' => 'fas fa-comments',
                    'badge_bg' => 'bg-indigo-500',
                    'badge_text' => 'text-indigo-500',
                    'bg_light' => 'bg-indigo-50',
                    'border' => 'border-indigo-200',
                    'is_critical' => false,
                ]);
            }
        } catch (\Throwable $e) {}

        return $notifications->sortByDesc('timestamp')->values();
    }

    /**
     * Endpoint API JSON untuk polling realtime navbar notifikasi penjual.
     */
    public function getUnreadJson()
    {
        $notifications = self::getSellerNotifications();
        $actionRequiredCount = $notifications->where('is_critical', true)->where('is_unread', true)->count();
        $totalUnreadCount = $notifications->where('is_unread', true)->count();
        $totalCount = $notifications->count();

        return response()->json([
            'success' => true,
            'action_required_count' => $actionRequiredCount,
            'total_unread_count' => $totalUnreadCount,
            'total_count' => $totalCount,
            'data' => $notifications
        ]);
    }

    /**
     * Tandai seluruh notifikasi telah dibaca oleh Penjual yang sedang login.
     */
    public function markAllAsRead(Request $request)
    {
        $userId = Auth::id();
        session(['penjual_notif_read_timestamp_' . $userId => time()]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi toko telah ditandai sudah dibaca.'
        ]);
    }
}

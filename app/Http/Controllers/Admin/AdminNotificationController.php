<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Umkm;
use App\Models\PenarikanSaldo;
use App\Models\Ulasan;
use App\Models\Message;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AdminNotificationController extends Controller
{
    /**
     * Ambil seluruh notifikasi real-time & kejadian penting di platform.
     */
    public static function getSystemNotifications()
    {
        $notifications = collect();
        $lastReadTimestamp = session('admin_notif_read_timestamp', 0);

        // 1. Pengajuan Tarik Saldo Mitra (Pending)
        try {
            $pendingPayouts = PenarikanSaldo::with('umkm')
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            foreach ($pendingPayouts as $payout) {
                $ts = $payout->created_at ? $payout->created_at->timestamp : time();
                $notifications->push([
                    'id' => 'payout_' . $payout->id,
                    'type' => 'payout',
                    'category' => 'action_required',
                    'title' => 'Pengajuan Tarik Saldo',
                    'description' => ($payout->umkm->nama_toko ?? 'Mitra') . ' mengajukan pencairan Rp ' . number_format($payout->jumlah, 0, ',', '.'),
                    'time' => $payout->created_at ? $payout->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('admin.penarikan.index'),
                    'icon' => 'fas fa-money-bill-transfer',
                    'badge_bg' => 'bg-amber-500',
                    'badge_text' => 'text-amber-500',
                    'bg_light' => 'bg-amber-50',
                    'border' => 'border-amber-200',
                    'is_critical' => true,
                ]);
            }
        } catch (\Throwable $e) {}

        // 2. Pendaftaran Toko UMKM Baru (Pending)
        try {
            $pendingUmkms = Umkm::with('user')
                ->where('status', 'pending')
                ->latest()
                ->take(5)
                ->get();

            foreach ($pendingUmkms as $umkm) {
                $ts = $umkm->created_at ? $umkm->created_at->timestamp : time();
                $notifications->push([
                    'id' => 'umkm_' . $umkm->id,
                    'type' => 'umkm',
                    'category' => 'action_required',
                    'title' => 'Verifikasi Toko Baru',
                    'description' => "'{$umkm->nama_toko}' oleh " . ($umkm->user->name ?? 'Penjual') . ' menunggu persetujuan',
                    'time' => $umkm->created_at ? $umkm->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('admin.umkm.index', ['status' => 'pending']),
                    'icon' => 'fas fa-store',
                    'badge_bg' => 'bg-indigo-500',
                    'badge_text' => 'text-indigo-500',
                    'bg_light' => 'bg-indigo-50',
                    'border' => 'border-indigo-200',
                    'is_critical' => true,
                ]);
            }
        } catch (\Throwable $e) {}

        // 3. SLA Pengiriman Terlambat (>24 Jam belum diproses/dikemas)
        try {
            $overdueOrders = Order::with(['user', 'produk.umkm'])
                ->where('status', 'complete')
                ->where(function ($q) {
                    $q->whereNull('status_pesanan')
                      ->orWhere('status_pesanan', 'menunggu_diproses');
                })
                ->where('created_at', '<', Carbon::now()->subHours(24))
                ->latest()
                ->take(4)
                ->get();

            foreach ($overdueOrders as $order) {
                $ts = $order->created_at ? $order->created_at->timestamp : time();
                $notifications->push([
                    'id' => 'sla_' . $order->id,
                    'type' => 'sla',
                    'category' => 'action_required',
                    'title' => 'SLA Pengiriman Terlambat',
                    'description' => "Pesanan #ORD-" . str_pad($order->id, 5, '0', STR_PAD_LEFT) . " belum diproses >24 jam oleh " . ($order->produk->umkm->nama_toko ?? 'Mitra'),
                    'time' => $order->created_at ? $order->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('admin.pengiriman.index', ['filter' => 'overdue']),
                    'icon' => 'fas fa-triangle-exclamation',
                    'badge_bg' => 'bg-rose-500',
                    'badge_text' => 'text-rose-500',
                    'bg_light' => 'bg-rose-50',
                    'border' => 'border-rose-200',
                    'is_critical' => true,
                ]);
            }
        } catch (\Throwable $e) {}

        // 4. Deteksi Chat Berisiko / Indikasi Transaksi Luar
        try {
            $riskKeywords = ['transfer langsung', 'nomor wa', 'no wa', 'rekening pribadi', 'luar aplikasi', 'bca', 'bri', 'mandiri', '081', '082', '085', '087', '088', '089'];
            $riskMessages = Message::where(function ($q) use ($riskKeywords) {
                foreach ($riskKeywords as $word) {
                    $q->orWhere('pesan', 'like', "%{$word}%");
                }
            })->latest()->take(3)->get();

            foreach ($riskMessages as $msg) {
                $ts = $msg->created_at ? $msg->created_at->timestamp : time();
                $notifications->push([
                    'id' => 'chat_risk_' . $msg->id,
                    'type' => 'chat_risk',
                    'category' => 'security',
                    'title' => 'Peringatan Anti-Fraud Chat',
                    'description' => "Percakapan mencurigakan: \"" . \Illuminate\Support\Str::limit($msg->pesan, 40) . "\"",
                    'time' => $msg->created_at ? $msg->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('admin.chat.index', ['risk_only' => 1]),
                    'icon' => 'fas fa-shield-halved',
                    'badge_bg' => 'bg-rose-600',
                    'badge_text' => 'text-rose-600',
                    'bg_light' => 'bg-rose-50',
                    'border' => 'border-rose-200',
                    'is_critical' => true,
                ]);
            }
        } catch (\Throwable $e) {}

        // 5. Ulasan Rating Rendah (1-2 Bintang)
        try {
            $badReviews = Ulasan::with('produk')
                ->where('bintang', '<=', 2)
                ->latest()
                ->take(3)
                ->get();

            foreach ($badReviews as $review) {
                $ts = $review->created_at ? $review->created_at->timestamp : time();
                $notifications->push([
                    'id' => 'review_' . $review->id,
                    'type' => 'review',
                    'category' => 'customer_satisfaction',
                    'title' => 'Komplain & Rating Rendah',
                    'description' => "Ulasan {$review->bintang}⭐ pada " . ($review->produk->nama ?? 'Produk') . ": \"" . \Illuminate\Support\Str::limit($review->ulasan, 35) . "\"",
                    'time' => $review->created_at ? $review->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('admin.ulasan.index', ['rating' => 'low']),
                    'icon' => 'fas fa-star-half-stroke',
                    'badge_bg' => 'bg-orange-500',
                    'badge_text' => 'text-orange-500',
                    'bg_light' => 'bg-orange-50',
                    'border' => 'border-orange-200',
                    'is_critical' => false,
                ]);
            }
        } catch (\Throwable $e) {}

        // 6. Transaksi Pesanan Baru Masuk (Lunas / Complete)
        try {
            $latestOrders = Order::with(['user', 'produk'])
                ->where('status', 'complete')
                ->latest()
                ->take(4)
                ->get();

            foreach ($latestOrders as $order) {
                $ts = $order->created_at ? $order->created_at->timestamp : time();
                $notifications->push([
                    'id' => 'order_' . $order->id,
                    'type' => 'order',
                    'category' => 'transaction',
                    'title' => 'Pesanan Masuk Lunas',
                    'description' => "Rp " . number_format($order->total_harga, 0, ',', '.') . " (" . ($order->name ?: ($order->user->name ?? 'Pembeli')) . ")",
                    'time' => $order->created_at ? $order->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('admin.pesanan.index'),
                    'icon' => 'fas fa-cart-shopping',
                    'badge_bg' => 'bg-emerald-500',
                    'badge_text' => 'text-emerald-500',
                    'bg_light' => 'bg-emerald-50',
                    'border' => 'border-emerald-200',
                    'is_critical' => false,
                ]);
            }
        } catch (\Throwable $e) {}

        // 7. Pesan Kontak & Kemitraan Baru (Belum Dibaca)
        try {
            $unreadPesan = \App\Models\PesanKontak::where('status', 'belum_dibaca')
                ->latest()
                ->take(5)
                ->get();

            foreach ($unreadPesan as $pesan) {
                $ts = $pesan->created_at ? $pesan->created_at->timestamp : time();
                $notifications->push([
                    'id' => 'pesan_' . $pesan->id,
                    'type' => 'pesan_kontak',
                    'category' => 'inquiry',
                    'title' => 'Pesan Masuk: ' . $pesan->kategori_label,
                    'description' => "Dari {$pesan->nama} ({$pesan->email}): \"{$pesan->subjek}\"",
                    'time' => $pesan->created_at ? $pesan->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => $ts > $lastReadTimestamp,
                    'url' => route('admin.pesan-kontak.index'),
                    'icon' => 'fas fa-envelope-open-text',
                    'badge_bg' => 'bg-brand-600',
                    'badge_text' => 'text-brand-600',
                    'bg_light' => 'bg-brand-50',
                    'border' => 'border-brand-200',
                    'is_critical' => in_array($pesan->kategori, ['partai_besar', 'kerjasama_umkm', 'kendala_transaksi']),
                ]);
            }
        } catch (\Throwable $e) {}

        return $notifications->sortByDesc('timestamp')->values();
    }

    /**
     * Endpoint API JSON untuk polling realtime navbar notifikasi.
     */
    public function getUnreadJson()
    {
        $notifications = self::getSystemNotifications();
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
     * Tandai seluruh notifikasi telah dibaca oleh Superadmin.
     */
    public function markAllAsRead(Request $request)
    {
        session(['admin_notif_read_timestamp' => time()]);

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai sudah dibaca.'
        ]);
    }
}

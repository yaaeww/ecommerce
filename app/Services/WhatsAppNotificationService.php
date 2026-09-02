<?php

namespace App\Services;

use App\Models\Order;
use App\Models\PenarikanSaldo;
use App\Models\Komplain;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class WhatsAppNotificationService
{
    /**
     * Normalisasi nomor telepon ke format internasional (contoh: 6281234567890)
     */
    public static function normalizePhone(?string $phone): ?string
    {
        if (!$phone) return null;

        $clean = preg_replace('/[^0-9]/', '', $phone);
        if (empty($clean)) return null;

        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        } elseif (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return strlen($clean) >= 9 ? $clean : null;
    }

    /**
     * Kirim notifikasi pesanan baru ke Petani / Toko UMKM
     */
    public static function sendNewOrderToSeller(Order $order)
    {
        $rawPhone = $order->produk->umkm->nomor_telepon ?? $order->produk->umkm->no_telp ?? null;
        $sellerPhone = self::normalizePhone($rawPhone);
        if (!$sellerPhone) return;

        $msg = "🥭 *PESANAN BARU MASUK - JURAGAN PELEM*\n\n"
             . "Halo *" . ($order->produk->umkm->user->name ?? 'Juragan') . "*,\n"
             . "Ada pesanan baru yang siap dikemas:\n\n"
             . "📦 *No. Pesanan*: #ORD-{$order->id}\n"
             . "🥭 *Komoditas*: {$order->produk->nama}\n"
             . "⚖️ *Jumlah*: {$order->jumlah} unit\n"
             . "💰 *Total Tagihan*: Rp " . number_format($order->total_harga, 0, ',', '.') . "\n"
             . "👤 *Penerima*: {$order->name} ({$order->phone})\n"
             . "📍 *Alamat*: {$order->alamat}\n\n"
             . "Silakan buka panel penjual untuk mencetak Label Resi Thermal A6 & menyiapkan kardus kemasan.\n\n"
             . "_Pesan otomatis dari Sistem Juragan Pelem Indramayu_";

        self::dispatch($sellerPhone, $msg);
    }

    /**
     * Kirim notifikasi resi pengiriman ke Pembeli
     */
    public static function sendOrderShippedToBuyer(Order $order)
    {
        $buyerPhone = self::normalizePhone($order->phone ?? null);
        if (!$buyerPhone) return;

        $msg = "🚚 *PESANAN ANDA SEDANG DIKIRIM - JURAGAN PELEM*\n\n"
             . "Halo *" . ($order->name) . "*,\n"
             . "Mangga segar pesanan Anda telah diserahkan ke kurir:\n\n"
             . "📦 *No. Pesanan*: #ORD-{$order->id}\n"
             . "🥭 *Komoditas*: {$order->produk->nama} ({$order->jumlah} unit)\n"
             . "🏢 *Ekspedisi*: " . strtoupper($order->kurir_ekspedisi ?? 'J&T Cargo') . "\n"
             . "🏷️ *No. Resi*: *" . ($order->no_resi ?? '-') . "*\n\n"
             . "Anda dapat melacak perjalanan paket di dashboard pembeli. Pastikan rekam Video Unboxing saat paket tiba untuk klaim Garansi Segar.\n\n"
             . "_Terima kasih telah mendukung petani mangga lokal Indramayu!_";

        self::dispatch($buyerPhone, $msg);
    }

    /**
     * Kirim notifikasi pencairan dana telah ditransfer ke Petani
     */
    public static function sendPayoutApprovedToSeller(PenarikanSaldo $penarikan)
    {
        $rawPhone = $penarikan->umkm->nomor_telepon ?? $penarikan->umkm->no_telp ?? null;
        $sellerPhone = self::normalizePhone($rawPhone);
        if (!$sellerPhone) return;

        $msg = "💵 *PENCAIRAN DANA BERHASIL - JURAGAN PELEM*\n\n"
             . "Halo *" . ($penarikan->umkm->nama_toko) . "*,\n"
             . "Permohonan penarikan saldo Anda telah disetujui & ditransfer:\n\n"
             . "💰 *Nominal Cair*: Rp " . number_format($penarikan->jumlah, 0, ',', '.') . "\n"
             . "🏦 *Bank Tujuan*: {$penarikan->nama_bank} - {$penarikan->nomor_rekening}\n"
             . "👤 *Atas Nama*: {$penarikan->atas_nama}\n"
             . "📅 *Waktu Transfer*: " . now()->translatedFormat('d F Y, H:i') . " WIB\n\n"
             . "Silakan cek mutasi rekening Anda. Bukti transfer dapat diunduh melalui panel riwayat penarikan.\n\n"
             . "_Salam hangat dari Tim Juragan Pelem_";

        self::dispatch($sellerPhone, $msg);
    }

    /**
     * Eksekusi pengiriman payload WhatsApp (Mocking / Fonnte / Webhook API)
     */
    protected static function dispatch(string $targetPhone, string $message)
    {
        Log::info("WhatsApp Notification Dispatched to [{$targetPhone}]:", ['message' => $message]);

        // Jika terdapat konfigurasi API Gateway (misal Fonnte/Twilio)
        $fonnteToken = config('services.fonnte.token');
        if ($fonnteToken) {
            try {
                Http::withHeaders(['Authorization' => $fonnteToken])->post('https://api.fonnte.com/send', [
                    'target' => $targetPhone,
                    'message' => $message,
                    'countryCode' => '62',
                ]);
            } catch (\Exception $e) {
                Log::error('Fonnte API Error:', ['error' => $e->getMessage()]);
            }
        }
    }
}

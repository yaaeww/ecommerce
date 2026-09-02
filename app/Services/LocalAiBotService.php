<?php

namespace App\Services;

use App\Models\User;
use App\Models\Produk;
use App\Models\Umkm;
use App\Models\Order;
use App\Models\Keranjang;
use App\Models\KategoriProduk;
use Illuminate\Support\Str;

class LocalAiBotService
{
    /**
     * Respon cerdas Asisten AI Lokal berbasis Realtime Database & User Context
     */
    public static function reply(User $user, string $message): string
    {
        $query = strtolower(trim($message));
        $userName = $user->name ?? 'Pelanggan';
        $userRole = $user->role ?? 'pembeli';

        // 1. Sapaan & Basa-basi
        if (self::isGreeting($query)) {
            return self::greetingResponse($userName, $userRole);
        }

        if (self::isThanks($query)) {
            return "Sama-sama Kak **{$userName}**! Senang bisa membantu. 🥭✨\n\nJika butuh info produk segar, cek stok, atau status pesanan lainnya, langsung tanyakan saja ya!";
        }

        // 2. User Context: Cek Status Pesanan Saya (Strict Intent)
        if (self::isOrderIntent($query)) {
            return self::myOrdersResponse($user);
        }

        // 3. User Context: Cek Keranjang Belanja Saya
        if (self::isCartIntent($query)) {
            return self::myCartResponse($user);
        }

        // 4. Menu Bantuan / Fitur Bot
        if (self::matchesAny($query, ['bisa apa', 'fitur bot', 'bantuan', 'menu bot', 'panduan', 'tolong', 'help', 'cara belanja', 'cara pesan'])) {
            return self::helpResponse($userName, $userRole);
        }

        // 5. Penjual Context (Khusus Penjual)
        if ($userRole === 'penjual' && self::matchesAny($query, ['toko saya', 'omzet', 'saldo toko', 'tarik saldo', 'pesanan masuk', 'produk saya'])) {
            return self::sellerOverviewResponse($user);
        }

        // 6. Cek Info / Daftar Toko & UMKM Mitra
        if (self::matchesAny($query, ['daftar toko', 'list toko', 'toko apa', 'umkm apa', 'mitra kebun', 'alamat toko', 'lokasi toko', 'petani'])) {
            return self::storesResponse($query);
        }

        // 7. Cek Kategori Produk
        if (self::matchesAny($query, ['kategori', 'jenis produk', 'macam produk', 'pilihan produk', 'varian produk'])) {
            return self::categoriesResponse();
        }

        // 8. Katalog Umum / Produk yang Tersedia
        if (self::isGeneralCatalogIntent($query)) {
            return self::allAvailableProductsResponse();
        }

        // 9. Rekomendasi Produk / Terlaris / Termurah
        if (self::matchesAny($query, ['rekomendasi', 'terlaris', 'paling laris', 'populer', 'favorit', 'termurah', 'paling murah', 'termahal', 'paling manis', 'paling enak'])) {
            return self::recommendationResponse($query);
        }

        // 10. Informasi Pengiriman, Ongkir & Logistik
        if (self::matchesAny($query, ['ongkir', 'pengiriman', 'kirim', 'ekspedisi', 'kurir', 'j&t', 'jnt', 'luar kota', 'antar', 'garansi buah', 'busuk di jalan'])) {
            return self::shippingInfoResponse();
        }

        // 11. Informasi Pembayaran & Checkout
        if (self::matchesAny($query, ['bayar', 'pembayaran', 'midtrans', 'qris', 'transfer', 'bca', 'bri', 'bni', 'mandiri', 'cod', 'kartu kredit', 'gopay', 'dana', 'shopeepay'])) {
            return self::paymentInfoResponse();
        }

        // 12. Informasi Komplain & Garansi
        if (self::matchesAny($query, ['komplain', 'retur', 'rusak', 'busuk', 'batal', 'refund', 'pengembalian', 'garansi', 'klaim'])) {
            return self::complaintInfoResponse();
        }

        // 13. Query Spesifik Produk (Pencarian Nama Produk, Harga, Stok)
        $productMatch = self::searchProductDatabase($query);
        if ($productMatch) {
            return $productMatch;
        }

        // 14. Fallback Cerdas
        return self::fallbackResponse($userName, $query);
    }

    /**
     * Helper deteksi kata kunci
     */
    private static function matchesAny(string $query, array $keywords): bool
    {
        foreach ($keywords as $k) {
            if (str_contains($query, $k)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Deteksi Sapaan
     */
    private static function isGreeting(string $query): bool
    {
        $greetings = ['halo', 'hai', 'hei', 'hello', 'assalamualaikum', 'sampurasun', 'pagi', 'siang', 'sore', 'malam'];
        foreach ($greetings as $g) {
            if ($query === $g || str_starts_with($query, $g . ' ') || str_ends_with($query, ' ' . $g)) {
                return true;
            }
        }
        return $query === 'p';
    }

    /**
     * Deteksi Terima Kasih
     */
    private static function isThanks(string $query): bool
    {
        return self::matchesAny($query, ['makasih', 'terima kasih', 'thanks', 'thx', 'nuhun', 'mantap', 'keren']);
    }

    /**
     * Deteksi Intent Cek Pesanan / Transaksi User
     */
    private static function isOrderIntent(string $query): bool
    {
        // Contoh: "pesanan apa yang saya buat", "status pesanan saya", "order saya", "cek pesanan", "riwayat belanja", "lacak paket saya"
        $hasOrderWord = self::matchesAny($query, ['pesan', 'pesanan', 'order', 'transaksi', 'riwayat belanja', 'pembelian', 'lacak', 'resi', 'paket']);
        $hasUserContext = self::matchesAny($query, ['saya', 'aku', 'punya', 'buat', 'bikin', 'beli', 'status', 'cek', 'lihat', 'apa']);

        if ($hasOrderWord && $hasUserContext) {
            return true;
        }

        if (self::matchesAny($query, ['pesanan saya', 'order saya', 'status order', 'status pesanan', 'lacak pesanan', 'resi saya', 'paket saya', 'riwayat pesanan', 'riwayat belanja', 'apa yang saya beli', 'pesanan yang saya buat'])) {
            return true;
        }

        return false;
    }

    /**
     * Deteksi Intent Keranjang Belanja
     */
    private static function isCartIntent(string $query): bool
    {
        return self::matchesAny($query, ['keranjang', 'isi keranjang', 'troli', 'cart', 'keranjang belanja', 'barang di keranjang']);
    }

    /**
     * Deteksi Intent Katalog Umum / Seluruh Produk
     */
    private static function isGeneralCatalogIntent(string $query): bool
    {
        return self::matchesAny($query, [
            'produk apa yang tersedia',
            'apa yang tersedia',
            'produk yang tersedia',
            'daftar produk',
            'semua produk',
            'katalog',
            'apa saja yang dijual',
            'jual apa saja',
            'list produk',
            'ada produk apa saja',
            'produk yang ada'
        ]);
    }

    /**
     * Respon Sapaan
     */
    private static function greetingResponse(string $userName, string $role): string
    {
        return "Halo Kak **{$userName}**! 👋 Selamat datang di **Juragan Pelem — Pusat Mangga & UMKM Indramayu**.\n\n" .
               "Saya adalah **Asisten AI Lokal** yang terhubung langsung dengan database sistem realtime. Ada yang bisa saya bantu?\n\n" .
               "💡 *Contoh pertanyaan yang bisa Anda ajukan:*\n" .
               "• *'Produk apa yang tersedia?'*\n" .
               "• *'Harga Mangga Gedong Gincu berapa?'*\n" .
               "• *'Cek pesanan saya'* (mengecek riwayat order akun Anda)\n" .
               "• *'Isi keranjang belanja saya'*\n" .
               "• *'Daftar toko dan mitra kebun'*";
    }

    /**
     * Respon Bantuan / Panduan
     */
    private static function helpResponse(string $userName, string $role): string
    {
        return "Halo Kak **{$userName}**, berikut hal-hal yang bisa saya informasikan secara realtime dari database:\n\n" .
               "1. 🥭 **Katalog & Stok:** Ketik *'Produk apa yang tersedia'* atau nama mangga (misal: *'Stok Mangga Cengkir'*).\n" .
               "2. 📦 **Pesanan Anda:** Ketik *'Pesanan apa yang saya buat'* untuk memeriksa riwayat belanja & nomor resi akun Anda.\n" .
               "3. 🛒 **Keranjang Belanja:** Ketik *'Isi keranjang saya'* untuk melihat item yang belum di-checkout.\n" .
               "4. 🏪 **Mitra Toko:** Ketik *'Daftar toko'* untuk melihat nama kebun & UMKM yang terdaftar.\n" .
               "5. 🚚 **Pengiriman & Pembayaran:** Tanyakan *'Metode pembayaran'* atau *'Ekspedisi pengiriman'*.\n\n" .
               "Silakan ketik kebutuhan Anda!";
    }

    /**
     * Respon Status Pesanan User Terkini (Realtime Database berdasarkan User ID)
     */
    private static function myOrdersResponse(User $user): string
    {
        $orders = Order::where('user_id', $user->id)
            ->with(['produk.umkm'])
            ->latest()
            ->take(5)
            ->get();

        if ($orders->isEmpty()) {
            return "Halo Kak **{$user->name}** (Akun ID: #{$user->id}) 😊\n\n" .
                   "📊 **Status:** Berdasarkan database kami, **Anda saat ini belum memiliki transaksi atau pesanan aktif.**\n\n" .
                   "👉 Ingin mulai berbelanja mangga segar langsung dari petani? Ketik *'Produk apa yang tersedia'* untuk melihat katalog komoditas!";
        }

        $res = "📦 **Riwayat Pesanan Anda (Akun ID #{$user->id} — Realtime Database):**\n\n";

        foreach ($orders as $index => $order) {
            $num = $index + 1;
            $namaProduk = $order->produk->nama ?? 'Komoditas Mangga';
            $toko = $order->produk->umkm->nama_toko ?? 'Mitra Kebun';
            $total = 'Rp ' . number_format($order->total_harga, 0, ',', '.');
            $statusBadge = $order->status === 'complete' ? '✅ Lunas' : '⏳ Menunggu Pembayaran';
            $statusKirim = match($order->status_pesanan) {
                'dikemas' => '📦 Sedang Dikemas Toko',
                'dikirim' => '🚚 Sedang Dikirim Ekspedisi',
                'diterima' => '🎉 Sudah Diterima',
                'batal' => '❌ Dibatalkan',
                default => '⏱️ Menunggu Konfirmasi Penjual'
            };

            $resiInfo = $order->resi_pengiriman 
                ? " (Resi {$order->kurir}: `{$order->resi_pengiriman}`)" 
                : "";

            $res .= "{$num}. **#ORD-{$order->id}** — {$namaProduk} ({$order->jumlah}x)\n" .
                    "   • Toko: *{$toko}*\n" .
                    "   • Total Bayar: **{$total}** | {$statusBadge}\n" .
                    "   • Status: **{$statusKirim}**{$resiInfo}\n" .
                    "   • Tanggal: " . ($order->created_at ? $order->created_at->format('d M Y, H:i') : '-') . "\n\n";
        }

        $res .= "💡 *Info:* Anda dapat melihat detail invoice lengkap pada menu **Pesanan Saya** di halaman akun Anda.";
        return $res;
    }

    /**
     * Respon Keranjang Belanja User (Realtime Database berdasarkan User ID)
     */
    private static function myCartResponse(User $user): string
    {
        $carts = Keranjang::where('user_id', $user->id)
            ->with('produk.umkm')
            ->get();

        if ($carts->isEmpty()) {
            return "🛒 **Keranjang Belanja Anda (Akun ID #{$user->id})** saat ini masih kosong.\n\n" .
                   "Silakan pilih mangga favorit Anda di katalog lalu klik tombol **+ Keranjang** untuk belanja!";
        }

        $totalItem = $carts->sum('jumlah');
        $totalHarga = 0;
        $res = "🛒 **Isi Keranjang Belanja Anda ({$totalItem} Item):**\n\n";

        foreach ($carts as $cart) {
            if ($cart->produk) {
                $subtotal = $cart->produk->harga * $cart->jumlah;
                $totalHarga += $subtotal;
                $toko = $cart->produk->umkm->nama_toko ?? 'Mitra Kebun';
                $res .= "• **{$cart->produk->nama}** ({$cart->jumlah}x) — Rp " . number_format($subtotal, 0, ',', '.') . "\n" .
                        "  *Toko: {$toko}*\n";
            }
        }

        $res .= "\n💰 **Total Estimasi:** **Rp " . number_format($totalHarga, 0, ',', '.') . "**\n\n" .
                "👉 Klik menu **Keranjang** di navigasi atas untuk melanjutkan proses *checkout*.";
        return $res;
    }

    /**
     * Respon Seluruh Produk yang Tersedia (Katalog Realtime)
     */
    private static function allAvailableProductsResponse(): string
    {
        $produks = Produk::with('umkm')
            ->where('is_active', true)
            ->where('stok', '>', 0)
            ->orderBy('harga', 'asc')
            ->take(6)
            ->get();

        if ($produks->isEmpty()) {
            return "Saat ini semua produk sedang dalam proses restock panen kebun.";
        }

        $res = "🥭 **Daftar Produk Ready Stock di Juragan Pelem (Realtime Database):**\n\n";
        foreach ($produks as $index => $p) {
            $num = $index + 1;
            $toko = $p->umkm->nama_toko ?? 'Mitra Kebun Indramayu';
            $diskonTxt = $p->diskon_persen > 0 ? " ~~(Rp " . number_format($p->harga_coret, 0, ',', '.') . ")~~ [Diskon {$p->diskon_persen}%]" : "";
            
            $res .= "{$num}. **{$p->nama}**\n" .
                    "   • Harga: **Rp " . number_format($p->harga, 0, ',', '.') . "**{$diskonTxt}\n" .
                    "   • Stok: **{$p->stok} unit**\n" .
                    "   • Toko: *{$toko}*\n\n";
        }

        $res .= "💡 *Untuk memesan, Anda bisa membuka menu **Kategori** atau mencari produk langsung melalui kolom pencarian di navigasi atas!*";
        return $res;
    }

    /**
     * Ringkasan Toko (Khusus Penjual)
     */
    private static function sellerOverviewResponse(User $user): string
    {
        $umkm = Umkm::where('user_id', $user->id)->first();
        if (!$umkm) {
            return "Halo Kak **{$user->name}**, toko UMKM Anda belum terdaftar aktif di sistem.";
        }

        $totalProduk = Produk::where('umkm_id', $umkm->id)->count();
        $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');
        $pesananMasuk = Order::whereIn('produk_id', $produkIds)->where('status', 'complete')->count();
        $pesananPending = Order::whereIn('produk_id', $produkIds)->where('status_pesanan', 'belum_diterima')->count();

        return "🏪 **Ringkasan Toko Anda — {$umkm->nama_toko}**\n\n" .
               "• Status Toko: **" . ucfirst($umkm->status ?? 'Aktif') . "**\n" .
               "• Alamat: {$umkm->alamat}\n" .
               "• Total Produk Aktif: **{$totalProduk} Produk**\n" .
               "• Total Transaksi Selesai: **{$pesananMasuk} Transaksi**\n" .
               "• Pesanan Perlu Diproses: **{$pesananPending} Pesanan**\n\n" .
               "👉 Buka **Dashboard Penjual** untuk memproses pesanan dan mencairkan saldo kebun.";
    }

    /**
     * Respon Daftar Toko / UMKM
     */
    private static function storesResponse(string $query): string
    {
        $umkms = Umkm::withCount('produks')
            ->where('status', 'active')
            ->orWhere('status', 'approved')
            ->take(5)
            ->get();

        if ($umkms->isEmpty()) {
            return "Saat ini sedang dilakukan verifikasi berkala untuk mitra kebun & UMKM di Indramayu.";
        }

        $res = "🏪 **Daftar Mitra Kebun & UMKM Terverifikasi di Juragan Pelem:**\n\n";
        foreach ($umkms as $umkm) {
            $statusLibur = $umkm->is_libur ? '*(Sedang Libur Panen)*' : '*(Buka & Siap Kirim)*';
            $res .= "• **{$umkm->nama_toko}** {$statusLibur}\n" .
                    "  📍 Lokasi: {$umkm->alamat}\n" .
                    "  📦 Katalog: {$umkm->produks_count} Produk Komoditas\n" .
                    "  📞 Kontak: " . ($umkm->no_telp ?: 'Tersedia di Profil') . "\n\n";
        }

        $res .= "Semua mitra kebun menjamin mangga matang pohon asli Indramayu bergaransi mutu.";
        return $res;
    }

    /**
     * Respon Kategori Produk
     */
    private static function categoriesResponse(): string
    {
        $categories = KategoriProduk::withCount('produks')->get();
        if ($categories->isEmpty()) {
            return "Kategori komoditas utama mencakup: **Mangga Segar**, **Olahan Mangga**, dan **Bibit Tanaman**.";
        }

        $res = "📂 **Kategori Komoditas yang Tersedia:**\n\n";
        foreach ($categories as $cat) {
            $res .= "• **{$cat->nama}** ({$cat->produks_count} Produk Aktif)\n" .
                    "  _{$cat->deskripsi}_\n\n";
        }
        return $res;
    }

    /**
     * Respon Rekomendasi Produk
     */
    private static function recommendationResponse(string $query): string
    {
        $produks = Produk::with('umkm')
            ->where('stok', '>', 0)
            ->where('is_active', true)
            ->orderBy('rating', 'desc')
            ->take(4)
            ->get();

        if ($produks->isEmpty()) {
            $produks = Produk::with('umkm')->take(4)->get();
        }

        $res = "🌟 **Rekomendasi Komoditas Unggulan Juragan Pelem:**\n\n";
        foreach ($produks as $p) {
            $toko = $p->umkm->nama_toko ?? 'Mitra Kebun Indramayu';
            $diskonTxt = $p->diskon_persen > 0 ? " ~~(Rp " . number_format($p->harga_coret, 0, ',', '.') . ")~~ [Diskon {$p->diskon_persen}%]" : "";
            $ratingTxt = $p->rating > 0 ? "⭐ {$p->rating}/5.0" : "⭐ Mutu Grade A";
            
            $res .= "🥭 **{$p->nama}**\n" .
                    "   • Harga: **Rp " . number_format($p->harga, 0, ',', '.') . "**{$diskonTxt}\n" .
                    "   • Stok: **{$p->stok} unit** | {$ratingTxt}\n" .
                    "   • Toko: *{$toko}*\n\n";
        }

        $res .= "💡 *Semua produk dipetik langsung dari kebun Indramayu dengan standar mutu terjamin.*";
        return $res;
    }

    /**
     * Pencarian Spesifik Produk di Database
     */
    private static function searchProductDatabase(string $query): ?string
    {
        // Stopwords yang diabaikan dalam ekstraksi kata kunci produk
        $stopwords = [
            'harga', 'stok', 'berapa', 'ada', 'jual', 'mangga', 'beli', 'apakah', 'buah', 'produk',
            'apa', 'yang', 'saya', 'buat', 'bikin', 'mau', 'dong', 'sih', 'ini', 'itu', 'di', 'ke',
            'dari', 'untuk', 'pada', 'dengan', 'adalah', 'tolong', 'info', 'tentang', 'pesan', 'pesanan',
            'order', 'transaksi', 'punya', 'kami', 'kita', 'dia', 'mereka'
        ];

        $rawWords = explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', ' ', $query));
        $keywords = [];

        foreach ($rawWords as $w) {
            $w = trim($w);
            if (strlen($w) >= 3 && !in_array($w, $stopwords)) {
                $keywords[] = $w;
            }
        }

        // Cek kata kunci mangga spesifik
        if (empty($keywords)) {
            if (str_contains($query, 'gedong') || str_contains($query, 'gincu')) $keywords = ['gedong'];
            elseif (str_contains($query, 'cengkir')) $keywords = ['cengkir'];
            elseif (str_contains($query, 'harum') || str_contains($query, 'manis')) $keywords = ['harum'];
            elseif (str_contains($query, 'manalagi')) $keywords = ['manalagi'];
            elseif (str_contains($query, 'keripik')) $keywords = ['keripik'];
            elseif (str_contains($query, 'sirup')) $keywords = ['sirup'];
            elseif (str_contains($query, 'dodol')) $keywords = ['dodol'];
            elseif (str_contains($query, 'bibit')) $keywords = ['bibit'];
            elseif (str_contains($query, 'pupuk')) $keywords = ['pupuk'];
            elseif (str_contains($query, 'nektar')) $keywords = ['nektar'];
            elseif (str_contains($query, 'selai')) $keywords = ['selai'];
            elseif (str_contains($query, 'sambal')) $keywords = ['sambal'];
            elseif (str_contains($query, 'puding')) $keywords = ['puding'];
            elseif (str_contains($query, 'puree')) $keywords = ['puree'];
            elseif (str_contains($query, 'hampers')) $keywords = ['hampers'];
            elseif (str_contains($query, 'jus')) $keywords = ['jus'];
        }

        if (empty($keywords)) {
            return null;
        }

        // Cari HANYA di kolom 'nama' agar tidak salah tangkap deskripsi acak
        $productQuery = Produk::with(['umkm', 'kategoriProduk'])
            ->where('is_active', true);

        $productQuery->where(function ($q) use ($keywords) {
            foreach ($keywords as $kw) {
                $q->orWhere('nama', 'LIKE', "%{$kw}%");
            }
        });

        $matchedProducts = $productQuery->take(3)->get();

        if ($matchedProducts->isEmpty()) {
            return null;
        }

        $res = "🔍 **Hasil Pengecekan Database Produk Realtime:**\n\n";
        foreach ($matchedProducts as $prod) {
            $statusStok = $prod->stok > 0 
                ? "🟢 Tersedia Ready Stock (**{$prod->stok} unit**)" 
                : "🔴 Stok Habis (Dalam Proses Panen)";
            
            $diskonTxt = $prod->diskon_persen > 0 
                ? " ~~(Rp " . number_format($prod->harga_coret, 0, ',', '.') . ")~~ **[Diskon {$prod->diskon_persen}%]**" 
                : "";

            $toko = $prod->umkm->nama_toko ?? 'Mitra Kebun Indramayu';
            $alamatToko = $prod->umkm->alamat ?? 'Indramayu';

            $res .= "🥭 **{$prod->nama}**\n" .
                    "   • Harga: **Rp " . number_format($prod->harga, 0, ',', '.') . "** / satuan{$diskonTxt}\n" .
                    "   • Status Stok: {$statusStok}\n" .
                    "   • Berat: " . ($prod->berat_gram ? number_format($prod->berat_gram) . " gram" : "1.000 gram (1 Kg)") . "\n" .
                    "   • Kebun / UMKM: **{$toko}** (Lokasi: {$alamatToko})\n" .
                    "   • Deskripsi: " . Str::limit(strip_tags($prod->deskripsi), 90) . "\n\n";
        }

        $res .= "👉 Anda bisa langsung memasukkannya ke keranjang belanja untuk melakukan pemesanan!";
        return $res;
    }

    /**
     * Informasi Pengiriman & Ekspedisi
     */
    private static function shippingInfoResponse(): string
    {
        return "🚚 **Informasi Pengiriman & Logistik Juragan Pelem:**\n\n" .
               "• **Jangkauan Pengiriman:** Seluruh wilayah Indonesia (khusus mangga segar dikemas dengan safety net + bubble wrap tebal).\n" .
               "• **Ekspedisi Resmi:** J&T Express (Next Day / Reguler) & Kurir Lokal Instant khusus area Indramayu.\n" .
               "• **Estimasi Waktu Kirim:**\n" .
               "  - Jabodetabek & Jawa Barat: 1 - 2 Hari\n" .
               "  - Jawa Tengah & Jawa Timur: 2 - 3 Hari\n" .
               "  - Luar Pulau Jawa: 3 - 4 Hari (disarankan menggunakan layanan kilat/udara).\n" .
               "• **Garansi Mutu Segar:** Jika buah busuk atau rusak dalam perjalanan akibat kendala ekspedisi, Anda berhak mengajukan retur/komplain melalui menu **Komplain**.";
    }

    /**
     * Informasi Pembayaran
     */
    private static function paymentInfoResponse(): string
    {
        return "💳 **Metode Pembayaran Resmi di Juragan Pelem:**\n\n" .
               "Kami menggunakan gateway resmi **Midtrans Snap (Berstandar Keamanan PCI-DSS)**:\n" .
               "1. **QRIS** (Gopay, OVO, DANA, LinkAja, ShopeePay, Mobile Banking).\n" .
               "2. **Virtual Account (Otomatis Terverifikasi):**\n" .
               "   • Bank BCA, BRI, BNI, Mandiri, Permata.\n" .
               "3. **Sistem Escrow Aman (Rekening Bersama):** Dana pembayaran Anda aman di platform dan baru diteruskan ke petani setelah buah diterima dengan baik.";
    }

    /**
     * Informasi Komplain & Retur
     */
    private static function complaintInfoResponse(): string
    {
        return "🛡️ **Prosedur Komplain & Garansi Mutu Buah:**\n\n" .
               "1. Buka menu **Pesanan Saya** di akun Anda.\n" .
               "2. Klik transaksi yang bermasalah, lalu pilih tombol **Ajukan Komplain**.\n" .
               "3. Unggah foto/video unboxing bukti buah yang rusak/busuk beserta deskripsi kendala.\n" .
               "4. Admin dan Penjual akan meninjau dalam waktu maksimal 1x24 jam untuk persetujuan penggantian buah atau pengembalian saldo (refund).";
    }

    /**
     * Fallback Respon ketika AI tidak yakin
     */
    private static function fallbackResponse(string $userName, string $query): string
    {
        return "Maaf Kak **{$userName}**, saya belum menemukan data spesifik untuk: *\"{$query}\"*.\n\n" .
               "💡 **Coba tanyakan hal-hal berikut:**\n" .
               "• *'Produk apa yang tersedia?'*\n" .
               "• *'Harga Mangga Gedong Gincu berapa?'*\n" .
               "• *'Pesanan apa yang saya buat?'*\n" .
               "• *'Isi keranjang saya apa saja?'*\n" .
               "• *'Daftar toko dan mitra kebun'*";
    }
}

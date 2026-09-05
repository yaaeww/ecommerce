<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use App\Models\User;
use App\Models\Umkm;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Diskon;
use App\Models\Order;
use App\Models\Ulasan;
use App\Models\Chat;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Helper untuk memastikan direktori dan file gambar lokal tersedia
     */
    private function ensureLocalImage(string $relativePath, string $onlineUrl, string $label, string $bgColor = '#f59e0b')
    {
        $storagePath = storage_path('app/public/' . $relativePath);
        $publicPath = public_path('storage/' . $relativePath);
        
        $dir = dirname($storagePath);
        if (!File::exists($dir)) {
            File::makeDirectory($dir, 0755, true);
        }

        // Jika file belum ada, coba unduh dari Unsplash, atau generate via GD
        if (!File::exists($storagePath)) {
            $downloaded = false;
            try {
                $response = Http::timeout(5)->withoutVerifying()->get($onlineUrl);
                if ($response->successful() && strlen($response->body()) > 1000) {
                    file_put_contents($storagePath, $response->body());
                    $downloaded = true;
                }
            } catch (\Throwable $e) {
                $downloaded = false;
            }

            // Fallback: Generate gambar SVG/GD lokal jika download gagal atau offline
            if (!$downloaded || !File::exists($storagePath)) {
                $this->generateFallbackImage($storagePath, $label, $bgColor);
            }
        }

        // Pastikan juga tersalin ke public/storage jika link belum aktif
        $pubDir = dirname($publicPath);
        if (!File::exists($pubDir)) {
            File::makeDirectory($pubDir, 0755, true);
        }
        if (File::exists($storagePath) && !File::exists($publicPath)) {
            @copy($storagePath, $publicPath);
        }
    }

    /**
     * Generate fallback JPEG gambar produk/kategori dengan GD
     */
    private function generateFallbackImage(string $filePath, string $title, string $hexColor)
    {
        $width = 600;
        $height = 600;
        $image = imagecreatetruecolor($width, $height);

        // Parse hex color
        $hex = ltrim($hexColor, '#');
        $r = hexdec(substr($hex, 0, 2)) ?: 245;
        $g = hexdec(substr($hex, 2, 2)) ?: 158;
        $b = hexdec(substr($hex, 4, 2)) ?: 11;

        $bg = imagecolorallocate($image, $r, $g, $b);
        $white = imagecolorallocate($image, 255, 255, 255);
        $dark = imagecolorallocate($image, 30, 41, 59);
        $badgeBg = imagecolorallocate($image, max(0, $r - 30), max(0, $g - 30), max(0, $b - 30));

        imagefilledrectangle($image, 0, 0, $width, $height, $bg);

        // Draw inner rounded box
        imagefilledrectangle($image, 30, 30, $width - 30, $height - 30, $badgeBg);
        imagefilledrectangle($image, 40, 40, $width - 40, $height - 40, $white);

        // Text branding
        $brandText = "JURAGAN PELEM INDRAMAYU";
        imagestring($image, 4, ($width - (strlen($brandText) * 8)) / 2, 80, $brandText, $dark);

        // Product text
        $shortTitle = mb_strimwidth($title, 0, 28, "...");
        imagestring($image, 5, ($width - (strlen($shortTitle) * 9)) / 2, 280, $shortTitle, $dark);

        $badgeText = "Produk Unggulan Indramayu";
        imagestring($image, 3, ($width - (strlen($badgeText) * 7)) / 2, 330, $badgeText, $badgeBg);

        imagejpeg($image, $filePath, 90);
        imagedestroy($image);
    }

    public function run(): void
    {
        // ===============================
        // 🔹 PERSIAPAN STORAGE
        // ===============================
        $this->command->info('📦 Mempersiapkan aset gambar lokal...');

        // Image dataset mapping (Unsplash high quality mango/agro images)
        $productImages = [
            'produks/mangga-gedong-gincu.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=600&auto=format&fit=crop&q=80',
                'label' => 'Mangga Gedong Gincu',
                'color' => '#f59e0b'
            ],
            'produks/mangga-harum-manis.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?w=600&auto=format&fit=crop&q=80',
                'label' => 'Mangga Harum Manis',
                'color' => '#10b981'
            ],
            'produks/mangga-cengkir.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1591073113125-e46713c829ed?w=600&auto=format&fit=crop&q=80',
                'label' => 'Mangga Cengkir Indramayu',
                'color' => '#84cc16'
            ],
            'produks/mangga-dermayu.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1546173159-315724a31696?w=600&auto=format&fit=crop&q=80',
                'label' => 'Mangga Dermayu Fresh',
                'color' => '#eab308'
            ],
            'produks/mangga-golek.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1567306226416-28f0efdc88ce?w=600&auto=format&fit=crop&q=80',
                'label' => 'Mangga Golek Manis',
                'color' => '#f97316'
            ],
            'produks/dodol-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1582293041079-7814c2f12063?w=600&auto=format&fit=crop&q=80',
                'label' => 'Dodol Mangga Gedong Gincu',
                'color' => '#b45309'
            ],
            'produks/sirup-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1546173159-315724a31696?w=600&auto=format&fit=crop&q=80',
                'label' => 'Sirup Mangga Gedong Gincu',
                'color' => '#f59e0b'
            ],
            'produks/manisan-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?w=600&auto=format&fit=crop&q=80',
                'label' => 'Manisan Mangga Pedas Manis',
                'color' => '#ef4444'
            ],
            'produks/keripik-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1596560548464-f010549b84d7?w=600&auto=format&fit=crop&q=80',
                'label' => 'Keripik Mangga Vacuum Fried',
                'color' => '#d97706'
            ],
            'produks/jus-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1525385133512-2f3bdd039054?w=600&auto=format&fit=crop&q=80',
                'label' => 'Jus Mangga Gedong Segar',
                'color' => '#ea580c'
            ],
            'produks/selai-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1589733955941-5eeaf752f6dd?w=600&auto=format&fit=crop&q=80',
                'label' => 'Selai Mangga Homemade Jar',
                'color' => '#ca8a04'
            ],
            'produks/sambal-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1588165171080-c89acfa5a259?w=600&auto=format&fit=crop&q=80',
                'label' => 'Sambal Mangga Pedas Gurih',
                'color' => '#dc2626'
            ],
            'produks/puding-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1551024709-8f23befc6f87?w=600&auto=format&fit=crop&q=80',
                'label' => 'Puding Jelly Mangga Gedong',
                'color' => '#f59e0b'
            ],
            'produks/nektar-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1546173159-315724a31696?w=600&auto=format&fit=crop&q=80',
                'label' => 'Nektar Sari Buah Mangga',
                'color' => '#d97706'
            ],
            'produks/puree-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1525385133512-2f3bdd039054?w=600&auto=format&fit=crop&q=80',
                'label' => 'Puree Mangga Gedong Frozen',
                'color' => '#ea580c'
            ],
            'produks/hampers-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=600&auto=format&fit=crop&q=80',
                'label' => 'Hampers Mangga Gedong Gincu',
                'color' => '#b45309'
            ],
            'produks/bibit-gedong-gincu.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=600&auto=format&fit=crop&q=80',
                'label' => 'Bibit Pohon Mangga Okulasi',
                'color' => '#15803d'
            ],
            'produks/bibit-harum-manis.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1416879595882-3373a0480b5b?w=600&auto=format&fit=crop&q=80',
                'label' => 'Bibit Mangga Harum Manis',
                'color' => '#166534'
            ],
            'produks/pupuk-booster-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=600&auto=format&fit=crop&q=80',
                'label' => 'Pupuk Booster Buah Mangga',
                'color' => '#3f6212'
            ],
            'produks/nutrisi-organik-mangga.jpg' => [
                'url' => 'https://images.unsplash.com/photo-1585320806297-9794b3e4eeae?w=600&auto=format&fit=crop&q=80',
                'label' => 'Nutrisi Pelebat Bunga Mangga',
                'color' => '#4d7c0f'
            ],
            // Logos
            'logos/kebun-gedong.png' => [
                'url' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=200&auto=format&fit=crop&q=80',
                'label' => 'Kebun Gedong Gincu',
                'color' => '#f59e0b'
            ],
            'logos/sentra-olahan.png' => [
                'url' => 'https://images.unsplash.com/photo-1546173159-315724a31696?w=200&auto=format&fit=crop&q=80',
                'label' => 'Sentra Olahan Mangga',
                'color' => '#ea580c'
            ],
            'logos/agro-dermayu.png' => [
                'url' => 'https://images.unsplash.com/photo-1601493700631-2b16ec4b4716?w=200&auto=format&fit=crop&q=80',
                'label' => 'Agro Pelem Dermayu',
                'color' => '#10b981'
            ],
            'logos/bibit-nusantara.png' => [
                'url' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=200&auto=format&fit=crop&q=80',
                'label' => 'Bibit Mangga Nusantara',
                'color' => '#15803d'
            ],
            // Categories
            'kategori/mangga-segar.png' => [
                'url' => 'https://images.unsplash.com/photo-1553279768-865429fa0078?w=300&auto=format&fit=crop&q=80',
                'label' => 'Mangga Segar Indramayu',
                'color' => '#f59e0b'
            ],
            'kategori/olahan-mangga.png' => [
                'url' => 'https://images.unsplash.com/photo-1546173159-315724a31696?w=300&auto=format&fit=crop&q=80',
                'label' => 'Olahan Mangga Khas',
                'color' => '#ea580c'
            ],
            'kategori/bibit-perkebunan.png' => [
                'url' => 'https://images.unsplash.com/photo-1513836279014-a89f7a76ae86?w=300&auto=format&fit=crop&q=80',
                'label' => 'Bibit & Perkebunan',
                'color' => '#15803d'
            ],
        ];

        foreach ($productImages as $relPath => $imgInfo) {
            $this->ensureLocalImage($relPath, $imgInfo['url'], $imgInfo['label'], $imgInfo['color']);
        }
        $this->command->info('✅ Aset gambar lokal berhasil disiapkan di storage/app/public/');

        // ===============================
        // 🔹 USERS
        // ===============================
        $users = [
            [
                'name' => 'Admin Juragan Pelem',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'avatar' => 'avatars/admin.png',
            ],
            [
                'name' => 'Pak Haji Sanusi (Petani Gedong Gincu)',
                'email' => 'penjual@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'penjual',
                'avatar' => 'avatars/penjual.png',
            ],
            [
                'name' => 'Ibu Siti Khodijah (Sentra Olahan)',
                'email' => 'jo@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'penjual',
                'avatar' => 'avatars/jo.png',
            ],
            [
                'name' => 'Pembeli Mangga',
                'email' => 'pembeli@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'pembeli',
                'avatar' => 'avatars/pembeli.png',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'pembeli',
                'avatar' => 'avatars/budi.png',
            ],
            [
                'name' => 'Sari Wijaya (Agro Dermayu)',
                'email' => 'sari@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'penjual',
                'avatar' => 'avatars/sari.png',
            ],
            [
                'name' => 'Ahmad Fauzi (Sentra Bibit Unggul)',
                'email' => 'ahmad@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'penjual',
                'avatar' => 'avatars/ahmad.png',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'pembeli',
                'avatar' => 'avatars/dewi.png',
            ],
            [
                'name' => 'Rudi Hermawan',
                'email' => 'rudi@gmail.com',
                'password' => Hash::make('password'),
                'role' => 'pembeli',
                'avatar' => 'avatars/rudi.png',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
        $this->command->info('✅ Users seeded');

        // ===============================
        // 🔹 UMKM MITRA AGRIKULTUR & MANGGA
        // ===============================
        $userSanusi = User::where('email', 'penjual@gmail.com')->first();
        $userSiti = User::where('email', 'jo@gmail.com')->first();
        $userSari = User::where('email', 'sari@gmail.com')->first();
        $userAhmad = User::where('email', 'ahmad@gmail.com')->first();

        $umkms = [
            [
                'user_id' => $userSanusi->id, // Pak Haji Sanusi
                'nama_toko' => 'Kebun Mangga Gedong Gincu',
                'deskripsi' => 'Pusat budidaya mangga gedong gincu super asli Indramayu dari kebun langsung. Garansi petik pohon dan mutu premium.',
                'alamat' => 'Desa Krasak, Kec. Jatibarang, Kab. Indramayu',
                'no_telp' => '081234567890',
                'logo' => 'logos/kebun-gedong.png',
                'status' => 'approved',
            ],
            [
                'user_id' => $userSiti->id, // Ibu Siti
                'nama_toko' => 'Sentra Olahan Mangga Indramayu',
                'deskripsi' => 'Spesialis produk hilirisasi mangga: Dodol, Sirup, Manisan, Keripik, dan Sambal Mangga Gedong Gincu alami tanpa pengawet.',
                'alamat' => 'Jl. Olahan Pangan No. 12, Indramayu',
                'no_telp' => '082345678901',
                'logo' => 'logos/sentra-olahan.png',
                'status' => 'approved',
            ],
            [
                'user_id' => $userSari->id, // Sari Wijaya
                'nama_toko' => 'Agro Pelem Super Dermayu',
                'deskripsi' => 'Koleksi lengkap mangga khas Indramayu: Harum Manis, Cengkir, Dermayu, dan Golek Segar siap kirim ke seluruh kota.',
                'alamat' => 'Jl. Raya Cikedung No. 45, Indramayu',
                'no_telp' => '083456789012',
                'logo' => 'logos/agro-dermayu.png',
                'status' => 'approved',
            ],
            [
                'user_id' => $userAhmad->id, // Ahmad Fauzi
                'nama_toko' => 'Bibit & Perkebunan Mangga Nusantara',
                'deskripsi' => 'Penyedia bibit pohon mangga okulasi bersertifikasi unggul, pupuk organik pelebat buah, dan nutrisi khusus perkebunan mangga.',
                'alamat' => 'Jl. Agraria Hijau No. 8, Indramayu',
                'no_telp' => '084567890123',
                'logo' => 'logos/bibit-nusantara.png',
                'status' => 'approved',
            ],
        ];

        foreach ($umkms as $umkm) {
            Umkm::updateOrCreate(['nama_toko' => $umkm['nama_toko']], $umkm);
        }
        $this->command->info('✅ UMKMs seeded');

        // ===============================
        // 🔹 KATEGORI PRODUK & SUBKATEGORI
        // ===============================
        $kategoriList = [
            // Kategori Utama
            ['nama' => 'Mangga Segar', 'slug' => 'mangga-segar', 'gambar' => 'kategori/mangga-segar.png', 'parent_id' => null],
            ['nama' => 'Olahan Mangga', 'slug' => 'olahan-mangga', 'gambar' => 'kategori/olahan-mangga.png', 'parent_id' => null],
            ['nama' => 'Bibit & Kebun', 'slug' => 'bibit-perkebunan', 'gambar' => 'kategori/bibit-perkebunan.png', 'parent_id' => null],
        ];

        foreach ($kategoriList as $kat) {
            KategoriProduk::updateOrCreate(['slug' => $kat['slug']], $kat);
        }

        $katSegar = KategoriProduk::where('slug', 'mangga-segar')->first();
        $katOlahan = KategoriProduk::where('slug', 'olahan-mangga')->first();
        $katBibit = KategoriProduk::where('slug', 'bibit-perkebunan')->first();

        // Subkategori
        $subkategoriList = [
            // Subkategori Mangga Segar
            ['nama' => 'Mangga Gedong Gincu', 'slug' => 'mangga-gedong-gincu', 'gambar' => 'produks/mangga-gedong-gincu.jpg', 'parent_id' => $katSegar->id],
            ['nama' => 'Mangga Harum Manis', 'slug' => 'mangga-harum-manis', 'gambar' => 'produks/mangga-harum-manis.jpg', 'parent_id' => $katSegar->id],
            ['nama' => 'Mangga Cengkir Indramayu', 'slug' => 'mangga-cengkir', 'gambar' => 'produks/mangga-cengkir.jpg', 'parent_id' => $katSegar->id],
            ['nama' => 'Mangga Dermayu & Golek', 'slug' => 'mangga-dermayu-golek', 'gambar' => 'produks/mangga-dermayu.jpg', 'parent_id' => $katSegar->id],

            // Subkategori Olahan Mangga
            ['nama' => 'Dodol & Manisan Mangga', 'slug' => 'dodol-manisan-mangga', 'gambar' => 'produks/dodol-mangga.jpg', 'parent_id' => $katOlahan->id],
            ['nama' => 'Sirup & Minuman Mangga', 'slug' => 'sirup-minuman-mangga', 'gambar' => 'produks/sirup-mangga.jpg', 'parent_id' => $katOlahan->id],
            ['nama' => 'Keripik & Camilan Mangga', 'slug' => 'keripik-camilan-mangga', 'gambar' => 'produks/keripik-mangga.jpg', 'parent_id' => $katOlahan->id],
            ['nama' => 'Selai & Sambal Mangga', 'slug' => 'selai-sambal-mangga', 'gambar' => 'produks/sambal-mangga.jpg', 'parent_id' => $katOlahan->id],

            // Subkategori Bibit & Kebun
            ['nama' => 'Bibit Pohon Mangga', 'slug' => 'bibit-pohon-mangga', 'gambar' => 'produks/bibit-gedong-gincu.jpg', 'parent_id' => $katBibit->id],
            ['nama' => 'Pupuk & Booster Buah', 'slug' => 'pupuk-booster-buah', 'gambar' => 'produks/pupuk-booster-mangga.jpg', 'parent_id' => $katBibit->id],
        ];

        foreach ($subkategoriList as $subkat) {
            KategoriProduk::updateOrCreate(['slug' => $subkat['slug']], $subkat);
        }
        $this->command->info('✅ Kategori & Subkategori seeded');

        // ===============================
        // 🔹 PRODUK - 20 PRODUK KHUSUS MANGGA & AGRO
        // ===============================
        $umkm1 = Umkm::where('nama_toko', 'Kebun Mangga Gedong Gincu')->first();
        $umkm2 = Umkm::where('nama_toko', 'Sentra Olahan Mangga Indramayu')->first();
        $umkm3 = Umkm::where('nama_toko', 'Agro Pelem Super Dermayu')->first();
        $umkm4 = Umkm::where('nama_toko', 'Bibit & Perkebunan Mangga Nusantara')->first();

        $subGedong = KategoriProduk::where('slug', 'mangga-gedong-gincu')->first();
        $subHarumManis = KategoriProduk::where('slug', 'mangga-harum-manis')->first();
        $subCengkir = KategoriProduk::where('slug', 'mangga-cengkir')->first();
        $subDermayu = KategoriProduk::where('slug', 'mangga-dermayu-golek')->first();

        $subDodol = KategoriProduk::where('slug', 'dodol-manisan-mangga')->first();
        $subSirup = KategoriProduk::where('slug', 'sirup-minuman-mangga')->first();
        $subKeripik = KategoriProduk::where('slug', 'keripik-camilan-mangga')->first();
        $subSambal = KategoriProduk::where('slug', 'selai-sambal-mangga')->first();

        $subBibit = KategoriProduk::where('slug', 'bibit-pohon-mangga')->first();
        $subPupuk = KategoriProduk::where('slug', 'pupuk-booster-buah')->first();

        $produks = [
            // 1. Mangga Gedong Gincu Super Grade A
            [
                'nama' => 'Mangga Gedong Gincu Super Grade A (1 Kg)',
                'deskripsi' => 'Mangga Gedong Gincu khas Indramayu matang pohon dengan aroma wangi semerbak dan rasa manis legit. Kulit buah merona kemerahan alami. Garansi segar 24 jam langsung dari pohon.',
                'harga' => 45000,
                'gambar' => 'produks/mangga-gedong-gincu.jpg',
                'user_id' => $umkm1->user_id,
                'stok' => 150,
                'rating' => 4.9,
                'kategori_produk_id' => $subGedong->id,
                'umkm_id' => $umkm1->id,
            ],
            // 2. Mangga Harum Manis Matang Pohon
            [
                'nama' => 'Mangga Harum Manis Matang Pohon (1 Kg)',
                'deskripsi' => 'Mangga Harum Manis segar dengan daging tebal tanpa serat kasar. Rasa manis madu yang pekat dan menyegarkan. Dipetik tepat saat tingkat kematangan optimal.',
                'harga' => 35000,
                'gambar' => 'produks/mangga-harum-manis.jpg',
                'user_id' => $umkm1->user_id,
                'stok' => 200,
                'rating' => 4.8,
                'kategori_produk_id' => $subHarumManis->id,
                'umkm_id' => $umkm1->id,
            ],
            // 3. Mangga Cengkir Indramayu Renyah Manis
            [
                'nama' => 'Mangga Cengkir Indramayu Renyah Manis (1 Kg)',
                'deskripsi' => 'Mangga Cengkir (Dermayu) dengan tekstur daging buah renyah, padat, dan rasa manis segar yang tahan lama. Sangat cocok dinikmati langsung atau untuk rujak buah.',
                'harga' => 28000,
                'gambar' => 'produks/mangga-cengkir.jpg',
                'user_id' => $umkm3->user_id,
                'stok' => 180,
                'rating' => 4.7,
                'kategori_produk_id' => $subCengkir->id,
                'umkm_id' => $umkm3->id,
            ],
            // 4. Mangga Dermayu Fresh Asli Petani
            [
                'nama' => 'Mangga Dermayu Fresh Asli Petani (1 Kg)',
                'deskripsi' => 'Mangga lokal asli Indramayu dengan aroma khas dan kadar air melimpah. Ditanam secara organik di lahan subur agrikultur Indramayu.',
                'harga' => 30000,
                'gambar' => 'produks/mangga-dermayu.jpg',
                'user_id' => $umkm3->user_id,
                'stok' => 120,
                'rating' => 4.6,
                'kategori_produk_id' => $subDermayu->id,
                'umkm_id' => $umkm3->id,
            ],
            // 5. Mangga Golek Manis Jumbo Indramayu
            [
                'nama' => 'Mangga Golek Manis Jumbo Indramayu (1 Kg)',
                'deskripsi' => 'Mangga Golek berbentuk lonjong panjang dengan bobot buah besar. Daging buah sangat tebal, biji tipis, manis berair nikmat.',
                'harga' => 32000,
                'gambar' => 'produks/mangga-golek.jpg',
                'user_id' => $umkm3->user_id,
                'stok' => 100,
                'rating' => 4.7,
                'kategori_produk_id' => $subDermayu->id,
                'umkm_id' => $umkm3->id,
            ],
            // 6. Dodol Mangga Gedong Gincu Premium
            [
                'nama' => 'Dodol Mangga Gedong Gincu Premium (250g)',
                'deskripsi' => 'Dodol manis legit dengan kandungan 70% sari buah mangga gedong gincu asli Indramayu. Dibuat secara higienis tanpa pemanis buatan dalam kemasan eksklusif.',
                'harga' => 25000,
                'gambar' => 'produks/dodol-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 300,
                'rating' => 4.9,
                'kategori_produk_id' => $subDodol->id,
                'umkm_id' => $umkm2->id,
            ],
            // 7. Sirup Mangga Gedong Gincu Premium
            [
                'nama' => 'Sirup Mangga Gedong Gincu Botol Kaca (500ml)',
                'deskripsi' => 'Konsentrat sirup mangga murni dengan rasa manis dan asam segar alami buah mangga gedong gincu. Sangat nikmat disajikan dingin untuk keluarga.',
                'harga' => 38000,
                'gambar' => 'produks/sirup-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 250,
                'rating' => 4.8,
                'kategori_produk_id' => $subSirup->id,
                'umkm_id' => $umkm2->id,
            ],
            // 8. Manisan Mangga Segar Pedas Manis
            [
                'nama' => 'Manisan Mangga Segar Pedas Manis (300g)',
                'deskripsi' => 'Manisan basah buah mangga muda pilihan dengan kuah racikan cabai segar, gula tebu asli, dan jeruk limau. Segar pedas bikin melek!',
                'harga' => 22000,
                'gambar' => 'produks/manisan-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 180,
                'rating' => 4.6,
                'kategori_produk_id' => $subDodol->id,
                'umkm_id' => $umkm2->id,
            ],
            // 9. Keripik Mangga Kering Vacuum Fried
            [
                'nama' => 'Keripik Mangga Kering Vacuum Fried (100g)',
                'deskripsi' => 'Camilan keripik mangga renyah yang diproses menggunakan teknologi penggorengan hampa udara (vacuum frying) sehingga nutrisi dan rasa aslinya tetap terjaga.',
                'harga' => 20000,
                'gambar' => 'produks/keripik-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 220,
                'rating' => 4.7,
                'kategori_produk_id' => $subKeripik->id,
                'umkm_id' => $umkm2->id,
            ],
            // 10. Jus Mangga Gedong Kemasan Botol
            [
                'nama' => 'Jus Mangga Gedong Kemasan Botol (250ml)',
                'deskripsi' => 'Minuman jus buah mangga siap minum dari sari mangga gedong gincu segar. Tanpa pewarna sintetik dan kaya vitamin C.',
                'harga' => 12000,
                'gambar' => 'produks/jus-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 350,
                'rating' => 4.8,
                'kategori_produk_id' => $subSirup->id,
                'umkm_id' => $umkm2->id,
            ],
            // 11. Selai Mangga Homemade Jar Premium
            [
                'nama' => 'Selai Mangga Homemade Jar (200g)',
                'deskripsi' => 'Selai olesan roti berbahan mangga matang pilihan dengan bulir daging mangga asli. Cocok untuk sarapan roti panggang, isian kue, atau pancake.',
                'harga' => 28000,
                'gambar' => 'produks/selai-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 140,
                'rating' => 4.7,
                'kategori_produk_id' => $subSambal->id,
                'umkm_id' => $umkm2->id,
            ],
            // 12. Sambal Mangga Pedas Gurih Khas Indramayu
            [
                'nama' => 'Sambal Mangga Pedas Gurih Khas Indramayu (150g)',
                'deskripsi' => 'Sambal ulek khas pesisir Indramayu dengan cacahan mangga muda segar dan terasi bakar. Paduan pedas, asam segar, dan gurih yang menggugah selera.',
                'harga' => 24000,
                'gambar' => 'produks/sambal-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 160,
                'rating' => 4.8,
                'kategori_produk_id' => $subSambal->id,
                'umkm_id' => $umkm2->id,
            ],
            // 13. Puding Jelly Mangga Gedong Cup
            [
                'nama' => 'Puding Jelly Mangga Gedong Cup (Isi 6)',
                'deskripsi' => 'Dessert puding lembut dengan ekstrak buah mangga gedong gincu dan vla susu vanilla lembut. Disajikan dalam cup praktis higienis.',
                'harga' => 30000,
                'gambar' => 'produks/puding-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 110,
                'rating' => 4.5,
                'kategori_produk_id' => $subDodol->id,
                'umkm_id' => $umkm2->id,
            ],
            // 14. Nektar Sari Buah Mangga Murni
            [
                'nama' => 'Nektar Sari Buah Mangga Murni (1 Liter)',
                'deskripsi' => 'Konsentrat nektar sari buah mangga murni kemasan 1 Liter untuk kebutuhan resto, cafe, atau konsumsi keluarga berkelas.',
                'harga' => 65000,
                'gambar' => 'produks/nektar-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 90,
                'rating' => 4.9,
                'kategori_produk_id' => $subSirup->id,
                'umkm_id' => $umkm2->id,
            ],
            // 15. Puree Mangga Gedong Gincu Frozen
            [
                'nama' => 'Puree Mangga Gedong Gincu Frozen (1 Kg)',
                'deskripsi' => 'Bubur buah mangga murni tanpa biji dan kulit yang dibekukan cepat untuk menjaga kesegaran. Sangat cocok untuk bahan baku es krim, bakery, dan smoothies.',
                'harga' => 50000,
                'gambar' => 'produks/puree-mangga.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 85,
                'rating' => 4.7,
                'kategori_produk_id' => $subSirup->id,
                'umkm_id' => $umkm2->id,
            ],
            // 16. Paket Hampers Mangga Gedong Gincu Eksklusif
            [
                'nama' => 'Paket Hampers Mangga Gedong Gincu Eksklusif (3 Kg)',
                'deskripsi' => 'Paket gift box premium berisi 3 Kg Mangga Gedong Gincu Grade A pilihan dengan box kayu anyaman khas Indramayu dan kartu ucapan. Cocok untuk hadiah keluarga dan relasi bisnis.',
                'harga' => 150000,
                'gambar' => 'produks/hampers-mangga.jpg',
                'user_id' => $umkm1->user_id,
                'stok' => 40,
                'rating' => 5.0,
                'kategori_produk_id' => $subGedong->id,
                'umkm_id' => $umkm1->id,
            ],
            // 17. Bibit Pohon Mangga Gedong Gincu Okulasi
            [
                'nama' => 'Bibit Pohon Mangga Gedong Gincu Okulasi',
                'deskripsi' => 'Bibit tanaman pohon mangga gedong gincu hasil okulasi indukan unggul bersertifikat. Cepat berbuah dalam 2-3 tahun dan cocok untuk tabulampot maupun kebun.',
                'harga' => 45000,
                'gambar' => 'produks/bibit-gedong-gincu.jpg',
                'user_id' => $umkm4->user_id,
                'stok' => 75,
                'rating' => 4.8,
                'kategori_produk_id' => $subBibit->id,
                'umkm_id' => $umkm4->id,
            ],
            // 18. Bibit Pohon Mangga Harum Manis Cangkok
            [
                'nama' => 'Bibit Pohon Mangga Harum Manis Cangkok Unggul',
                'deskripsi' => 'Bibit mangga harum manis hasil perbanyakan cangkokan dengan perakaran kuat. Batang kokoh dan tahan terhadap hama cuaca tropis.',
                'harga' => 40000,
                'gambar' => 'produks/bibit-harum-manis.jpg',
                'user_id' => $umkm4->user_id,
                'stok' => 60,
                'rating' => 4.7,
                'kategori_produk_id' => $subBibit->id,
                'umkm_id' => $umkm4->id,
            ],
            // 19. Pupuk Organik Booster Pembesar Buah Mangga
            [
                'nama' => 'Pupuk Organik Booster Pembesar Buah Mangga (1 Kg)',
                'deskripsi' => 'Pupuk khusus pembungaan dan pembuahan tanaman mangga yang kaya kalium dan asam amino organik. Mencegah kerontokan bunga dan meningkatkan kadar kemanisan buah.',
                'harga' => 35000,
                'gambar' => 'produks/pupuk-booster-mangga.jpg',
                'user_id' => $umkm4->user_id,
                'stok' => 150,
                'rating' => 4.8,
                'kategori_produk_id' => $subPupuk->id,
                'umkm_id' => $umkm4->id,
            ],
            // 20. Nutrisi Organik Cair Pelebat Bunga & Buah
            [
                'nama' => 'Nutrisi Organik Cair Pelebat Bunga & Buah (500ml)',
                'deskripsi' => 'Formula nutrisi spray daun organik untuk memacu pertumbuhan tunas bunga mangga di luar musim (off-season) secara alami dan aman bagi lingkungan.',
                'harga' => 48000,
                'gambar' => 'produks/nutrisi-organik-mangga.jpg',
                'user_id' => $umkm4->user_id,
                'stok' => 130,
                'rating' => 4.9,
                'kategori_produk_id' => $subPupuk->id,
                'umkm_id' => $umkm4->id,
            ],
        ];

        foreach ($produks as $produk) {
            Produk::updateOrCreate(['nama' => $produk['nama']], $produk);
        }
        $this->command->info('✅ 20 Produk Mangga & Agro seeded dengan subkategori');

        // ===============================
        // 🔹 PROMO DISKON
        // ===============================
        $diskonData = [
            [
                'nama_produk' => 'Mangga Gedong Gincu Super Grade A (1 Kg)',
                'persen' => 10,
                'mulai' => Carbon::now()->subDays(2)->toDateString(),
                'selesai' => Carbon::now()->addDays(5)->toDateString(),
            ],
            [
                'nama_produk' => 'Sirup Mangga Gedong Gincu Botol Kaca (500ml)',
                'persen' => 15,
                'mulai' => Carbon::now()->toDateString(),
                'selesai' => Carbon::now()->addDays(7)->toDateString(),
            ],
            [
                'nama_produk' => 'Keripik Mangga Kering Vacuum Fried (100g)',
                'persen' => 20,
                'mulai' => Carbon::now()->subDays(1)->toDateString(),
                'selesai' => Carbon::now()->addDays(3)->toDateString(),
            ],
            [
                'nama_produk' => 'Paket Hampers Mangga Gedong Gincu Eksklusif (3 Kg)',
                'persen' => 15,
                'mulai' => Carbon::now()->toDateString(),
                'selesai' => Carbon::now()->addDays(10)->toDateString(),
            ],
            [
                'nama_produk' => 'Pupuk Organik Booster Pembesar Buah Mangga (1 Kg)',
                'persen' => 25,
                'mulai' => Carbon::now()->subDays(3)->toDateString(),
                'selesai' => Carbon::now()->addDays(4)->toDateString(),
            ],
        ];

        foreach ($diskonData as $d) {
            $p = Produk::where('nama', $d['nama_produk'])->first();
            if ($p) {
                Diskon::updateOrCreate(
                    ['produks_id' => $p->id],
                    [
                        'produks_id' => $p->id,
                        'persen_diskon' => $d['persen'],
                        'tanggal_mulai' => $d['mulai'],
                        'tanggal_berakhir' => $d['selesai'],
                    ]
                );
            }
        }
        $this->command->info('✅ Diskon promo seeded');

        // ===============================
        // 🔹 ORDERS SUCCESS & PENDING
        // ===============================
        $pembeli1 = User::where('email', 'pembeli@gmail.com')->first();
        $pembeli2 = User::where('email', 'budi@gmail.com')->first();
        $pembeli3 = User::where('email', 'sari@gmail.com')->first();
        $pembeli4 = User::where('email', 'dewi@gmail.com')->first();
        $pembeli5 = User::where('email', 'rudi@gmail.com')->first();

        $produkList = Produk::all();

        $successOrders = [
            [
                'user_id' => $pembeli1->id,
                'produk_id' => $produkList[0]->id, // Gedong Gincu - 15 kg
                'name' => $pembeli1->name,
                'alamat' => 'Jl. Sudirman No. 50, Surabaya',
                'phone' => '081212345678',
                'jumlah' => 15,
                'total_harga' => $produkList[0]->harga * 15,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(20),
            ],
            [
                'user_id' => $pembeli2->id,
                'produk_id' => $produkList[6]->id, // Sirup Mangga - 12 botol
                'name' => $pembeli2->name,
                'alamat' => 'Jl. Gatot Subroto No. 25, Jakarta',
                'phone' => '081312345679',
                'jumlah' => 12,
                'total_harga' => $produkList[6]->harga * 12,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(18),
            ],
            [
                'user_id' => $pembeli3->id,
                'produk_id' => $produkList[5]->id, // Dodol Mangga - 20 pcs
                'name' => $pembeli3->name,
                'alamat' => 'Jl. Asia Afrika No. 100, Bandung',
                'phone' => '081412345680',
                'jumlah' => 20,
                'total_harga' => $produkList[5]->harga * 20,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'user_id' => $pembeli4->id,
                'produk_id' => $produkList[8]->id, // Keripik Mangga - 25 pcs
                'name' => $pembeli4->name,
                'alamat' => 'Jl. Thamrin No. 75, Medan',
                'phone' => '081512345681',
                'jumlah' => 25,
                'total_harga' => $produkList[8]->harga * 25,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(12),
            ],
            [
                'user_id' => $pembeli5->id,
                'produk_id' => $produkList[7]->id, // Manisan Mangga - 18 pcs
                'name' => $pembeli5->name,
                'alamat' => 'Jl. Diponegoro No. 30, Yogyakarta',
                'phone' => '081612345682',
                'jumlah' => 18,
                'total_harga' => $produkList[7]->harga * 18,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(10),
            ],
            [
                'user_id' => $pembeli1->id,
                'produk_id' => $produkList[1]->id, // Harum Manis - 5 kg
                'name' => $pembeli1->name,
                'alamat' => 'Jl. Sudirman No. 50, Surabaya',
                'phone' => '081212345678',
                'jumlah' => 5,
                'total_harga' => $produkList[1]->harga * 5,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(8),
            ],
            [
                'user_id' => $pembeli2->id,
                'produk_id' => $produkList[15]->id, // Hampers Mangga - 2 box
                'name' => $pembeli2->name,
                'alamat' => 'Jl. Gatot Subroto No. 25, Jakarta',
                'phone' => '081312345679',
                'jumlah' => 2,
                'total_harga' => $produkList[15]->harga * 2,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'user_id' => $pembeli3->id,
                'produk_id' => $produkList[11]->id, // Sambal Mangga - 6 jar
                'name' => $pembeli3->name,
                'alamat' => 'Jl. Asia Afrika No. 100, Bandung',
                'phone' => '081412345680',
                'jumlah' => 6,
                'total_harga' => $produkList[11]->harga * 6,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(6),
            ],
            [
                'user_id' => $pembeli4->id,
                'produk_id' => $produkList[9]->id, // Jus Mangga - 10 botol
                'name' => $pembeli4->name,
                'alamat' => 'Jl. Thamrin No. 75, Medan',
                'phone' => '081512345681',
                'jumlah' => 10,
                'total_harga' => $produkList[9]->harga * 10,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => $pembeli5->id,
                'produk_id' => $produkList[16]->id, // Bibit Gedong Gincu - 3 pohon
                'name' => $pembeli5->name,
                'alamat' => 'Jl. Diponegoro No. 30, Yogyakarta',
                'phone' => '081612345682',
                'jumlah' => 3,
                'total_harga' => $produkList[16]->harga * 3,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(4),
            ],
        ];

        foreach ($successOrders as $orderData) {
            Order::create($orderData);
        }

        $this->command->info('✅ Orders seeded (10 success)');

        // ===============================
        // 🔹 ULASAN (Review)
        // ===============================
        $completedOrders = Order::where('status', 'complete')->get();

        $ulasanData = [
            [
                'orders_id' => $completedOrders[0]->id,
                'users_id' => $completedOrders[0]->user_id,
                'produks_id' => $completedOrders[0]->produk_id,
                'bintang' => 5,
                'ulasan' => 'Mangga gedong gincunya luar biasa wangi dan manis! Kulit buahnya mulus dan matang sempurna. Pesan 15 Kg untuk acara arisan, semua tamu memuji!',
            ],
            [
                'orders_id' => $completedOrders[1]->id,
                'users_id' => $completedOrders[1]->user_id,
                'produks_id' => $completedOrders[1]->produk_id,
                'bintang' => 5,
                'ulasan' => 'Sirup mangganya segar dan kental alami, rasa manis dan aroma gedong gincunya sangat terasa. Wajib stok di rumah!',
            ],
            [
                'orders_id' => $completedOrders[2]->id,
                'users_id' => $completedOrders[2]->user_id,
                'produks_id' => $completedOrders[2]->produk_id,
                'bintang' => 5,
                'ulasan' => 'Dodol mangganya legit dan tidak serik di tenggorokan. Rasa mangganya asli bukan perisa buatan. Recommended!',
            ],
            [
                'orders_id' => $completedOrders[3]->id,
                'users_id' => $completedOrders[3]->user_id,
                'produks_id' => $completedOrders[3]->produk_id,
                'bintang' => 5,
                'ulasan' => 'Keripik mangganya renyah banget dan manis alaminya terasa. Anak-anak suka sekali jadi camilan sehat.',
            ],
            [
                'orders_id' => $completedOrders[4]->id,
                'users_id' => $completedOrders[4]->user_id,
                'produks_id' => $completedOrders[4]->produk_id,
                'bintang' => 4,
                'ulasan' => 'Manisan mangga kuah pedas manisnya mantap! Segar banget dimakan dingin-dingin siang hari.',
            ],
            [
                'orders_id' => $completedOrders[5]->id,
                'users_id' => $completedOrders[5]->user_id,
                'produks_id' => $completedOrders[5]->produk_id,
                'bintang' => 5,
                'ulasan' => 'Mangga Harum Manisnya manis pol tanpa serat. Dagingnya tebal dan bijinya tipis. Puas belanja di Juragan Pelem!',
            ],
            [
                'orders_id' => $completedOrders[6]->id,
                'users_id' => $completedOrders[6]->user_id,
                'produks_id' => $completedOrders[6]->produk_id,
                'bintang' => 5,
                'ulasan' => 'Hampers box kayunya sangat mewah dan buah mangganya kualitas premium pilihan. Sangat pas untuk kiriman bingkisan direksi.',
            ],
            [
                'orders_id' => $completedOrders[7]->id,
                'users_id' => $completedOrders[7]->user_id,
                'produks_id' => $completedOrders[7]->produk_id,
                'bintang' => 5,
                'ulasan' => 'Sambal mangganya pedas gurih mantap! Paduan mangga muda dan terasinya juara banget dimakan sama ikan bakar.',
            ],
            [
                'orders_id' => $completedOrders[8]->id,
                'users_id' => $completedOrders[8]->user_id,
                'produks_id' => $completedOrders[8]->produk_id,
                'bintang' => 5,
                'ulasan' => 'Jus mangganya murni dan fresh banget! Dingin-dingin langsung habis 2 botol sekaligus.',
            ],
            [
                'orders_id' => $completedOrders[9]->id,
                'users_id' => $completedOrders[9]->user_id,
                'produks_id' => $completedOrders[9]->produk_id,
                'bintang' => 5,
                'ulasan' => 'Bibit pohon mangganya sampai dengan kondisi segar dan daun rimbun. Batang okulasi sudah kuat dan siap tanam di pekarangan.',
            ],
        ];

        foreach ($ulasanData as $ulasan) {
            Ulasan::updateOrCreate(['orders_id' => $ulasan['orders_id']], $ulasan);
        }
        $this->command->info('✅ Ulasans seeded');

        // ===============================
        // 🔹 CHATS
        // ===============================
        $chatSamples = [
            [
                'sender_id' => $pembeli1->id,
                'receiver_id' => $umkm1->user_id,
                'umkm_id' => $umkm1->id,
                'message' => 'Halo Pak Haji, mangga gedong gincu yang Grade A panenan hari ini masih ready?',
                'is_ai' => 0,
                'is_read' => 1,
                'created_at' => now()->subDays(3),
            ],
            [
                'sender_id' => $umkm1->user_id,
                'receiver_id' => $pembeli1->id,
                'umkm_id' => $umkm1->id,
                'message' => 'Halo! Masih ready baru petik pagi ini segar-segar. Siap dikirim hari ini juga kak.',
                'is_ai' => 0,
                'is_read' => 1,
                'created_at' => now()->subDays(3)->addMinutes(5),
            ],
            [
                'sender_id' => $pembeli2->id,
                'receiver_id' => $umkm2->user_id,
                'umkm_id' => $umkm2->id,
                'message' => 'Siang Bu Siti, sirup mangga dan dodolnya ada promo pembelian grosir untuk toko oleh-oleh?',
                'is_ai' => 0,
                'is_read' => 1,
                'created_at' => now()->subDays(2),
            ],
            [
                'sender_id' => $umkm2->user_id,
                'receiver_id' => $pembeli2->id,
                'umkm_id' => $umkm2->id,
                'message' => 'Siang kak! Ada diskon 15% untuk pemesanan minimal 10 botol ya kak. Silakan langsung checkout di katalog.',
                'is_ai' => 0,
                'is_read' => 1,
                'created_at' => now()->subDays(2)->addMinutes(4),
            ],
        ];

        foreach ($chatSamples as $chat) {
            Chat::create($chat);
        }
        $this->command->info('✅ Chats seeded');

        $this->command->info('🎉 Semua data produk mangga & UMKM Indramayu berhasil di-seed!');
    }
}

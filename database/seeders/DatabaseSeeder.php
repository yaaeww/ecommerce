<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
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
    public function run(): void
    {
        // ===============================
        // 🔹 USERS
        // ===============================
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'admin',
                'avatar' => 'storage/avatars/admin.png',
            ],
            [
                'name' => 'Penjual',
                'email' => 'penjual@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'penjual',
                'avatar' => 'storage/avatars/penjual.png',
            ],
            [
                'name' => 'Jo',
                'email' => 'jo@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'penjual',
                'avatar' => 'storage/avatars/jo.png',
            ],
            [
                'name' => 'Pembeli',
                'email' => 'pembeli@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'pembeli',
                'avatar' => 'storage/avatars/pembeli.png',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'pembeli',
                'avatar' => 'storage/avatars/budi.png',
            ],
            [
                'name' => 'Sari Wijaya',
                'email' => 'sari@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'penjual',
                'avatar' => 'storage/avatars/sari.png',
            ],
            [
                'name' => 'Ahmad Fauzi',
                'email' => 'ahmad@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'penjual',
                'avatar' => 'storage/avatars/ahmad.png',
            ],
            [
                'name' => 'Dewi Lestari',
                'email' => 'dewi@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'pembeli',
                'avatar' => 'storage/avatars/dewi.png',
            ],
            [
                'name' => 'Rudi Hermawan',
                'email' => 'rudi@gmail.com',
                'password' => Hash::make('11111111'),
                'role' => 'pembeli',
                'avatar' => 'storage/avatars/rudi.png',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(['email' => $user['email']], $user);
        }
        $this->command->info('✅ Users seeded');

        // ===============================
        // 🔹 UMKM
        // ===============================
        $umkms = [
            [
                'user_id' => 2, // Penjual
                'nama_toko' => 'Toko Sukses Jaya',
                'deskripsi' => 'Menjual berbagai produk lokal berkualitas tinggi dengan bahan-bahan fresh dari Indramayu. Spesialis makanan ringan tradisional.',
                'alamat' => 'Jl. Merdeka No. 123, Jakarta',
                'no_telp' => '081234567890',
                'logo' => 'storage/logos/toko-sukses.png',
                'status' => 'approved',
            ],
            [
                'user_id' => 3, // Jo
                'nama_toko' => 'UMKM Jo Craft',
                'deskripsi' => 'Kerajinan tangan kreatif dan unik dengan sentuhan tradisional khas Indramayu. Mengutamakan kualitas dan keunikan desain.',
                'alamat' => 'Jl. Mawar No. 45, Bandung',
                'no_telp' => '082345678901',
                'logo' => 'storage/logos/jo-craft.png',
                'status' => 'approved',
            ],
            [
                'user_id' => 6, // User penjual baru
                'nama_toko' => 'Mangga Indramayu',
                'deskripsi' => 'Spesialis produk olahan mangga gedong gincu khas Indramayu dengan kualitas terbaik. Mengolah mangga lokal menjadi berbagai produk bernilai tinggi.',
                'alamat' => 'Jl. Mangga No. 10, Indramayu',
                'no_telp' => '083456789012',
                'logo' => 'storage/logos/mangga-indramayu.png',
                'status' => 'approved',
            ],
            [
                'user_id' => 7, // Ahmad Fauzi
                'nama_toko' => 'SeaFood Indramayu',
                'deskripsi' => 'Menjual berbagai hasil laut segar dan olahan seafood khas Indramayu. Ikan segar langsung dari nelayan lokal.',
                'alamat' => 'Jl. Pelabuhan No. 5, Indramayu',
                'no_telp' => '084567890123',
                'logo' => 'storage/logos/seafood-indramayu.png',
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
            ['nama' => 'Makanan', 'slug' => 'makanan', 'gambar' => 'storage/kategori/makanan.png', 'parent_id' => null],
            ['nama' => 'Minuman', 'slug' => 'minuman', 'gambar' => 'storage/kategori/minuman.png', 'parent_id' => null],
            ['nama' => 'Kerajinan', 'slug' => 'kerajinan', 'gambar' => 'storage/kategori/kerajinan.png', 'parent_id' => null],
            ['nama' => 'Fashion', 'slug' => 'fashion', 'gambar' => 'storage/kategori/fashion.png', 'parent_id' => null],
            ['nama' => 'Kesehatan', 'slug' => 'kesehatan', 'gambar' => 'storage/kategori/kesehatan.png', 'parent_id' => null],
            ['nama' => 'Seafood', 'slug' => 'seafood', 'gambar' => 'storage/kategori/seafood.png', 'parent_id' => null],
            ['nama' => 'Oleh-oleh', 'slug' => 'oleh-oleh', 'gambar' => 'storage/kategori/oleh-oleh.png', 'parent_id' => null],
        ];

        // Create main categories
        foreach ($kategoriList as $kat) {
            KategoriProduk::updateOrCreate(['slug' => $kat['slug']], $kat);
        }

        // Get main categories for creating subcategories
        $katMakanan = KategoriProduk::where('slug', 'makanan')->first();
        $katMinuman = KategoriProduk::where('slug', 'minuman')->first();
        $katKerajinan = KategoriProduk::where('slug', 'kerajinan')->first();
        $katFashion = KategoriProduk::where('slug', 'fashion')->first();
        $katKesehatan = KategoriProduk::where('slug', 'kesehatan')->first();
        $katSeafood = KategoriProduk::where('slug', 'seafood')->first();
        $katOlehOleh = KategoriProduk::where('slug', 'oleh-oleh')->first();

        // Subkategori untuk setiap kategori utama
        $subkategoriList = [
            // Subkategori Makanan
            ['nama' => 'Makanan Ringan', 'slug' => 'makanan-ringan', 'gambar' => 'storage/kategori/makanan-ringan.png', 'parent_id' => $katMakanan->id],
            ['nama' => 'Makanan Instan', 'slug' => 'makanan-instan', 'gambar' => 'storage/kategori/makanan-instan.png', 'parent_id' => $katMakanan->id],
            ['nama' => 'Kue & Roti', 'slug' => 'kue-roti', 'gambar' => 'storage/kategori/kue-roti.png', 'parent_id' => $katMakanan->id],
            ['nama' => 'Makanan Beku', 'slug' => 'makanan-beku', 'gambar' => 'storage/kategori/makanan-beku.png', 'parent_id' => $katMakanan->id],

            // Subkategori Minuman
            ['nama' => 'Minuman Ringan', 'slug' => 'minuman-ringan', 'gambar' => 'storage/kategori/minuman-ringan.png', 'parent_id' => $katMinuman->id],
            ['nama' => 'Jus & Smoothie', 'slug' => 'jus-smoothie', 'gambar' => 'storage/kategori/jus-smoothie.png', 'parent_id' => $katMinuman->id],
            ['nama' => 'Sirup & Konsentrat', 'slug' => 'sirup-konsentrat', 'gambar' => 'storage/kategori/sirup-konsentrat.png', 'parent_id' => $katMinuman->id],
            ['nama' => 'Minuman Tradisional', 'slug' => 'minuman-tradisional', 'gambar' => 'storage/kategori/minuman-tradisional.png', 'parent_id' => $katMinuman->id],

            // Subkategori Kerajinan
            ['nama' => 'Kerajinan Tangan', 'slug' => 'kerajinan-tangan', 'gambar' => 'storage/kategori/kerajinan-tangan.png', 'parent_id' => $katKerajinan->id],
            ['nama' => 'Aksesoris', 'slug' => 'aksesoris-kerajinan', 'gambar' => 'storage/kategori/aksesoris.png', 'parent_id' => $katKerajinan->id],
            ['nama' => 'Dekorasi Rumah', 'slug' => 'dekorasi-rumah', 'gambar' => 'storage/kategori/dekorasi-rumah.png', 'parent_id' => $katKerajinan->id],
            ['nama' => 'Kerajinan Rajut', 'slug' => 'kerajinan-rajut', 'gambar' => 'storage/kategori/kerajinan-rajut.png', 'parent_id' => $katKerajinan->id],

            // Subkategori Fashion
            ['nama' => 'Pakaian', 'slug' => 'pakaian', 'gambar' => 'storage/kategori/pakaian.png', 'parent_id' => $katFashion->id],
            ['nama' => 'Aksesoris Fashion', 'slug' => 'aksesoris-fashion', 'gambar' => 'storage/kategori/aksesoris-fashion.png', 'parent_id' => $katFashion->id],
            ['nama' => 'Tas & Dompet', 'slug' => 'tas-dompet', 'gambar' => 'storage/kategori/tas-dompet.png', 'parent_id' => $katFashion->id],
            ['nama' => 'Batik', 'slug' => 'batik', 'gambar' => 'storage/kategori/batik.png', 'parent_id' => $katFashion->id],

            // Subkategori Kesehatan
            ['nama' => 'Herbal', 'slug' => 'herbal', 'gambar' => 'storage/kategori/herbal.png', 'parent_id' => $katKesehatan->id],
            ['nama' => 'Suplemen', 'slug' => 'suplemen', 'gambar' => 'storage/kategori/suplemen.png', 'parent_id' => $katKesehatan->id],
            ['nama' => 'Perawatan Tubuh', 'slug' => 'perawatan-tubuh', 'gambar' => 'storage/kategori/perawatan-tubuh.png', 'parent_id' => $katKesehatan->id],

            // Subkategori Seafood
            ['nama' => 'Ikan Segar', 'slug' => 'ikan-segar', 'gambar' => 'storage/kategori/ikan-segar.png', 'parent_id' => $katSeafood->id],
            ['nama' => 'Ikan Olahan', 'slug' => 'ikan-olahan', 'gambar' => 'storage/kategori/ikan-olahan.png', 'parent_id' => $katSeafood->id],
            ['nama' => 'Udang & Cumi', 'slug' => 'udang-cumi', 'gambar' => 'storage/kategori/udang-cumi.png', 'parent_id' => $katSeafood->id],
            ['nama' => 'Kerang & Tiram', 'slug' => 'kerang-tiram', 'gambar' => 'storage/kategori/kerang-tiram.png', 'parent_id' => $katSeafood->id],

            // Subkategori Oleh-oleh
            ['nama' => 'Oleh-oleh Khas', 'slug' => 'oleh-oleh-khas', 'gambar' => 'storage/kategori/oleh-oleh-khas.png', 'parent_id' => $katOlehOleh->id],
            ['nama' => 'Camilan Khas', 'slug' => 'camilan-khas', 'gambar' => 'storage/kategori/camilan-khas.png', 'parent_id' => $katOlehOleh->id],
            ['nama' => 'Kerajinan Souvenir', 'slug' => 'kerajinan-souvenir', 'gambar' => 'storage/kategori/kerajinan-souvenir.png', 'parent_id' => $katOlehOleh->id],
        ];

        foreach ($subkategoriList as $subkat) {
            KategoriProduk::updateOrCreate(['slug' => $subkat['slug']], $subkat);
        }
        $this->command->info('✅ Kategori & Subkategori seeded');

        // ===============================
        // 🔹 PRODUK - 20+ PRODUK dengan SUBKATEGORI
        // ===============================
        $umkm1 = Umkm::where('nama_toko', 'Toko Sukses Jaya')->first();
        $umkm2 = Umkm::where('nama_toko', 'UMKM Jo Craft')->first();
        $umkm3 = Umkm::where('nama_toko', 'Mangga Indramayu')->first();
        $umkm4 = Umkm::where('nama_toko', 'SeaFood Indramayu')->first();

        // Get subkategori untuk produk
        $subkatMakananRingan = KategoriProduk::where('slug', 'makanan-ringan')->first();
        $subkatKueRoti = KategoriProduk::where('slug', 'kue-roti')->first();
        $subkatOlehOlehKhas = KategoriProduk::where('slug', 'oleh-oleh-khas')->first();
        $subkatCamilanKhas = KategoriProduk::where('slug', 'camilan-khas')->first();

        $subkatKerajinanTangan = KategoriProduk::where('slug', 'kerajinan-tangan')->first();
        $subkatAksesorisKerajinan = KategoriProduk::where('slug', 'aksesoris-kerajinan')->first();
        $subkatDekorasiRumah = KategoriProduk::where('slug', 'dekorasi-rumah')->first();
        $subkatKerajinanRajut = KategoriProduk::where('slug', 'kerajinan-rajut')->first();

        $subkatPakaian = KategoriProduk::where('slug', 'pakaian')->first();
        $subkatAksesorisFashion = KategoriProduk::where('slug', 'aksesoris-fashion')->first();
        $subkatTasDompet = KategoriProduk::where('slug', 'tas-dompet')->first();
        $subkatBatik = KategoriProduk::where('slug', 'batik')->first();

        $subkatMinumanRingan = KategoriProduk::where('slug', 'minuman-ringan')->first();
        $subkatJusSmoothie = KategoriProduk::where('slug', 'jus-smoothie')->first();
        $subkatSirupKonsentrat = KategoriProduk::where('slug', 'sirup-konsentrat')->first();

        $subkatIkanSegar = KategoriProduk::where('slug', 'ikan-segar')->first();
        $subkatIkanOlahan = KategoriProduk::where('slug', 'ikan-olahan')->first();
        $subkatUdangCumi = KategoriProduk::where('slug', 'udang-cumi')->first();

        $produks = [
            // Produk UMKM 1 - Toko Sukses Jaya (Makanan & Oleh-oleh)
            [
                'nama' => 'Keripik Pisang Manis',
                'deskripsi' => 'Cemilan renyah khas UMKM lokal dengan rasa manis alami tanpa pengawet. Dibuat dari pisang pilihan.',
                'harga' => 15000,
                'gambar' => 'storage/produks/keripik-pisang.png',
                'user_id' => $umkm1->user_id,
                'stok' => 200,
                'rating' => 4.7,
                'kategori_produk_id' => $subkatMakananRingan->id,
                'umkm_id' => $umkm1->id,
            ],
            [
                'nama' => 'Dodol Mangga Premium',
                'deskripsi' => 'Dodol manis dengan rasa mangga gedong gincu asli Indramayu, tekstur lembut dan legit. Kemasan eksklusif.',
                'harga' => 35000,
                'gambar' => 'storage/produks/dodol-mangga.jpg',
                'user_id' => $umkm1->user_id,
                'stok' => 120,
                'rating' => 4.8,
                'kategori_produk_id' => $subkatOlehOlehKhas->id,
                'umkm_id' => $umkm1->id,
            ],
            [
                'nama' => 'Kerupuk Ikan Tenggiri',
                'deskripsi' => 'Kerupuk ikan khas Indramayu dengan rasa gurih dan renyah, dibuat dari ikan tenggiri segar pilihan.',
                'harga' => 18000,
                'gambar' => 'storage/produks/kerupuk-ikan.jpg',
                'user_id' => $umkm1->user_id,
                'stok' => 300,
                'rating' => 4.5,
                'kategori_produk_id' => $subkatMakananRingan->id,
                'umkm_id' => $umkm1->id,
            ],
            [
                'nama' => 'Kue Lumpur Indramayu',
                'deskripsi' => 'Kue lumpur tradisional dengan topping kismis dan keju. Lembut dan manisnya pas.',
                'harga' => 12000,
                'gambar' => 'storage/produks/kue-lumpur.jpg',
                'user_id' => $umkm1->user_id,
                'stok' => 80,
                'rating' => 4.6,
                'kategori_produk_id' => $subkatKueRoti->id,
                'umkm_id' => $umkm1->id,
            ],
            [
                'nama' => 'Rempeyek Kacang',
                'deskripsi' => 'Rempeyek kacang tanah renyah dengan bumbu rempah khas. Cocok untuk lauk atau camilan.',
                'harga' => 14000,
                'gambar' => 'storage/produks/rempeyek.jpg',
                'user_id' => $umkm1->user_id,
                'stok' => 150,
                'rating' => 4.4,
                'kategori_produk_id' => $subkatCamilanKhas->id,
                'umkm_id' => $umkm1->id,
            ],

            // Produk UMKM 2 - UMKM Jo Craft (Kerajinan & Fashion)
            [
                'nama' => 'Tas Rajut Handmade Premium',
                'deskripsi' => 'Kerajinan tangan buatan Jo Craft dengan desain modern dan bahan berkualitas tinggi. Tahan lama dan stylish.',
                'harga' => 125000,
                'gambar' => 'storage/produks/tasrajut.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 45,
                'rating' => 4.9,
                'kategori_produk_id' => $subkatTasDompet->id,
                'umkm_id' => $umkm2->id,
            ],
            [
                'nama' => 'Batik Complongan Modern',
                'deskripsi' => 'Kain batik khas Indramayu dengan motif complongan yang elegan dan warna natural. Bahan katun premium.',
                'harga' => 185000,
                'gambar' => 'storage/produks/batik-complongan.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 30,
                'rating' => 4.7,
                'kategori_produk_id' => $subkatBatik->id,
                'umkm_id' => $umkm2->id,
            ],
            [
                'nama' => 'Dompet Rajut Seri Minimalis',
                'deskripsi' => 'Dompet rajut handmade dengan desain minimalis, berbagai pilihan warna, praktis dan stylish.',
                'harga' => 55000,
                'gambar' => 'storage/produks/dompet-rajut.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 75,
                'rating' => 4.5,
                'kategori_produk_id' => $subkatAksesorisFashion->id,
                'umkm_id' => $umkm2->id,
            ],
            [
                'nama' => 'Gantungan Kunci Rajut',
                'deskripsi' => 'Gantungan kunci rajut dengan karakter lucu khas Indramayu. Cocok untuk buah tangan.',
                'harga' => 25000,
                'gambar' => 'storage/produks/gantungan-kunci.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 200,
                'rating' => 4.3,
                'kategori_produk_id' => $subkatAksesorisKerajinan->id,
                'umkm_id' => $umkm2->id,
            ],
            [
                'nama' => 'Sarung Bantal Batik',
                'deskripsi' => 'Sarung bantal dengan motif batik complongan. Mempercantik interior rumah dengan sentuhan tradisional.',
                'harga' => 45000,
                'gambar' => 'storage/produks/sarung-bantal.jpg',
                'user_id' => $umkm2->user_id,
                'stok' => 60,
                'rating' => 4.6,
                'kategori_produk_id' => $subkatDekorasiRumah->id,
                'umkm_id' => $umkm2->id,
            ],

            // Produk UMKM 3 - Mangga Indramayu (Makanan & Minuman)
            [
                'nama' => 'Sirup Mangga Gedong Premium',
                'deskripsi' => 'Sirup mangga gedong gincu asli Indramayu, tanpa pengawet dan pewarna buatan. Rasa manis alami.',
                'harga' => 45000,
                'gambar' => 'storage/produks/sirup-mangga.jpg',
                'user_id' => $umkm3->user_id,
                'stok' => 150,
                'rating' => 4.8,
                'kategori_produk_id' => $subkatSirupKonsentrat->id,
                'umkm_id' => $umkm3->id,
            ],
            [
                'nama' => 'Manisan Mangga Pedas',
                'deskripsi' => 'Manisan mangga gedong gincu dengan rasa manis asam pedas yang segar, cocok untuk cemilan.',
                'harga' => 32000,
                'gambar' => 'storage/produks/manisan-mangga.jpg',
                'user_id' => $umkm3->user_id,
                'stok' => 120,
                'rating' => 4.7,
                'kategori_produk_id' => $subkatCamilanKhas->id,
                'umkm_id' => $umkm3->id,
            ],
            [
                'nama' => 'Jus Mangga Kemasan Premium',
                'deskripsi' => 'Jus mangga segar dalam kemasan botol glass, siap minum dan sehat tanpa bahan pengawet.',
                'harga' => 20000,
                'gambar' => 'storage/produks/jus-mangga.jpg',
                'user_id' => $umkm3->user_id,
                'stok' => 180,
                'rating' => 4.5,
                'kategori_produk_id' => $subkatJusSmoothie->id,
                'umkm_id' => $umkm3->id,
            ],
            [
                'nama' => 'Selai Mangga Homemade',
                'deskripsi' => 'Selai mangga homemade dengan rasa manis alami. Cocok untuk roti, pancake, atau dessert.',
                'harga' => 28000,
                'gambar' => 'storage/produks/selai-mangga.jpg',
                'user_id' => $umkm3->user_id,
                'stok' => 90,
                'rating' => 4.6,
                'kategori_produk_id' => $subkatMakananRingan->id,
                'umkm_id' => $umkm3->id,
            ],
            [
                'nama' => 'Kripik Mangga',
                'deskripsi' => 'Keripik mangga dengan rasa manis asam yang unik. Cemilan sehat dan rendah kalori.',
                'harga' => 22000,
                'gambar' => 'storage/produks/keripik-mangga.jpg',
                'user_id' => $umkm3->user_id,
                'stok' => 110,
                'rating' => 4.4,
                'kategori_produk_id' => $subkatMakananRingan->id,
                'umkm_id' => $umkm3->id,
            ],

            // Produk UMKM 4 - SeaFood Indramayu (Seafood)
            [
                'nama' => 'Ikan Bandeng Asap',
                'deskripsi' => 'Ikan bandeng asap khas Indramayu dengan proses pengasapan tradisional. Rasanya gurih dan nikmat.',
                'harga' => 55000,
                'gambar' => 'storage/produks/bandeng-asap.jpg',
                'user_id' => $umkm4->user_id,
                'stok' => 40,
                'rating' => 4.8,
                'kategori_produk_id' => $subkatIkanOlahan->id,
                'umkm_id' => $umkm4->id,
            ],
            [
                'nama' => 'Terasi Indramayu Premium',
                'deskripsi' => 'Terasi khas Indramayu dengan kualitas premium. Dibuat dari udang segar pilihan.',
                'harga' => 25000,
                'gambar' => 'storage/produks/terasi.jpg',
                'user_id' => $umkm4->user_id,
                'stok' => 100,
                'rating' => 4.6,
                'kategori_produk_id' => $subkatIkanOlahan->id,
                'umkm_id' => $umkm4->id,
            ],
            [
                'nama' => 'Ikan Asin Jambal Roti',
                'deskripsi' => 'Ikan asin jambal roti dengan tekstur renyah dan rasa gurih. Cocok untuk lauk sehari-hari.',
                'harga' => 35000,
                'gambar' => 'storage/produks/ikan-asin.jpg',
                'user_id' => $umkm4->user_id,
                'stok' => 70,
                'rating' => 4.5,
                'kategori_produk_id' => $subkatIkanOlahan->id,
                'umkm_id' => $umkm4->id,
            ],
            [
                'nama' => 'Udang Rebon Kering',
                'deskripsi' => 'Udang rebon kering berkualitas tinggi. Cocok untuk campuran sambal, pepes, atau tumisan.',
                'harga' => 42000,
                'gambar' => 'storage/produks/udang-rebon.jpg',
                'user_id' => $umkm4->user_id,
                'stok' => 60,
                'rating' => 4.7,
                'kategori_produk_id' => $subkatUdangCumi->id,
                'umkm_id' => $umkm4->id,
            ],
            [
                'nama' => 'Cumi Asin Kualitas Terbaik',
                'deskripsi' => 'Cumi asin dengan kualitas terbaik, ukuran besar dan tebal. Rasanya gurih dan nikmat.',
                'harga' => 68000,
                'gambar' => 'storage/produks/cumi-asin.jpg',
                'user_id' => $umkm4->user_id,
                'stok' => 35,
                'rating' => 4.9,
                'kategori_produk_id' => $subkatUdangCumi->id,
                'umkm_id' => $umkm4->id,
            ],
        ];

        foreach ($produks as $produk) {
            Produk::updateOrCreate(['nama' => $produk['nama']], $produk);
        }
        $this->command->info('✅ Produks seeded dengan subkategori');

        // ===============================
        // 🔹 DISKON
        // ===============================
        $produkDiskon = [
            Produk::where('nama', 'Keripik Pisang Manis')->first(),
            Produk::where('nama', 'Tas Rajut Handmade Premium')->first(),
            Produk::where('nama', 'Sirup Mangga Gedong Premium')->first(),
            Produk::where('nama', 'Ikan Bandeng Asap')->first(),
            Produk::where('nama', 'Gantungan Kunci Rajut')->first(),
        ];

        $diskons = [
            [
                'produks_id' => $produkDiskon[0]->id,
                'persen_diskon' => 15,
                'tanggal_mulai' => Carbon::now()->subDays(2)->toDateString(),
                'tanggal_berakhir' => Carbon::now()->addDays(5)->toDateString(),
            ],
            [
                'produks_id' => $produkDiskon[1]->id,
                'persen_diskon' => 20,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_berakhir' => Carbon::now()->addDays(7)->toDateString(),
            ],
            [
                'produks_id' => $produkDiskon[2]->id,
                'persen_diskon' => 25,
                'tanggal_mulai' => Carbon::now()->subDays(1)->toDateString(),
                'tanggal_berakhir' => Carbon::now()->addDays(3)->toDateString(),
            ],
            [
                'produks_id' => $produkDiskon[3]->id,
                'persen_diskon' => 10,
                'tanggal_mulai' => Carbon::now()->toDateString(),
                'tanggal_berakhir' => Carbon::now()->addDays(10)->toDateString(),
            ],
            [
                'produks_id' => $produkDiskon[4]->id,
                'persen_diskon' => 30,
                'tanggal_mulai' => Carbon::now()->subDays(3)->toDateString(),
                'tanggal_berakhir' => Carbon::now()->addDays(4)->toDateString(),
            ],
        ];

        foreach ($diskons as $diskon) {
            Diskon::updateOrCreate([
                'produks_id' => $diskon['produks_id'],
            ], $diskon);
        }
        $this->command->info('✅ Diskon seeded');

        // ===============================
        // 🔹 ORDER - 15 Success & 10 Pending (dengan beberapa produk terjual >10)
        // ===============================
        $pembeli1 = User::where('email', 'pembeli@gmail.com')->first();
        $pembeli2 = User::where('email', 'budi@gmail.com')->first();
        $pembeli3 = User::where('email', 'sari@gmail.com')->first();
        $pembeli4 = User::where('email', 'dewi@gmail.com')->first();
        $pembeli5 = User::where('email', 'rudi@gmail.com')->first();

        $produkList = Produk::all();

        // Orders Success (15 orders) - Beberapa dengan jumlah >10
        $successOrders = [
            // Produk dengan penjualan tinggi (>10)
            [
                'user_id' => $pembeli1->id,
                'produk_id' => $produkList[0]->id, // Keripik Pisang - 15 pcs
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
                'produk_id' => $produkList[10]->id, // Sirup Mangga - 12 pcs
                'name' => $pembeli2->name,
                'alamat' => 'Jl. Gatot Subroto No. 25, Jakarta',
                'phone' => '081312345679',
                'jumlah' => 12,
                'total_harga' => $produkList[10]->harga * 12,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(18),
            ],
            [
                'user_id' => $pembeli3->id,
                'produk_id' => $produkList[2]->id, // Kerupuk Ikan - 20 pcs
                'name' => $pembeli3->name,
                'alamat' => 'Jl. Asia Afrika No. 100, Bandung',
                'phone' => '081412345680',
                'jumlah' => 20,
                'total_harga' => $produkList[2]->harga * 20,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(15),
            ],
            [
                'user_id' => $pembeli4->id,
                'produk_id' => $produkList[8]->id, // Gantungan Kunci - 25 pcs
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
                'produk_id' => $produkList[11]->id, // Manisan Mangga - 18 pcs
                'name' => $pembeli5->name,
                'alamat' => 'Jl. Diponegoro No. 30, Yogyakarta',
                'phone' => '081612345682',
                'jumlah' => 18,
                'total_harga' => $produkList[11]->harga * 18,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(10),
            ],

            // Orders success reguler
            [
                'user_id' => $pembeli1->id,
                'produk_id' => $produkList[5]->id, // Tas Rajut - 2 pcs
                'name' => $pembeli1->name,
                'alamat' => 'Jl. Sudirman No. 50, Surabaya',
                'phone' => '081212345678',
                'jumlah' => 2,
                'total_harga' => $produkList[5]->harga * 2,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(8),
            ],
            [
                'user_id' => $pembeli2->id,
                'produk_id' => $produkList[6]->id, // Batik Complongan - 1 pcs
                'name' => $pembeli2->name,
                'alamat' => 'Jl. Gatot Subroto No. 25, Jakarta',
                'phone' => '081312345679',
                'jumlah' => 1,
                'total_harga' => $produkList[6]->harga * 1,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(7),
            ],
            [
                'user_id' => $pembeli3->id,
                'produk_id' => $produkList[15]->id, // Ikan Bandeng Asap - 3 pcs
                'name' => $pembeli3->name,
                'alamat' => 'Jl. Asia Afrika No. 100, Bandung',
                'phone' => '081412345680',
                'jumlah' => 3,
                'total_harga' => $produkList[15]->harga * 3,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(6),
            ],
            [
                'user_id' => $pembeli4->id,
                'produk_id' => $produkList[7]->id, // Dompet Rajut - 4 pcs
                'name' => $pembeli4->name,
                'alamat' => 'Jl. Thamrin No. 75, Medan',
                'phone' => '081512345681',
                'jumlah' => 4,
                'total_harga' => $produkList[7]->harga * 4,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(5),
            ],
            [
                'user_id' => $pembeli5->id,
                'produk_id' => $produkList[16]->id, // Terasi - 5 pcs
                'name' => $pembeli5->name,
                'alamat' => 'Jl. Diponegoro No. 30, Yogyakarta',
                'phone' => '081612345682',
                'jumlah' => 5,
                'total_harga' => $produkList[16]->harga * 5,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(4),
            ],
            [
                'user_id' => $pembeli1->id,
                'produk_id' => $produkList[12]->id, // Jus Mangga - 8 pcs
                'name' => $pembeli1->name,
                'alamat' => 'Jl. Sudirman No. 50, Surabaya',
                'phone' => '081212345678',
                'jumlah' => 8,
                'total_harga' => $produkList[12]->harga * 8,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(3),
            ],
            [
                'user_id' => $pembeli2->id,
                'produk_id' => $produkList[17]->id, // Ikan Asin - 6 pcs
                'name' => $pembeli2->name,
                'alamat' => 'Jl. Gatot Subroto No. 25, Jakarta',
                'phone' => '081312345679',
                'jumlah' => 6,
                'total_harga' => $produkList[17]->harga * 6,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $pembeli3->id,
                'produk_id' => $produkList[13]->id, // Selai Mangga - 3 pcs
                'name' => $pembeli3->name,
                'alamat' => 'Jl. Asia Afrika No. 100, Bandung',
                'phone' => '081412345680',
                'jumlah' => 3,
                'total_harga' => $produkList[13]->harga * 3,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(2),
            ],
            [
                'user_id' => $pembeli4->id,
                'produk_id' => $produkList[18]->id, // Udang Rebon - 4 pcs
                'name' => $pembeli4->name,
                'alamat' => 'Jl. Thamrin No. 75, Medan',
                'phone' => '081512345681',
                'jumlah' => 4,
                'total_harga' => $produkList[18]->harga * 4,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(1),
            ],
            [
                'user_id' => $pembeli5->id,
                'produk_id' => $produkList[19]->id, // Cumi Asin - 2 pcs
                'name' => $pembeli5->name,
                'alamat' => 'Jl. Diponegoro No. 30, Yogyakarta',
                'phone' => '081612345682',
                'jumlah' => 2,
                'total_harga' => $produkList[19]->harga * 2,
                'status' => 'complete',
                'status_pesanan' => 'diterima',
                'created_at' => Carbon::now()->subDays(1),
            ],
        ];

        // Orders Pending (10 orders)
        $pendingOrders = [
            [
                'user_id' => $pembeli1->id,
                'produk_id' => $produkList[3]->id, // Kue Lumpur
                'name' => $pembeli1->name,
                'alamat' => 'Jl. Sudirman No. 50, Surabaya',
                'phone' => '081212345678',
                'jumlah' => 5,
                'total_harga' => $produkList[3]->harga * 5,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subHours(24),
            ],
            [
                'user_id' => $pembeli2->id,
                'produk_id' => $produkList[4]->id, // Rempeyek
                'name' => $pembeli2->name,
                'alamat' => 'Jl. Gatot Subroto No. 25, Jakarta',
                'phone' => '081312345679',
                'jumlah' => 8,
                'total_harga' => $produkList[4]->harga * 8,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subHours(18),
            ],
            [
                'user_id' => $pembeli3->id,
                'produk_id' => $produkList[9]->id, // Sarung Bantal
                'name' => $pembeli3->name,
                'alamat' => 'Jl. Asia Afrika No. 100, Bandung',
                'phone' => '081412345680',
                'jumlah' => 3,
                'total_harga' => $produkList[9]->harga * 3,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subHours(12),
            ],
            [
                'user_id' => $pembeli4->id,
                'produk_id' => $produkList[14]->id, // Keripik Mangga
                'name' => $pembeli4->name,
                'alamat' => 'Jl. Thamrin No. 75, Medan',
                'phone' => '081512345681',
                'jumlah' => 7,
                'total_harga' => $produkList[14]->harga * 7,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subHours(8),
            ],
            [
                'user_id' => $pembeli5->id,
                'produk_id' => $produkList[1]->id, // Dodol Mangga
                'name' => $pembeli5->name,
                'alamat' => 'Jl. Diponegoro No. 30, Yogyakarta',
                'phone' => '081612345682',
                'jumlah' => 4,
                'total_harga' => $produkList[1]->harga * 4,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subHours(6),
            ],
            [
                'user_id' => $pembeli1->id,
                'produk_id' => $produkList[6]->id, // Batik Complongan
                'name' => $pembeli1->name,
                'alamat' => 'Jl. Sudirman No. 50, Surabaya',
                'phone' => '081212345678',
                'jumlah' => 2,
                'total_harga' => $produkList[6]->harga * 2,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subHours(4),
            ],
            [
                'user_id' => $pembeli2->id,
                'produk_id' => $produkList[15]->id, // Ikan Bandeng
                'name' => $pembeli2->name,
                'alamat' => 'Jl. Gatot Subroto No. 25, Jakarta',
                'phone' => '081312345679',
                'jumlah' => 1,
                'total_harga' => $produkList[15]->harga * 1,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subHours(3),
            ],
            [
                'user_id' => $pembeli3->id,
                'produk_id' => $produkList[10]->id, // Sirup Mangga
                'name' => $pembeli3->name,
                'alamat' => 'Jl. Asia Afrika No. 100, Bandung',
                'phone' => '081412345680',
                'jumlah' => 6,
                'total_harga' => $produkList[10]->harga * 6,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subHours(2),
            ],
            [
                'user_id' => $pembeli4->id,
                'produk_id' => $produkList[0]->id, // Keripik Pisang
                'name' => $pembeli4->name,
                'alamat' => 'Jl. Thamrin No. 75, Medan',
                'phone' => '081512345681',
                'jumlah' => 10,
                'total_harga' => $produkList[0]->harga * 10,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subHours(1),
            ],
            [
                'user_id' => $pembeli5->id,
                'produk_id' => $produkList[8]->id, // Gantungan Kunci
                'name' => $pembeli5->name,
                'alamat' => 'Jl. Diponegoro No. 30, Yogyakarta',
                'phone' => '081612345682',
                'jumlah' => 15,
                'total_harga' => $produkList[8]->harga * 15,
                'status' => 'pending',
                'status_pesanan' => 'belum_diterima',
                'created_at' => Carbon::now()->subMinutes(30),
            ],
        ];

        // Create success orders
        foreach ($successOrders as $orderData) {
            Order::create($orderData);
        }

        // Create pending orders
        foreach ($pendingOrders as $orderData) {
            Order::create($orderData);
        }

        $this->command->info('✅ Orders seeded (15 success, 10 pending)');

        // ===============================
        // 🔹 ULASAN (Review)
        // ===============================
        $successOrders = Order::where('status', 'complete')->get();

        $ulasanData = [
            [
                'users_id' => $successOrders[0]->user_id,
                'produks_id' => $successOrders[0]->produk_id,
                'orders_id' => $successOrders[0]->id,
                'bintang' => 5,
                'ulasan' => 'Keripik pisangnya enak banget! Renyah dan manisnya pas. Pesan 15 untuk acara keluarga, habis dalam sekejap!',
            ],
            [
                'users_id' => $successOrders[1]->user_id,
                'produks_id' => $successOrders[1]->produk_id,
                'orders_id' => $successOrders[1]->id,
                'bintang' => 5,
                'ulasan' => 'Sirup mangganya segar dan manis alami. Pesan 12 botol untuk oleh-oleh kantor, semua kolega suka!',
            ],
            [
                'users_id' => $successOrders[2]->user_id,
                'produks_id' => $successOrders[2]->produk_id,
                'orders_id' => $successOrders[2]->id,
                'bintang' => 4,
                'ulasan' => 'Kerupuk ikannya renyah dan gurih. Packing aman, pengiriman cepat. Pesan 20 bungkus untuk warung, laris manis!',
            ],
            [
                'users_id' => $successOrders[3]->user_id,
                'produks_id' => $successOrders[3]->produk_id,
                'orders_id' => $successOrders[3]->id,
                'bintang' => 5,
                'ulasan' => 'Gantungan kuncinya lucu-lucu! Pesan 25 untuk souvenir pernikahan, tamu-tamu suka semua. Terima kasih!',
            ],
            [
                'users_id' => $successOrders[4]->user_id,
                'produks_id' => $successOrders[4]->produk_id,
                'orders_id' => $successOrders[4]->id,
                'bintang' => 4,
                'ulasan' => 'Manisan mangganya segar dengan rasa pedas yang unik. Pesan 18 untuk dijual lagi di toko, respon customer bagus.',
            ],
            [
                'users_id' => $successOrders[5]->user_id,
                'produks_id' => $successOrders[5]->produk_id,
                'orders_id' => $successOrders[5]->id,
                'bintang' => 5,
                'ulasan' => 'Tas rajutnya bagus sekali, kualitas jahitan rapi dan bahannya kuat. Recommended banget!',
            ],
            [
                'users_id' => $successOrders[6]->user_id,
                'produks_id' => $successOrders[6]->produk_id,
                'orders_id' => $successOrders[6]->id,
                'bintang' => 5,
                'ulasan' => 'Batiknya cantik dan bahannya nyaman dipakai. Motifnya unik khas Indramayu, harga worth it!',
            ],
            [
                'users_id' => $successOrders[7]->user_id,
                'produks_id' => $successOrders[7]->produk_id,
                'orders_id' => $successOrders[7]->id,
                'bintang' => 4,
                'ulasan' => 'Ikan bandeng asapnya gurih dan tidak amis. Proses pengasapannya pas, dagingnya masih lembut.',
            ],
            [
                'users_id' => $successOrders[8]->user_id,
                'produks_id' => $successOrders[8]->produk_id,
                'orders_id' => $successOrders[8]->id,
                'bintang' => 5,
                'ulasan' => 'Dompet rajutnya praktis dan desainnya minimalis. Beli 4 untuk hadiah, semuanya suka!',
            ],
            [
                'users_id' => $successOrders[9]->user_id,
                'produks_id' => $successOrders[9]->produk_id,
                'orders_id' => $successOrders[9]->id,
                'bintang' => 4,
                'ulasan' => 'Terasinya wangi dan tidak terlalu asin. Cocok untuk sambal dan masakan sehari-hari.',
            ],
        ];

        foreach ($ulasanData as $ulasan) {
            Ulasan::updateOrCreate([
                'orders_id' => $ulasan['orders_id'],
            ], $ulasan);
        }
        $this->command->info('✅ Ulasans seeded');

        // ===============================
        // 🔹 CHATS (User ↔ UMKM)
        // ===============================
        $chatSamples = [
            // Chat dengan UMKM Jo Craft
            [
                'sender_id' => $pembeli1->id,
                'receiver_id' => $umkm2->user_id,
                'umkm_id' => $umkm2->id,
                'message' => 'Halo kak, tas rajutnya masih tersedia?',
                'is_ai' => 0,
                'created_at' => now()->subDays(5),
            ],
            [
                'sender_id' => $umkm2->user_id,
                'receiver_id' => $pembeli1->id,
                'umkm_id' => $umkm2->id,
                'message' => 'Halo! Masih ready kak, mau pesan berapa?',
                'is_ai' => 0,
                'created_at' => now()->subDays(5)->addMinutes(5),
            ],
            [
                'sender_id' => $pembeli1->id,
                'receiver_id' => $umkm2->user_id,
                'umkm_id' => $umkm2->id,
                'message' => 'Aku pesan 2 ya kak, warnanya ada apa aja?',
                'is_ai' => 0,
                'created_at' => now()->subDays(5)->addMinutes(10),
            ],

            // Chat dengan Toko Sukses Jaya
            [
                'sender_id' => $pembeli2->id,
                'receiver_id' => $umkm1->user_id,
                'umkm_id' => $umkm1->id,
                'message' => 'Selamat siang, untuk keripik pisang ada diskon gak?',
                'is_ai' => 0,
                'created_at' => now()->subDays(4),
            ],
            [
                'sender_id' => $umkm1->user_id,
                'receiver_id' => $pembeli2->id,
                'umkm_id' => $umkm1->id,
                'message' => 'Siang kak, sedang ada diskon 15% sampai besok. Mau pesan berapa?',
                'is_ai' => 0,
                'created_at' => now()->subDays(4)->addMinutes(3),
            ],

            // Chat dengan Mangga Indramayu
            [
                'sender_id' => $pembeli3->id,
                'receiver_id' => $umkm3->user_id,
                'umkm_id' => $umkm3->id,
                'message' => 'Halo, sirup mangganya bisa COD di daerah Indramayu?',
                'is_ai' => 0,
                'created_at' => now()->subDays(3),
            ],
            [
                'sender_id' => $umkm3->user_id,
                'receiver_id' => $pembeli3->id,
                'umkm_id' => $umkm3->id,
                'message' => 'Bisa kak, untuk COD area Indramayu gratis ongkir. Minat pesan berapa?',
                'is_ai' => 0,
                'created_at' => now()->subDays(3)->addMinutes(2),
            ],

            // Chat dengan SeaFood Indramayu
            [
                'sender_id' => $pembeli4->id,
                'receiver_id' => $umkm4->user_id,
                'umkm_id' => $umkm4->id,
                'message' => 'Selamat pagi, ikan bandeng asapnya ready stok?',
                'is_ai' => 0,
                'created_at' => now()->subDays(2),
            ],
            [
                'sender_id' => $umkm4->user_id,
                'receiver_id' => $pembeli4->id,
                'umkm_id' => $umkm4->id,
                'message' => 'Pagi kak, masih ready. Baru saja produksi kemarin, fresh banget!',
                'is_ai' => 0,
                'created_at' => now()->subDays(2)->addMinutes(5),
            ],
            [
                'sender_id' => $pembeli4->id,
                'receiver_id' => $umkm4->user_id,
                'umkm_id' => $umkm4->id,
                'message' => 'Bagus kalau begitu, saya pesan 3 ya untuk lebaran',
                'is_ai' => 0,
                'created_at' => now()->subDays(2)->addMinutes(8),
            ],
        ];

        foreach ($chatSamples as $chat) {
            Chat::create($chat);
        }

        $this->command->info('✅ Chats seeded');

        // ===============================
        // 📊 RINGKASAN PRODUK TERLARIS
        // ===============================
        $this->command->info('🎉 Semua data dummy berhasil dimasukkan ke database!');
        $this->command->info('📊 Ringkasan Data:');
        $this->command->info('   - Users: ' . User::count());
        $this->command->info('   - UMKM: ' . Umkm::count());
        $this->command->info('   - Kategori Utama: ' . KategoriProduk::whereNull('parent_id')->count());
        $this->command->info('   - Subkategori: ' . KategoriProduk::whereNotNull('parent_id')->count());
        $this->command->info('   - Total Kategori: ' . KategoriProduk::count());
        $this->command->info('   - Produk: ' . Produk::count());
        $this->command->info('   - Diskon: ' . Diskon::count());
        $this->command->info('   - Orders Success: ' . Order::where('status', 'complete')->count());
        $this->command->info('   - Orders Pending: ' . Order::where('status', 'pending')->count());
        $this->command->info('   - Ulasan: ' . Ulasan::count());
        $this->command->info('   - Chats: ' . Chat::count());

        $this->command->info('🔥 PRODUK TERLARIS (dengan penjualan >10):');
        $bestSellers = Order::where('status', 'complete')
            ->selectRaw('produk_id, SUM(jumlah) as total_terjual')
            ->groupBy('produk_id')
            ->having('total_terjual', '>', 10)
            ->orderBy('total_terjual', 'DESC')
            ->get();

        foreach ($bestSellers as $best) {
            $produk = Produk::find($best->produk_id);
            $this->command->info("   - {$produk->nama}: {$best->total_terjual} pcs terjual");
        }

        $this->command->info('📁 STRUKTUR KATEGORI:');
        $mainCategories = KategoriProduk::whereNull('parent_id')->get();
        foreach ($mainCategories as $mainCat) {
            $this->command->info("   - {$mainCat->nama}:");
            $subcategories = KategoriProduk::where('parent_id', $mainCat->id)->get();
            foreach ($subcategories as $subcat) {
                $productCount = Produk::where('kategori_produk_id', $subcat->id)->count();
                $this->command->info("     └── {$subcat->nama} ({$productCount} produk)");
            }
        }
    }
}

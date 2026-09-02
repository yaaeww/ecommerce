@extends('layouts.public')

@section('title', 'Kebijakan Privasi Data — Juragan Pelem')
@section('meta_description', 'Pelajari bagaimana Juragan Pelem melindungi dan mengelola data pribadi pengguna sesuai standar keamanan data dan Undang-Undang Perlindungan Data Pribadi (UU PDP).')

@section('content')
<!-- Hero Section -->
<section class="relative pt-16 pb-16 lg:pt-20 lg:pb-20 bg-gradient-to-b from-white via-brand-cream/40 to-brand-cream border-b border-slate-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold tracking-wide">
                <i class="fas fa-lock text-slate-600"></i>
                <span>Privasi, Enkripsi & Keamanan Informasi</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-brand-slate leading-tight">
                Kebijakan Privasi <br>
                <span class="text-brand-green">Data Pengguna</span>.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Terakhir diperbarui: 02 September 2026 • Kepatuhan penuh terhadap UU Perlindungan Data Pribadi (UU PDP).
            </p>
        </div>
    </div>
</section>

<!-- Privacy Policy Content Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 text-slate-700 text-sm leading-relaxed">
        
        <!-- Pendahuluan -->
        <div class="space-y-3">
            <p>
                Di <strong>Juragan Pelem</strong>, kami sangat menghargai privasi Anda dan berkomitmen penuh untuk melindungi informasi pribadi yang Anda percayakan kepada kami. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, menyimpan, dan melindungi data pribadi Anda saat menggunakan platform kami.
            </p>
        </div>

        <!-- Pasal 1: Data yang Dikumpulkan -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-database text-brand-green"></i>
                <span>1. Informasi yang Kami Kumpulkan</span>
            </h3>
            <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-slate-600">
                <li><strong>Data Identitas Akun:</strong> Nama lengkap, alamat email, nomor telepon, dan foto profil saat registrasi atau login SSO (Google OAuth).</li>
                <li><strong>Data Alamat & Pengiriman:</strong> Alamat lengkap pengantaran barang, kecamatan, kota, kode pos, dan nama penerima untuk keperluan kurir ekspedisi.</li>
                <li><strong>Data Transaksi:</strong> Riwayat pemesanan produk, nomor resi, catatan pengiriman, dan status pembayaran Midtrans. *(Kami tidak pernah menyimpan informasi sensitif nomor kartu kredit/CVV Anda di server kami)*.</li>
                <li><strong>Data Komunikasi:</strong> Pesan obrolan antar-pengguna dan pesan bantuan yang tersimpan dengan enkripsi.</li>
            </ul>
        </div>

        <!-- Pasal 2: Penggunaan Data -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-arrows-spin text-brand-amber"></i>
                <span>2. Bagaimana Kami Menggunakan Informasi Anda</span>
            </h3>
            <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-slate-600">
                <li>Memproses transaksi pemesanan, verifikasi pembayaran, dan pengantaran produk mangga ke alamat Anda.</li>
                <li>Mengirimkan notifikasi status pengiriman paket, pembaruan resi, dan bukti penerimaan secara realtime.</li>
                <li>Meningkatkan kualitas pengalaman belanja dan akurasi rekomendasi produk lokal Indramayu.</li>
                <li>Mencegah aktivitas mencurigakan, penipuan transaksi, dan menjaga integritas keamanan sistem.</li>
            </ul>
        </div>

        <!-- Pasal 3: Pembagian Data ke Pihak Ketiga -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-share-nodes text-indigo-600"></i>
                <span>3. Pembagian Data kepada Pihak Ketiga</span>
            </h3>
            <p class="text-xs sm:text-sm text-slate-600">
                <strong>Kami TIDAK PERNAH menjual, menyewakan, atau memperdagangkan data pribadi Anda kepada pihak mana pun untuk tujuan periklanan pihak ketiga.</strong> Data hanya diteruskan secara terbatas kepada mitra resmi yang berwenang:
            </p>
            <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-slate-600 mt-2">
                <li><strong>Mitra Ekspedisi Logistik (J&T Express & Kurir Rekanan):</strong> Hanya nama penerima, alamat kirim, dan nomor telepon untuk keperluan label pengiriman (*shipping label*).</li>
                <li><strong>Payment Gateway Resmi (PT Midtrans):</strong> Untuk pemrosesan otorisasi transaksi yang aman dan berstandar PCI-DSS.</li>
            </ul>
        </div>

        <!-- Pasal 4: Keamanan & Enkripsi -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-shield-virus text-emerald-600"></i>
                <span>4. Keamanan & Retensi Data</span>
            </h3>
            <p class="text-xs sm:text-sm text-slate-600">
                Kami menerapkan protokol keamanan enkripsi bertingkat (SSL/TLS Encryption), *password hashing*, dan pembatasan akses database ketat untuk mencegah akses tanpa izin, kebocoran, atau perubahan data tanpa hak.
            </p>
        </div>

        <!-- Pasal 5: Hak Pengguna -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-user-check text-blue-600"></i>
                <span>5. Hak Anda atas Data Pribadi</span>
            </h3>
            <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-slate-600">
                <li>Hak untuk mengakses, meninjau, dan memperbarui informasi profil akun Anda kapan saja.</li>
                <li>Hak untuk meminta penghapusan akun beserta riwayat data pribadi melalui permohonan ke layanan pelanggan.</li>
            </ul>
        </div>

        <!-- Penutup -->
        <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>Pertanyaan mengenai perlindungan data pribadi?</p>
            <a href="mailto:privacy@juraganpelem.id" class="font-bold text-brand-green hover:underline">
                Hubungi Petugas Privasi Data (DPO) &rarr;
            </a>
        </div>

    </div>
</section>
@endsection

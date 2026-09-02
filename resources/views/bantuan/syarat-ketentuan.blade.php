@extends('layouts.public')

@section('title', 'Syarat & Ketentuan Layanan — Juragan Pelem')
@section('meta_description', 'Pelajari syarat dan ketentuan penggunaan platform Juragan Pelem untuk menjamin transaksi jual beli komoditas mangga yang aman, tertib, dan transparan.')

@section('content')
<!-- Hero Section -->
<section class="relative pt-16 pb-16 lg:pt-20 lg:pb-20 bg-gradient-to-b from-white via-brand-cream/40 to-brand-cream border-b border-slate-100 overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-3xl space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold tracking-wide">
                <i class="fas fa-file-contract text-slate-600"></i>
                <span>Ketentuan Hukum & Perjanjian Pengguna</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-brand-slate leading-tight">
                Syarat & Ketentuan <br>
                <span class="text-brand-green">Layanan Juragan Pelem</span>.
            </h1>
            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">
                Terakhir diperbarui: 02 September 2026 • Berlaku bagi seluruh pengguna platform Juragan Pelem.
            </p>
        </div>
    </div>
</section>

<!-- Terms Content Section -->
<section class="py-16 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10 text-slate-700 text-sm leading-relaxed">
        
        <!-- Pendahuluan -->
        <div class="space-y-3">
            <p>
                Selamat datang di <strong>Juragan Pelem</strong> (selanjutnya disebut "Platform"). Dengan mengakses, mendaftar, atau melakukan transaksi di platform ini, Anda menyatakan telah membaca, memahami, dan menyetujui untuk terikat dengan Syarat dan Ketentuan berikut. Jika Anda tidak menyetujui salah satu poin di bawah ini, mohon untuk tidak menggunakan layanan kami.
            </p>
        </div>

        <!-- Pasal 1: Ketentuan Akun -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-user-shield text-brand-green"></i>
                <span>1. Ketentuan Akun & Keamanan</span>
            </h3>
            <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-slate-600">
                <li>Pengguna wajib memberikan data identitas yang akurat, valid, dan dapat dipertanggungjawabkan saat pendaftaran.</li>
                <li>Pengguna bertanggung jawab penuh untuk menjaga kerahasiaan kata sandi (*password*) dan aktivitas yang terjadi di bawah akun masing-masing.</li>
                <li>Platform berhak menonaktifkan atau memblokir akun yang terindikasi melakukan penipuan, manipulasi pesanan, atau pelanggaran hukum.</li>
            </ul>
        </div>

        <!-- Pasal 2: Transaksi & Pembayaran -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-credit-card text-brand-amber"></i>
                <span>2. Ketentuan Transaksi & Sistem Escrow</span>
            </h3>
            <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-slate-600">
                <li>Seluruh pembayaran wajib dilakukan melalui metode resmi yang disediakan oleh platform (Midtrans Snap: QRIS, Virtual Account, Transfer Bank).</li>
                <li>Platform menerapkan sistem penampungan dana bersama (*Escrow Protection*). Dana pembayaran pembeli disimpan dengan aman dan baru diteruskan ke saldo dompet penjual setelah paket buah terkonfirmasi diterima dengan baik.</li>
                <li>Harga produk yang tertera adalah harga resmi yang ditetapkan oleh masing-masing toko mitra petani/UMKM.</li>
            </ul>
        </div>

        <!-- Pasal 3: Pengiriman & Logistik -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-truck-fast text-indigo-600"></i>
                <span>3. Pengiriman Komoditas & Force Majeure</span>
            </h3>
            <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-slate-600">
                <li>Penjual wajib mengemas komoditas mangga segar sesuai SOP perlindungan buah (*safety net* dan kardus berventilasi).</li>
                <li>Keterlambatan atau kendala yang disebabkan oleh bencana alam, pemogokan transportasi massal, atau kondisi darurat (*Force Majeure*) akan diselesaikan secara musyawarah mufakat antara pembeli, penjual, dan platform.</li>
                <li>Pembeli berhak mengajukan klaim garansi dalam waktu maksimal 1x24 jam sejak paket berstatus *Delivered*.</li>
            </ul>
        </div>

        <!-- Pasal 4: Hak & Kewajiban Penjual -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-store text-emerald-600"></i>
                <span>4. Kewajiban Mitra Toko / Penjual</span>
            </h3>
            <ul class="list-disc list-inside space-y-1.5 text-xs sm:text-sm text-slate-600">
                <li>Penjual menjamin bahwa produk mangga yang dipasarkan adalah hasil petik panen asli yang layak konsumsi dan higienis.</li>
                <li>Penjual dilarang memanipulasi stok, menjual buah busuk yang disengaja, atau melakukan transaksi palsu (*fake transaction*).</li>
                <li>Skema bagi hasil penjualan mengikuti ketentuan platform: 90% hak bruto toko dan 10% kontribusi operasional platform.</li>
            </ul>
        </div>

        <!-- Pasal 5: Penyelesaian Sengketa -->
        <div class="space-y-3 p-6 rounded-2xl bg-slate-50 border border-slate-200/80">
            <h3 class="text-base font-bold text-slate-900 font-display flex items-center gap-2">
                <i class="fas fa-scale-balanced text-rose-600"></i>
                <span>5. Penyelesaian Sengketa & Hukum yang Berlaku</span>
            </h3>
            <p class="text-xs sm:text-sm text-slate-600">
                Syarat dan Ketentuan ini diatur dan ditafsirkan sesuai dengan hukum yang berlaku di Republik Indonesia. Setiap sengketa transaksi akan diselesaikan terlebih dahulu melalui musyawarah mufakat melalui fitur mediasi/komplain platform. Jika tidak tercapai kesepakatan, sengketa akan diselesaikan di yurisdiksi Pengadilan Negeri Indramayu.
            </p>
        </div>

        <!-- Penutup -->
        <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
            <p>Ada pertanyaan seputar Syarat & Ketentuan?</p>
            <a href="{{ route('kontak') }}" class="font-bold text-brand-green hover:underline">
                Hubungi Tim Legal & Support &rarr;
            </a>
        </div>

    </div>
</section>
@endsection

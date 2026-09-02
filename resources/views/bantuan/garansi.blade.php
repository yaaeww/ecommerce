@extends('layouts.public')

@section('title', 'Kebijakan Garansi 100% — Juragan Pelem | Jaminan Mutu & Kesegaran')
@section('meta_description', 'Pelajari komitmen garansi mutu 100% Juragan Pelem untuk memastikan setiap pesanan mangga dan produk olahan UMKM sampai dalam kondisi terbaik.')

@section('content')
<!-- Hero Section -->
<section class="relative pt-16 pb-16 lg:pt-20 lg:pb-24 bg-gradient-to-b from-white via-brand-cream/40 to-brand-cream border-b border-slate-100 overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-10 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-brand-green text-xs font-bold tracking-wide">
                <i class="fas fa-shield-halved text-emerald-600"></i>
                <span>Jaminan Transaksi & Mutu Komoditas</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-brand-slate leading-tight">
                Kebijakan Garansi Mutu <br>
                <span class="text-brand-green">100% Segar & Asli</span> Indramayu.
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Di Juragan Pelem, kami memegang teguh kepercayaan pelanggan. Setiap buah mangga yang Anda beli dilindungi oleh sistem garansi terpadu untuk memastikan Anda hanya menerima buah kualitas terbaik dari pohon petani.
            </p>
        </div>

        <!-- 4 Key Warranty Pillars -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mt-12">
            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-brand-green flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-tree"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1.5">100% Matang Pohon</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Bebas karbit kimia buatan. Dipetik pada usia panen optimal agar aroma wangi dan kadar manis alami maksimal.
                </p>
            </div>

            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-brand-amber flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1.5">Garansi Rusak Ekspedisi</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Jika buah busuk parah, pecah, atau rusak selama perjalanan, kami ganti baru atau kembalikan dana (refund).
                </p>
            </div>

            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-handshake-angle"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1.5">Escrow Rekening Bersama</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Dana pembayaran Anda ditahan aman di sistem platform dan baru dicairkan ke penjual setelah paket Anda terima dengan baik.
                </p>
            </div>

            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1.5">Klaim Cepat 1x24 Jam</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Proses peninjauan komplain mudah dan cepat langsung melalui dashboard pesanan tanpa birokrasi berbelit.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Policy Content -->
<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Section 1: Kriteria Garansi -->
        <div class="space-y-4">
            <h2 class="text-2xl font-bold text-brand-slate font-display flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-brand-green flex items-center justify-center text-sm font-black">1</span>
                <span>Kriteria Produk yang Dilindungi Garansi</span>
            </h2>
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200/80 text-sm text-slate-600 leading-relaxed space-y-3">
                <p>Garansi pengembalian dana (*refund*) atau penggantian produk berlaku untuk kondisi berikut:</p>
                <ul class="list-disc list-inside space-y-1.5 text-slate-700 font-medium">
                    <li>Buah mangga busuk, berulat dari dalam, atau tidak layak konsumsi saat paket pertama kali dibuka.</li>
                    <li>Kerusakan fisik berat pada buah akibat benturan selama pengiriman logistik (hancur/pecah parah).</li>
                    <li>Produk yang dikirimkan tidak sesuai dengan varietas atau jumlah pesanan (misal memesan Gedong Gincu namun dikirim varietas lain).</li>
                    <li>Produk olahan kemasan (sirup, dodol, keripik) dalam keadaan kedaluwarsa atau segel rusak saat diterima.</li>
                </ul>
            </div>
        </div>

        <!-- Section 2: Alur Pengajuan Klaim -->
        <div class="space-y-4">
            <h2 class="text-2xl font-bold text-brand-slate font-display flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-brand-green flex items-center justify-center text-sm font-black">2</span>
                <span>Tahapan Mudah Pengajuan Klaim Garansi</span>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="p-5 rounded-2xl bg-white border border-slate-200 text-center space-y-2">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs mx-auto">1</div>
                    <h4 class="font-bold text-sm text-slate-900">Foto / Video Unboxing</h4>
                    <p class="text-xs text-slate-500">Ambil foto jelas atau video unboxing saat paket pertama kali diterima.</p>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-slate-200 text-center space-y-2">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs mx-auto">2</div>
                    <h4 class="font-bold text-sm text-slate-900">Buka Menu Komplain</h4>
                    <p class="text-xs text-slate-500">Masuk ke riwayat pesanan akun Anda dan klik tombol <strong>"Ajukan Komplain"</strong>.</p>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-slate-200 text-center space-y-2">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs mx-auto">3</div>
                    <h4 class="font-bold text-sm text-slate-900">Verifikasi 1x24 Jam</h4>
                    <p class="text-xs text-slate-500">Tim pengawas dan penjual meninjau bukti kerusakan secara objektif.</p>
                </div>
                <div class="p-5 rounded-2xl bg-white border border-slate-200 text-center space-y-2">
                    <div class="w-8 h-8 rounded-full bg-slate-900 text-white font-bold flex items-center justify-center text-xs mx-auto">4</div>
                    <h4 class="font-bold text-sm text-slate-900">Penyelesaian Garansi</h4>
                    <p class="text-xs text-slate-500">Pilihan pengembalian saldo (refund penuh) atau pengiriman paket buah baru.</p>
                </div>
            </div>
        </div>

        <!-- Section 3: Batasan dan Pengecualian -->
        <div class="space-y-4">
            <h2 class="text-2xl font-bold text-brand-slate font-display flex items-center gap-3">
                <span class="w-8 h-8 rounded-xl bg-emerald-100 text-brand-green flex items-center justify-center text-sm font-black">3</span>
                <span>Batasan & Pengecualian Garansi</span>
            </h2>
            <div class="p-6 rounded-2xl bg-amber-50/70 border border-amber-200/80 text-xs sm:text-sm text-amber-900 leading-relaxed space-y-2">
                <p class="font-bold">Garansi tidak berlaku apabila:</p>
                <ul class="list-disc list-inside space-y-1 text-amber-800">
                    <li>Klaim diajukan lebih dari 1x24 jam setelah kurir ekspedisi menyatakan paket berhasil terkirim (*delivered*).</li>
                    <li>Alamat tujuan yang dicantumkan pembeli salah atau kurir tidak dapat menghubungi penerima lebih dari 2 hari berturut-turut.</li>
                    <li>Kerusakan buah diakibatkan oleh kelalaian penyimpanan pembeli setelah paket diterima dalam keadaan baik.</li>
                </ul>
            </div>
        </div>

        <!-- CTA Box -->
        <div class="p-8 rounded-3xl bg-brand-green text-white flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
            <div class="space-y-1 text-center sm:text-left">
                <h3 class="text-xl font-bold font-display">Butuh Bantuan Lebih Lanjut?</h3>
                <p class="text-xs sm:text-sm text-emerald-100">Tim Customer Support Juragan Pelem siap membantu kendala transaksi Anda.</p>
            </div>
            <a href="{{ route('kontak') }}" class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition shadow-md whitespace-nowrap">
                Hubungi Layanan Bantuan &rarr;
            </a>
        </div>

    </div>
</section>
@endsection

@extends('layouts.public')

@section('title', 'Panduan Mitra Petani & UMKM — Juragan Pelem | Akses Pasar Digital Mangga')
@section('meta_description', 'Panduan lengkap bagi petani mangga dan pelaku UMKM lokal Indramayu untuk membuka toko digital, memasarkan hasil panen, dan mencairkan hasil penjualan.')

@section('content')
<!-- Hero Section -->
<section class="relative pt-16 pb-16 lg:pt-20 lg:pb-24 bg-gradient-to-b from-white via-brand-cream/40 to-brand-cream border-b border-slate-100 overflow-hidden">
    <div class="absolute top-0 right-0 w-96 h-96 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-10 w-80 h-80 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-3xl space-y-4">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-amber-50 border border-amber-200 text-brand-amber text-xs font-bold tracking-wide">
                <i class="fas fa-handshake text-amber-600"></i>
                <span>Ekosistem Pemberdayaan Agribisnis</span>
            </div>
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-brand-slate leading-tight">
                Panduan Mitra Petani & <br>
                <span class="text-brand-green">UMKM Olahan Mangga</span>.
            </h1>
            <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                Bergabunglah bersama puluhan mitra kebun dan produsen olahan mangga di Kabupaten Indramayu. Jangkau jutaan penikmat mangga di seluruh Indonesia dengan sistem digital yang adil, transparan, dan menguntungkan.
            </p>
        </div>

        <!-- 4 Keunggulan Mitra -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mt-12">
            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-brand-green flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-chart-line-up"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1.5">Akses Pasar Nasional</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Produk kebun Anda dapat dipesan langsung oleh konsumen dan distributor dari Sabang sampai Merauke tanpa tengkulak.
                </p>
            </div>

            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 text-brand-amber flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-wallet"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1.5">Bagi Hasil 90% Petani</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Platform menerapkan skema bagi hasil transparan: 90% mutlak milik toko kebun Anda, hanya 10% untuk biaya pemeliharaan sistem.
                </p>
            </div>

            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-boxes-packing"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1.5">Standarisasi Packing</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Dukungan panduan SOP pengemasan safety net dan kotak berventilasi agar mangga sampai tetap segar dan tidak rusak.
                </p>
            </div>

            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-xs">
                <div class="w-12 h-12 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl mb-4">
                    <i class="fas fa-money-bill-transfer"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1.5">Pencairan Saldo Kilat</h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Tarik penghasilan toko langsung ke rekening bank lokal Anda kapan saja dengan proses verifikasi audit yang aman.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Step-by-Step Guide Section -->
<section class="py-16 bg-white">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-12">
        
        <!-- Langkah 1: Pendaftaran -->
        <div class="flex flex-col md:flex-row gap-6 items-start">
            <div class="w-12 h-12 rounded-2xl bg-brand-green text-white font-black flex items-center justify-center text-lg shrink-0 shadow-md">
                1
            </div>
            <div class="space-y-3 flex-1">
                <h3 class="text-xl font-bold text-slate-900 font-display">Registrasi Akun & Pengajuan Profil Toko UMKM</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Daftar akun di website dan pilih peran <strong>"Sebagai Penjual / Mitra UMKM"</strong>. Lengkapi formulir profil toko meliputi:
                </p>
                <ul class="list-disc list-inside text-xs sm:text-sm text-slate-600 space-y-1 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                    <li>Nama Toko Kebun / Sentra Pengolahan Mangga.</li>
                    <li>Alamat kebun/workshop lengkap (Kecamatan di Kabupaten Indramayu).</li>
                    <li>Nomor WhatsApp aktif untuk koordinasi pengiriman pesanan.</li>
                    <li>Upload foto logo atau suasana kebun mangga Anda.</li>
                </ul>
            </div>
        </div>

        <!-- Langkah 2: Upload Produk -->
        <div class="flex flex-col md:flex-row gap-6 items-start">
            <div class="w-12 h-12 rounded-2xl bg-brand-green text-white font-black flex items-center justify-center text-lg shrink-0 shadow-md">
                2
            </div>
            <div class="space-y-3 flex-1">
                <h3 class="text-xl font-bold text-slate-900 font-display">Mengunggah Katalog Produk & Penetapan Harga</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Melalui dashboard penjual, Anda dapat menambahkan komoditas mangga segar maupun olahan makanan/minuman dengan mengisi:
                </p>
                <ul class="list-disc list-inside text-xs sm:text-sm text-slate-600 space-y-1 bg-slate-50 p-4 rounded-2xl border border-slate-200/80">
                    <li>Kategori produk (Mangga Segar, Olahan Pangan, Bibit Tanaman).</li>
                    <li>Harga jual per kilogram atau per kemasan satuan.</li>
                    <li>Ketersediaan stok riil di gudang/pohon.</li>
                    <li>Foto produk asli beresolusi jelas dan deskripsi rasa / keunggulan.</li>
                </ul>
            </div>
        </div>

        <!-- Langkah 3: Pengemasan & Ekspedisi -->
        <div class="flex flex-col md:flex-row gap-6 items-start">
            <div class="w-12 h-12 rounded-2xl bg-brand-green text-white font-black flex items-center justify-center text-lg shrink-0 shadow-md">
                3
            </div>
            <div class="space-y-3 flex-1">
                <h3 class="text-xl font-bold text-slate-900 font-display">SOP Pengemasan Khusus Buah & Pengiriman Logistik</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Saat pesanan berstatus lunas diterima, lakukan proses *fulfillment* sesuai SOP berikut:
                </p>
                <div class="p-5 rounded-2xl bg-emerald-50/70 border border-emerald-200/80 space-y-2 text-xs sm:text-sm text-emerald-900">
                    <p class="font-bold"><i class="fas fa-check-circle mr-1 text-emerald-600"></i> Standar Kemas Mangga Segar:</p>
                    <p>Sortir mangga bertekstur kencang, bungkus tiap buah dengan busa jaring (*fruit net*), dan masukkan ke dalam kardus berperekat kuat dengan lubang sirkulasi udara.</p>
                    <p class="font-bold mt-2"><i class="fas fa-truck mr-1 text-emerald-600"></i> Input Resi Pengiriman:</p>
                    <p>Serahkan paket ke kurir J&T Express atau kurir rekanan, lalu masukkan nomor resi dan foto paket di menu <strong>"Manajemen Pesanan"</strong>.</p>
                </div>
            </div>
        </div>

        <!-- Langkah 4: Pencairan Saldo -->
        <div class="flex flex-col md:flex-row gap-6 items-start">
            <div class="w-12 h-12 rounded-2xl bg-brand-green text-white font-black flex items-center justify-center text-lg shrink-0 shadow-md">
                4
            </div>
            <div class="space-y-3 flex-1">
                <h3 class="text-xl font-bold text-slate-900 font-display">Penerimaan Dana & Pencairan Saldo Toko</h3>
                <p class="text-sm text-slate-600 leading-relaxed">
                    Setelah pembeli mengonfirmasi penerimaan barang atau status kurir selesai, dana pembayaran akan masuk ke saldo dompet toko Anda secara otomatis. Anda dapat mengajukan pencairan saldo kapan saja ke rekening bank yang terdaftar.
                </p>
            </div>
        </div>

        <!-- CTA Box -->
        <div class="p-8 rounded-3xl bg-gradient-to-r from-emerald-800 to-brand-green text-white flex flex-col sm:flex-row items-center justify-between gap-6 shadow-xl">
            <div class="space-y-1 text-center sm:text-left">
                <h3 class="text-xl font-bold font-display">Siap Menjadi Mitra Juragan Pelem?</h3>
                <p class="text-xs sm:text-sm text-emerald-100">Buka toko gratis sekarang dan mulailah berjualan langsung ke konsumen di seluruh Indonesia.</p>
            </div>
            <a href="{{ route('register') }}" class="px-6 py-3 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition shadow-md whitespace-nowrap">
                Daftar Jadi Penjual Sekarang &rarr;
            </a>
        </div>

    </div>
</section>
@endsection

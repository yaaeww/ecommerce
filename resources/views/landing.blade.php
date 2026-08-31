@extends('layouts.public')

@section('title', 'Juragan Pelem — Marketplace Resmi Mangga & UMKM Indramayu')
@section('meta_description', 'Platform digital penghubung langsung petani mangga Indramayu dan UMKM lokal dengan konsumen seluruh Indonesia.')

@section('content')
<!-- Hero Section (Asymmetric & High Impact) -->
<section class="relative pt-12 pb-20 lg:pt-16 lg:pb-28 overflow-hidden">
    <!-- Subtle Glow Elements -->
    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-brand-green-light/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-10 w-80 h-80 bg-brand-amber/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">
            
            <!-- Left Column (Content) -->
            <div class="lg:col-span-7 space-y-7">
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-brand-slate leading-[1.12]">
                    Mangga Segar Pilihan, <br>
                    <span class="text-brand-green">Langsung Dari Petani</span> Indramayu.
                </h1>

                <p class="text-base sm:text-lg text-slate-600 leading-relaxed max-w-xl">
                    Dapatkan mangga kualitas ekspor dengan standar kemanisan 16-18° Brix serta aneka olahan UMKM lokal. Dipetik dari kebun saat matang pohon, dikemas aman, dan dikirim cepat ke pintu rumah Anda.
                </p>

                <!-- CTAs -->
                <div class="flex flex-wrap items-center gap-4 pt-2">
                    <a href="#produk" class="inline-flex items-center gap-3 px-7 py-3.5 bg-brand-green text-white rounded-xl text-base font-semibold hover:bg-brand-green-dark transition shadow-lg shadow-brand-green/20 hover:shadow-xl hover:-translate-y-0.5">
                        <i class="fas fa-bag-shopping"></i> Belanja Mangga Segar
                    </a>
                    <a href="{{ route('kategori') }}" class="inline-flex items-center gap-2.5 px-6 py-3.5 bg-white text-slate-700 rounded-xl text-base font-semibold border border-slate-200 hover:bg-slate-50 hover:text-brand-green transition hover:-translate-y-0.5">
                        <i class="fas fa-layer-group text-slate-400"></i> Jelajahi Kategori
                    </a>
                </div>

                <!-- Social Proof / Stats Ticker -->
                <div class="pt-6 border-t border-slate-200/80 flex flex-wrap items-center gap-8 text-slate-700">
                    <div class="flex items-center gap-3">
                        <div class="flex -space-x-2 overflow-hidden">
                            <span class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-amber-500 text-white font-bold text-xs flex items-center justify-center">HR</span>
                            <span class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-emerald-600 text-white font-bold text-xs flex items-center justify-center">SW</span>
                            <span class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-indigo-600 text-white font-bold text-xs flex items-center justify-center">AF</span>
                            <span class="inline-block h-8 w-8 rounded-full ring-2 ring-white bg-rose-500 text-white font-bold text-xs flex items-center justify-center">+50</span>
                        </div>
                        <div>
                            <div class="flex items-center text-amber-400 text-xs gap-0.5">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                                <span class="font-bold text-slate-900 ml-1">4.9/5.0</span>
                            </div>
                            <span class="text-xs text-slate-500">2.400+ Ulasan Pembeli</span>
                        </div>
                    </div>

                    <div class="h-8 w-px bg-slate-200 hidden sm:block"></div>

                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-emerald-100 flex items-center justify-center text-brand-green">
                            <i class="fas fa-certificate text-base"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">Garansi Kualitas 100%</p>
                            <p class="text-xs text-slate-500">Buah Rusak Diganti Baru</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column (Interactive Visual Card) -->
            <div class="lg:col-span-5 relative">
                <div class="relative mx-auto max-w-md lg:max-w-none">
                    <!-- Main Card -->
                    <div class="rounded-3xl overflow-hidden shadow-2xl border border-slate-100 bg-white relative group">
                        <img src="{{ asset('aset/belanjain.jpg') }}" alt="Juragan Pelem Packaging" class="w-full aspect-[4/3] object-cover group-hover:scale-105 transition-transform duration-500">
                        
                        <!-- Floating Badge 1 (Top Left) -->
                        <div class="absolute top-4 left-4 bg-white/95 backdrop-blur-md px-3.5 py-1.5 rounded-xl shadow-lg border border-slate-100 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                            <span class="text-xs font-bold text-slate-800">Panen Segar Hari Ini</span>
                        </div>

                        <!-- Floating Badge 2 (Bottom Right) -->
                        <div class="absolute bottom-4 right-4 bg-white/95 backdrop-blur-md p-3.5 rounded-2xl shadow-xl border border-slate-100 flex items-center gap-3 max-w-[240px]">
                            <div class="w-10 h-10 rounded-xl bg-brand-green/10 flex items-center justify-center text-brand-green shrink-0">
                                <i class="fas fa-temperature-arrow-up text-lg"></i>
                            </div>
                            <div>
                                <p class="text-[11px] font-medium text-slate-500">Kadar Manis Teruji</p>
                                <p class="text-xs font-bold text-slate-900">17.5° Brix (Super Sweet)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Micro Metrics Card (Bottom Left) -->
                    <div class="absolute -bottom-6 -left-6 bg-brand-slate text-white p-4 rounded-2xl shadow-2xl border border-slate-700 hidden sm:flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-lg">
                            <i class="fas fa-location-dot"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-400">Sentra Kebun Utama</p>
                            <p class="text-sm font-bold text-white">Krasak & Cikedung, Indramayu</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Partners / Trust Ecosystem Marquee -->
<section class="py-8 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <p class="text-center text-xs uppercase tracking-widest text-slate-400 font-bold mb-6">
            Didukung Oleh & Bekerja Sama Dengan Ekosistem Terpercaya
        </p>
        <div class="flex flex-wrap items-center justify-center gap-8 md:gap-14 text-slate-400 font-semibold text-sm">
            <div class="flex items-center gap-2 hover:text-slate-700 transition">
                <i class="fas fa-seedling text-brand-green text-lg"></i>
                <span>Asosiasi Petani Mangga Indramayu</span>
            </div>
            <div class="flex items-center gap-2 hover:text-slate-700 transition">
                <i class="fas fa-building-columns text-blue-600 text-lg"></i>
                <span>Dinas Pertanian Kab. Indramayu</span>
            </div>
            <div class="flex items-center gap-2 hover:text-slate-700 transition">
                <i class="fas fa-boxes-packing text-amber-600 text-lg"></i>
                <span>Koperasi UMKM Mangga Sejahtera</span>
            </div>
            <div class="flex items-center gap-2 hover:text-slate-700 transition">
                <i class="fas fa-truck-ramp-box text-emerald-600 text-lg"></i>
                <span>J&T Cargo Fresh Chain</span>
            </div>
            <div class="flex items-center gap-2 hover:text-slate-700 transition">
                <i class="fas fa-lock text-slate-700 text-lg"></i>
                <span>Midtrans Payment Gateway</span>
            </div>
        </div>
    </div>
</section>

<!-- Bento Grid Section: Ekosistem Digital Unggulan -->
<section id="ekosistem" class="py-20 lg:py-28">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-16">
            <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">
                Standar Agro-Teknologi
            </p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-slate tracking-tight">
                Bukan Sekadar Jual Buah, Ini Komitmen Mutu Dari Kebun Indramayu.
            </h2>
            <p class="text-slate-600 mt-3 text-base">
                Kami membangun infrastruktur terintegrasi dari pemupukan organik, seleksi kadar gula, hingga kemasan anti-benturan.
            </p>
        </div>

        <!-- Bento Layout -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
            
            <!-- Bento 1: Direct Traceability (7 cols) -->
            <div class="md:col-span-7 bento-card p-8 flex flex-col justify-between relative overflow-hidden bg-gradient-to-br from-white to-emerald-50/40">
                <div class="space-y-4 max-w-lg">
                    <div class="w-12 h-12 rounded-2xl bg-brand-green/10 text-brand-green flex items-center justify-center text-xl">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-slate">Transparansi Kebun & Petik Matang Pohon</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Setiap keranjang pesanan tercatat asal kebun dan tanggal panennya. Tidak melalui proses pemeraman bahan kimia karbit, menjaga aroma khas dan rasa manis alami.
                    </p>
                </div>
                <div class="mt-8 pt-6 border-t border-slate-100 flex items-center justify-between text-xs font-semibold text-brand-green">
                    <span><i class="fas fa-check-circle mr-1"></i> 100% Organik Friendly</span>
                    <span><i class="fas fa-check-circle mr-1"></i> Tanpa Karbit</span>
                    <span><i class="fas fa-check-circle mr-1"></i> Fresh Picked</span>
                </div>
            </div>

            <!-- Bento 2: Standardisasi Brix (5 cols) -->
            <div class="md:col-span-5 bento-card p-8 flex flex-col justify-between bg-gradient-to-br from-white to-amber-50/40">
                <div class="space-y-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/10 text-brand-amber flex items-center justify-center text-xl">
                        <i class="fas fa-gauge-high"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-brand-slate">Standar Kemanisan 16-18° Brix</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        Pengukuran berkala menggunakan refraktometer optik memastikan setiap batch mangga yang dikirim berada pada puncak kelezatan.
                    </p>
                </div>
                <div class="mt-6 bg-white p-4 rounded-xl border border-slate-100 flex items-center justify-between">
                    <span class="text-xs text-slate-500 font-medium">Tingkat Manis Rata-rata</span>
                    <span class="text-sm font-bold text-brand-amber">Super Sweet Grade A</span>
                </div>
            </div>

            <!-- Bento 3: Packaging Khusus (4 cols) -->
            <div class="md:col-span-4 bento-card p-7 space-y-4">
                <div class="w-11 h-11 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg">
                    <i class="fas fa-box-open"></i>
                </div>
                <h4 class="text-xl font-bold text-brand-slate">Kemasan Foam Cushioning</h4>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Busa jaring pelindung individual dan kardus berpartisi khusus menahan getaran pengiriman ekspedisi antar kota.
                </p>
            </div>

            <!-- Bento 4: Berdayakan Petani (4 cols) -->
            <div class="md:col-span-4 bento-card p-7 space-y-4">
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-brand-green flex items-center justify-center text-lg">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <h4 class="text-xl font-bold text-brand-slate">Harga Adil untuk Petani</h4>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Memotong rantai tengkulak yang panjang agar petani menerima pendapatan layak dan pembeli mendapatkan harga terbaik.
                </p>
            </div>

            <!-- Bento 5: AI Assist (4 cols) -->
            <div class="md:col-span-4 bento-card p-7 space-y-4">
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                    <i class="fas fa-robot"></i>
                </div>
                <h4 class="text-xl font-bold text-brand-slate">Rekomendasi Varietas Cerdas</h4>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Dapatkan rekomendasi varietas mangga yang cocok untuk jus, rujak, puding, atau konsumsi langsung sesuai selera Anda.
                </p>
            </div>

        </div>
    </div>
</section>

<!-- Kategori Produk (Interactive Grid) -->
<section id="kategori" class="py-16 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">Katalog Terpadu</p>
                <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight">Kategori Pilihan</h2>
                <p class="text-slate-600 text-sm mt-1">Eksplorasi buah segar hingga produk hilirisasi UMKM lokal.</p>
            </div>
            <a href="{{ route('kategori') }}" class="inline-flex items-center gap-2 text-brand-green font-bold text-sm hover:underline">
                Lihat Semua Kategori <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse($kategoris as $kategori)
                @php
                    $countProduk = $kategori->produks->count() + $kategori->subkategoris->sum(fn($sub) => $sub->produks->count());
                @endphp
                <a href="{{ route('kategori') }}" class="group block bento-card p-5 hover:border-brand-green">
                    <div class="aspect-square rounded-2xl bg-emerald-50/50 relative overflow-hidden mb-4 border border-slate-100 flex items-center justify-center">
                        <div class="w-16 h-16 rounded-2xl bg-white shadow-sm flex items-center justify-center text-brand-green group-hover:scale-110 transition-transform">
                            @if(Str::contains(strtolower($kategori->nama), 'makan'))
                                <i class="fas fa-bowl-food text-2xl"></i>
                            @elseif(Str::contains(strtolower($kategori->nama), 'minum'))
                                <i class="fas fa-glass-water text-2xl"></i>
                            @elseif(Str::contains(strtolower($kategori->nama), 'kerajin'))
                                <i class="fas fa-shapes text-2xl"></i>
                            @elseif(Str::contains(strtolower($kategori->nama), 'fashion'))
                                <i class="fas fa-shirt text-2xl"></i>
                            @elseif(Str::contains(strtolower($kategori->nama), 'seafood'))
                                <i class="fas fa-fish text-2xl"></i>
                            @elseif(Str::contains(strtolower($kategori->nama), 'oleh'))
                                <i class="fas fa-gifts text-2xl"></i>
                            @else
                                <i class="fas fa-boxes-stacked text-2xl"></i>
                            @endif
                        </div>
                        <span class="absolute top-3 right-3 bg-white/90 backdrop-blur px-2.5 py-1 rounded-lg text-[11px] font-bold text-slate-700 shadow-sm">
                            {{ $countProduk }} Item
                        </span>
                    </div>
                    <h3 class="font-bold text-slate-900 text-base group-hover:text-brand-green transition flex items-center justify-between">
                        {{ $kategori->nama }}
                        <i class="fas fa-chevron-right text-xs text-slate-300 group-hover:text-brand-green group-hover:translate-x-1 transition-all"></i>
                    </h3>
                    @if($kategori->subkategoris->count())
                        <p class="text-xs text-slate-500 mt-1 line-clamp-1">
                            {{ $kategori->subkategoris->pluck('nama')->take(3)->implode(', ') }}
                        </p>
                    @endif
                </a>
            @empty
                <div class="col-span-full py-12 text-center text-slate-400">
                    <i class="fas fa-inbox text-4xl mb-3"></i>
                    <p>Kategori belum tersedia.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- Flash Sale / Promo Section -->
@if(isset($diskonProduks) && $diskonProduks->count() > 0)
<section class="py-16 bg-gradient-to-r from-brand-slate via-slate-900 to-brand-green-dark text-white relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-4">
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-amber-400 mb-1">
                    <i class="fas fa-bolt mr-1"></i> Penawaran Spesial Panen
                </p>
                <h2 class="text-3xl font-extrabold tracking-tight text-white">Diskon Unggulan Pekan Ini</h2>
            </div>
            <div class="flex items-center gap-3 text-xs bg-slate-800/80 px-4 py-2 rounded-xl border border-slate-700">
                <span class="text-slate-400">Penawaran Berbatas Waktu</span>
                <span class="font-bold text-amber-400"><i class="fas fa-clock mr-1"></i> Stok Terbatas</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($diskonProduks as $item)
                <div class="bg-slate-800/90 rounded-2xl p-4 border border-slate-700 hover:border-emerald-500 transition-all flex flex-col justify-between group">
                    <div class="relative rounded-xl overflow-hidden aspect-square mb-4 bg-slate-900">
                        @if($item->gambar)
                            <img src="{{ asset('storage/' . $item->gambar) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                            <div class="hidden w-full h-full flex items-center justify-center text-slate-600">
                                <i class="fas fa-box-open text-3xl"></i>
                            </div>
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-600">
                                <i class="fas fa-box-open text-3xl"></i>
                            </div>
                        @endif
                        <span class="absolute top-2.5 left-2.5 bg-red-600 text-white font-black text-xs px-2 py-0.5 rounded-md shadow-md">
                            -{{ $item->diskon->persen_diskon }}%
                        </span>
                    </div>
                    <div>
                        <span class="text-[11px] font-semibold text-emerald-400 uppercase tracking-wider">{{ $item->umkm->nama_toko ?? 'Petani Mitra' }}</span>
                        <h3 class="font-bold text-white text-base line-clamp-1 mb-2">{{ $item->nama }}</h3>
                        <div class="flex items-baseline gap-2 mb-4">
                            <span class="text-lg font-extrabold text-amber-400">
                                Rp{{ number_format($item->harga_setelah_diskon, 0, ',', '.') }}
                            </span>
                            <span class="text-xs text-slate-400 line-through">
                                Rp{{ number_format($item->harga, 0, ',', '.') }}
                            </span>
                        </div>
                        <a href="{{ route('pembeli.produk.show', $item->id) }}" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition">
                            <i class="fas fa-cart-plus"></i> Beli Sekarang
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Produk Terbaru & Terpopuler -->
<section id="produk" class="py-20">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">Etalase Produk</p>
                <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight">Katalog Produk Terlaris</h2>
                <p class="text-slate-600 text-sm mt-1">Dapatkan mangga segar utuh, manisan, sirup, hingga kerajinan khas Indramayu.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-semibold text-slate-500">Menampilkan {{ $produks->count() }} produk unggulan</span>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @forelse($produks as $produk)
                <div class="bento-card p-4 flex flex-col justify-between group">
                    <div>
                        <div class="aspect-square rounded-2xl overflow-hidden bg-slate-100 relative mb-4 border border-slate-100">
                            @if($produk->gambar)
                                <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                <div class="hidden w-full h-full flex items-center justify-center text-slate-300 bg-emerald-50/50">
                                    <i class="fas fa-box-open text-4xl text-emerald-400"></i>
                                </div>
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300 bg-emerald-50/50">
                                    <i class="fas fa-box-open text-4xl text-emerald-400"></i>
                                </div>
                            @endif

                            @if($produk->diskon && now()->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir))
                                <span class="absolute top-3 left-3 bg-red-500 text-white text-xs font-black px-2 py-0.5 rounded-md shadow">
                                    -{{ $produk->diskon->persen_diskon }}%
                                </span>
                            @endif

                            <div class="absolute bottom-3 left-3 bg-white/90 backdrop-blur px-2.5 py-1 rounded-lg text-[11px] font-bold text-slate-700 flex items-center gap-1 shadow-sm">
                                <i class="fas fa-star text-amber-400 text-xs"></i>
                                <span>{{ $produk->rating > 0 ? number_format($produk->rating, 1) : '5.0' }}</span>
                            </div>
                        </div>

                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">
                            {{ $produk->umkm->nama_toko ?? 'Kebun Mitra' }}
                        </span>
                        <h3 class="font-bold text-slate-900 text-base group-hover:text-brand-green transition line-clamp-1 mb-1">
                            {{ $produk->nama }}
                        </h3>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-3">
                            {{ $produk->deskripsi }}
                        </p>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-3">
                            <div>
                                <p class="text-xs text-slate-400 font-medium">Harga</p>
                                <p class="text-base font-extrabold text-brand-green">
                                    Rp{{ number_format($produk->harga, 0, ',', '.') }}
                                </p>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 bg-slate-100 rounded-lg text-slate-600">
                                Stok: {{ $produk->stok }}
                            </span>
                        </div>
                        <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="w-full py-2.5 bg-brand-green hover:bg-brand-green-dark text-white font-bold rounded-xl text-xs flex items-center justify-center gap-2 transition shadow-sm">
                            <i class="fas fa-eye"></i> Detail Produk
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center text-slate-400 bg-white rounded-3xl border border-slate-100">
                    <i class="fas fa-box-open text-5xl mb-3 text-slate-300"></i>
                    <p class="text-base font-semibold text-slate-600">Belum ada produk yang dipublikasikan.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<!-- 4 Langkah Alur Dari Pohon ke Meja Makan (How It Works) -->
<section class="py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">Proses Terjamin</p>
            <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight">
                Dari Pohon Hingga Depan Pintu Anda
            </h2>
            <p class="text-slate-600 text-sm mt-1">Alur pemesanan transparan tanpa perantara untuk menjaga kesegaran maksimal.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 relative">
            <!-- Step 1 -->
            <div class="bento-card p-6 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100 text-brand-green mx-auto flex items-center justify-center text-2xl font-black font-display shadow-sm">
                    01
                </div>
                <h3 class="text-lg font-bold text-brand-slate">Pilih & Pesan Online</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Pilih varietas mangga atau olahan UMKM favorit Anda dan bayar secara aman lewat Midtrans.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="bento-card p-6 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-amber-100 text-brand-amber mx-auto flex items-center justify-center text-2xl font-black font-display shadow-sm">
                    02
                </div>
                <h3 class="text-lg font-bold text-brand-slate">Petik Saat Matang Pohon</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Petani memetik buah tepat setelah pesanan diverifikasi guna memastikan kematangan dan aroma alami.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="bento-card p-6 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-indigo-100 text-indigo-600 mx-auto flex items-center justify-center text-2xl font-black font-display shadow-sm">
                    03
                </div>
                <h3 class="text-lg font-bold text-brand-slate">Grading & Packing Cushion</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Pemeriksaan mutu kebersihan buah dan pengemasan dengan busa jaring tebal tahan benturan.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="bento-card p-6 space-y-4 text-center">
                <div class="w-14 h-14 rounded-2xl bg-teal-100 text-teal-600 mx-auto flex items-center justify-center text-2xl font-black font-display shadow-sm">
                    04
                </div>
                <h3 class="text-lg font-bold text-brand-slate">Pengiriman Cepat 24 Jam</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Kurir kargo meluncur mengantarkan paket segar langsung ke alamat tujuan Anda di seluruh Nusantara.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Mitra UMKM Spotlight Section -->
@if(isset($umkms) && $umkms->count() > 0)
<section class="py-20 bg-brand-cream">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
            <div>
                <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">Mitra Lokal Terverifikasi</p>
                <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight">UMKM & Petani Binaan</h2>
                <p class="text-slate-600 text-sm mt-1">Dukung pengusaha lokal Indramayu untuk terus berdaya dan berinovasi.</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($umkms as $umkm)
                <div class="bento-card p-6 flex flex-col justify-between">
                    <div class="flex items-start gap-4 mb-4">
                        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-100 shadow-sm p-1.5 flex items-center justify-center shrink-0">
                            @if($umkm->logo)
                                <img src="{{ asset('storage/' . $umkm->logo) }}" alt="{{ $umkm->nama_toko }}" class="w-full h-full object-contain rounded-xl">
                            @else
                                <i class="fas fa-store text-brand-green text-2xl"></i>
                            @endif
                        </div>
                        <div>
                            <h3 class="font-bold text-base text-slate-900">{{ $umkm->nama_toko }}</h3>
                            <p class="text-xs text-slate-500"><i class="fas fa-location-dot text-brand-amber mr-1"></i> {{ $umkm->alamat ?? 'Indramayu' }}</p>
                            <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 bg-emerald-100 text-brand-green rounded-md">
                                <i class="fas fa-check-circle text-[9px] mr-1"></i> Mitra Terverifikasi
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-slate-600 line-clamp-2 mb-4">
                        {{ $umkm->deskripsi ?? 'Produsen produk mangga segar dan olahan makanan unggulan khas Indramayu.' }}
                    </p>
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500">
                        <span><i class="fas fa-phone mr-1"></i> {{ $umkm->no_telp ?? '08xxxxxxxx' }}</span>
                        <span class="font-semibold text-brand-green">{{ $umkm->produks->count() }} Produk Aktif</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<!-- Testimoni / Verified Reviews Bento -->
<section class="py-20 bg-white border-y border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">Testimoni Pelanggan</p>
            <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight">
                Apa Kata Mereka Tentang Juragan Pelem?
            </h2>
            <p class="text-slate-600 text-sm mt-1">Ulasan nyata dari pecinta mangga di berbagai penjuru Indonesia.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bento-card p-6 space-y-4">
                <div class="flex items-center text-amber-400 text-sm gap-1">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-slate-700 text-sm italic leading-relaxed">
                    "Mangga Gedong Gincunya beneran manis legit tanpa asam sama sekali. Packing kardusnya tebal dan setiap buah dilapisi busa jaring. Tiba di Jakarta dalam kondisi mulus!"
                </p>
                <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-100 text-brand-green font-bold flex items-center justify-center text-sm">
                        AN
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Anisa Nurul</h4>
                        <p class="text-[11px] text-slate-400">Pembeli Terverifikasi — Jakarta Selatan</p>
                    </div>
                </div>
            </div>

            <div class="bento-card p-6 space-y-4">
                <div class="flex items-center text-amber-400 text-sm gap-1">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-slate-700 text-sm italic leading-relaxed">
                    "Sirup mangga dan keripik pisangnya juara buat oleh-oleh kantor. Senang rasanya bisa belanja langsung dari UMKM Indramayu tanpa ribet."
                </p>
                <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-amber-100 text-brand-amber font-bold flex items-center justify-center text-sm">
                        BW
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Bambang Wijaya</h4>
                        <p class="text-[11px] text-slate-400">Pembeli Terverifikasi — Bandung</p>
                    </div>
                </div>
            </div>

            <div class="bento-card p-6 space-y-4">
                <div class="flex items-center text-amber-400 text-sm gap-1">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-slate-700 text-sm italic leading-relaxed">
                    "Sebagai pengusaha olahan kue, pasokan mangga berkualitas standar sangat krusial. Juragan Pelem memberikan konsistensi kualitas rasa terbaik."
                </p>
                <div class="pt-3 border-t border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 font-bold flex items-center justify-center text-sm">
                        RD
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Rina Damayanti</h4>
                        <p class="text-[11px] text-slate-400">Owner Bakery — Surabaya</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- FAQ Accordion Section -->
<section class="py-20 bg-brand-cream">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">Pusat Bantuan</p>
            <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight">
                Pertanyaan yang Sering Diajukan
            </h2>
            <p class="text-slate-600 text-sm mt-1">Informasi lengkap seputar pemesanan, pengiriman, dan garansi.</p>
        </div>

        <div class="space-y-4">
            <!-- FAQ 1 -->
            <div class="bento-card p-6 cursor-pointer" onclick="toggleFaq(1)">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Bagaimana jika buah yang diterima dalam kondisi busuk atau rusak?</h3>
                    <i id="faq-icon-1" class="fas fa-chevron-down text-sm text-slate-400 transition-transform"></i>
                </div>
                <div id="faq-content-1" class="hidden mt-3 pt-3 border-t border-slate-100 text-slate-600 text-xs leading-relaxed">
                    Kami memberikan <strong>Garansi Penggantian 100%</strong>. Cukup kirimkan foto/video unboxing paket dalam waktu 1x24 jam setelah diterima melalui kontak customer support kami, dan buah baru akan segera dikirimkan atau dana direfund.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="bento-card p-6 cursor-pointer" onclick="toggleFaq(2)">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Berapa lama estimasi pengiriman ke luar kota dan luar pulau?</h3>
                    <i id="faq-icon-2" class="fas fa-chevron-down text-sm text-slate-400 transition-transform"></i>
                </div>
                <div id="faq-content-2" class="hidden mt-3 pt-3 border-t border-slate-100 text-slate-600 text-xs leading-relaxed">
                    Untuk wilayah Jabodetabek dan Jawa Barat pengiriman memakan waktu 1 hari kerja (Next Day). Untuk kota lain di Pulau Jawa 1-2 hari, dan luar Pulau Jawa 2-3 hari menggunakan layanan ekspedisi prioritas.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="bento-card p-6 cursor-pointer" onclick="toggleFaq(3)">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Metode pembayaran apa saja yang didukung?</h3>
                    <i id="faq-icon-3" class="fas fa-chevron-down text-sm text-slate-400 transition-transform"></i>
                </div>
                <div id="faq-content-3" class="hidden mt-3 pt-3 border-t border-slate-100 text-slate-600 text-xs leading-relaxed">
                    Kami bekerja sama dengan Midtrans, mendukung QRIS (BCA, Mandiri, BRI, BNI), Virtual Account, e-Wallet (GoPay, OVO, ShopeePay, Dana), serta transfer bank otomatis.
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="bento-card p-6 cursor-pointer" onclick="toggleFaq(4)">
                <div class="flex items-center justify-between">
                    <h3 class="text-base font-bold text-slate-900">Bagaimana cara mendaftar sebagai petani atau mitra UMKM penjual?</h3>
                    <i id="faq-icon-4" class="fas fa-chevron-down text-sm text-slate-400 transition-transform"></i>
                </div>
                <div id="faq-content-4" class="hidden mt-3 pt-3 border-t border-slate-100 text-slate-600 text-xs leading-relaxed">
                    Anda dapat mendaftar dengan memilih role <strong>Penjual</strong> saat registrasi akun, kemudian mengisi data profil toko dan mengajukan UMKM untuk diverifikasi oleh tim kurasi kami.
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bottom CTA Banner -->
<section class="py-16 bg-gradient-to-br from-brand-green-dark via-brand-green to-emerald-800 text-white relative overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 text-center relative z-10 space-y-6">
        <p class="text-xs uppercase tracking-wider font-bold text-amber-300">Pesan Sekarang Juga</p>
        <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
            Nikmati Manis Asli Mangga Indramayu <br>Dari Kebun Pilihan Terbaik.
        </h2>
        <p class="text-emerald-100 text-base max-w-xl mx-auto leading-relaxed">
            Bergabunglah bersama ribuan konsumen yang telah merasakan kesegaran mangga kualitas ekspor dan mendukung kemandirian petani lokal.
        </p>
        <div class="flex flex-wrap justify-center items-center gap-4 pt-4">
            <a href="#produk" class="px-8 py-3.5 bg-brand-amber hover:bg-amber-500 text-slate-900 font-bold rounded-xl text-base transition shadow-xl hover:scale-105">
                <i class="fas fa-cart-shopping mr-2"></i> Belanja Produk Sekarang
            </a>
            <a href="{{ route('register') }}" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-base border border-white/30 backdrop-blur transition">
                Daftar Sebagai Penjual
            </a>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    function toggleFaq(id) {
        const content = document.getElementById(`faq-content-${id}`);
        const icon = document.getElementById(`faq-icon-${id}`);
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            icon.classList.add('rotate-180');
        } else {
            content.classList.add('hidden');
            icon.classList.remove('rotate-180');
        }
    }
</script>
@endpush

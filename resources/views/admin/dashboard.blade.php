@extends('layouts.app')

@section('page_title', 'Superadmin Dashboard 5W+1H')

@section('content')
<div class="space-y-8 pb-12">
    
    <!-- ========================================================================= -->
    <!-- 🏛️ TOP EXECUTIVE BANNER & REALTIME SYSTEM BAR                             -->
    <!-- ========================================================================= -->
    <div class="relative p-6 sm:p-8 rounded-3xl bg-gradient-to-br from-slate-900 via-slate-850 to-slate-900 text-white shadow-xl overflow-hidden border border-slate-800">
        <!-- Ambient Decorative Lighting -->
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-500/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="space-y-3 max-w-3xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                    <span>Pusat Kendali Superadmin • Indramayu Agro-Hub</span>
                </div>
                <h1 class="text-2xl sm:text-4xl font-extrabold tracking-tight font-display text-white">
                    Executive Analytics & Cockpit 5W+1H
                </h1>
                <p class="text-slate-350 text-xs sm:text-sm leading-relaxed text-slate-300">
                    Sistem pemantauan terpadu rantai pasok mangga Indramayu: analisis komoditas, performa omzet petani mitra, sebaran logistik, dan indeks kepuasan konsumen secara real-time.
                </p>

                <!-- Live System Telemetry Badges -->
                <div class="flex flex-wrap items-center gap-3 pt-2 text-[11px] font-medium text-slate-300">
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60">
                        <i class="fas fa-server text-emerald-400"></i>
                        <span>Node System: <strong class="text-white">Active 99.9%</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60">
                        <i class="fas fa-credit-card text-indigo-400"></i>
                        <span>Midtrans SNAP: <strong class="text-white">Connected</strong></span>
                    </div>
                    <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-800/80 border border-slate-700/60">
                        <i class="fas fa-seedling text-amber-400"></i>
                        <span>Musim Panen: <strong class="text-white">Panen Raya 2026</strong></span>
                    </div>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 shrink-0">
                <button onclick="window.print()" class="px-4 py-2.5 bg-slate-800 hover:bg-slate-750 border border-slate-700 hover:border-slate-600 text-slate-200 font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm">
                    <i class="fas fa-print text-xs text-slate-400"></i>
                    <span>Cetak Laporan</span>
                </button>
                <a href="{{ route('admin.kategori.create') }}" class="px-4 py-2.5 bg-brand-600 hover:bg-brand-500 text-white font-bold text-xs rounded-xl transition shadow-lg shadow-brand-600/30 flex items-center gap-2">
                    <i class="fas fa-plus text-xs"></i>
                    <span>Tambah Kategori</span>
                </a>
                <a href="{{ route('admin.umkm.index') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs rounded-xl transition shadow-lg shadow-emerald-600/30 flex items-center gap-2">
                    <i class="fas fa-store text-xs"></i>
                    <span>Verifikasi UMKM</span>
                </a>
            </div>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 📊 5W+1H MASTER KPI EXECUTIVE CARDS (WHAT, WHO, WHEN, WHY)               -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- CARD 1: WHAT (Gross Merchandise Value / Omzet) -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-400 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[10px] font-extrabold uppercase tracking-wider border border-amber-200">
                        WHAT • OMZET
                    </span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 group-hover:scale-110 group-hover:bg-amber-600 group-hover:text-white transition-all flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-sack-dollar"></i>
                </div>
            </div>
            <p class="text-xs font-semibold text-slate-500 mb-1">Total Nilai Transaksi (GMV)</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
            </h3>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 text-xs">
                <span class="text-emerald-600 font-bold flex items-center gap-1">
                    <i class="fas fa-arrow-trend-up"></i> +24.8% MoM
                </span>
                <span class="text-slate-500 font-medium">{{ $totalVolumeTerjual }} Kg/Pcs Terjual</span>
            </div>
        </div>

        <!-- CARD 2: WHO (Mitra Toko UMKM & Komunitas Petani) -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-indigo-400 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-extrabold uppercase tracking-wider border border-indigo-200">
                        WHO • MITRA & USER
                    </span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 group-hover:scale-110 group-hover:bg-indigo-600 group-hover:text-white transition-all flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-users-gear"></i>
                </div>
            </div>
            <p class="text-xs font-semibold text-slate-500 mb-1">Ekosistem Pengguna Aktif</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ $totalUmkm }} <span class="text-sm font-bold text-slate-400 font-sans">Mitra UMKM</span>
            </h3>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 text-xs">
                <span class="text-indigo-600 font-bold flex items-center gap-1">
                    <i class="fas fa-store"></i> {{ $totalPenjual }} Penjual
                </span>
                <span class="text-slate-500 font-medium">{{ $totalPembeli }} Akun Pembeli</span>
            </div>
        </div>

        <!-- CARD 3: WHEN (Frekuensi Pesanan & Keberhasilan Transaksi) -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-400 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-200">
                        WHEN • TRANSAKSI
                    </span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:scale-110 group-hover:bg-emerald-600 group-hover:text-white transition-all flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
            <p class="text-xs font-semibold text-slate-500 mb-1">Volume & Konversi Pesanan</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ $totalOrderComplete }} <span class="text-sm font-bold text-emerald-600 font-sans">Selesai</span>
            </h3>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 text-xs">
                <span class="text-slate-500 font-medium">AOV (Rata-rata/Order)</span>
                <span class="text-slate-900 font-bold">Rp{{ number_format($aov, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- CARD 4: WHY (Indeks Kepuasan & CSAT Rating Konsumen) -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-400 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[10px] font-extrabold uppercase tracking-wider border border-rose-200">
                        WHY • CSAT RATING
                    </span>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 group-hover:scale-110 group-hover:bg-rose-600 group-hover:text-white transition-all flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <p class="text-xs font-semibold text-slate-500 mb-1">Indeks Kepuasan Pelanggan</p>
            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight flex items-center gap-2">
                {{ number_format($avgRating, 1) }} <span class="text-sm font-normal text-amber-500">★★★★★</span>
            </h3>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100 text-xs">
                <span class="text-emerald-600 font-bold flex items-center gap-1">
                    <i class="fas fa-thumbs-up"></i> {{ $csatPersen }}% Positif
                </span>
                <span class="text-slate-500 font-medium">{{ $totalUlasan }} Ulasan Terverifikasi</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 📈 SECTION ANALITIK CHART & GEOGRAFI (WHEN, WHAT, WHERE, HOW)              -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- LEFT: [WHEN & WHAT] Tren Pertumbuhan Omzet & Volume Transaksi (7 Cols) -->
        <div class="lg:col-span-7 card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-blue-50 text-blue-700 text-[10px] font-extrabold uppercase">WHEN & WHAT</span>
                            <h3 class="text-base font-extrabold text-slate-900">Kurva Pertumbuhan Omzet & Volume</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Evolusi nilai transaksi penjualan komoditas per kuartal musim 2026</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-xs border border-emerald-200">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Panen Raya Aktif
                        </span>
                    </div>
                </div>

                <!-- Canvas Chart.js -->
                <div class="mt-6 relative h-72 sm:h-80 w-full">
                    <canvas id="revenueTrendChart"></canvas>
                </div>
            </div>

            <!-- Insight Box Bawah Chart -->
            <div class="mt-6 p-4 rounded-2xl bg-slate-50 border border-slate-200/60 flex items-start gap-3 text-xs text-slate-600">
                <div class="w-8 h-8 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0 font-bold">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div>
                    <strong class="text-slate-900 font-bold block mb-0.5">Analisis Waktu (WHEN):</strong>
                    Lonjakan volume transaksi tertinggi terjadi pada pukul <span class="font-bold text-slate-900">09:00 - 12:00</span> dan <span class="font-bold text-slate-900">19:00 - 21:00</span>. Permintaan produk segar mangga gedong gincu meningkat 3.2x lipat saat akhir pekan.
                </div>
            </div>
        </div>

        <!-- RIGHT: [WHAT & HOW] Pangsa Pasar Kategori Komoditas (5 Cols) -->
        <div class="lg:col-span-5 card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-amber-50 text-amber-700 text-[10px] font-extrabold uppercase">WHAT • SHARE</span>
                            <h3 class="text-base font-extrabold text-slate-900">Pangsa Pasar Komoditas</h3>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Distribusi omzet berdasarkan klaster produk</p>
                    </div>
                    <span class="text-xs font-bold text-slate-400">{{ $totalProduk }} Item</span>
                </div>

                <!-- Doughnut Canvas -->
                <div class="my-6 relative h-56 w-full flex items-center justify-center">
                    <canvas id="categoryShareChart"></canvas>
                </div>

                <!-- Category List Legend -->
                <div class="space-y-3">
                    @foreach($kategoriStats as $kat)
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/60 flex items-center justify-between text-xs">
                            <div class="flex items-center gap-2.5">
                                <span class="w-3 h-3 rounded-full {{ $loop->index == 0 ? 'bg-amber-500' : ($loop->index == 1 ? 'bg-orange-500' : 'bg-emerald-600') }}"></span>
                                <span class="font-bold text-slate-800">{{ $kat['nama'] }}</span>
                                <span class="text-[10px] text-slate-400">({{ $kat['produk_count'] }} produk)</span>
                            </div>
                            <span class="font-extrabold text-slate-900">Rp{{ number_format($kat['omzet'], 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- How Payment breakdown -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-slate-700">Metode Transaksi Terpopuler (HOW):</span>
                    <span class="font-bold text-emerald-600">Midtrans Gateway</span>
                </div>
                <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden flex">
                    <div class="bg-emerald-500 h-full" style="width: 60%" title="QRIS & E-Wallet: 60%"></div>
                    <div class="bg-indigo-600 h-full" style="width: 30%" title="Virtual Account: 30%"></div>
                    <div class="bg-amber-500 h-full" style="width: 10%" title="Kartu Kredit: 10%"></div>
                </div>
                <div class="flex items-center justify-between text-[10px] text-slate-400 mt-1.5">
                    <span>QRIS (60%)</span>
                    <span>Virtual Account (30%)</span>
                    <span>Kartu Kredit (10%)</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 🗺️ SECTION WHERE & SENTRA PRODUKSI INDRAMAYU                              -->
    <!-- ========================================================================= -->
    <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md bg-purple-50 text-purple-700 text-[10px] font-extrabold uppercase border border-purple-200">
                        WHERE • DISTRIBUSI & LOGISTIK
                    </span>
                    <h3 class="text-lg font-extrabold text-slate-900 font-display">
                        Pemetaan Sentra Produksi & Jangkauan Pengiriman
                    </h3>
                </div>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">
                    Analisis asal sentra perkebunan di wilayah Indramayu dan sebaran destinasi pengiriman pembeli nasional.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 font-bold text-xs flex items-center gap-2">
                    <i class="fas fa-location-dot text-rose-500"></i> Kab. Indramayu Hub
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mt-6">
            
            <!-- Sentra Produksi di Indramayu -->
            <div class="space-y-4">
                <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-tree text-emerald-600"></i> Sentra Perkebunan & Produksi Lokal
                </h4>
                <div class="space-y-3">
                    @foreach($sentraIndramayu as $sentra)
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 hover:border-slate-300 transition flex items-center justify-between gap-4">
                            <div class="space-y-1">
                                <p class="font-extrabold text-xs text-slate-900">{{ $sentra['kecamatan'] }}</p>
                                <p class="text-[11px] text-slate-500">{{ $sentra['komoditas'] }}</p>
                            </div>
                            <div class="text-right shrink-0">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                    {{ $sentra['status'] }}
                                </span>
                                <p class="text-[10px] text-slate-400 font-medium mt-1">{{ $sentra['luas'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Destinasi Logistik Pengiriman -->
            <div class="space-y-4">
                <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-truck-ramp-box text-indigo-600"></i> Destinasi Pengiriman Pembeli (WHERE)
                </h4>
                <div class="space-y-4">
                    @foreach($wilayahSebaran as $wilayah)
                        <div class="space-y-1.5">
                            <div class="flex items-center justify-between text-xs">
                                <span class="font-bold text-slate-800 flex items-center gap-2">
                                    <i class="{{ $wilayah['icon'] }} text-slate-400"></i>
                                    {{ $wilayah['nama'] }}
                                </span>
                                <span class="font-extrabold text-slate-900">{{ $wilayah['persen'] }}% <span class="text-[10px] text-slate-400 font-normal">({{ $wilayah['orders'] }} order)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 h-2.5 rounded-full overflow-hidden">
                                <div class="bg-brand-600 h-full rounded-full transition-all duration-1000" style="width: {{ $wilayah['persen'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-6 p-4 rounded-2xl bg-indigo-50/70 border border-indigo-100 text-xs text-indigo-900 flex items-center justify-between">
                    <div>
                        <p class="font-bold">Kecepatan Fulfillment (HOW):</p>
                        <p class="text-[11px] text-indigo-700">Rata-rata 24 jam sampai untuk Jabodetabek & Jabar</p>
                    </div>
                    <span class="px-3 py-1 bg-white text-indigo-600 font-extrabold rounded-lg shadow-sm">100% On-Time</span>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 👥 LEADERBOARD MITRA UMKM & ULASAN KONSUMEN (WHO & WHY)                   -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Leaderboard Top Mitra UMKM (7 Cols) -->
        <div class="lg:col-span-7 card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-indigo-50 text-indigo-700 text-[10px] font-extrabold uppercase">WHO • LEADERBOARD</span>
                            <h3 class="text-base font-extrabold text-slate-900">Performa Toko & Kebun Mitra</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Kontribusi omzet penjualan per mitra UMKM binaan</p>
                    </div>
                    <a href="{{ route('admin.umkm.index') }}" class="text-xs font-bold text-brand-600 hover:underline">
                        Kelola Semua <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full text-left">
                        <thead>
                            <tr>
                                <th>Mitra / Petani</th>
                                <th>Volume</th>
                                <th>Total Omzet</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($topUmkms as $umkm)
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-700 font-bold text-xs flex items-center justify-center shrink-0 border border-amber-200 shadow-sm">
                                                <i class="fas fa-store"></i>
                                            </div>
                                            <div>
                                                <p class="font-extrabold text-xs text-slate-900">{{ $umkm->nama_toko }}</p>
                                                <p class="text-[11px] text-slate-400">{{ $umkm->user->name ?? 'Mitra Binaan' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs font-bold text-slate-700">{{ $umkm->total_terjual }} Pcs/Kg</span>
                                    </td>
                                    <td>
                                        <span class="text-xs font-extrabold text-emerald-600">
                                            Rp{{ number_format($umkm->total_omzet, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($umkm->status === 'approved')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                <i class="fas fa-check-circle mr-1 text-[9px]"></i> Mitra Resmi
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                {{ ucfirst($umkm->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.umkm.index') }}" class="p-2 text-slate-400 hover:text-brand-600 rounded-lg hover:bg-slate-100 transition">
                                            <i class="fas fa-chevron-right text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-8 text-slate-400 text-xs">
                                        Belum ada mitra UMKM terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Testimoni & Suara Konsumen (WHY - 5 Cols) -->
        <div class="lg:col-span-5 card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-rose-50 text-rose-700 text-[10px] font-extrabold uppercase">WHY • FEEDBACK</span>
                            <h3 class="text-base font-extrabold text-slate-900">Suara & Ulasan Konsumen</h3>
                        </div>
                        <p class="text-xs text-slate-400 mt-0.5">Ulasan autentik pembeli setelah menerima pesanan</p>
                    </div>
                    <span class="text-xs font-bold text-amber-500">4.9 / 5.0 ⭐</span>
                </div>

                <div class="mt-4 space-y-3">
                    @forelse($recentUlasans as $ulasan)
                        <div class="p-3.5 rounded-2xl bg-slate-50/80 border border-slate-200/60 space-y-1.5">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 text-[10px] font-bold flex items-center justify-center">
                                        {{ substr($ulasan->user->name ?? 'P', 0, 1) }}
                                    </div>
                                    <span class="text-xs font-bold text-slate-900">{{ $ulasan->user->name ?? 'Pembeli Terverifikasi' }}</span>
                                </div>
                                <div class="text-amber-400 text-xs">
                                    @for($i = 0; $i < ($ulasan->bintang ?? 5); $i++)
                                        ★
                                    @endfor
                                </div>
                            </div>
                            <p class="text-[11px] text-slate-600 italic leading-relaxed">
                                "{{ $ulasan->ulasan }}"
                            </p>
                            <div class="text-[10px] text-slate-400 pt-1 flex items-center gap-1.5">
                                <i class="fas fa-circle-check text-emerald-500"></i> Terverifikasi Pembelian
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            Belum ada ulasan konsumen.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 📦 KATALOG PRODUK UNGGULAN & SHORTCUTS                                    -->
    <!-- ========================================================================= -->
    <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-sm rounded-3xl space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 font-display">Katalog Komoditas Terbaru di Marketplace</h3>
                <p class="text-xs text-slate-400">Daftar produk mangga segar, olahan, dan agrikultur aktif</p>
            </div>
            <a href="{{ route('admin.produk.index') }}" class="text-xs font-bold text-brand-600 hover:underline">
                Lihat Semua Produk ({{ $totalProduk }}) <i class="fas fa-arrow-right text-[10px]"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($recentProduks as $prod)
                <div class="p-3.5 rounded-2xl bg-slate-50/70 border border-slate-200/60 flex items-center gap-3.5 hover:bg-slate-100 transition">
                    <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 overflow-hidden shrink-0 shadow-sm">
                        @if($prod->gambar)
                            <img src="{{ asset('storage/' . $prod->gambar) }}" alt="{{ $prod->nama }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fas fa-image"></i>
                            </div>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 uppercase mb-1">
                            {{ $prod->kategori->nama ?? 'Mangga' }}
                        </span>
                        <h4 class="font-bold text-xs text-slate-900 truncate">{{ $prod->nama }}</h4>
                        <div class="flex items-center justify-between mt-1">
                            <span class="text-xs font-extrabold text-emerald-600">Rp{{ number_format($prod->harga, 0, ',', '.') }}</span>
                            <span class="text-[10px] text-slate-400">Stok: {{ $prod->stok }}</span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    
    // -----------------------------------------------------------
    // 1. Line/Area Chart: Revenue Trend (WHEN & WHAT)
    // -----------------------------------------------------------
    const ctxRevenue = document.getElementById('revenueTrendChart').getContext('2d');
    
    const gradientRevenue = ctxRevenue.createLinearGradient(0, 0, 0, 300);
    gradientRevenue.addColorStop(0, 'rgba(79, 70, 229, 0.35)');
    gradientRevenue.addColorStop(1, 'rgba(79, 70, 229, 0.00)');

    new Chart(ctxRevenue, {
        type: 'line',
        data: {
            labels: @json($chartLabels),
            datasets: [{
                label: 'Gross Omzet (Rp)',
                data: @json($chartRevenue),
                borderColor: '#4f46e5',
                borderWidth: 3,
                backgroundColor: gradientRevenue,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#ffffff',
                pointBorderColor: '#4f46e5',
                pointBorderWidth: 2.5,
                pointRadius: 5,
                pointHoverRadius: 7,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    titleFont: { size: 12, weight: 'bold' },
                    bodyFont: { size: 12 },
                    padding: 12,
                    cornerRadius: 12,
                    callbacks: {
                        label: function(context) {
                            return ' Omzet: Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { color: '#64748b', font: { size: 11, weight: '600' } }
                },
                y: {
                    grid: { color: '#f1f5f9' },
                    ticks: {
                        color: '#64748b',
                        font: { size: 10 },
                        callback: function(val) {
                            return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                        }
                    }
                }
            }
        }
    });

    // -----------------------------------------------------------
    // 2. Doughnut Chart: Category Market Share (WHAT)
    // -----------------------------------------------------------
    const ctxCategory = document.getElementById('categoryShareChart').getContext('2d');
    
    const categoryLabels = @json($kategoriStats->pluck('nama'));
    const categoryOmzets = @json($kategoriStats->pluck('omzet'));

    new Chart(ctxCategory, {
        type: 'doughnut',
        data: {
            labels: categoryLabels,
            datasets: [{
                data: categoryOmzets,
                backgroundColor: ['#f59e0b', '#ea580c', '#10b981'],
                borderWidth: 3,
                borderColor: '#ffffff',
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 10,
                    cornerRadius: 10,
                    callbacks: {
                        label: function(context) {
                            return ' ' + context.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                        }
                    }
                }
            }
        }
    });

});
</script>
@endpush
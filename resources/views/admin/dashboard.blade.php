@extends('layouts.app')

@section('page_title', 'Superadmin Dashboard')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- ========================================================================= -->
    <!-- 🏛️ HEADER TITLE & QUICK ACTIONS                                           -->
    <!-- ========================================================================= -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Dashboard Superadmin
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm mt-0.5">
                Pusat kendali dan analitik performa komoditas mangga Indramayu, omzet mitra, serta log transaksi.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <a href="{{ route('admin.pesanan.index') }}" class="px-3.5 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-receipt text-amber-500"></i>
                <span>Log Semua Pesanan</span>
            </a>
            <a href="{{ route('admin.kategori.create') }}" class="px-3.5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Kategori</span>
            </a>
            <a href="{{ route('admin.umkm.index') }}" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2">
                <i class="fas fa-store text-xs"></i>
                <span>Verifikasi UMKM</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 📅 DYNAMIC CALENDAR & DATE RANGE FILTER BAR                               -->
    <!-- ========================================================================= -->
    <div class="card p-4 sm:p-5 bg-white border border-slate-200/80 rounded-3xl shadow-sm space-y-3.5">
        <form id="dashboardDateFilterForm" action="{{ route('admin.dashboard') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Hidden inputs for custom date selection -->
            <input type="hidden" name="period" id="filterPeriod" value="{{ $period ?? 'all' }}">
            <input type="hidden" name="start_date" id="filterStartDate" value="{{ $startDateInput ?? ($startDate ? $startDate->format('Y-m-d') : '') }}">
            <input type="hidden" name="end_date" id="filterEndDate" value="{{ $endDateInput ?? ($endDate ? $endDate->format('Y-m-d') : '') }}">

            <!-- Left: Calendar API Input & Presets -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 flex-wrap">
                
                <!-- Dynamic Interactive Calendar Range Input -->
                <div class="relative min-w-[260px] sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-brand-600">
                        <i class="fas fa-calendar-days text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        id="flatpickrCalendar" 
                        placeholder="Pilih Rentang Tanggal Kalender..." 
                        class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:outline-hidden transition shadow-xs cursor-pointer"
                        readonly
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        @if(($period ?? 'all') !== 'all')
                            <a href="{{ route('admin.dashboard') }}" title="Reset Filter" class="text-slate-400 hover:text-rose-500 transition text-xs">
                                <i class="fas fa-circle-xmark"></i>
                            </a>
                        @else
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 pointer-events-none"></i>
                        @endif
                    </div>
                </div>

                <!-- Quick Filter Period Pills -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" onclick="setFilterPeriod('all')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Waktu
                    </button>
                    <button type="button" onclick="setFilterPeriod('today')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'today' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setFilterPeriod('7days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '7days' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        7 Hari Terakhir
                    </button>
                    <button type="button" onclick="setFilterPeriod('30days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '30days' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        30 Hari Terakhir
                    </button>
                    <button type="button" onclick="setFilterPeriod('this_month')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_month' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setFilterPeriod('this_year')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_year' ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Tahun Ini
                    </button>
                </div>

            </div>

            <!-- Right: Active Period Badge & Reset Button -->
            <div class="flex items-center justify-between sm:justify-end gap-2.5 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-brand-50 border border-brand-200/80 text-brand-900 text-xs font-bold">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-brand-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-brand-600"></span>
                    </span>
                    <span class="text-[11px] text-slate-500 font-normal">Periode:</span>
                    <strong class="font-extrabold text-brand-800">{{ $activePeriodLabel }}</strong>
                </div>

                @if(($period ?? 'all') !== 'all')
                    <a href="{{ route('admin.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fas fa-rotate-left text-[10px]"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 📊 MASTER KPI EXECUTIVE CARDS                                             -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- CARD 1: Gross Merchandise Value / Omzet -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-400 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-3">
                <span class="px-2.5 py-0.5 rounded-lg bg-amber-50 text-amber-700 text-[10px] font-extrabold uppercase tracking-wider border border-amber-200">
                    Total Pendapatan
                </span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 group-hover:scale-105 group-hover:bg-amber-600 group-hover:text-white transition-all flex items-center justify-center text-sm shadow-sm">
                    <i class="fas fa-sack-dollar"></i>
                </div>
            </div>
            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Total Nilai Transaksi (GMV)</p>
            <h3 class="text-2xl font-extrabold text-slate-900 font-display tracking-tight">
                Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
            </h3>
            <div class="flex items-center justify-between mt-2.5 pt-2.5 border-t border-slate-100 text-[11px]">
                <span class="text-emerald-600 font-bold flex items-center gap-1">
                    <i class="fas fa-arrow-trend-up"></i> +24.8% MoM
                </span>
                <span class="text-slate-400">{{ $totalVolumeTerjual }} Kg Terjual</span>
            </div>
        </div>

        <!-- CARD 2: Mitra Toko UMKM & Komunitas Petani -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-indigo-400 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-3">
                <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 text-indigo-700 text-[10px] font-extrabold uppercase tracking-wider border border-indigo-200">
                    Pengguna & Mitra
                </span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 group-hover:scale-105 group-hover:bg-indigo-600 group-hover:text-white transition-all flex items-center justify-center text-sm shadow-sm">
                    <i class="fas fa-users-gear"></i>
                </div>
            </div>
            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Ekosistem Pengguna Aktif</p>
            <h3 class="text-2xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ $totalUmkm }} <span class="text-xs font-bold text-slate-400 font-sans">Mitra UMKM</span>
            </h3>
            <div class="flex items-center justify-between mt-2.5 pt-2.5 border-t border-slate-100 text-[11px]">
                <span class="text-indigo-600 font-bold flex items-center gap-1">
                    <i class="fas fa-store"></i> {{ $totalPenjual }} Penjual
                </span>
                <span class="text-slate-400">{{ $totalPembeli }} Pembeli</span>
            </div>
        </div>

        <!-- CARD 3: Frekuensi Pesanan & Keberhasilan Transaksi -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-emerald-400 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-3">
                <span class="px-2.5 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-extrabold uppercase tracking-wider border border-emerald-200">
                    Pesanan Selesai
                </span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 group-hover:scale-105 group-hover:bg-emerald-600 group-hover:text-white transition-all flex items-center justify-center text-sm shadow-sm">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Volume & Konversi</p>
            <h3 class="text-2xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ $totalOrderComplete }} <span class="text-xs font-bold text-emerald-600 font-sans">Selesai</span>
            </h3>
            <div class="flex items-center justify-between mt-2.5 pt-2.5 border-t border-slate-100 text-[11px]">
                <span class="text-slate-400">Rata-rata/Order</span>
                <span class="text-slate-800 font-bold">Rp{{ number_format($aov, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- CARD 4: Indeks Kepuasan & CSAT Rating Konsumen -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-400 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-3">
                <span class="px-2.5 py-0.5 rounded-lg bg-rose-50 text-rose-700 text-[10px] font-extrabold uppercase tracking-wider border border-rose-200">
                    Rating Kepuasan
                </span>
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 group-hover:scale-105 group-hover:bg-rose-600 group-hover:text-white transition-all flex items-center justify-center text-sm shadow-sm">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <p class="text-[11px] font-semibold text-slate-400 mb-0.5">Indeks Kepuasan Pelanggan</p>
            <h3 class="text-2xl font-extrabold text-slate-900 font-display tracking-tight flex items-center gap-1.5">
                {{ number_format($avgRating, 1) }} <span class="text-xs font-normal text-amber-500">★★★★★</span>
            </h3>
            <div class="flex items-center justify-between mt-2.5 pt-2.5 border-t border-slate-100 text-[11px]">
                <span class="text-emerald-600 font-bold flex items-center gap-1">
                    <i class="fas fa-thumbs-up"></i> {{ $csatPersen }}% Positif
                </span>
                <span class="text-slate-400">{{ $totalUlasan }} Ulasan</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 🔍 LOG & TRANSPARANSI PESANAN TERBARU (FITTED TO CONTAINER, NO SCROLL)     -->
    <!-- ========================================================================= -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 sm:px-8 sm:py-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="space-y-1.5">
                <div class="flex items-center gap-2.5">
                    <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-[10px] font-extrabold uppercase border border-emerald-200 shadow-xs">
                        Transparansi Pesanan
                    </span>
                    <h3 class="text-base sm:text-lg font-extrabold text-slate-900 font-display">
                        Log Aktivitas Pesanan & Toko Asal Pemroses
                    </h3>
                </div>
                <p class="text-xs text-slate-500">
                    Informasi transparan mengenai produk yang dibeli, asal toko/kebun mitra, profil pembeli, dan status pembayaran.
                </p>
            </div>
            <div class="flex items-center gap-2 shrink-0">
                <a href="{{ route('admin.pesanan.index') }}" class="px-3.5 py-2 bg-slate-50 hover:bg-brand-50 border border-slate-200 hover:border-brand-200 text-brand-600 font-bold text-xs rounded-xl transition flex items-center gap-1.5 shadow-xs">
                    <span>Lihat Semua ({{ $totalSemuaOrder }})</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <div class="w-full overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6 whitespace-nowrap">ID Pesanan</th>
                        <th class="py-3.5 px-4">Komoditas & Produk</th>
                        <th class="py-3.5 px-4">Asal Toko Mitra</th>
                        <th class="py-3.5 px-4">Pembeli & Tujuan</th>
                        <th class="py-3.5 px-4">Total & Bagi Hasil</th>
                        <th class="py-3.5 px-4 text-center">Status</th>
                        <th class="py-3.5 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($recentOrders as $order)
                        @php
                            $prod = $order->produk;
                            $store = $prod->umkm ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50/60 transition">
                            <!-- ID Pesanan -->
                            <td class="py-4 px-6 align-middle whitespace-nowrap">
                                <span class="font-extrabold text-slate-900 block font-mono text-xs">
                                    #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    {{ $order->created_at->translatedFormat('d M, H:i') }}
                                </span>
                            </td>

                            <!-- Komoditas & Produk -->
                            <td class="py-4 px-4 align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 shadow-xs">
                                        @if($prod && $prod->gambar)
                                            <img src="{{ asset('storage/' . $prod->gambar) }}" alt="{{ $prod->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i class="fas fa-box text-xs"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <h4 class="font-bold text-xs text-slate-900 truncate" title="{{ $prod->nama ?? 'Produk Komoditas' }}">
                                            {{ $prod->nama ?? 'Produk Komoditas' }}
                                        </h4>
                                        <p class="text-[11px] text-slate-400 mt-0.5">
                                            {{ $order->jumlah }} Pcs/Kg × Rp{{ number_format($prod->harga ?? 0, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Asal Toko Mitra -->
                            <td class="py-4 px-4 align-middle">
                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-store text-amber-500 text-xs"></i>
                                        <strong class="font-bold text-slate-900 truncate block">{{ $store->nama_toko ?? 'Kebun Mitra' }}</strong>
                                    </div>
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                        {{ $store->user->name ?? 'Petani' }} • {{ $store->alamat ?? 'Indramayu' }}
                                    </p>
                                </div>
                            </td>

                            <!-- Pembeli & Tujuan -->
                            <td class="py-4 px-4 align-middle">
                                <div class="min-w-0">
                                    <p class="font-bold text-slate-900 truncate">{{ $order->name ?: ($order->user->name ?? 'Pembeli') }}</p>
                                    <p class="text-[11px] text-slate-400 truncate mt-0.5">
                                        {{ $order->phone ?: '-' }} • {{ $order->alamat ?: 'Indramayu' }}
                                    </p>
                                </div>
                            </td>

                            <!-- Total & Bagi Hasil -->
                            <td class="py-4 px-4 align-middle">
                                <span class="font-extrabold text-slate-900 block text-xs">
                                    Rp{{ number_format($order->total_harga, 0, ',', '.') }}
                                </span>
                                <span class="text-[10px] text-emerald-700 font-semibold block mt-0.5">
                                    Petani ({{ $tokoPersen }}%): Rp{{ number_format($order->total_harga * ($tokoPersen / 100), 0, ',', '.') }}
                                </span>
                            </td>

                            <!-- Status -->
                            <td class="py-4 px-4 align-middle text-center">
                                @if($order->status === 'complete')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-circle-check mr-1 text-[8px]"></i> Lunas
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fas fa-clock mr-1 text-[8px]"></i> Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Transparansi -->
                            <td class="py-4 px-6 align-middle text-right">
                                <button 
                                    type="button" 
                                    onclick="openOrderModal({{ $order->id }})" 
                                    class="px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white font-bold text-xs transition inline-flex items-center gap-1.5 border border-brand-200 shadow-xs"
                                >
                                    <i class="fas fa-eye text-xs"></i>
                                    <span>Detail</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400 text-xs">
                                Belum ada riwayat pesanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 📈 SECTION ANALITIK CHART & GEOGRAFI                                      -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- LEFT: Tren Pertumbuhan Omzet & Volume Transaksi (7 Cols) -->
        <div class="lg:col-span-7 card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-5 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md bg-blue-50 text-blue-700 text-[10px] font-extrabold uppercase border border-blue-200">
                                Tren Omzet
                            </span>
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
                    <strong class="text-slate-900 font-bold block mb-0.5">Insight Penjualan:</strong>
                    Lonjakan volume transaksi tertinggi terjadi pada pukul <span class="font-bold text-slate-900">09:00 - 12:00</span> dan <span class="font-bold text-slate-900">19:00 - 21:00</span>. Permintaan produk segar mangga gedong gincu meningkat 3.2x lipat saat akhir pekan.
                </div>
            </div>
        </div>

        <!-- RIGHT: Pangsa Pasar Kategori Komoditas (5 Cols) -->
        <div class="lg:col-span-5 card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-700 text-[10px] font-extrabold uppercase border border-amber-200">
                                Distribusi Kategori
                            </span>
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

            <!-- Payment breakdown -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-xs">
                <div class="flex items-center justify-between mb-2">
                    <span class="font-bold text-slate-700">Metode Transaksi Terpopuler:</span>
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
    <!-- 🗺️ SECTION SENTRA PRODUKSI INDRAMAYU & LOGISTIK                          -->
    <!-- ========================================================================= -->
    <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-6 border-b border-slate-100">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md bg-purple-50 text-purple-700 text-[10px] font-extrabold uppercase border border-purple-200">
                        Distribusi & Logistik
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
                    <i class="fas fa-truck-ramp-box text-indigo-600"></i> Destinasi Pengiriman Pembeli
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
                        <p class="font-bold">Kecepatan Fulfillment:</p>
                        <p class="text-[11px] text-indigo-700">Rata-rata 24 jam sampai untuk Jabodetabek & Jabar</p>
                    </div>
                    <span class="px-3 py-1 bg-white text-indigo-600 font-extrabold rounded-lg shadow-sm">100% On-Time</span>
                </div>
            </div>

        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 👥 LEADERBOARD MITRA UMKM & ULASAN KONSUMEN                               -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Leaderboard Top Mitra UMKM (7 Cols) -->
        <div class="lg:col-span-7 card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md bg-indigo-50 text-indigo-700 text-[10px] font-extrabold uppercase border border-indigo-200">
                                Performa Mitra
                            </span>
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

        <!-- Right: Testimoni & Suara Konsumen (5 Cols) -->
        <div class="lg:col-span-5 card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between pb-5 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-md bg-rose-50 text-rose-700 text-[10px] font-extrabold uppercase border border-rose-200">
                                Ulasan Pembeli
                            </span>
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

<!-- ========================================================================= -->
<!-- 🔍 MODAL DETAIL TRANSPARANSI PESANAN (POP-UP INTERAKTIF)                   -->
<!-- ========================================================================= -->
<div id="orderDetailModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-brand-500/20 text-brand-400 flex items-center justify-center text-lg border border-brand-500/40">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-white font-display" id="modalOrderId">Rincian Transparansi Pesanan</h3>
                    <p class="text-xs text-slate-400" id="modalOrderDate">-</p>
                </div>
            </div>
            <button onclick="closeOrderModal()" class="text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Modal Body (Content Dynamically Loaded) -->
        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto" id="modalBody">
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 text-brand-600"></i>
                <p class="text-xs">Memuat data transparansi pesanan...</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">
                <i class="fas fa-lock text-emerald-500 mr-1"></i> Data diverifikasi oleh Sistem Superadmin
            </span>
            <div class="flex items-center gap-2">
                <button onclick="closeOrderModal()" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
                    Tutup
                </button>
                <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- Flatpickr CSS & JS for Dynamic Calendar Filtering -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .flatpickr-calendar {
        font-family: 'Plus Jakarta Sans', -apple-system, sans-serif !important;
        background: #ffffff !important;
        border-radius: 1.25rem !important;
        box-shadow: 0 25px 50px -12px rgba(15, 23, 42, 0.25), 0 0 0 1px rgba(15, 23, 42, 0.08) !important;
        border: 1px solid #cbd5e1 !important;
        padding: 0.75rem !important;
        z-index: 99999 !important;
        color: #0f172a !important;
        width: 320px !important;
    }
    .flatpickr-months {
        margin-bottom: 0.5rem !important;
        display: flex !important;
        align-items: center !important;
    }
    .flatpickr-months .flatpickr-month {
        color: #0f172a !important;
        fill: #0f172a !important;
        font-weight: 800 !important;
        height: 38px !important;
    }
    .flatpickr-current-month {
        font-size: 1rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        padding: 0 !important;
    }
    .flatpickr-current-month .cur-month {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-current-month input.cur-year {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-months .flatpickr-prev-month, 
    .flatpickr-months .flatpickr-next-month {
        color: #0f172a !important;
        fill: #0f172a !important;
        padding: 0.5rem !important;
        top: 8px !important;
    }
    .flatpickr-months .flatpickr-prev-month:hover svg, 
    .flatpickr-months .flatpickr-next-month:hover svg {
        fill: #059669 !important;
    }
    .flatpickr-weekdays {
        margin-bottom: 0.25rem !important;
    }
    span.flatpickr-weekday {
        color: #475569 !important;
        font-weight: 800 !important;
        font-size: 0.75rem !important;
    }
    .flatpickr-days {
        width: 100% !important;
    }
    .dayContainer {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        justify-content: space-around !important;
    }
    .flatpickr-day {
        color: #0f172a !important;
        font-weight: 700 !important;
        font-size: 0.85rem !important;
        border-radius: 0.65rem !important;
        border: 1px solid transparent !important;
        margin: 1.5px !important;
        height: 36px !important;
        line-height: 34px !important;
        max-width: 38px !important;
        cursor: pointer !important;
    }
    .flatpickr-day:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .flatpickr-day.prevMonthDay, 
    .flatpickr-day.nextMonthDay {
        color: #64748b !important;
        font-weight: 600 !important;
    }
    .flatpickr-day.prevMonthDay:hover, 
    .flatpickr-day.nextMonthDay:hover {
        background: #f1f5f9 !important;
        color: #0f172a !important;
    }
    .flatpickr-day.today {
        border-color: #10b981 !important;
        color: #059669 !important;
        font-weight: 900 !important;
    }
    .flatpickr-day.selected, 
    .flatpickr-day.startRange, 
    .flatpickr-day.endRange {
        background: #059669 !important;
        border-color: #059669 !important;
        color: #ffffff !important;
        font-weight: 900 !important;
    }
    .flatpickr-day.inRange {
        background: #d1fae5 !important;
        border-color: #a7f3d0 !important;
        color: #065f46 !important;
        font-weight: 800 !important;
    }
    .flatpickr-day.flatpickr-disabled,
    .flatpickr-day.flatpickr-disabled:hover {
        color: #94a3b8 !important;
        cursor: default !important;
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://npmcdn.com/flatpickr/dist/l10n/id.js"></script>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Function for Quick Filter Pills
function setFilterPeriod(period) {
    document.getElementById('filterPeriod').value = period;
    if (period !== 'custom') {
        document.getElementById('filterStartDate').value = '';
        document.getElementById('filterEndDate').value = '';
    }
    document.getElementById('dashboardDateFilterForm').submit();
}

function openOrderModal(orderId) {
    const modal = document.getElementById('orderDetailModal');
    const body = document.getElementById('modalBody');
    modal.classList.remove('hidden');

    body.innerHTML = `
        <div class="text-center py-10 text-slate-400">
            <i class="fas fa-circle-notch fa-spin text-3xl mb-2 text-brand-600"></i>
            <p class="text-xs font-semibold">Mengambil rincian transparansi pesanan...</p>
        </div>
    `;

    fetch(`/admin/pesanan/${orderId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(json => {
        if (!json.success) throw new Error('Gagal mengambil data');
        const data = json.data;

        document.getElementById('modalOrderId').innerText = `Rincian Pesanan #${data.order_id_midtrans}`;
        document.getElementById('modalOrderDate').innerText = `Waktu Transaksi: ${data.created_at}`;

        const isComplete = data.status === 'complete';

        body.innerHTML = `
            <!-- Status Pill Banner -->
            <div class="p-4 rounded-2xl ${isComplete ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200'} flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas ${isComplete ? 'fa-check-circle text-emerald-600' : 'fa-clock text-amber-600'} text-lg"></i>
                    <div>
                        <p class="text-xs font-extrabold ${isComplete ? 'text-emerald-900' : 'text-amber-900'} uppercase">Status: ${data.status}</p>
                        <p class="text-[11px] ${isComplete ? 'text-emerald-700' : 'text-amber-700'}">Fulfillment: ${data.status_pesanan || 'Diterima'}</p>
                    </div>
                </div>
                <span class="text-xs font-extrabold ${isComplete ? 'text-emerald-700 bg-emerald-100' : 'text-amber-700 bg-amber-100'} px-3 py-1 rounded-xl">
                    ${data.total_harga_formatted}
                </span>
            </div>

            <!-- Toko & Petani Asal (Seller Information) -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2">
                <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-100 px-2 py-0.5 rounded">
                        <i class="fas fa-store mr-1"></i> Asal Toko Mitra (Penjual)
                    </span>
                    <span class="text-xs font-bold text-slate-800">${data.toko.nama_toko}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Nama Petani / Pemilik:</span>
                        <strong class="text-slate-800">${data.toko.pemilik}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">No. Telepon Toko:</span>
                        <strong class="text-slate-800">${data.toko.no_telp}</strong>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 block text-[10px]">Alamat Sentra Kebun:</span>
                        <span class="text-slate-700">${data.toko.alamat}</span>
                    </div>
                </div>
            </div>

            <!-- Produk Komoditas Yang Dipesan -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">
                    <i class="fas fa-box-open mr-1"></i> Komoditas Yang Dibeli
                </span>
                <div class="flex items-center gap-3">
                    <img src="${data.produk.gambar_url}" alt="${data.produk.nama}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-sm">
                    <div class="flex-1">
                        <h4 class="font-extrabold text-xs text-slate-900">${data.produk.nama}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Kategori: <strong class="text-slate-700">${data.produk.kategori}</strong></p>
                        <div class="flex items-center justify-between mt-1 text-xs">
                            <span class="text-slate-600 font-bold">${data.jumlah} Pcs/Kg × ${data.produk.harga_formatted}</span>
                            <span class="font-extrabold text-slate-900">${data.total_harga_formatted}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profil Pembeli & Destinasi -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700 bg-purple-100 px-2 py-0.5 rounded">
                    <i class="fas fa-user-check mr-1"></i> Data Pembeli & Destinasi Pengiriman
                </span>
                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Nama Pembeli:</span>
                        <strong class="text-slate-800">${data.pembeli.name}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">Kontak / HP:</span>
                        <strong class="text-slate-800">${data.pembeli.phone}</strong>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 block text-[10px]">Alamat Tujuan:</span>
                        <span class="text-slate-700">${data.pembeli.alamat}</span>
                    </div>
                </div>
            </div>

            <!-- Transparansi Bagi Hasil -->
            <div class="p-4 rounded-2xl bg-brand-50/70 border border-brand-100 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-extrabold text-brand-900">Transparansi Finansial Marketplace:</span>
                    <span class="text-brand-700 font-bold">100% Tercatat</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-brand-100 text-[11px]">
                    <span class="text-slate-600">Hak Omzet Mitra Petani (80%):</span>
                    <strong class="text-emerald-700">${data.bagi_hasil.omzet_petani}</strong>
                </div>
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-600">Komisi Operasional Platform (20%):</span>
                    <strong class="text-indigo-700">${data.bagi_hasil.komisi_platform}</strong>
                </div>
            </div>
        `;
    })
    .catch(err => {
        body.innerHTML = `
            <div class="text-center py-8 text-rose-500">
                <i class="fas fa-triangle-exclamation text-3xl mb-2"></i>
                <p class="text-xs font-bold">Gagal memuat detail pesanan.</p>
            </div>
        `;
    });
}

function closeOrderModal() {
    document.getElementById('orderDetailModal').classList.add('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    
    // -----------------------------------------------------------
    // 0. Dynamic Calendar (Flatpickr API) Initialization
    // -----------------------------------------------------------
    const calendarEl = document.getElementById('flatpickrCalendar');
    if (calendarEl && typeof flatpickr !== 'undefined') {
        const initialStart = "{{ $startDateInput ?? ($startDate ? $startDate->format('Y-m-d') : '') }}";
        const initialEnd = "{{ $endDateInput ?? ($endDate ? $endDate->format('Y-m-d') : '') }}";
        const defaultDates = (initialStart && initialEnd) ? [initialStart, initialEnd] : (initialStart ? [initialStart] : []);

        flatpickr(calendarEl, {
            mode: "range",
            locale: "id",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            altInputClass: "w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-brand-500 focus:border-brand-500 focus:outline-hidden transition shadow-xs cursor-pointer",
            defaultDate: defaultDates,
            showMonths: 1,
            animate: true,
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const startStr = instance.formatDate(selectedDates[0], "Y-m-d");
                    const endStr = instance.formatDate(selectedDates[1], "Y-m-d");

                    document.getElementById('filterPeriod').value = 'custom';
                    document.getElementById('filterStartDate').value = startStr;
                    document.getElementById('filterEndDate').value = endStr;
                    document.getElementById('dashboardDateFilterForm').submit();
                } else if (selectedDates.length === 1) {
                    const singleStr = instance.formatDate(selectedDates[0], "Y-m-d");
                    document.getElementById('filterPeriod').value = 'custom';
                    document.getElementById('filterStartDate').value = singleStr;
                    document.getElementById('filterEndDate').value = singleStr;
                    document.getElementById('dashboardDateFilterForm').submit();
                }
            }
        });
    }

    // -----------------------------------------------------------
    // 1. Line/Area Chart: Revenue Trend
    // -----------------------------------------------------------
    const ctxRevenue = document.getElementById('revenueTrendChart')?.getContext('2d');
    
    if (ctxRevenue) {
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
                                if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'K';
                                return 'Rp ' + val;
                            }
                        }
                    }
                }
            }
        });
    }

    // -----------------------------------------------------------
    // 2. Doughnut Chart: Category Market Share
    // -----------------------------------------------------------
    const ctxCategory = document.getElementById('categoryShareChart')?.getContext('2d');
    
    if (ctxCategory) {
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
    }

});
</script>
@endpush
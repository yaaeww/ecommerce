@extends('layouts.app')

@section('page_title', 'Dashboard Penjual')

@section('content')
<div class="space-y-8 pb-12">
    
    <!-- ========================================================================= -->
    <!-- 🌟 WELCOME HEADER BANNER (EXECUTIVE STORE OVERVIEW)                      -->
    <!-- ========================================================================= -->
    <div class="relative p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <!-- Ambient Glow -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-emerald-50 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-12 -left-12 w-48 h-48 bg-brand-50 rounded-full blur-2xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold tracking-wide">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>{{ $umkm ? $umkm->nama_toko : 'Mitra Kebun & UMKM' }}</span>
                <span class="text-slate-300">•</span>
                <span class="text-[10px] font-extrabold uppercase text-emerald-800">{{ $umkm->status ?? 'Aktif' }}</span>
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-display">
                Selamat Datang, {{ Auth::user()->name }}! 👋
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                Pantau performa penjualan mangga, siklus pengiriman pesanan, dan pencairan saldo kebun Anda secara transparan.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2.5 relative z-10 shrink-0">
            <a 
                href="{{ route('penjual.penarikan.index') }}" 
                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2"
            >
                <i class="fas fa-wallet text-xs"></i>
                <span>Tarik Saldo</span>
            </a>
            <a 
                href="{{ route('penjual.produk.create') }}" 
                class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2"
            >
                <i class="fas fa-plus text-xs"></i>
                <span>Tambah Produk</span>
            </a>
            <a 
                href="{{ route('penjual.umkm.index') }}" 
                class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs rounded-xl transition flex items-center gap-2"
            >
                <i class="fas fa-store text-xs text-slate-400"></i>
                <span>Profil Toko</span>
            </a>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- 📅 DYNAMIC CALENDAR & DATE RANGE FILTER BAR                               -->
    <!-- ========================================================================= -->
    <div class="card p-4 sm:p-5 bg-white border border-slate-200/80 rounded-3xl shadow-sm space-y-3.5">
        <form id="sellerDateFilterForm" action="{{ route('penjual.dashboard') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
            
            <!-- Hidden inputs for custom date selection -->
            <input type="hidden" name="period" id="sellerFilterPeriod" value="{{ $period ?? 'all' }}">
            <input type="hidden" name="start_date" id="sellerFilterStartDate" value="{{ $startDateInput ?? ($startDate ? $startDate->format('Y-m-d') : '') }}">
            <input type="hidden" name="end_date" id="sellerFilterEndDate" value="{{ $endDateInput ?? ($endDate ? $endDate->format('Y-m-d') : '') }}">

            <!-- Left: Calendar API Input & Presets -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 flex-wrap">
                
                <!-- Dynamic Interactive Calendar Range Input -->
                <div class="relative min-w-[260px] sm:w-72">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-calendar-days text-sm"></i>
                    </div>
                    <input 
                        type="text" 
                        id="flatpickrSellerCalendar" 
                        placeholder="Pilih Rentang Tanggal Kalender..." 
                        class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer"
                        readonly
                    >
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        @if(($period ?? 'all') !== 'all')
                            <a href="{{ route('penjual.dashboard') }}" title="Reset Filter" class="text-slate-400 hover:text-rose-500 transition text-xs">
                                <i class="fas fa-circle-xmark"></i>
                            </a>
                        @else
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 pointer-events-none"></i>
                        @endif
                    </div>
                </div>

                <!-- Quick Filter Period Pills -->
                <div class="flex items-center gap-1.5 flex-wrap">
                    <button type="button" onclick="setSellerFilterPeriod('all')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Waktu
                    </button>
                    <button type="button" onclick="setSellerFilterPeriod('today')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'today' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Hari Ini
                    </button>
                    <button type="button" onclick="setSellerFilterPeriod('7days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '7days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        7 Hari Terakhir
                    </button>
                    <button type="button" onclick="setSellerFilterPeriod('30days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '30days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        30 Hari Terakhir
                    </button>
                    <button type="button" onclick="setSellerFilterPeriod('this_month')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_month' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setSellerFilterPeriod('this_year')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_year' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Tahun Ini
                    </button>
                </div>

            </div>

            <!-- Right: Active Period Badge & Reset Button -->
            <div class="flex items-center justify-between sm:justify-end gap-2.5 pt-2 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs font-bold">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600"></span>
                    </span>
                    <span class="text-[11px] text-slate-500 font-normal">Periode:</span>
                    <strong class="font-extrabold text-emerald-800">{{ $activePeriodLabel }}</strong>
                </div>

                @if(($period ?? 'all') !== 'all')
                    <a href="{{ route('penjual.dashboard') }}" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5 shadow-2xs">
                        <i class="fas fa-rotate-left text-[10px]"></i>
                        <span>Reset</span>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 📊 TOP EXECUTIVE STAT CARDS (FINANSIAL & METRIK KUNCI)                   -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Card 1: Omzet Bersih Toko -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-xs hover:border-emerald-300 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 tracking-wide">Omzet Bersih Toko ({{ $tokoPersen }}%)</span>
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition flex items-center justify-center text-sm shadow-xs">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Rp{{ number_format($omzetBersihToko, 0, ',', '.') }}
            </p>
            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mt-2.5 pt-2.5 border-t border-slate-100">
                <span>Bruto: Rp{{ number_format($totalOmzetKotor, 0, ',', '.') }}</span>
                <span class="text-emerald-700 font-bold bg-emerald-50 px-1.5 py-0.5 rounded">Lunas</span>
            </div>
        </div>

        <!-- Card 2: Saldo Siap Ditarik -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-xs hover:border-brand-300 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 tracking-wide">Saldo Siap Ditarik</span>
                <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 group-hover:bg-brand-600 group-hover:text-white transition flex items-center justify-center text-sm shadow-xs">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-brand-600 font-display tracking-tight">
                Rp{{ number_format($saldoTersedia, 0, ',', '.') }}
            </p>
            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mt-2.5 pt-2.5 border-t border-slate-100">
                @if($totalPenarikanPending > 0)
                    <span class="text-amber-600 font-bold">Proses: Rp{{ number_format($totalPenarikanPending, 0, ',', '.') }}</span>
                @else
                    <span class="text-slate-400">Dicairkan: Rp{{ number_format($totalPenarikanApproved, 0, ',', '.') }}</span>
                @endif
                <a href="{{ route('penjual.penarikan.index') }}" class="text-brand-600 font-bold hover:underline">Tarik &rarr;</a>
            </div>
        </div>

        <!-- Card 3: Total Volume Terjual -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-xs hover:border-amber-300 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 tracking-wide">Volume Terjual</span>
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 group-hover:bg-amber-600 group-hover:text-white transition flex items-center justify-center text-sm shadow-xs">
                    <i class="fas fa-box-open"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ number_format($totalVolumeTerjual) }} <span class="text-sm font-bold text-slate-400">Kg / Unit</span>
            </p>
            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mt-2.5 pt-2.5 border-t border-slate-100">
                <span>Stok Gudang: {{ $totalStok }} Unit</span>
                <span class="text-slate-600 font-bold">{{ $totalProduk }} Produk</span>
            </div>
        </div>

        <!-- Card 4: Rata-Rata Nilai Pesanan (AOV) -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-xs hover:border-indigo-300 transition-all rounded-3xl group">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 tracking-wide">Rata-Rata Pesanan (AOV)</span>
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition flex items-center justify-center text-sm shadow-xs">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Rp{{ number_format($aov, 0, ',', '.') }}
            </p>
            <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400 mt-2.5 pt-2.5 border-t border-slate-100">
                <span>{{ $totalPembeliUnik }} Pembeli Unik</span>
                <span class="text-indigo-600 font-bold">{{ $repeatBuyersCount }} Repeat</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 📈 VISUAL CHARTS (TREN PENDAPATAN & SIKLUS PENGIRIMAN FULFILLMENT)       -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Chart 1: Tren Pendapatan & Volume Bulanan (7 Cols) -->
        <div class="lg:col-span-7 card p-6 bg-white border border-slate-200/80 shadow-xs rounded-3xl">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 font-display">Tren Pendapatan & Penjualan Kebun</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Pertumbuhan omzet bersih dan volume transaksi 6 bulan terakhir</p>
                </div>
                <span class="px-2.5 py-1 rounded-lg bg-slate-50 border border-slate-200 text-slate-600 text-xs font-bold">
                    6 Bulan
                </span>
            </div>

            <!-- Canvas Chart.js -->
            <div class="relative h-64 w-full">
                <canvas id="sellerRevenueChart"></canvas>
            </div>
        </div>

        <!-- Chart 2: Status Fulfillment & SLA Tracker (5 Cols) -->
        <div class="lg:col-span-5 card p-6 bg-white border border-slate-200/80 shadow-xs rounded-3xl flex flex-col justify-between">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                <div>
                    <h3 class="text-base font-extrabold text-slate-900 font-display">Siklus Pengiriman & SLA</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Monitoring pemrosesan pesanan mitra</p>
                </div>
                @if($ordersOverdue > 0)
                    <span class="px-2 py-0.5 rounded-full bg-rose-50 text-rose-700 border border-rose-200 text-[10px] font-black animate-pulse">
                        {{ $ordersOverdue }} Terlambat (>24h)
                    </span>
                @else
                    <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 text-[10px] font-bold">
                        SLA Aman
                    </span>
                @endif
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 items-center gap-4 my-auto py-2">
                <div class="relative h-44 flex items-center justify-center">
                    <canvas id="sellerFulfillmentChart"></canvas>
                </div>

                <div class="space-y-2.5 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-xl bg-amber-50/70 border border-amber-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                            <span class="font-bold text-slate-700">Perlu Diproses</span>
                        </div>
                        <span class="font-extrabold text-amber-700">{{ $ordersMenunggu }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-xl bg-indigo-50/70 border border-indigo-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                            <span class="font-bold text-slate-700">Sedang Dikemas</span>
                        </div>
                        <span class="font-extrabold text-indigo-700">{{ $ordersDikemas }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-xl bg-blue-50/70 border border-blue-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                            <span class="font-bold text-slate-700">Dalam Kiriman</span>
                        </div>
                        <span class="font-extrabold text-blue-700">{{ $ordersDikirim }}</span>
                    </div>

                    <div class="flex items-center justify-between p-2 rounded-xl bg-emerald-50/70 border border-emerald-100">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                            <span class="font-bold text-slate-700">Sampai & Selesai</span>
                        </div>
                        <span class="font-extrabold text-emerald-700">{{ $ordersSelesai }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                <a href="{{ route('penjual.pesanan.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1.5">
                    <span>Buka Manajemen Pesanan</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 🧭 4 STRATEGIC ANALYTICS PILLARS (AUDIT KINERJA OPERASIONAL TOKO)        -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Pillar 1: Kepuasan Pelanggan & Sentimen Ulasan -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-xs rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="fas fa-star text-amber-400"></i> Kepuasan Pelanggan
                    </span>
                    <span class="text-[10px] font-extrabold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                        {{ $csatPersen }}% CSAT
                    </span>
                </div>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-3xl font-extrabold text-slate-900 font-display">{{ number_format($avgRating, 1) }}</span>
                    <span class="text-xs font-bold text-slate-400">/ 5.0 Skala Kepuasan</span>
                </div>
                <p class="text-xs text-slate-500 leading-snug">
                    Berdasarkan <strong>{{ $totalUlasan }} ulasan</strong> terverifikasi dari pembeli komoditas toko Anda.
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400">Rating Rata-Rata</span>
                <div class="flex text-amber-400 text-xs">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star {{ $i <= round($avgRating) ? '' : 'text-slate-200' }}"></i>
                    @endfor
                </div>
            </div>
        </div>

        <!-- Pillar 2: Profil & Retensi Pembeli -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-xs rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="fas fa-user-group text-indigo-500"></i> Retensi Konsumen
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-700 border border-indigo-200">
                        Loyalitas
                    </span>
                </div>
                <div class="flex items-baseline gap-2 mb-2">
                    <span class="text-3xl font-extrabold text-slate-900 font-display">{{ $totalPembeliUnik }}</span>
                    <span class="text-xs font-bold text-slate-400">Pelanggan Unik</span>
                </div>
                <p class="text-xs text-slate-500 leading-snug">
                    Tercatat <strong>{{ $repeatBuyersCount }} pembeli loyal</strong> yang telah melakukan pemesanan ulang (repeat purchase).
                </p>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-400">Tingkat Repeat Order</span>
                <span class="font-extrabold text-indigo-600">
                    {{ $totalPembeliUnik > 0 ? round(($repeatBuyersCount / $totalPembeliUnik) * 100) : 0 }}%
                </span>
            </div>
        </div>

        <!-- Pillar 3: Sebaran Destinasi Pengiriman -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-xs rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="fas fa-location-dot text-emerald-500"></i> Destinasi Terbesar
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Logistik
                    </span>
                </div>
                <div class="space-y-2 my-1">
                    @foreach(array_slice($wilayahSebaran, 0, 2) as $w)
                        <div>
                            <div class="flex items-center justify-between text-[11px] font-bold text-slate-700 mb-0.5">
                                <span class="truncate max-w-[150px]">{{ $w['nama'] }}</span>
                                <span>{{ $w['persen'] }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-slate-100 rounded-full overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $w['persen'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="mt-3 pt-3 border-t border-slate-100 text-[11px] text-slate-400">
                Ekspedisi utama: <strong>J&T Fresh & Instant Kurir</strong>
            </div>
        </div>

        <!-- Pillar 4: Efisiensi & Transparansi Biaya -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-xs rounded-3xl flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="fas fa-handshake text-brand-600"></i> Bagi Hasil Transparan
                    </span>
                    <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-50 text-brand-700 border border-brand-200">
                        SOP Otomatis
                    </span>
                </div>
                <div class="space-y-2 text-xs">
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-semibold">Hak Toko Kebun</span>
                        <span class="font-extrabold text-emerald-700">{{ $tokoPersen }}%</span>
                    </div>
                    <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-slate-500 font-semibold">Komisi Platform</span>
                        <span class="font-extrabold text-indigo-700">{{ $komisiPersen }}%</span>
                    </div>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="text-slate-400">Kontribusi Platform</span>
                <span class="font-extrabold text-slate-700">Rp{{ number_format($komisiPlatform, 0, ',', '.') }}</span>
            </div>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 📦 TABEL PRODUK TERLARIS & 3 PESANAN TERBARU                              -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Produk Terlaris (7 Cols) -->
        <div class="lg:col-span-7 card bg-white border border-slate-200/80 shadow-xs rounded-3xl overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 font-display flex items-center gap-2">
                            <i class="fas fa-fire text-amber-500"></i>
                            <span>Komoditas Unggulan Terlaris</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">Peringkat produk berdasarkan total volume penjualan</p>
                    </div>
                    <span class="px-2.5 py-1 rounded-full text-[10px] font-black bg-amber-50 text-amber-700 border border-amber-200">
                        TOP PERFORMERS
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full text-left">
                        <thead>
                            <tr>
                                <th class="w-12">Rank</th>
                                <th>Produk</th>
                                <th>Harga Jual</th>
                                <th>Terjual</th>
                                <th class="text-right">Pendapatan Kotor</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs">
                            @forelse($produkTerlaris as $index => $produk)
                                @php
                                    $gambarPath = $produk->gambar ?? '';
                                    $gambarUrl = ($gambarPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarPath))
                                        ? asset('storage/' . $gambarPath)
                                        : asset('aset/finalisasi logo.png');
                                @endphp
                                <tr class="hover:bg-slate-50/70 transition">
                                    <td class="align-middle text-center">
                                        <span class="w-6 h-6 rounded-full inline-flex items-center justify-center text-[10px] font-black {{ $index == 0 ? 'bg-amber-100 text-amber-800' : ($index == 1 ? 'bg-slate-200 text-slate-700' : 'bg-slate-100 text-slate-500') }}">
                                            #{{ $index + 1 }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $gambarUrl }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shadow-2xs shrink-0" alt="{{ $produk->nama }}">
                                            <div class="min-w-0">
                                                <h5 class="font-extrabold text-xs text-slate-900 truncate max-w-[160px]">{{ $produk->nama }}</h5>
                                                <span class="text-[10px] text-slate-400">Stok: {{ $produk->stok }} Unit</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle font-bold text-slate-700">
                                        Rp{{ number_format($produk->harga, 0, ',', '.') }}
                                    </td>
                                    <td class="align-middle">
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs font-extrabold bg-brand-50 text-brand-700">
                                            {{ $produk->total_unit }} Unit
                                        </span>
                                    </td>
                                    <td class="align-middle text-right font-extrabold text-emerald-600">
                                        Rp{{ number_format($produk->total_penjualan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-10 text-slate-400 text-xs">
                                        <i class="fas fa-box-open text-2xl text-slate-300 mb-2 block"></i>
                                        Belum ada data penjualan komoditas.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <a href="{{ route('penjual.produk.index') }}" class="text-xs font-bold text-slate-600 hover:text-brand-600 transition flex items-center gap-1.5">
                    <span>Lihat Seluruh Katalog Produk ({{ $totalProduk }})</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- 3 Pesanan Terbaru Masuk (5 Cols) -->
        <div class="lg:col-span-5 card bg-white border border-slate-200/80 shadow-xs rounded-3xl overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-extrabold text-slate-900 font-display flex items-center gap-2">
                            <i class="fas fa-truck-fast text-brand-600"></i>
                            <span>Pesanan Terbaru Toko</span>
                        </h3>
                        <p class="text-xs text-slate-400 mt-0.5">3 transaksi terkini yang membutuhkan pemrosesan</p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-600">
                        Real-time
                    </span>
                </div>

                <div class="divide-y divide-slate-100 p-2">
                    @forelse($recentOrders as $order)
                        <div class="p-4 hover:bg-slate-50/70 transition rounded-2xl">
                            <div class="flex items-start justify-between gap-2 mb-2">
                                <div>
                                    <span class="text-[10px] font-extrabold text-slate-400">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                                    <h5 class="text-xs font-bold text-slate-900">{{ $order->name ?: ($order->user->name ?? 'Pembeli') }}</h5>
                                </div>
                                @if($order->status === 'complete')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        Lunas
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        Pending
                                    </span>
                                @endif
                            </div>

                            <div class="flex items-center justify-between text-xs text-slate-500">
                                <span class="truncate max-w-[180px] font-medium text-slate-700">{{ $order->produk->nama ?? 'Komoditas' }} ({{ $order->jumlah }}x)</span>
                                <span class="font-extrabold text-slate-900">Rp{{ number_format($order->total_harga, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex items-center justify-between text-[10px] text-slate-400 mt-2.5 pt-2 border-t border-slate-100">
                                <span><i class="fas fa-clock mr-1"></i>{{ $order->created_at ? $order->created_at->diffForHumans() : 'Baru saja' }}</span>
                                <span class="capitalize font-semibold text-slate-600">
                                    Status: {{ str_replace('_', ' ', $order->status_pesanan ?? 'Menunggu Diproses') }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center text-slate-400 text-xs">
                            <i class="fas fa-receipt text-2xl text-slate-300 mb-2 block"></i>
                            Belum ada pesanan masuk.
                        </div>
                    @endforelse
                </div>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <a href="{{ route('penjual.pesanan.index') }}" class="text-xs font-bold text-brand-600 hover:text-brand-700 transition flex items-center gap-1.5">
                    <span>Buka Semua Pesanan Masuk</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>

</div>

<!-- ========================================================================= -->
<!-- 📊 CHART.JS & FLATPICKR CALENDAR SCRIPT                                   -->
<!-- ========================================================================= -->
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Custom High-Contrast Styling for Flatpickr in Seller Dashboard */
    .flatpickr-calendar {
        background: #ffffff !important;
        border-radius: 1.25rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
        padding: 0.75rem !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        width: 320px !important;
        color: #0f172a !important;
    }
    .flatpickr-months {
        margin-bottom: 0.5rem !important;
    }
    .flatpickr-months .flatpickr-month {
        color: #0f172a !important;
        fill: #0f172a !important;
        height: 36px !important;
    }
    .flatpickr-current-month {
        font-size: 0.95rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        padding-top: 4px !important;
    }
    .flatpickr-current-month .cur-month {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-current-month input.cur-year {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-weekdays {
        margin-bottom: 0.5rem !important;
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
        border-radius: 0.65rem !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        height: 38px !important;
        line-height: 38px !important;
        max-width: 38px !important;
        margin: 2px 0 !important;
        border: 1px solid transparent !important;
    }
    .flatpickr-day:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .flatpickr-day.today {
        border-color: #10b981 !important;
        color: #047857 !important;
        font-weight: 900 !important;
        background: #ecfdf5 !important;
    }
    .flatpickr-day.selected, 
    .flatpickr-day.startRange, 
    .flatpickr-day.endRange {
        background: #059669 !important;
        color: #ffffff !important;
        border-color: #059669 !important;
        font-weight: 800 !important;
    }
    .flatpickr-day.inRange {
        background: #d1fae5 !important;
        border-color: #a7f3d0 !important;
        color: #065f46 !important;
        box-shadow: -5px 0 0 #d1fae5, 5px 0 0 #d1fae5 !important;
    }
    .flatpickr-day.prevMonthDay, 
    .flatpickr-day.nextMonthDay {
        color: #94a3b8 !important;
        font-weight: 500 !important;
    }
    .flatpickr-prev-month svg, 
    .flatpickr-next-month svg {
        fill: #0f172a !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
    // Seller Preset Filter Handler
    function setSellerFilterPeriod(periodName) {
        document.getElementById('sellerFilterPeriod').value = periodName;
        document.getElementById('sellerFilterStartDate').value = '';
        document.getElementById('sellerFilterEndDate').value = '';
        document.getElementById('sellerDateFilterForm').submit();
    }

    document.addEventListener('DOMContentLoaded', function () {
        
        // 0. Initialize Dynamic Calendar Range Picker (Flatpickr API)
        const calendarEl = document.getElementById('flatpickrSellerCalendar');
        const initialStart = "{{ $startDateInput ?? ($startDate ? $startDate->format('Y-m-d') : '') }}";
        const initialEnd = "{{ $endDateInput ?? ($endDate ? $endDate->format('Y-m-d') : '') }}";
        let defaultDates = [];
        if (initialStart && initialEnd) {
            defaultDates = [initialStart, initialEnd];
        } else if (initialStart) {
            defaultDates = [initialStart];
        }

        if (calendarEl && typeof flatpickr !== 'undefined') {
            flatpickr(calendarEl, {
                mode: "range",
                locale: "id",
                dateFormat: "Y-m-d",
                altInput: true,
                altFormat: "d M Y",
                altInputClass: "w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer",
                defaultDate: defaultDates,
                showMonths: 1,
                animate: true,
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        const startStr = instance.formatDate(selectedDates[0], "Y-m-d");
                        const endStr = instance.formatDate(selectedDates[1], "Y-m-d");

                        document.getElementById('sellerFilterPeriod').value = 'custom';
                        document.getElementById('sellerFilterStartDate').value = startStr;
                        document.getElementById('sellerFilterEndDate').value = endStr;
                        document.getElementById('sellerDateFilterForm').submit();
                    } else if (selectedDates.length === 1) {
                        const singleStr = instance.formatDate(selectedDates[0], "Y-m-d");
                        document.getElementById('sellerFilterPeriod').value = 'custom';
                        document.getElementById('sellerFilterStartDate').value = singleStr;
                        document.getElementById('sellerFilterEndDate').value = singleStr;
                        document.getElementById('sellerDateFilterForm').submit();
                    }
                }
            });
        }

        // 1. Line / Area Chart: Tren Pendapatan & Volume Bulanan Penjual
        const ctxRev = document.getElementById('sellerRevenueChart');
        if (ctxRev) {
            const gradient = ctxRev.getContext('2d').createLinearGradient(0, 0, 0, 240);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)'); // Emerald gradient
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.0)');

            new Chart(ctxRev, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels) !!},
                    datasets: [
                        {
                            label: 'Omzet Bersih Toko (Rp)',
                            data: {!! json_encode($chartRevenue) !!},
                            borderColor: '#10b981',
                            borderWidth: 3,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Jumlah Transaksi Selesai',
                            data: {!! json_encode($chartOrders) !!},
                            borderColor: '#6366f1',
                            borderWidth: 2,
                            borderDash: [4, 4],
                            backgroundColor: 'transparent',
                            fill: false,
                            tension: 0.3,
                            pointBackgroundColor: '#6366f1',
                            pointRadius: 3,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false,
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            align: 'end',
                            labels: {
                                boxWidth: 12,
                                font: {
                                    family: "'Plus Jakarta Sans', sans-serif",
                                    size: 11,
                                    weight: 'bold'
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { family: "'Outfit', sans-serif", size: 12, weight: 'bold' },
                            bodyFont: { family: "'Plus Jakarta Sans', sans-serif", size: 11 },
                            padding: 10,
                            cornerRadius: 12,
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label.includes('Omzet')) {
                                        return label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                                    }
                                    return label + ': ' + context.raw + ' Pesanan';
                                }
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 10, weight: 'bold' },
                                color: '#94a3b8'
                            }
                        },
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            grid: { color: '#f1f5f9' },
                            ticks: {
                                font: { family: "'Plus Jakarta Sans', sans-serif", size: 10 },
                                color: '#94a3b8',
                                callback: function(value) {
                                    return 'Rp ' + (value >= 1000000 ? (value/1000000).toFixed(1) + 'M' : (value/1000).toFixed(0) + 'k');
                                }
                            }
                        },
                        y1: {
                            type: 'linear',
                            display: false,
                            position: 'right',
                            grid: { drawOnChartArea: false }
                        }
                    }
                }
            });
        }

        // 2. Doughnut Chart: Distribusi Status Fulfillment & SLA
        const ctxFulfill = document.getElementById('sellerFulfillmentChart');
        if (ctxFulfill) {
            new Chart(ctxFulfill, {
                type: 'doughnut',
                data: {
                    labels: ['Perlu Diproses', 'Dikemas', 'Dikirim', 'Selesai'],
                    datasets: [{
                        data: [
                            {{ $ordersMenunggu }},
                            {{ $ordersDikemas }},
                            {{ $ordersDikirim }},
                            {{ $ordersSelesai > 0 ? $ordersSelesai : ($ordersMenunggu + $ordersDikemas + $ordersDikirim == 0 ? 1 : 0) }}
                        ],
                        backgroundColor: ['#f59e0b', '#6366f1', '#3b82f6', '#10b981'],
                        borderWidth: 3,
                        borderColor: '#ffffff',
                        hoverOffset: 4
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
                            cornerRadius: 10,
                            padding: 8,
                            titleFont: { size: 11, weight: 'bold' },
                            bodyFont: { size: 10 }
                        }
                    }
                }
            });
        }

    });
</script>
@endpush
@endsection

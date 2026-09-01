@extends('layouts.app')

@section('page_title', 'Audit & Transparansi Pesanan')

@section('content')
<div class="space-y-6 pb-12">

    <!-- 🖨️ KOP SURAT & HEADER RESMI KHUSUS CETAK LAPORAN (Hanya Tampil Saat Print) -->
    <div class="hidden print:block mb-6 text-slate-900 font-sans border-b-2 border-slate-900 pb-4">
        <!-- Kop Surat Header -->
        <div class="flex items-center justify-between gap-4 pb-3">
            <div class="flex items-center gap-3">
                <div class="w-14 h-14 rounded-xl border border-slate-300 p-1 flex items-center justify-center">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                </div>
                <div>
                    <h2 class="text-sm font-extrabold tracking-tight uppercase leading-tight">
                        PEMERINTAH KABUPATEN INDRAMAYU
                    </h2>
                    <h1 class="text-base font-black tracking-normal uppercase leading-tight text-slate-900 font-display">
                        SENTRA DIGITAL AGRO-COMMERCE & PENGEMBANGAN UMKM MANGGA
                    </h1>
                    <p class="text-[10px] text-slate-600 mt-0.5">
                        Pusat Transparansi, Escrow & Audit Marketplace "Juragan Pelem" — Jl. Mayor Dasuki No. 12, Indramayu
                    </p>
                </div>
            </div>
            <div class="text-right text-[10px] space-y-0.5 border-l border-slate-300 pl-3">
                <p><strong class="font-bold">Kode Dokumen:</strong> RKP-TRX-{{ date('Ymd') }}</p>
                <p><strong class="font-bold">Sifat:</strong> Resmi / Audit Finansial</p>
                <p><strong class="font-bold">Dicetak:</strong> {{ now()->translatedFormat('d M Y, H:i') }} WIB</p>
            </div>
        </div>

        <div class="h-0.5 bg-slate-900 w-full mb-1"></div>
        <div class="h-px bg-slate-400 w-full mb-4"></div>

        <!-- Judul Laporan & Metadata Filter -->
        <div class="text-center py-2">
            <h3 class="text-sm font-black uppercase tracking-wider text-slate-900 font-display">
                LAPORAN REKAPITULASI AUDIT & LOG TRANSAKSI PESANAN
            </h3>
            <p class="text-[11px] text-slate-600 mt-0.5">
                Periode: <strong class="font-bold text-slate-900">{{ $activePeriodLabel ?? 'Semua Waktu' }}</strong>
                | Status Data: <strong class="font-bold text-slate-900">{{ $status === 'complete' ? 'Transaksi Sukses / Lunas' : ($status === 'pending' ? 'Menunggu Pembayaran' : 'Seluruh Status Pesanan') }}</strong>
                @if($umkmId && $umkms->find($umkmId))
                    | Toko Mitra: <strong class="font-bold text-slate-900">{{ $umkms->find($umkmId)->nama_toko }}</strong>
                @endif
                | Operator Superadmin: <strong class="font-bold text-slate-900">{{ Auth::user()->name ?? 'Admin' }}</strong>
            </p>
        </div>

        <!-- Ringkasan Finansial Eksekutif (Print Mode) -->
        <div class="grid grid-cols-4 gap-3 my-3 text-[10px] border border-slate-300 rounded-lg p-2.5 bg-slate-50">
            <div class="p-1.5 border-r border-slate-300">
                <span class="text-slate-500 block uppercase font-bold text-[9px]">Total Transaksi</span>
                <strong class="text-xs font-black text-slate-900">{{ $totalOrders }} Pesanan</strong>
            </div>
            <div class="p-1.5 border-r border-slate-300">
                <span class="text-slate-500 block uppercase font-bold text-[9px]">Transaksi Lunas Sukses</span>
                <strong class="text-xs font-black text-emerald-800">{{ $totalSuccess }} Lunas (100%)</strong>
            </div>
            <div class="p-1.5 border-r border-slate-300">
                <span class="text-slate-500 block uppercase font-bold text-[9px]">Menunggu Pembayaran</span>
                <strong class="text-xs font-black text-amber-800">{{ $totalPending }} Pending</strong>
            </div>
            <div class="p-1.5">
                <span class="text-slate-500 block uppercase font-bold text-[9px]">Total Nilai Omzet Lunas</span>
                <strong class="text-xs font-black text-indigo-900">Rp{{ number_format($totalNominal, 0, ',', '.') }}</strong>
                <span class="text-[8px] text-slate-500 block">Petani (80%): Rp{{ number_format($totalNominal * ($tokoPersen/100), 0, ',', '.') }}</span>
            </div>
        </div>
    </div>
    
    <!-- Top Action Bar (Screen Only) -->
    <div class="print:hidden flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Audit & Transparansi Pesanan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Monitoring log seluruh transaksi marketplace lintas UMKM, pembagian komisi, dan audit invoice.
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            <button onclick="window.print()" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2 cursor-pointer">
                <i class="fas fa-print text-xs text-slate-400"></i>
                <span>Cetak Rekap Audit</span>
            </button>
        </div>
    </div>

    <!-- 4 Stats Cards (Screen Only) -->
    <div class="print:hidden grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pesanan</span>
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 font-display">{{ $totalOrders }} <span class="text-xs font-sans text-slate-400 font-normal">Transaksi</span></p>
            <p class="text-[11px] text-slate-400 mt-1">Periode: {{ $activePeriodLabel }}</p>
        </div>

        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Transaksi Sukses</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-emerald-600 font-display">{{ $totalSuccess }} <span class="text-xs font-sans text-slate-400 font-normal">Pesanan</span></p>
            <p class="text-[11px] text-emerald-600 font-semibold mt-1">100% Lunas & Terverifikasi</p>
        </div>

        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Menunggu Pembayaran</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-amber-600 font-display">{{ $totalPending }} <span class="text-xs font-sans text-slate-400 font-normal">Pesanan</span></p>
            <p class="text-[11px] text-amber-600 font-semibold mt-1">Pending Midtrans SNAP</p>
        </div>

        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Nilai Lunas</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <p class="text-xl font-extrabold text-slate-900 font-display">Rp{{ number_format($totalNominal, 0, ',', '.') }}</p>
            <p class="text-[11px] text-indigo-600 font-semibold mt-1">Omzet Bruto Terdata</p>
        </div>
    </div>

    <!-- Filter & Search Controls (Screen Only) -->
    <div class="print:hidden card p-5 sm:p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl space-y-4">
        <form id="adminOrderFilterForm" method="GET" action="{{ route('admin.pesanan.index') }}" class="space-y-4">
            
            <input type="hidden" name="status" id="adminOrderStatus" value="{{ $status ?? 'semua' }}">
            <input type="hidden" name="period" id="adminOrderPeriod" value="{{ $period ?? 'all' }}">
            <input type="hidden" name="start_date" id="adminOrderStartDate" value="{{ $startDateInput ?? '' }}">
            <input type="hidden" name="end_date" id="adminOrderEndDate" value="{{ $endDateInput ?? '' }}">

            <!-- Row 1: Calendar API Input & Presets -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 pb-4 border-b border-slate-100">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 flex-wrap">
                    
                    <!-- Dynamic Interactive Calendar Range Input -->
                    <div class="relative min-w-[260px] sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                            <i class="fas fa-calendar-days text-sm"></i>
                        </div>
                        <input 
                            type="text" 
                            id="flatpickrAdminOrderCalendar" 
                            placeholder="Pilih Rentang Tanggal Pesanan..." 
                            class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer"
                            readonly
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            @if(($period ?? 'all') !== 'all' || request('start_date'))
                                <a href="{{ route('admin.pesanan.index', ['status' => $status, 'umkm_id' => $umkmId]) }}" title="Reset Rentang Tanggal" class="text-slate-400 hover:text-rose-500 transition text-xs">
                                    <i class="fas fa-circle-xmark"></i>
                                </a>
                            @else
                                <i class="fas fa-chevron-down text-[10px] text-slate-400 pointer-events-none"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Filter Period Pills -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" onclick="setAdminOrderPeriod('all')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? 'all') === 'all' ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua Waktu
                        </button>
                        <button type="button" onclick="setAdminOrderPeriod('today')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'today' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Hari Ini
                        </button>
                        <button type="button" onclick="setAdminOrderPeriod('7days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '7days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            7 Hari Terakhir
                        </button>
                        <button type="button" onclick="setAdminOrderPeriod('30days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '30days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            30 Hari Terakhir
                        </button>
                        <button type="button" onclick="setAdminOrderPeriod('this_month')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_month' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Bulan Ini
                        </button>
                        <button type="button" onclick="setAdminOrderPeriod('this_year')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_year' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Tahun Ini
                        </button>
                    </div>

                </div>

                <!-- Right: Active Period Badge -->
                <div class="flex items-center gap-2 shrink-0">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs font-bold">
                        <div class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></div>
                        <span class="text-slate-500 font-normal">Periode:</span>
                        <strong class="font-extrabold text-emerald-800">{{ $activePeriodLabel }}</strong>
                    </div>
                </div>
            </div>

            <!-- Row 2: Status Tabs, Store Dropdown & Search -->
            <div class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
                
                <!-- Status Tabs -->
                <div class="flex flex-wrap items-center gap-2">
                    <button type="button" onclick="setAdminOrderStatus('semua')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer {{ $status === 'semua' || !$status ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua ({{ $totalOrders }})
                    </button>
                    <button type="button" onclick="setAdminOrderStatus('complete')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer {{ $status === 'complete' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        Sukses Lunas ({{ $totalSuccess }})
                    </button>
                    <button type="button" onclick="setAdminOrderStatus('pending')" class="px-3.5 py-2 rounded-xl text-xs font-bold transition cursor-pointer {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                        Pending ({{ $totalPending }})
                    </button>
                </div>

                <!-- Store Dropdown & Search -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative min-w-[220px]">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                            <i class="fas fa-store text-xs"></i>
                        </div>
                        <select name="umkm_id" onchange="this.form.submit()" class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer">
                            <option value="">Semua Toko Mitra UMKM</option>
                            @foreach($umkms as $u)
                                <option value="{{ $u->id }}" {{ $umkmId == $u->id ? 'selected' : '' }}>
                                    {{ $u->nama_toko }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="relative min-w-[240px]">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i class="fas fa-magnifying-glass text-xs"></i>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            value="{{ $search }}" 
                            placeholder="Cari pembeli, produk, toko..." 
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs"
                        >
                    </div>

                    <button type="submit" class="px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white rounded-2xl text-xs font-extrabold transition shrink-0 cursor-pointer shadow-sm">
                        Cari
                    </button>

                    @if($status !== 'semua' || $umkmId || $search || ($period ?? 'all') !== 'all')
                        <a href="{{ route('admin.pesanan.index') }}" class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-2xl text-xs font-bold transition flex items-center justify-center border border-rose-200" title="Reset Semua Filter">
                            <i class="fas fa-rotate-left"></i>
                        </a>
                    @endif
                </div>

            </div>

        </form>
    </div>

    <!-- Main Transparency Orders Table -->
    <div class="card bg-white border border-slate-200/80 print:border-slate-300 shadow-sm print:shadow-none rounded-3xl print:rounded-lg overflow-hidden">
        <div class="print:hidden p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 font-display">Tabel Rincian & Transparansi Pesanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar menyeluruh transaksi marketplace, produk yang dipesan, dan toko mitra pemroses</p>
            </div>
            <span class="text-xs font-bold text-slate-500">{{ $orders->total() }} Transaksi Ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left print:text-[9pt]">
                <thead>
                    <tr class="bg-slate-50 print:bg-slate-100 text-slate-700 font-extrabold text-xs print:text-[8pt] uppercase">
                        <th class="w-8 text-center print:table-cell">No</th>
                        <th class="w-28">No. Order & Waktu</th>
                        <th>Komoditas & Produk</th>
                        <th>Asal Toko Mitra (Penjual)</th>
                        <th>Pembeli & Destinasi</th>
                        <th>Nilai Transaksi (Bagi Hasil)</th>
                        <th class="w-28">Status</th>
                        <th class="text-right print:hidden action-column">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 print:divide-slate-300 text-xs print:text-[8.5pt]">
                    @forelse($orders as $item)
                        @php
                            $produk = $item->produk;
                            $umkm = $produk->umkm ?? null;
                            $rowNumber = ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration;
                        @endphp
                        <tr class="hover:bg-slate-50/70 print:hover:bg-transparent transition">
                            <!-- No Urut -->
                            <td class="text-center font-bold text-slate-500 print:text-slate-800 print:table-cell">
                                {{ $rowNumber }}
                            </td>

                            <!-- ID & Waktu -->
                            <td>
                                <span class="font-extrabold text-xs print:text-[8.5pt] text-slate-900 block font-mono">
                                    #ORD-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="text-[10.5px] print:text-[7.5pt] text-slate-500 block mt-0.5">
                                    {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                                </span>
                                @if($item->order_id_midtrans)
                                    <span class="inline-block text-[9px] print:text-[7pt] font-mono text-indigo-700 bg-indigo-50 print:bg-transparent px-1 rounded mt-0.5">
                                        {{ Str::limit($item->order_id_midtrans, 16) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Komoditas & Produk -->
                            <td>
                                <div class="flex items-center gap-2.5 max-w-xs">
                                    <div class="print:hidden w-10 h-10 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 shadow-2xs">
                                        @if($produk && $produk->gambar)
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i class="fas fa-box text-xs"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-xs print:text-[8.5pt] text-slate-900 line-clamp-1">
                                            {{ $produk->nama ?? 'Produk Komoditas' }}
                                        </h4>
                                        <p class="text-[11px] print:text-[8pt] text-slate-600 mt-0.5">
                                            <strong class="text-slate-800">{{ $item->jumlah }} Pcs/Kg</strong> × Rp{{ number_format($produk->harga ?? 0, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Asal Toko Mitra (Penjual) -->
                            <td>
                                <div class="space-y-0.5">
                                    <p class="font-extrabold text-xs print:text-[8.5pt] text-slate-900">{{ $umkm->nama_toko ?? 'Mitra Petani' }}</p>
                                    <p class="text-[10.5px] print:text-[7.5pt] text-slate-600">
                                        Petani: <span class="font-semibold text-slate-800">{{ $umkm->user->name ?? 'Penjual' }}</span>
                                    </p>
                                    <p class="text-[10px] print:text-[7pt] text-slate-500 truncate max-w-[160px]">
                                        {{ $umkm->alamat ?? 'Kab. Indramayu' }}
                                    </p>
                                </div>
                            </td>

                            <!-- Pembeli & Tujuan -->
                            <td>
                                <div class="space-y-0.5">
                                    <p class="font-bold text-xs print:text-[8.5pt] text-slate-900">{{ $item->name ?: ($item->user->name ?? 'Pembeli') }}</p>
                                    <p class="text-[10.5px] print:text-[7.5pt] text-slate-600">{{ $item->phone ?: '-' }}</p>
                                    <p class="text-[10px] print:text-[7pt] text-slate-500 truncate max-w-[160px]">
                                        {{ $item->alamat ?: 'Indramayu' }}
                                    </p>
                                </div>
                            </td>

                            <!-- Total & Bagi Hasil Transparan -->
                            <td>
                                <p class="text-xs print:text-[8.5pt] font-black text-slate-900">
                                    Rp{{ number_format($item->total_harga, 0, ',', '.') }}
                                </p>
                                <div class="text-[10px] print:text-[7pt] text-slate-600 mt-0.5 space-y-0.5">
                                    <span class="text-emerald-800 font-semibold block">
                                        Petani ({{ $tokoPersen }}%): Rp{{ number_format($item->total_harga * ($tokoPersen / 100), 0, ',', '.') }}
                                    </span>
                                    <span class="text-indigo-800 font-semibold block">
                                        Platform ({{ $komisiPersen }}%): Rp{{ number_format($item->total_harga * ($komisiPersen / 100), 0, ',', '.') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td>
                                @if($item->status === 'complete')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9.5px] print:text-[7.5pt] font-bold bg-emerald-50 text-emerald-800 border border-emerald-300">
                                        <i class="fas fa-check-circle mr-1 text-[8px] print:hidden"></i> LUNAS (Complete)
                                    </span>
                                    <span class="block text-[9.5px] print:text-[7pt] text-slate-600 mt-0.5 capitalize">
                                        {{ str_replace('_', ' ', $item->status_pesanan ?? 'diterima') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9.5px] print:text-[7.5pt] font-bold bg-amber-50 text-amber-800 border border-amber-300">
                                        <i class="fas fa-clock mr-1 text-[8px] print:hidden"></i> PENDING
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi (Screen Only) -->
                            <td class="text-right print:hidden action-column">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button 
                                        type="button" 
                                        onclick="openOrderModal({{ $item->id }})" 
                                        class="px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm border border-brand-200 cursor-pointer"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                        <span>Detail</span>
                                    </button>
                                    <a 
                                        href="{{ route('admin.pesanan.show', $item->id) }}" 
                                        class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition"
                                        title="Buka Halaman Lengkap"
                                    >
                                        <i class="fas fa-arrow-up-right-from-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-12 text-slate-400 text-xs">
                                <i class="fas fa-receipt text-3xl text-slate-300 mb-2 block print:hidden"></i>
                                Tidak ada data pesanan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (Screen Only) -->
        @if($orders->hasPages())
            <div class="print:hidden p-6 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

    <!-- 🖨️ LEMBAR PENGESAHAN & TANDA TANGAN (Hanya Tampil Saat Print) -->
    <div class="hidden print:block mt-8 pt-4 border-t border-slate-300 text-slate-900 font-sans text-[10px]">
        <div class="grid grid-cols-2 gap-8 items-start">
            <!-- Kolom Catatan Legalitas -->
            <div class="space-y-1.5 pr-6">
                <p class="font-extrabold uppercase tracking-wider text-slate-900 text-[10px]">Ketentuan & Validasi Dokumen:</p>
                <ol class="list-decimal list-inside space-y-1 text-slate-600 text-[9px] leading-relaxed">
                    <li>Dokumen rekapitulasi ini diekspor secara sah dari Basis Data Audit & Escrow Sistem Juragan Pelem Indramayu.</li>
                    <li>Semua transaksi berstatus <strong>Complete</strong> telah tervalidasi settlement oleh Payment Gateway Midtrans.</li>
                    <li>Penyaluran hak dana petani mitra (80%) dan komisi operasional platform (20%) tercatat transparan dan dapat dipertanggungjawabkan.</li>
                </ol>
            </div>

            <!-- Kolom Tanda Tangan & Stempel -->
            <div class="text-center ml-auto w-64 space-y-1">
                <p class="text-[10px] text-slate-700">Indramayu, {{ now()->translatedFormat('d F Y') }}</p>
                <p class="font-bold text-slate-900 uppercase text-[10px]">Petugas Pengawas & Verifikasi Superadmin,</p>
                
                <div class="h-16 flex items-center justify-center">
                    <!-- Stempel Digital / QR Verifikasi -->
                    <div class="border-2 border-dashed border-slate-400 rounded-lg px-3 py-1 text-slate-500 text-[8px] uppercase tracking-wider font-mono">
                        [ DIVERIFIKASI SECARA ELEKTRONIK ]<br>
                        JP-VERIFIED-SYSTEM
                    </div>
                </div>

                <p class="font-black text-slate-900 underline text-xs uppercase">{{ Auth::user()->name ?? 'Administrator' }}</p>
                <p class="text-[9px] text-slate-500">ID Petugas: #JP-ADM-{{ str_pad(Auth::user()->id ?? 1, 4, '0', STR_PAD_LEFT) }}</p>
            </div>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- 🔍 MODAL DETAIL TRANSPARANSI PESANAN (POP-UP INTERAKTIF & PRINTABLE)        -->
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
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between print:hidden">
            <span class="text-[11px] text-slate-400">
                <i class="fas fa-lock text-emerald-500 mr-1"></i> Data diverifikasi oleh Sistem Superadmin
            </span>
            <div class="flex items-center gap-2">
                <button onclick="closeOrderModal()" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition cursor-pointer">
                    Tutup
                </button>
                <button onclick="printSingleOrder()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm cursor-pointer">
                    <i class="fas fa-print"></i> Cetak Invoice
                </button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function triggerPrintReport() {
    document.body.classList.remove('printing-modal-active');
    window.print();
}

function printSingleOrder() {
    document.body.classList.add('printing-modal-active');
    window.print();
    setTimeout(() => {
        document.body.classList.remove('printing-modal-active');
    }, 1000);
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Custom High-Contrast Styling for Flatpickr */
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
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
function setAdminOrderStatus(statusVal) {
    document.getElementById('adminOrderStatus').value = statusVal;
    document.getElementById('adminOrderFilterForm').submit();
}

function setAdminOrderPeriod(periodVal) {
    document.getElementById('adminOrderPeriod').value = periodVal;
    document.getElementById('adminOrderStartDate').value = '';
    document.getElementById('adminOrderEndDate').value = '';
    document.getElementById('adminOrderFilterForm').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('flatpickrAdminOrderCalendar');
    const initialStart = "{{ $startDateInput ?? '' }}";
    const initialEnd = "{{ $endDateInput ?? '' }}";
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

                    document.getElementById('adminOrderPeriod').value = 'custom';
                    document.getElementById('adminOrderStartDate').value = startStr;
                    document.getElementById('adminOrderEndDate').value = endStr;
                    document.getElementById('adminOrderFilterForm').submit();
                } else if (selectedDates.length === 1) {
                    const singleStr = instance.formatDate(selectedDates[0], "Y-m-d");
                    document.getElementById('adminOrderPeriod').value = 'custom';
                    document.getElementById('adminOrderStartDate').value = singleStr;
                    document.getElementById('adminOrderEndDate').value = singleStr;
                    document.getElementById('adminOrderFilterForm').submit();
                }
            }
        });
    }
});

function openOrderDetail(orderId) {
    const modal = document.getElementById('orderDetailModal');
    const body = document.getElementById('orderModalBody');
    
    modal.classList.remove('hidden');
    body.innerHTML = `
        <div class="text-center py-8">
            <i class="fas fa-spinner fa-spin text-brand-600 text-3xl mb-2"></i>
            <p class="text-xs text-slate-500 font-bold">Mengambil data transparansi...</p>
        </div>
    `;

    fetch(`{{ url('/admin/pesanan') }}/${orderId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(res => {
        if (!res.success) throw new Error('Data tidak ditemukan');
        const data = res.data;
        
        let statusBadge = '';
        if (data.status === 'complete') {
            statusBadge = '<span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800"><i class="fas fa-check-circle mr-1"></i> Selesai / Lunas</span>';
        } else if (data.status === 'pending') {
            statusBadge = '<span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800"><i class="fas fa-clock mr-1"></i> Menunggu Bayar</span>';
        } else {
            statusBadge = `<span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-slate-100 text-slate-700">${data.status}</span>`;
        }

        body.innerHTML = `
            <!-- Info Header -->
            <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 border border-slate-200/70">
                <div>
                    <span class="text-[10px] font-extrabold uppercase text-slate-400">Order ID (Midtrans)</span>
                    <h4 class="font-mono font-black text-xs text-slate-900">${data.order_id_midtrans}</h4>
                    <span class="text-[10px] text-slate-400">${data.created_at}</span>
                </div>
                <div>
                    ${statusBadge}
                </div>
            </div>

            <!-- Asal Toko & Petani -->
            <div class="p-4 rounded-2xl bg-emerald-50/60 border border-emerald-100 space-y-2">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-800 bg-emerald-200/60 px-2 py-0.5 rounded">
                        <i class="fas fa-store mr-1"></i> Toko / UMKM Mitra Pemroses
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
</script>
@endpush

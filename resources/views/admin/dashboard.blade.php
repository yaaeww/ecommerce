@extends('layouts.app')

@section('page_title', 'Dashboard')

@section('content')
<div class="space-y-8">
    
    <!-- Welcome Header Banner -->
    <div class="relative p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <!-- Subtle Glow -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-brand-50 rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-bold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                Pusat Kendali Superadmin
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-display">
                Selamat Datang, {{ Auth::user()->name }}! 👋
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                Pantau seluruh aktivitas transaksi, verifikasi kemitraan toko UMKM, serta kelola katalog komoditas mangga dan olahan pangan se-Kabupaten Indramayu.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 relative z-10 shrink-0">
            <a 
                href="{{ route('admin.kategori.create') }}" 
                class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-sm hover:shadow flex items-center gap-2"
            >
                <i class="fas fa-plus text-xs"></i> Tambah Kategori
            </a>
            <a 
                href="{{ route('admin.umkm.index') }}" 
                class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs rounded-xl transition flex items-center gap-2"
            >
                <i class="fas fa-store text-xs text-slate-400"></i> Kelola Toko UMKM
            </a>
        </div>
    </div>

    <!-- 4 Key Stat Cards (White Bento Cards with Sleek Indigo Accent) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Stat 1: Total Produk -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Produk</span>
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-base">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">{{ $totalProduk }}</p>
            <div class="flex items-center gap-2 mt-2 text-xs font-semibold text-slate-500">
                <span class="text-brand-600 flex items-center gap-1">
                    <i class="fas fa-check-circle"></i> Aktif
                </span>
                <span>di seluruh marketplace</span>
            </div>
        </div>

        <!-- Stat 2: Kategori & Subkategori -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori Produk</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                    <i class="fas fa-layer-group"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">{{ $jumlahKategori }}</p>
            <div class="flex items-center gap-2 mt-2 text-xs font-semibold text-slate-500">
                <span class="text-indigo-600 font-bold">+{{ $totalSubkategori }}</span>
                <span>subkategori terdaftar</span>
            </div>
        </div>

        <!-- Stat 3: Mitra UMKM & Penjual -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Mitra Toko UMKM</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">{{ $totalUmkm }}</p>
            <div class="flex items-center gap-2 mt-2 text-xs font-semibold text-slate-500">
                @if($umkmPending > 0)
                    <span class="text-amber-600 font-bold flex items-center gap-1">
                        <i class="fas fa-clock"></i> {{ $umkmPending }} Menunggu
                    </span>
                @else
                    <span class="text-emerald-600 font-bold flex items-center gap-1">
                        <i class="fas fa-badge-check"></i> {{ $umkmApproved }} Terverifikasi
                    </span>
                @endif
            </div>
        </div>

        <!-- Stat 4: Total Pendapatan -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pendapatan</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
            </p>
            <div class="flex items-center gap-2 mt-2 text-xs font-semibold text-slate-500">
                <span class="text-emerald-600 font-bold">
                    <i class="fas fa-arrow-trend-up"></i> Terintegrasi
                </span>
                <span>via Midtrans</span>
            </div>
        </div>

    </div>

    <!-- 2 Columns: Recent UMKM Stores & Latest Products -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- Left: Recent UMKM Registrations (7 cols) -->
        <div class="lg:col-span-7 card bg-white border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Toko Mitra UMKM</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar toko dan kebun binaan terdaftar</p>
                    </div>
                    <a href="{{ route('admin.umkm.index') }}" class="text-xs font-bold text-brand-600 hover:underline">
                        Lihat Semua <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full text-left">
                        <thead>
                            <tr>
                                <th>Toko / Pemilik</th>
                                <th>Alamat</th>
                                <th>Status</th>
                                <th class="text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentUmkms as $umkm)
                                <tr class="hover:bg-slate-50/60 transition">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-600 font-bold text-xs flex items-center justify-center shrink-0 border border-slate-200">
                                                <i class="fas fa-store text-xs"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-xs text-slate-900">{{ $umkm->nama_toko }}</p>
                                                <p class="text-[11px] text-slate-400">{{ $umkm->user->name ?? 'Penjual' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-xs text-slate-600 truncate max-w-xs block">
                                            {{ $umkm->alamat ?? 'Indramayu' }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($umkm->status === 'approved')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                                Disetujui
                                            </span>
                                        @elseif($umkm->status === 'pending')
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                Menunggu
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 text-slate-700">
                                                {{ ucfirst($umkm->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-right">
                                        <a href="{{ route('admin.umkm.index') }}" class="p-1.5 text-slate-400 hover:text-brand-600 transition">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-slate-400 text-xs">
                                        Belum ada toko UMKM terdaftar.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Right: Recent Products Catalog (5 cols) -->
        <div class="lg:col-span-5 card bg-white border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
            <div>
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-bold text-slate-900">Produk Terbaru</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Komoditas yang baru ditambahkan</p>
                    </div>
                    <a href="{{ route('admin.produk.index') }}" class="text-xs font-bold text-brand-600 hover:underline">
                        Semua <i class="fas fa-arrow-right text-[10px]"></i>
                    </a>
                </div>

                <div class="p-4 space-y-3">
                    @forelse($recentProduks as $item)
                        <div class="p-3 rounded-2xl bg-slate-50/70 border border-slate-200/60 flex items-center justify-between gap-3 hover:bg-slate-100/70 transition">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-400 flex items-center justify-center text-sm shrink-0">
                                    <i class="fas fa-box-open text-brand-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-xs text-slate-900 line-clamp-1">{{ $item->nama }}</h4>
                                    <p class="text-[11px] text-slate-400">{{ $item->umkm->nama_toko ?? 'Kebun Mitra' }}</p>
                                </div>
                            </div>
                            <div class="text-right shrink-0">
                                <p class="text-xs font-bold text-slate-900">Rp{{ number_format($item->harga, 0, ',', '.') }}</p>
                                <span class="text-[10px] text-slate-400">Stok: {{ $item->stok }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400 text-xs">
                            Belum ada produk terdaftar.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Quick Management Shortcut Cards -->
    <div class="card p-6 bg-white border border-slate-200/80 shadow-sm">
        <h3 class="text-sm font-bold uppercase tracking-wider text-slate-400 mb-4">Pintasan Cepat Manajemen</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <a href="{{ route('admin.kategori.index') }}" class="p-4 rounded-2xl bg-slate-50 hover:bg-brand-50 border border-slate-200 hover:border-brand-200 text-slate-700 hover:text-brand-700 transition flex flex-col items-center text-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-white text-slate-600 group-hover:text-brand-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-layer-group"></i>
                </div>
                <span class="text-xs font-bold">Kategori Produk</span>
            </a>

            <a href="{{ route('admin.umkm.index') }}" class="p-4 rounded-2xl bg-slate-50 hover:bg-brand-50 border border-slate-200 hover:border-brand-200 text-slate-700 hover:text-brand-700 transition flex flex-col items-center text-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-white text-slate-600 group-hover:text-brand-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-store"></i>
                </div>
                <span class="text-xs font-bold">Daftar UMKM</span>
            </a>

            <a href="{{ route('admin.penjual.index') }}" class="p-4 rounded-2xl bg-slate-50 hover:bg-brand-50 border border-slate-200 hover:border-brand-200 text-slate-700 hover:text-brand-700 transition flex flex-col items-center text-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-white text-slate-600 group-hover:text-brand-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-user-tie"></i>
                </div>
                <span class="text-xs font-bold">Akun Penjual</span>
            </a>

            <a href="{{ route('admin.pembeli.index') }}" class="p-4 rounded-2xl bg-slate-50 hover:bg-brand-50 border border-slate-200 hover:border-brand-200 text-slate-700 hover:text-brand-700 transition flex flex-col items-center text-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-white text-slate-600 group-hover:text-brand-600 flex items-center justify-center text-lg shadow-sm">
                    <i class="fas fa-users"></i>
                </div>
                <span class="text-xs font-bold">Akun Pembeli</span>
            </a>
        </div>
    </div>

</div>
@endsection
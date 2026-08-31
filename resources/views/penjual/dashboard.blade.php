@extends('layouts.app')

@section('page_title', 'Dashboard Penjual')

@section('content')
<div class="space-y-8">
    
    <!-- Welcome Header Banner -->
    <div class="relative p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm overflow-hidden flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <!-- Subtle Glow -->
        <div class="absolute -top-12 -right-12 w-64 h-64 bg-brand-50 rounded-full blur-3xl pointer-events-none"></div>

        <div class="space-y-2 relative z-10 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-bold uppercase tracking-wider">
                <span class="w-2 h-2 rounded-full bg-brand-600 animate-pulse"></span>
                Dashboard Penjual
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-display">
                Selamat Datang, {{ Auth::user()->name }}! 👋
            </h2>
            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                Pantau seluruh aktivitas toko, produk, dan penjualan Anda dengan mudah.
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-3 relative z-10 shrink-0">
            <a 
                href="{{ route('penjual.produk.create') }}" 
                class="px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-sm hover:shadow flex items-center gap-2"
            >
                <i class="fas fa-plus text-xs"></i> Tambah Produk
            </a>
            <a 
                href="{{ route('penjual.umkm.index') }}" 
                class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 border border-slate-200 font-bold text-xs rounded-xl transition flex items-center gap-2"
            >
                <i class="fas fa-store text-xs text-slate-400"></i> Kelola Toko
            </a>
        </div>
    </div>

    <!-- 4 Key Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        
        <!-- Stat 1: Total Produk -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Produk</span>
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-base">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">{{ $totalProduk ?? 0 }}</p>
            <div class="flex items-center gap-2 mt-2 text-xs font-semibold text-slate-500">
                <span class="text-brand-600 flex items-center gap-1">
                    <i class="fas fa-box-open"></i> Aktif
                </span>
            </div>
        </div>

        <!-- Stat 2: Kategori -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori Produk</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">{{ $totalKategori ?? 0 }}</p>
        </div>

        <!-- Stat 3: Total Pembeli -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pembeli</span>
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-base">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">{{ $totalPembeliUnik ?? 0 }}</p>
        </div>

        <!-- Stat 4: Total Pendapatan -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm hover:border-brand-300 transition-all">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pendapatan</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Rp{{ number_format($pendapatan ?? 0, 0, ',', '.') }}
            </p>
        </div>

    </div>

    <!-- Produk Terlaris -->
    <div class="card bg-white border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between">
        <div>
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900"><i class="fas fa-fire text-amber-500 me-2"></i>Produk Terlaris</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Produk dengan penjualan terbanyak</p>
                </div>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                    <i class="fas fa-crown me-1"></i> TOP SELLERS
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-left">
                    <thead>
                        <tr>
                            <th class="px-6 py-4">Produk</th>
                            <th class="px-6 py-4">Harga</th>
                            <th class="px-6 py-4">Terjual</th>
                            <th class="px-6 py-4 text-right">Pendapatan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($produkTerlaris ?? [] as $produk)
                            @php
                                $gambarPath = $produk->gambar ?? '';
                                $gambarUrl = ($gambarPath && \Illuminate\Support\Facades\Storage::disk('public')->exists($gambarPath))
                                    ? asset('storage/' . $gambarPath)
                                    : asset('images/no-image.png');
                            @endphp
                            <tr class="hover:bg-slate-50/60 transition">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <img src="{{ $gambarUrl }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200" alt="Produk">
                                        <div>
                                            <p class="font-bold text-sm text-slate-900">{{ $produk->nama ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-slate-700">Rp{{ number_format($produk->harga ?? 0, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-bold bg-brand-50 text-brand-700">
                                        {{ $produk->total_unit ?? 0 }} unit
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-emerald-600">Rp{{ number_format($produk->total_penjualan ?? 0, 0, ',', '.') }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-8 text-slate-400 text-sm">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 rounded-full bg-slate-50 flex items-center justify-center mb-3">
                                            <i class="fas fa-box-open text-slate-300 text-xl"></i>
                                        </div>
                                        Belum ada data produk terlaris.
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

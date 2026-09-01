@extends('layouts.app')

@section('page_title', 'Analisis Keranjang & Minat Pembeli')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Analisis Keranjang & Minat Pasar</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Pantau produk yang diminati konsumen di keranjang belanja, ukur potensi omzet tertahan, dan identifikasi peluang konversi.
            </p>
        </div>
    </div>

    <!-- 3 Metrics KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        
        <!-- Potential GMV -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-brand-600">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Potensi Omzet Tertahan</span>
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-base">
                    <i class="fas fa-sack-dollar"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-brand-600 font-display tracking-tight">
                Rp{{ number_format($potentialGMV, 0, ',', '.') }}
            </p>
            <p class="text-xs text-slate-400 mt-1.5">Nilai total produk yang tersimpan di keranjang pembeli</p>
        </div>

        <!-- Total Items -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kuantitas di Keranjang</span>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base">
                    <i class="fas fa-cart-shopping"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ number_format($totalItemsInCart, 0, ',', '.') }} Pcs/Kg
            </p>
            <p class="text-xs text-slate-400 mt-1.5">Akumulasi seluruh barang siap checkout</p>
        </div>

        <!-- Active Users -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Pembeli Memiliki Keranjang</span>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base">
                    <i class="fas fa-users-viewfinder"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                {{ $totalActiveUsersWithCart }} Akun
            </p>
            <p class="text-xs text-slate-400 mt-1.5">Calon pembeli aktif dalam funnel pembelian</p>
        </div>

    </div>

    <!-- Top Demanded Products / High Interest Items -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 font-display">Varietas Mangga & Produk Paling Banyak Diminati</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar produk dengan frekuensi penambahan ke keranjang belanja tertinggi</p>
            </div>
            <span class="text-xs font-bold text-amber-700 bg-amber-50 border border-amber-200 px-3 py-1 rounded-xl">
                🔥 High Buyer Demand
            </span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 pt-1">
            @forelse($topDemandedProducts as $idx => $prod)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 overflow-hidden shrink-0 shadow-xs">
                            @if($prod->gambar)
                                <img src="{{ asset('storage/' . $prod->gambar) }}" alt="{{ $prod->nama }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i class="fas fa-box text-sm"></i>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <h4 class="font-extrabold text-xs text-slate-900 truncate">{{ $prod->nama }}</h4>
                            <span class="text-[10px] text-brand-600 font-bold block">{{ $prod->nama_toko }}</span>
                            <span class="text-[11px] font-bold text-slate-700 block mt-0.5">Rp{{ number_format($prod->harga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 pt-2 border-t border-slate-200/60 text-xs">
                        <div>
                            <span class="text-[10px] text-slate-400 block">Tersimpan:</span>
                            <strong class="font-extrabold text-slate-800">{{ $prod->total_kuantitas }} Unit</strong>
                        </div>
                        <div>
                            <span class="text-[10px] text-slate-400 block">Peminat:</span>
                            <strong class="font-extrabold text-indigo-600">{{ $prod->total_pembeli_peminat }} Pembeli</strong>
                        </div>
                    </div>
                </div>
            @empty
                <p class="col-span-3 text-center py-6 text-slate-400 text-xs">Belum ada data produk di keranjang.</p>
            @endforelse
        </div>
    </div>

    <!-- Table: Rincian Keranjang Pembeli Aktif -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 font-display">Daftar Keranjang Aktif Pembeli</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar item belanja yang sedang disimpan oleh masing-masing akun</p>
            </div>
            
            <form method="GET" action="{{ route('admin.keranjang.index') }}" class="flex items-center gap-2">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari pembeli atau produk..." 
                    class="px-3.5 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500"
                >
                <button type="submit" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                    Cari
                </button>
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th>Akun Pembeli</th>
                        <th>Komoditas Produk</th>
                        <th>Toko / Kebun Asal</th>
                        <th class="text-center">Kuantitas</th>
                        <th>Subtotal Potensial</th>
                        <th class="text-right">Waktu Ditambahkan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($activeCarts as $cart)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="align-middle">
                                <strong class="font-bold text-slate-900 block">{{ $cart->user->name ?? 'Pembeli' }}</strong>
                                <span class="text-[10px] text-slate-400">{{ $cart->user->email ?? '-' }}</span>
                            </td>
                            <td class="align-middle">
                                <strong class="font-bold text-slate-900 block truncate max-w-[180px]">{{ $cart->produk->nama ?? 'Produk' }}</strong>
                                <span class="text-[10px] text-slate-400">Rp{{ number_format($cart->produk->harga ?? 0, 0, ',', '.') }} / unit</span>
                            </td>
                            <td class="align-middle">
                                <span class="font-bold text-brand-600">{{ $cart->produk->umkm->nama_toko ?? 'Kebun Mitra' }}</span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-800 font-extrabold text-xs">
                                    {{ $cart->quantity }} Pcs/Kg
                                </span>
                            </td>
                            <td class="align-middle">
                                <strong class="font-extrabold text-slate-900">
                                    Rp{{ number_format(($cart->produk->harga ?? 0) * $cart->quantity, 0, ',', '.') }}
                                </strong>
                            </td>
                            <td class="align-middle text-right text-slate-400 text-[11px]">
                                {{ $cart->updated_at->diffForHumans() }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400 text-xs">
                                Tidak ada keranjang aktif.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($activeCarts->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $activeCarts->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

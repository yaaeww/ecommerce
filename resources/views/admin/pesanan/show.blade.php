@extends('layouts.app')

@section('page_title', 'Detail Transparansi Pesanan #' . ($order->order_id_midtrans ?: $order->id))

@section('content')
<div class="max-w-4xl mx-auto space-y-8 pb-12">
    
    <!-- Top Action Nav -->
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.pesanan.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-slate-900 transition">
            <i class="fas fa-arrow-left"></i> Kembali ke Log Pesanan
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center gap-2 shadow-sm">
                <i class="fas fa-print"></i> Cetak Invoice / Bukti
            </button>
        </div>
    </div>

    <!-- Main Order Invoice Card -->
    <div class="card bg-white border border-slate-200/80 shadow-md rounded-3xl overflow-hidden p-6 sm:p-10 space-y-8">
        
        <!-- Header Brand & Order Code -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 border border-amber-200 p-2 flex items-center justify-center">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                </div>
                <div>
                    <h2 class="text-xl font-extrabold font-display text-slate-900">Juragan Pelem Indramayu</h2>
                    <p class="text-xs text-slate-400">Bukti Transparansi Transaksi Agro-Commerce</p>
                </div>
            </div>

            <div class="sm:text-right space-y-1">
                <span class="text-xs font-extrabold uppercase tracking-wider text-slate-400 block">Nomor Transaksi</span>
                <p class="text-base font-extrabold font-mono text-slate-900">
                    {{ $order->order_id_midtrans ?: ('ORD-JP-' . str_pad($order->id, 5, '0', STR_PAD_LEFT)) }}
                </p>
                <p class="text-xs text-slate-400">
                    {{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB
                </p>
            </div>
        </div>

        <!-- Status Banner -->
        <div class="p-4 rounded-2xl {{ $order->status === 'complete' ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200' }} flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl {{ $order->status === 'complete' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }} flex items-center justify-center text-lg shrink-0">
                    <i class="fas {{ $order->status === 'complete' ? 'fa-circle-check' : 'fa-clock' }}"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-xs {{ $order->status === 'complete' ? 'text-emerald-900' : 'text-amber-900' }} uppercase">
                        Status Pembayaran: {{ $order->status }}
                    </h4>
                    <p class="text-[11px] {{ $order->status === 'complete' ? 'text-emerald-700' : 'text-amber-700' }}">
                        Fulfillment Pengiriman: {{ str_replace('_', ' ', $order->status_pesanan ?? 'Diterima') }}
                    </p>
                </div>
            </div>
            <span class="text-sm font-extrabold {{ $order->status === 'complete' ? 'text-emerald-700' : 'text-amber-700' }}">
                Rp{{ number_format($order->total_harga, 0, ',', '.') }}
            </span>
        </div>

        <!-- Two Column Entity Details (Seller UMKM & Buyer) -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            
            <!-- Asal Toko & Petani Mitra (Seller) -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-100 px-2 py-0.5 rounded">
                        <i class="fas fa-store mr-1"></i> Asal Toko Mitra (Penjual)
                    </span>
                </div>
                <div class="space-y-1.5 text-xs">
                    <p class="text-sm font-extrabold text-slate-900">{{ $order->produk->umkm->nama_toko ?? 'Kebun Mitra' }}</p>
                    <p class="text-slate-600">
                        <span class="text-slate-400">Pemilik Kebun:</span> {{ $order->produk->umkm->user->name ?? 'Petani Mitra' }}
                    </p>
                    <p class="text-slate-600">
                        <span class="text-slate-400">Telepon Toko:</span> {{ $order->produk->umkm->no_telp ?? '-' }}
                    </p>
                    <p class="text-slate-600">
                        <span class="text-slate-400">Lokasi Sentra:</span> {{ $order->produk->umkm->alamat ?? 'Indramayu' }}
                    </p>
                </div>
            </div>

            <!-- Data Pembeli & Tujuan Pengiriman (Buyer) -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3">
                <div class="flex items-center justify-between pb-2 border-b border-slate-200/60">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">
                        <i class="fas fa-user-check mr-1"></i> Data Pembeli & Pengiriman
                    </span>
                </div>
                <div class="space-y-1.5 text-xs">
                    <p class="text-sm font-extrabold text-slate-900">{{ $order->name ?: ($order->user->name ?? 'Pembeli') }}</p>
                    <p class="text-slate-600">
                        <span class="text-slate-400">Email Akun:</span> {{ $order->user->email ?? '-' }}
                    </p>
                    <p class="text-slate-600">
                        <span class="text-slate-400">No. WhatsApp / HP:</span> {{ $order->phone ?: '-' }}
                    </p>
                    <p class="text-slate-600">
                        <span class="text-slate-400">Alamat Tujuan:</span> {{ $order->alamat ?: 'Indramayu' }}
                    </p>
                </div>
            </div>

        </div>

        <!-- Product Table Breakdown -->
        <div class="space-y-3">
            <h4 class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Rincian Komoditas Dipesan</h4>
            <div class="overflow-x-auto border border-slate-200/80 rounded-2xl">
                <table class="table w-full text-left">
                    <thead>
                        <tr>
                            <th>Produk</th>
                            <th>Kategori</th>
                            <th>Harga Satuan</th>
                            <th>Kuantitas</th>
                            <th class="text-right">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200">
                                        @if($order->produk && $order->produk->gambar)
                                            <img src="{{ asset('storage/' . $order->produk->gambar) }}" alt="{{ $order->produk->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i class="fas fa-box"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h5 class="font-extrabold text-xs text-slate-900">{{ $order->produk->nama ?? 'Produk Komoditas' }}</h5>
                                        <p class="text-[11px] text-slate-400">{{ $order->produk->umkm->nama_toko ?? 'Mitra Kebun' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="text-xs text-slate-600">{{ $order->produk->kategori->nama ?? 'Mangga' }}</span>
                            </td>
                            <td>
                                <span class="text-xs font-semibold text-slate-700">Rp{{ number_format($order->produk->harga ?? 0, 0, ',', '.') }}</span>
                            </td>
                            <td>
                                <span class="text-xs font-bold text-slate-900">{{ $order->jumlah }} Pcs/Kg</span>
                            </td>
                            <td class="text-right">
                                <span class="text-xs font-extrabold text-slate-900">Rp{{ number_format($order->total_harga, 0, ',', '.') }}</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financial Transparency Breakdown -->
        <div class="p-6 rounded-2xl bg-gradient-to-br from-brand-50/80 via-white to-brand-50/50 border border-brand-200/80 space-y-3">
            <h4 class="text-xs font-extrabold text-brand-900 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-scale-balanced text-brand-600"></i> Skema Transparansi Finansial Marketplace
            </h4>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                <div class="p-4 rounded-xl bg-white border border-brand-100 shadow-sm space-y-1">
                    <span class="text-[11px] text-slate-500 font-medium">Hak Omzet Petani Mitra ({{ $tokoPersen }}%):</span>
                    <p class="text-lg font-extrabold text-emerald-600 font-display">
                        Rp{{ number_format($order->total_harga * ($tokoPersen / 100), 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-slate-400">Diteruskan langsung ke rekening mitra UMKM</p>
                </div>
                <div class="p-4 rounded-xl bg-white border border-brand-100 shadow-sm space-y-1">
                    <span class="text-[11px] text-slate-500 font-medium">Komisi Pemeliharaan Platform ({{ $komisiPersen }}%):</span>
                    <p class="text-lg font-extrabold text-brand-600 font-display">
                        Rp{{ number_format($order->total_harga * ($komisiPersen / 100), 0, ',', '.') }}
                    </p>
                    <p class="text-[10px] text-slate-400">Biaya gateway, server, dan operasional agro-hub</p>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@extends('layouts.app')

@section('page_title', 'Detail Pendapatan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Detail Pendapatan</h2>
            <p class="text-sm text-slate-500 mt-1">Laporan lengkap penjualan untuk produk spesifik</p>
        </div>
        <div class="flex items-center gap-2">
            @if(!$detail->isEmpty())
                <a href="{{ route('penjual.pendapatan.detail.export.excel', $produk->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm rounded-xl transition shadow-sm" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Mengekspor...'; setTimeout(() => { this.innerHTML='<i class=\'fas fa-file-excel\'></i> Export Excel'; }, 3000);">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
                <a href="{{ route('penjual.pendapatan.detail.export.pdf', $produk->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm rounded-xl transition shadow-sm" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Mengekspor...'; setTimeout(() => { this.innerHTML='<i class=\'fas fa-file-pdf\'></i> Export PDF'; }, 3000);">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
            @endif
            <a href="{{ route('penjual.pendapatan.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition border border-slate-200">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Product Info -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6 flex flex-col sm:flex-row items-center sm:items-start gap-6">
        <div class="w-24 h-24 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0">
            @if($produk->gambar)
                <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-slate-300">
                    <i class="fas fa-image text-3xl"></i>
                </div>
            @endif
        </div>
        <div class="flex-1 text-center sm:text-left">
            <h3 class="text-xl font-bold text-slate-900 font-display mb-2">{{ $produk->nama }}</h3>
            <div class="flex flex-wrap items-center justify-center sm:justify-start gap-4">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-brand-50 text-brand-700 text-sm font-bold border border-brand-200">
                    <i class="fas fa-tag"></i> Rp {{ number_format($produk->harga, 0, ',', '.') }}
                </span>
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-slate-50 text-slate-700 text-sm font-bold border border-slate-200">
                    <i class="fas fa-box"></i> Stok: {{ $produk->stok }}
                </span>
            </div>
        </div>
    </div>

    @if(!$detail->isEmpty())
        @php
            $totalPendapatan = $detail->sum('total_harga');
            $totalTerjual = $detail->sum('jumlah');
            $totalTransaksi = $detail->count();
            $rataRata = $totalTransaksi > 0 ? $totalPendapatan / $totalTransaksi : 0;
        @endphp

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Pendapatan</h3>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 font-display">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
            
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Terjual</h3>
                    <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center">
                        <i class="fas fa-box-open"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 font-display">{{ $totalTerjual }} <span class="text-sm text-slate-500 font-medium">unit</span></p>
            </div>

            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Transaksi</h3>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 font-display">{{ $totalTransaksi }} <span class="text-sm text-slate-500 font-medium">trx</span></p>
            </div>

            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider">Rata-rata/Trx</h3>
                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-slate-900 font-display">Rp {{ number_format($rataRata, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Table -->
        <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-slate-900">Riwayat Transaksi</h3>
                <span class="text-sm text-slate-500">Menampilkan {{ $detail->count() }} transaksi</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 whitespace-nowrap">ID Order</th>
                            <th class="px-6 py-4 whitespace-nowrap">Pembeli</th>
                            <th class="px-6 py-4 whitespace-nowrap">Jumlah</th>
                            <th class="px-6 py-4 whitespace-nowrap">Total Harga</th>
                            <th class="px-6 py-4 whitespace-nowrap">Tanggal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($detail as $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-slate-700 text-xs font-mono font-bold">
                                        #{{ $item->id }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ $item->nama_pemesan ?? 'Tidak diketahui' }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ $item->jumlah }}
                                </td>
                                <td class="px-6 py-4 font-bold text-brand-700">
                                    Rp {{ number_format($item->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d M Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-12 text-center max-w-3xl mx-auto">
            <div class="w-20 h-20 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-6 transform -rotate-3">
                <i class="fas fa-receipt text-4xl"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-900 mb-2 font-display">Belum Ada Transaksi</h3>
            <p class="text-slate-500 max-w-md mx-auto leading-relaxed">
                Produk ini belum memiliki riwayat transaksi penjualan.
            </p>
        </div>
    @endif

</div>
@endsection

@extends('layouts.app')

@section('page_title', 'Invoice Pesanan #' . str_pad($order->id, 5, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold mb-1">
                <i class="fas fa-file-invoice text-[10px]"></i> Faktur Transaksi
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 font-display">Invoice Pemesanan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Rincian transaksi resmi dan status pemrosesan pesanan mitra.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('penjual.pesanan.index') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-2">
                <i class="fas fa-arrow-left text-xs"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('penjual.pesanan.invoice.pdf', $order->id) }}" target="_blank" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2">
                <i class="fas fa-file-pdf text-xs"></i>
                <span>Cetak PDF</span>
            </a>
        </div>
    </div>

    <!-- Main Invoice Card -->
    <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-xs rounded-3xl space-y-6">
        
        <!-- Invoice Header -->
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 pb-6 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold shadow-2xs">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <span class="text-xs font-extrabold text-slate-400 uppercase tracking-wider">Nomor Faktur</span>
                    <h3 class="text-lg font-black text-slate-900 font-display">INV-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</h3>
                </div>
            </div>

            <div class="text-left sm:text-right">
                <span class="text-xs text-slate-400 block mb-1">Tanggal Transaksi</span>
                <span class="text-xs font-bold text-slate-800">{{ $order->created_at ? $order->created_at->translatedFormat('d F Y H:i') : '-' }} WIB</span>
            </div>
        </div>

        <!-- 2 Column: Data Pembeli & Status Pesanan -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Data Pembeli -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3 text-xs">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-slate-200">
                    <i class="fas fa-user text-emerald-600"></i> Informasi Pembeli
                </h4>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nama Penerima:</span>
                    <span class="font-bold text-slate-800 text-right">{{ $order->name ?: ($order->user->name ?? '-') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">No. WhatsApp / HP:</span>
                    <span class="font-bold text-slate-800 text-right">{{ $order->phone ?: '-' }}</span>
                </div>
                <div class="flex justify-between items-start gap-4">
                    <span class="text-slate-400 shrink-0">Alamat Kirim:</span>
                    <span class="font-medium text-slate-700 text-right">{{ $order->alamat ?: '-' }}</span>
                </div>
            </div>

            <!-- Status Pesanan -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 space-y-3 text-xs">
                <h4 class="text-xs font-extrabold text-slate-900 uppercase tracking-wider flex items-center gap-2 pb-2 border-b border-slate-200">
                    <i class="fas fa-info-circle text-emerald-600"></i> Status & Logistik
                </h4>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Pembayaran Midtrans:</span>
                    @if($order->status === 'complete' || $order->status === 'paid')
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Lunas Terverifikasi
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200">
                            Pending Pembayaran
                        </span>
                    @endif
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-slate-400">Tahap Pemrosesan:</span>
                    <span class="font-bold text-slate-800 capitalize">
                        {{ str_replace('_', ' ', $order->status_pesanan ?: 'Menunggu Diproses') }}
                    </span>
                </div>
                @if($order->no_resi)
                    <div class="flex justify-between items-center">
                        <span class="text-slate-400">Nomor Resi:</span>
                        <span class="font-mono font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">{{ $order->no_resi }}</span>
                    </div>
                @endif
            </div>
        </div>

        <!-- Detail Komoditas Table -->
        <div class="border border-slate-100 rounded-2xl overflow-hidden">
            <table class="table w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-3">Komoditas Produk</th>
                        <th class="px-4 py-3">Harga Satuan</th>
                        <th class="px-4 py-3 text-center">Jumlah</th>
                        <th class="px-4 py-3 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <tr>
                        <td class="px-4 py-3.5">
                            <div class="flex items-center gap-3">
                                @if($order->produk && $order->produk->gambar)
                                    <img src="{{ asset('storage/' . $order->produk->gambar) }}" class="w-10 h-10 rounded-xl object-cover border border-slate-200 shrink-0" alt="Produk">
                                @else
                                    <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-300 shrink-0">
                                        <i class="fas fa-box"></i>
                                    </div>
                                @endif
                                <div>
                                    <p class="font-extrabold text-slate-900">{{ $order->produk->nama ?? 'Komoditas Mangga' }}</p>
                                    <span class="text-[10px] text-slate-400">Petik Matang Pohon Indramayu</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3.5 font-bold text-slate-700">
                            Rp{{ number_format($order->produk->harga ?? 0, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3.5 text-center font-extrabold text-slate-900">
                            {{ $order->jumlah }}x
                        </td>
                        <td class="px-4 py-3.5 text-right font-extrabold text-emerald-600">
                            Rp{{ number_format($order->total_harga ?? 0, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total Breakdown -->
        <div class="flex justify-end pt-4">
            <div class="w-full sm:w-80 space-y-2.5 text-xs">
                <div class="flex justify-between text-slate-500">
                    <span>Subtotal Produk</span>
                    <span class="font-bold text-slate-800">Rp{{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-slate-500">
                    <span>Ongkos Kirim & Layanan</span>
                    <span class="font-bold text-emerald-600">Gratis / Termasuk</span>
                </div>
                <div class="flex justify-between items-center text-sm font-extrabold text-slate-900 pt-3 border-t border-slate-200">
                    <span>Total Transaksi</span>
                    <span class="text-base font-black text-emerald-600 font-display">Rp{{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
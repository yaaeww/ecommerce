@extends('layouts.app')

@section('page_title', 'Daftar Pesanan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Daftar Pesanan</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola pesanan yang masuk dan pantau statusnya</p>
        </div>
    </div>

    <!-- Pesanan Selesai Section -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Pesanan Selesai</h3>
                <p class="text-xs text-slate-500">Pesanan yang telah berhasil diselesaikan</p>
            </div>
        </div>

        @if($pesananComplete->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-check-circle text-2xl text-slate-300"></i>
                </div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Belum Ada Pesanan Selesai</h4>
                <p class="text-sm text-slate-500">Semua pesanan yang telah selesai akan muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 whitespace-nowrap w-16">No</th>
                            <th class="px-6 py-4 whitespace-nowrap">Produk</th>
                            <th class="px-6 py-4 whitespace-nowrap">Pembeli</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Jumlah</th>
                            <th class="px-6 py-4 whitespace-nowrap text-right">Total Harga</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pesananComplete as $key => $order)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-400">{{ $key + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $order->produk->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $order->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">
                                    {{ $order->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    {{ $order->jumlah }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-brand-700">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center space-y-2">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-blue-50 text-blue-700 text-[10px] font-bold uppercase tracking-wider">
                                            {{ str_replace('_', ' ', $order->status_pesanan) }}
                                        </span>
                                    </div>
                                    @if($order->status_pesanan === 'diterima')
                                        <div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-emerald-50 text-emerald-700 text-[10px] font-bold uppercase tracking-wider">
                                                Diterima
                                            </span>
                                        </div>
                                    @elseif($order->status_pesanan === 'belum_diterima')
                                        <div>
                                            <span class="inline-flex items-center px-2 py-1 rounded-md bg-amber-50 text-amber-700 text-[10px] font-bold uppercase tracking-wider">
                                                Belum Diterima
                                            </span>
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex flex-col items-center justify-center gap-1.5">
                                        <a href="{{ route('penjual.pesanan.create', $order->id) }}" class="inline-flex items-center justify-center gap-1.5 w-28 px-2.5 py-1.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-lg transition shadow-2xs">
                                            <i class="fas fa-truck-fast text-[10px]"></i> Proses / Resi
                                        </a>
                                        <a href="{{ route('penjual.pesanan.shipping-label', $order->id) }}" target="_blank" class="inline-flex items-center justify-center gap-1.5 w-28 px-2.5 py-1 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold text-[11px] rounded-lg transition border border-emerald-200" title="Cetak Label Resi Thermal A6">
                                            <i class="fas fa-barcode text-[10px]"></i> Label A6
                                        </a>
                                        <a href="{{ route('penjual.invoice.show', $order->id) }}" class="inline-flex items-center justify-center gap-1.5 w-28 px-2.5 py-1 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold text-[11px] rounded-lg transition">
                                            <i class="fas fa-file-invoice text-[10px]"></i> Faktur
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    <!-- Pesanan Dibatalkan Section -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden mt-8">
        <div class="p-6 border-b border-slate-100 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center shrink-0">
                <i class="fas fa-times-circle"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-slate-900">Pesanan Dibatalkan</h3>
                <p class="text-xs text-slate-500">Riwayat pesanan yang telah dibatalkan</p>
            </div>
        </div>

        @if($pesananCancel->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-ban text-2xl text-slate-300"></i>
                </div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Tidak Ada Pesanan Dibatalkan</h4>
                <p class="text-sm text-slate-500">Semua pesanan yang dibatalkan akan muncul di sini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 whitespace-nowrap w-16">No</th>
                            <th class="px-6 py-4 whitespace-nowrap">Produk</th>
                            <th class="px-6 py-4 whitespace-nowrap">Pembeli</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Jumlah</th>
                            <th class="px-6 py-4 whitespace-nowrap text-right">Total Harga</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pesananCancel as $key => $order)
                            <tr class="hover:bg-slate-50/50 transition opacity-80 hover:opacity-100">
                                <td class="px-6 py-4 font-bold text-slate-400">{{ $key + 1 }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ $order->produk->nama ?? '-' }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $order->created_at->format('d M Y H:i') }}</div>
                                </td>
                                <td class="px-6 py-4 font-medium text-slate-700">
                                    {{ $order->user->name ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    {{ $order->jumlah }}
                                </td>
                                <td class="px-6 py-4 text-right font-bold text-slate-700 line-through">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>
                                <td class="px-6 py-4 text-center space-y-2">
                                    <div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-rose-50 text-rose-700 text-[10px] font-bold uppercase tracking-wider">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="inline-flex items-center px-2 py-1 rounded-md bg-slate-100 text-slate-600 text-[10px] font-bold uppercase tracking-wider">
                                            {{ str_replace('_', ' ', $order->status_pesanan) }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-slate-400 font-medium text-xs">-</span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

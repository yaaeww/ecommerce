@extends('layouts.public')
@section('title', 'Pesanan Saya')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8 border-b border-gray-200 pb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 flex items-center gap-3">
                <div class="w-12 h-12 bg-indigo-100 rounded-xl flex items-center justify-center text-indigo-600">
                    <i class="fas fa-shopping-bag"></i>
                </div>
                Pesanan Saya
            </h1>
            <p class="mt-2 text-sm text-gray-600">Kelola dan pantau status semua pesanan Anda.</p>
        </div>
    </div>

    @php
        $pesananLunas = $orders->where('status', 'complete');
        $pesananPending = $orders->where('status', 'pending');
        $pesananCancel = $orders->where('status', 'cancel');
    @endphp

    <!-- PESANAN LUNAS -->
    <div class="mb-12">
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-6 pb-2 border-b border-gray-200">
            <div class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                <i class="fas fa-check"></i>
            </div>
            Pesanan Lunas
        </h3>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @if ($pesananLunas->isEmpty())
                <div class="text-center py-12 px-6">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-box-open text-3xl text-gray-300"></i>
                    </div>
                    <h5 class="text-lg font-bold text-gray-900 mb-1">Tidak ada pesanan lunas</h5>
                    <p class="text-gray-500 text-sm">Belum ada pesanan yang telah diselesaikan.</p>
                </div>
            @else
                <form action="{{ route('pembeli.pesanan.bulkDelete') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan yang dipilih?')">
                    @csrf
                    @method('DELETE')
                    
                    <div class="bg-gray-50 p-4 border-b border-gray-200 flex justify-end">
                        <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 py-2 px-4 rounded-lg font-medium shadow-sm transition-colors duration-200 flex items-center gap-2 text-sm">
                            <i class="fas fa-trash"></i> Hapus yang Dipilih
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50 text-xs text-gray-500 uppercase tracking-wider font-semibold">
                                    <th class="p-4 sm:pl-6 w-12">
                                        <input type="checkbox" id="select-all-lunas" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </th>
                                    <th class="p-4">Produk</th>
                                    <th class="p-4 text-center">Jumlah</th>
                                    <th class="p-4">Total Harga</th>
                                    <th class="p-4">Status Pembayaran</th>
                                    <th class="p-4">Status Pengiriman</th>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4 sm:pr-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach ($pesananLunas as $order)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-4 sm:pl-6">
                                            <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox-lunas w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                        </td>
                                        <td class="p-4 font-medium text-gray-900">{{ $order->produk->nama ?? '-' }}</td>
                                        <td class="p-4 text-center text-gray-600">{{ $order->jumlah }}</td>
                                        <td class="p-4 font-bold text-gray-900">Rp{{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            @if ($order->status_pesanan)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Belum Diproses
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-gray-500 text-xs whitespace-nowrap">{{ $order->created_at->format('d-m-Y H:i') }}</td>
                                        <td class="p-4 sm:pr-6 text-right">
                                            <a href="{{ route('pembeli.invoice.show', $order->id) }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 py-1.5 px-3 rounded-lg font-medium shadow-sm transition-colors text-xs whitespace-nowrap">
                                                <i class="fas fa-file-invoice text-gray-400"></i> Invoice
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- PESANAN MENUNGGU PEMBAYARAN -->
    <div class="mb-12">
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-6 pb-2 border-b border-gray-200">
            <div class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center">
                <i class="fas fa-clock"></i>
            </div>
            Pesanan Menunggu Pembayaran
        </h3>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @if ($pesananPending->isEmpty())
                <div class="text-center py-12 px-6">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-wallet text-3xl text-gray-300"></i>
                    </div>
                    <h5 class="text-lg font-bold text-gray-900 mb-1">Tidak ada tagihan tertunda</h5>
                    <p class="text-gray-500 text-sm">Belum ada pesanan yang menunggu pembayaran.</p>
                </div>
            @else
                <form action="{{ route('pembeli.pesanan.bulkDelete') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan yang dipilih?')">
                    @csrf
                    @method('DELETE')
                    
                    <div class="bg-gray-50 p-4 border-b border-gray-200 flex justify-end">
                        <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 py-2 px-4 rounded-lg font-medium shadow-sm transition-colors duration-200 flex items-center gap-2 text-sm">
                            <i class="fas fa-trash"></i> Hapus yang Dipilih
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50 text-xs text-gray-500 uppercase tracking-wider font-semibold">
                                    <th class="p-4 sm:pl-6 w-12">
                                        <input type="checkbox" id="select-all-pending" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </th>
                                    <th class="p-4">Produk</th>
                                    <th class="p-4 text-center">Jumlah</th>
                                    <th class="p-4">Total Harga</th>
                                    <th class="p-4">Status Pembayaran</th>
                                    <th class="p-4">Status Pengiriman</th>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4 sm:pr-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach ($pesananPending as $order)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-4 sm:pl-6">
                                            <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox-pending w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                        </td>
                                        <td class="p-4 font-medium text-gray-900">{{ $order->produk->nama ?? '-' }}</td>
                                        <td class="p-4 text-center text-gray-600">{{ $order->jumlah }}</td>
                                        <td class="p-4 font-bold text-gray-900">Rp{{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            @if ($order->status_pesanan)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Belum Diproses
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-gray-500 text-xs whitespace-nowrap">{{ $order->created_at->format('d-m-Y H:i') }}</td>
                                        <td class="p-4 sm:pr-6 text-right">
                                            <a href="{{ route('pembeli.status.belum-bayar') }}" class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white py-1.5 px-3 rounded-lg font-medium shadow-sm transition-colors text-xs whitespace-nowrap">
                                                <i class="fas fa-credit-card"></i> Bayar
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            @endif
        </div>
    </div>

    <!-- PESANAN DIBATALKAN -->
    <div class="mb-12">
        <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2 mb-6 pb-2 border-b border-gray-200">
            <div class="w-8 h-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                <i class="fas fa-times"></i>
            </div>
            Pesanan Dibatalkan
        </h3>

        <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
            @if ($pesananCancel->isEmpty())
                <div class="text-center py-12 px-6">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-ban text-3xl text-gray-300"></i>
                    </div>
                    <h5 class="text-lg font-bold text-gray-900 mb-1">Tidak ada pesanan yang dibatalkan</h5>
                    <p class="text-gray-500 text-sm">Belum ada pesanan yang dibatalkan.</p>
                </div>
            @else
                <form action="{{ route('pembeli.pesanan.bulkDelete') }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus pesanan yang dipilih?')">
                    @csrf
                    @method('DELETE')
                    
                    <div class="bg-gray-50 p-4 border-b border-gray-200 flex justify-end">
                        <button type="submit" class="bg-white border border-red-200 text-red-600 hover:bg-red-50 py-2 px-4 rounded-lg font-medium shadow-sm transition-colors duration-200 flex items-center gap-2 text-sm">
                            <i class="fas fa-trash"></i> Hapus yang Dipilih
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="border-b border-gray-200 bg-gray-50 text-xs text-gray-500 uppercase tracking-wider font-semibold">
                                    <th class="p-4 sm:pl-6 w-12">
                                        <input type="checkbox" id="select-all-cancel" class="w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                    </th>
                                    <th class="p-4">Produk</th>
                                    <th class="p-4 text-center">Jumlah</th>
                                    <th class="p-4">Total Harga</th>
                                    <th class="p-4">Status Pembayaran</th>
                                    <th class="p-4">Status Pengiriman</th>
                                    <th class="p-4">Tanggal</th>
                                    <th class="p-4 sm:pr-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 text-sm">
                                @foreach ($pesananCancel as $order)
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="p-4 sm:pl-6">
                                            <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" class="order-checkbox-cancel w-4 h-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500 cursor-pointer">
                                        </td>
                                        <td class="p-4 font-medium text-gray-900">{{ $order->produk->nama ?? '-' }}</td>
                                        <td class="p-4 text-center text-gray-600">{{ $order->jumlah }}</td>
                                        <td class="p-4 font-bold text-gray-900">Rp{{ number_format($order->total_harga, 0, ',', '.') }}</td>
                                        <td class="p-4">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="p-4">
                                            @if ($order->status_pesanan)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    Belum Diproses
                                                </span>
                                            @endif
                                        </td>
                                        <td class="p-4 text-gray-500 text-xs whitespace-nowrap">{{ $order->created_at->format('d-m-Y H:i') }}</td>
                                        <td class="p-4 sm:pr-6 text-right">
                                            <a href="{{ route('pembeli.invoice.show', $order->id) }}" class="inline-flex items-center gap-2 bg-white border border-gray-300 text-gray-700 hover:bg-gray-50 py-1.5 px-3 rounded-lg font-medium shadow-sm transition-colors text-xs whitespace-nowrap">
                                                <i class="fas fa-file-invoice text-gray-400"></i> Invoice
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script>
    // Menangani aksi "Select All" untuk memilih semua checkbox di bagian lunas
    document.getElementById('select-all-lunas')?.addEventListener('change', function (e) {
        const checkboxes = document.querySelectorAll('.order-checkbox-lunas');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });

    // Menangani aksi "Select All" untuk memilih semua checkbox di bagian pending
    document.getElementById('select-all-pending')?.addEventListener('change', function (e) {
        const checkboxes = document.querySelectorAll('.order-checkbox-pending');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });

    // Menangani aksi "Select All" untuk memilih semua checkbox di bagian cancel
    document.getElementById('select-all-cancel')?.addEventListener('change', function (e) {
        const checkboxes = document.querySelectorAll('.order-checkbox-cancel');
        checkboxes.forEach(checkbox => {
            checkbox.checked = e.target.checked;
        });
    });
</script>
@endsection
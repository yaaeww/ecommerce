@extends('layouts.public')
@section('title', 'Detail Pembayaran')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8 text-center sm:text-left">
        <h1 class="text-3xl font-extrabold text-gray-900 flex items-center justify-center sm:justify-start gap-3">
            <i class="fas fa-file-invoice text-indigo-600"></i>
            Detail Pembayaran
        </h1>
        <p class="mt-2 text-sm text-gray-600">Rincian pesanan dan informasi pengiriman Anda.</p>
    </div>

    <!-- Invoice Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <!-- Order Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-100">
            <div class="p-6 sm:p-8">
                <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 pb-4 border-b border-gray-100">
                    <i class="fas fa-user text-indigo-500"></i>
                    Informasi Pembeli
                </h4>
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nama</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $order->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">No HP</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $order->phone }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Alamat</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $order->alamat }}</dd>
                    </div>
                </dl>
            </div>
            <div class="p-6 sm:p-8 bg-gray-50/50">
                <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 pb-4 border-b border-gray-100">
                    <i class="fas fa-shopping-bag text-indigo-500"></i>
                    Informasi Pesanan
                </h4>
                <dl class="space-y-4">
                    <div class="flex justify-between items-start sm:block">
                        <dt class="text-sm font-medium text-gray-500">No. Pesanan</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $order->order_id_midtrans }}</dd>
                    </div>
                    <div class="flex justify-between items-start sm:block">
                        <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                        <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $order->created_at->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between items-center sm:items-start sm:block">
                        <dt class="text-sm font-medium text-gray-500 sm:mb-1">Status Pembayaran</dt>
                        <dd>
                            @if ($order->status === 'complete')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">
                                    <i class="fas fa-check-circle mr-1.5"></i> Lunas
                                </span>
                            @elseif ($order->status === 'cancel')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 border border-red-200">
                                    <i class="fas fa-times-circle mr-1.5"></i> Dibatalkan
                                </span>
                            @elseif ($order->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                                    <i class="fas fa-clock mr-1.5"></i> Pending
                                </span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between items-center sm:items-start sm:block">
                        <dt class="text-sm font-medium text-gray-500 sm:mb-1">Status Pesanan</dt>
                        <dd>
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200">
                                <i class="fas fa-info-circle mr-1.5"></i> {{ ucfirst(str_replace('_', ' ', $order->status_pesanan)) }}
                            </span>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Order Details Table -->
        <div class="p-6 sm:p-8 border-t border-gray-100">
            <h4 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-list text-indigo-500"></i>
                Detail Produk
            </h4>
            
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider sm:pl-6">Produk</th>
                            <th scope="col" class="px-3 py-3.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider hidden sm:table-cell">Harga</th>
                            <th scope="col" class="px-3 py-3.5 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                            <th scope="col" class="py-3.5 pl-3 pr-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider sm:pr-6">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        <tr>
                            <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                {{ $order->produk->nama }}
                                <div class="mt-1 sm:hidden text-xs text-gray-500">
                                    Rp {{ number_format($order->produk->harga, 0, ',', '.') }}
                                </div>
                            </td>
                            <td class="px-3 py-4 text-sm text-gray-500 text-right hidden sm:table-cell">Rp {{ number_format($order->produk->harga, 0, ',', '.') }}</td>
                            <td class="px-3 py-4 text-sm text-gray-500 text-center">{{ $order->jumlah }}</td>
                            <td class="py-4 pl-3 pr-4 text-sm font-semibold text-gray-900 text-right sm:pr-6">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Total Price -->
            <div class="mt-6 flex justify-end items-center gap-4 bg-indigo-50 p-4 rounded-lg border border-indigo-100">
                <span class="text-sm font-semibold text-indigo-900">Total Harga:</span>
                <span class="text-2xl font-extrabold text-indigo-700">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Form Update Status Pesanan -->
        @if($order->status === 'complete' && $order->status_pesanan === 'dikirim')
            <div class="p-6 sm:p-8 bg-gray-50 border-t border-gray-200">
                <h4 class="text-lg font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-truck text-indigo-500"></i>
                    Konfirmasi Penerimaan Barang
                </h4>
                <form action="{{ route('pembeli.pesanan.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <p class="text-sm text-gray-600 mb-4">Silakan konfirmasi status penerimaan barang, apakah sudah Anda terima dengan baik?</p>
                    
                    <div class="flex flex-col sm:flex-row gap-3">
                        @php
                            $pengirimanStatus = ['diterima' => 'Diterima', 'belum_diterima' => 'Belum Diterima'];
                            $currentStatus = old('status_pesanan', $order->status_pesanan);
                        @endphp
                        
                        @foreach ($pengirimanStatus as $value => $label)
                            <div class="flex-1 relative">
                                <input type="radio" class="peer sr-only" name="status_pesanan" 
                                       id="status-{{ $value }}" value="{{ $value }}"
                                       {{ $currentStatus === $value ? 'checked' : '' }}>
                                <label class="flex items-center justify-between px-4 py-3 bg-white border border-gray-300 rounded-lg cursor-pointer hover:bg-gray-50 peer-checked:border-indigo-600 peer-checked:bg-indigo-50 peer-checked:ring-1 peer-checked:ring-indigo-600 transition-all" 
                                       for="status-{{ $value }}">
                                    <div class="flex items-center">
                                        <div class="w-5 h-5 border-2 rounded-full border-gray-300 peer-checked:border-indigo-600 flex items-center justify-center mr-3 radio-indicator">
                                            <div class="w-2.5 h-2.5 rounded-full bg-indigo-600 scale-0 transition-transform"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $label }}</span>
                                    </div>
                                    @if($value == 'diterima')
                                        <i class="fas fa-check text-green-500 text-lg"></i>
                                    @else
                                        <i class="fas fa-box-open text-gray-400 text-lg"></i>
                                    @endif
                                </label>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 border border-transparent shadow-sm text-base font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                            <i class="fas fa-sync-alt mr-2"></i>
                            Perbarui Status Pesanan
                        </button>
                    </div>
                </form>
            </div>
        @endif
    </div>
    
    <!-- Action Buttons -->
    <div class="flex justify-center">
        <a href="{{ route('pembeli.pesanan.index') }}" class="inline-flex justify-center items-center px-6 py-3 border border-gray-300 shadow-sm text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors w-full sm:w-auto">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i>
            Kembali ke Daftar Pesanan
        </a>
    </div>
</div>

<style>
    /* Styling custom untuk radio buttons */
    input[type="radio"]:checked + label .radio-indicator {
        border-color: #4f46e5;
    }
    input[type="radio"]:checked + label .radio-indicator div {
        transform: scale(1);
    }
</style>
@endsection
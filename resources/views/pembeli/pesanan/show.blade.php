@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <i class="fas fa-receipt text-indigo-600"></i>
                Detail Pesanan
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Informasi detail untuk pesanan <span class="font-semibold text-gray-900">{{ $pesanan->kode_pesanan }}</span>
            </p>
        </div>
        <a href="{{ route('pembeli.pesanan.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden mb-8">
        <!-- Order Summary Section -->
        <div class="p-6 sm:p-8 border-b border-gray-100">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-info-circle text-indigo-500"></i>
                Informasi Pesanan
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Kode Pesanan -->
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-barcode text-gray-400"></i>Kode Pesanan
                    </span>
                    <span class="text-base font-semibold text-gray-900">{{ $pesanan->kode_pesanan }}</span>
                </div>

                <!-- Status -->
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-tag text-gray-400"></i>Status
                    </span>
                    <div>
                        @if($pesanan->status == 'lunas')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1.5"></i> Lunas
                            </span>
                        @elseif($pesanan->status == 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1.5"></i> Pending
                            </span>
                        @elseif($pesanan->status == 'batal')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                <i class="fas fa-times-circle mr-1.5"></i> Dibatalkan
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <i class="fas fa-info-circle mr-1.5"></i> {{ ucfirst($pesanan->status) }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Tanggal Pesan -->
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-calendar text-gray-400"></i>Tanggal Pesan
                    </span>
                    <span class="text-base font-medium text-gray-900">{{ $pesanan->created_at->format('d M Y H:i') }}</span>
                </div>
                
                <!-- Metode Pembayaran -->
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-credit-card text-gray-400"></i>Metode Pembayaran
                    </span>
                    <span class="text-base font-medium text-gray-900">
                        @if(strtolower($pesanan->metode_pembayaran) == 'bank_transfer')
                            Bank Transfer
                        @elseif(strtolower($pesanan->metode_pembayaran) == 'cod')
                            Cash on Delivery (COD)
                        @else
                            {{ strtoupper($pesanan->metode_pembayaran) }}
                        @endif
                    </span>
                </div>
                
                <!-- Total Harga -->
                <div class="flex flex-col gap-1">
                    <span class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-money-bill-wave text-gray-400"></i>Total Harga
                    </span>
                    <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($pesanan->total_harga, 0, ',', '.') }}</span>
                </div>

                <!-- Alamat Pengiriman -->
                <div class="flex flex-col gap-1 lg:col-span-3">
                    <span class="text-sm font-medium text-gray-500 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-gray-400"></i>Alamat Pengiriman
                    </span>
                    <span class="text-base text-gray-700 bg-gray-50 p-4 rounded-lg mt-1 border border-gray-100">{{ $pesanan->alamat_pengiriman }}</span>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="p-6 sm:p-8 bg-gray-50/50">
            <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                <i class="fas fa-box-open text-indigo-500"></i>
                Produk yang Dipesan
            </h3>

            @if($pesanan->pesananDetails && $pesanan->pesananDetails->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($pesanan->pesananDetails as $detail)
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
                            <!-- Product Image -->
                            <div class="h-48 w-full bg-gray-100 relative group overflow-hidden">
                                @if ($detail->produk && $detail->produk->gambar)
                                    <img src="{{ asset('storage/' . $detail->produk->gambar) }}" 
                                         alt="{{ $detail->produk->nama }}" 
                                         class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                                @else
                                    <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                        <i class="fas fa-image text-4xl"></i>
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Product Details -->
                            <div class="p-5">
                                <h4 class="text-lg font-bold text-gray-900 mb-4 line-clamp-2">{{ $detail->produk->nama ?? 'Produk Tidak Tersedia' }}</h4>
                                
                                <div class="space-y-3">
                                    <div class="flex justify-between items-center text-sm">
                                        <span class="text-gray-500">Harga Satuan</span>
                                        <span class="font-medium text-gray-900">Rp {{ number_format($detail->harga, 0, ',', '.') }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-sm border-b border-gray-100 pb-3">
                                        <span class="text-gray-500">Jumlah</span>
                                        <span class="font-medium text-gray-900">{{ $detail->jumlah }} item</span>
                                    </div>
                                    <div class="flex justify-between items-center pt-1">
                                        <span class="text-sm font-medium text-gray-700">Subtotal</span>
                                        <span class="text-base font-bold text-indigo-600">Rp {{ number_format($detail->harga * $detail->jumlah, 0, ',', '.') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12 bg-white rounded-xl border border-dashed border-gray-300">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4 text-gray-400">
                        <i class="fas fa-box-open text-2xl"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada produk</h3>
                    <p class="text-gray-500">Detail produk tidak tersedia untuk pesanan ini.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
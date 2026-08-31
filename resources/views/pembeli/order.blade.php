@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8 text-center sm:text-left">
        <h1 class="text-3xl font-extrabold text-gray-900 flex items-center justify-center sm:justify-start gap-3">
            <i class="fas fa-shopping-bag text-indigo-600"></i>
            Checkout Pesanan
        </h1>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        {{ session('error') }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <div class="-mx-1.5 -my-1.5">
                        <button type="button" class="inline-flex bg-red-50 rounded-md p-1.5 text-red-500 hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-red-50 focus:ring-red-600" onclick="this.parentElement.parentElement.parentElement.parentElement.remove()">
                            <span class="sr-only">Dismiss</span>
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
            {{-- Kolom Gambar dan Info Produk --}}
            <div class="lg:col-span-6 p-6 sm:p-8 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 pb-4 border-b border-gray-200">
                    <i class="fas fa-box text-indigo-500"></i>
                    Detail Produk
                </h3>

                <div class="space-y-4 mb-6 max-h-96 overflow-y-auto pr-2">
                    @foreach($items as $item)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden group flex">
                        <div class="w-32 bg-gray-100 relative shrink-0">
                            @if ($item->produk->gambar)
                                <img src="{{ asset('storage/' . $item->produk->gambar) }}" alt="{{ $item->produk->nama }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        <div class="p-4 flex-1">
                            <h4 class="text-md font-bold text-gray-900 mb-1 line-clamp-2">{{ $item->produk->nama }}</h4>
                            <div class="text-xs text-gray-500 mb-2">Toko: {{ $item->produk->umkm->nama_perusahaan ?? 'UMKM' }}</div>
                            <div class="mb-2">
                                @if($item->harga_satuan < $item->produk->harga)
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-bold text-green-600">
                                            Rp{{ number_format($item->harga_satuan, 0, ',', '.') }}
                                        </span>
                                        <span class="text-xs text-gray-500 line-through">
                                            Rp{{ number_format($item->produk->harga, 0, ',', '.') }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-sm font-bold text-gray-900">
                                        Rp{{ number_format($item->produk->harga, 0, ',', '.') }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-xs font-medium text-gray-700 bg-gray-50 rounded p-1.5 border border-gray-100 inline-block">
                                <span class="text-indigo-600">{{ $item->jumlah }}</span> x pesanan
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="bg-indigo-50/50 p-5 sm:p-6 border border-indigo-100 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-700 flex items-center gap-2">
                            <i class="fas fa-receipt text-indigo-400"></i>
                            Total Pembayaran
                        </span>
                        <span class="text-xl font-extrabold text-indigo-700">
                            Rp{{ number_format($total_harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Kolom Form Pemesanan --}}
            <div class="lg:col-span-6 p-6 sm:p-8">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 pb-4 border-b border-gray-200">
                    <i class="fas fa-user-edit text-indigo-500"></i>
                    Data Pengiriman
                </h3>

                <form action="{{ route('pembeli.checkout') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    <input type="hidden" name="is_cart" value="{{ $is_cart ? '1' : '0' }}">
                    @if(!$is_cart)
                        <input type="hidden" name="produk_id" value="{{ $produkId }}">
                        <input type="hidden" name="jumlah" value="{{ $quantity }}">
                    @endif

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-user text-gray-400 mr-1.5"></i>Nama Lengkap
                        </label>
                        <input type="text" name="name" 
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 border focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-colors sm:text-sm"
                            placeholder="Masukkan nama lengkap penerima" required value="{{ old('name', Auth::user()->name) }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-phone text-gray-400 mr-1.5"></i>Nomor HP/WhatsApp
                        </label>
                        <input type="text" name="phone" 
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 border focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-colors sm:text-sm"
                            placeholder="08xxxxxxxxxx" required value="{{ old('phone') }}">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-map-marker-alt text-gray-400 mr-1.5"></i>Alamat Pengiriman
                        </label>
                        <textarea name="alamat" rows="4"
                            class="block w-full px-4 py-3 rounded-lg border-gray-300 bg-gray-50 border focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white transition-colors sm:text-sm resize-y"
                            placeholder="Masukkan alamat lengkap pengiriman beserta patokan jika ada" required>{{ old('alamat', Auth::user()->alamat) }}</textarea>
                    </div>

                    <div class="pt-6 border-t border-gray-100">
                        <button type="submit" class="w-full flex justify-center items-center px-6 py-4 border border-transparent text-base font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm transition-all transform active:scale-[0.98]">
                            <i class="fas fa-lock mr-2"></i>
                            Pesan & Lanjut ke Pembayaran
                        </button>
                        <p class="text-center text-xs text-gray-500 mt-4 flex items-center justify-center gap-1.5">
                            <i class="fas fa-shield-alt text-green-500"></i>
                            Transaksi aman dan terenkripsi
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
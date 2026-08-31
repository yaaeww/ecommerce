@extends('layouts.public')
@section('title', 'Semua Produk')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-4xl md:text-5xl font-extrabold text-gray-900 mb-4 flex items-center justify-center gap-3">
            <div class="w-14 h-14 bg-indigo-100 rounded-2xl flex items-center justify-center text-indigo-600 shadow-sm">
                <i class="fas fa-boxes"></i>
            </div>
            Semua Produk
        </h1>
        <p class="text-lg text-gray-600 max-w-2xl mx-auto">
            Temukan produk-produk unggulan pilihan terbaik dari UMKM Indramayu
        </p>
    </div>

    <!-- Products Grid -->
    @if(count($produks) > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach ($produks as $produk)
                <div class="bg-white rounded-2xl overflow-hidden border border-gray-200 shadow-sm hover:shadow-lg transition-all duration-300 group flex flex-col h-full relative hover:-translate-y-1">
                    <!-- Product Image -->
                    <div class="relative h-64 overflow-hidden bg-gray-100">
                        @if ($produk->gambar)
                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <i class="fas fa-image text-5xl opacity-50"></i>
                            </div>
                        @endif

                        <!-- Badges -->
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            @if(isset($produk->harga_setelah_diskon) && $produk->harga_setelah_diskon < $produk->harga)
                                @php
                                    $diskon = 100 - round(($produk->harga_setelah_diskon / $produk->harga) * 100);
                                @endphp
                                <span class="bg-red-500 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-sm flex items-center gap-1">
                                    <i class="fas fa-tag"></i> Diskon {{ $diskon }}%
                                </span>
                            @endif
                        </div>

                        <!-- Overlay Actions -->
                        <div class="absolute inset-0 bg-gray-900/40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-4 backdrop-blur-[2px] z-10">
                            <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="px-6 py-2.5 bg-white text-gray-900 font-semibold rounded-full hover:bg-indigo-50 hover:text-indigo-600 transition-colors transform hover:scale-105 shadow-lg flex items-center gap-2 text-sm">
                                <i class="fas fa-eye"></i> Lihat Detail
                            </a>
                        </div>
                    </div>

                    <!-- Product Info -->
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex-grow">
                            <!-- Title -->
                            <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="block mb-2">
                                <h3 class="text-lg font-bold text-gray-900 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                                    {{ $produk->nama }}
                                </h3>
                            </a>

                            <div class="text-gray-500 text-sm mb-4 line-clamp-2">
                                {{ $produk->deskripsi }}
                            </div>

                            <!-- Price -->
                            <div class="mb-6">
                                @if(isset($produk->harga_setelah_diskon) && $produk->harga_setelah_diskon < $produk->harga)
                                    <div class="flex flex-col">
                                        <span class="text-gray-400 line-through text-xs mb-0.5">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                                        <span class="text-xl font-extrabold text-indigo-700">Rp {{ number_format($produk->harga_setelah_diskon, 0, ',', '.') }}</span>
                                    </div>
                                @else
                                    <span class="text-xl font-extrabold text-gray-900">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                                @endif
                            </div>
                        </div>

                        <!-- Add to Cart Form -->
                        <form action="{{ route('pembeli.keranjang.store') }}" method="POST" class="mt-auto">
                            @csrf
                            <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="w-full bg-white text-indigo-600 border border-indigo-200 py-2.5 rounded-xl font-semibold hover:bg-indigo-50 hover:border-indigo-300 transition-all duration-300 flex items-center justify-center gap-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-sm">
                                <i class="fas fa-cart-plus"></i>
                                <span>Tambah ke Keranjang</span>
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($produks->hasPages())
            <div class="mt-12 flex flex-col items-center">
                {{ $produks->links() }}
                <div class="text-gray-500 text-sm mt-4">
                    Menampilkan {{ $produks->firstItem() ?? 0 }} - {{ $produks->lastItem() ?? 0 }} dari {{ $produks->total() }} produk
                </div>
            </div>
        @endif

    @else
        <!-- Empty State -->
        <div class="text-center py-24 bg-gray-50 rounded-3xl border border-gray-200 border-dashed">
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm border border-gray-100">
                <i class="fas fa-box-open text-4xl text-gray-400"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2">Belum ada produk</h3>
            <p class="text-gray-500 max-w-sm mx-auto">Maaf, saat ini belum ada produk yang tersedia di toko kami.</p>
        </div>
    @endif
</div>
@endsection
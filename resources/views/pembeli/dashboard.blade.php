@extends('layouts.public')

@section('title', 'Beranda Belanja - Juragan Pelem')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-12">

    <!-- Header Section -->
    <div class="bg-white rounded-3xl p-8 border border-slate-100 shadow-sm relative overflow-hidden">
        <div class="absolute inset-y-0 right-0 w-1/3 bg-gradient-to-l from-indigo-600/10 to-transparent"></div>
        <div class="relative z-10">
            <h1 class="text-3xl md:text-4xl font-bold font-display text-slate-800 mb-2">Selamat datang, {{ Auth::user()->name }}!</h1>
            <p class="text-slate-600">Temukan produk mangga dan olahan UMKM terbaik dari Indramayu.</p>
        </div>
    </div>

    <!-- Alert / Messages -->
    @if (session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl p-4 flex items-center gap-3">
            <i class="fas fa-check-circle text-emerald-500"></i>
            <p class="text-sm font-medium">{{ session('success') }}</p>
        </div>
    @endif

    @if (request('search'))
        <div class="bg-blue-50 border border-blue-200 text-blue-800 rounded-2xl p-4 flex items-center gap-3">
            <i class="fas fa-search text-blue-500"></i>
            <p class="text-sm font-medium">Menampilkan hasil pencarian untuk: <strong>{{ request('search') }}</strong></p>
            <a href="{{ route('pembeli.dashboard') }}" class="ml-auto text-sm underline hover:text-blue-900">Reset</a>
        </div>
    @endif

    <!-- Kategori Utama -->
    <section>
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-bold font-display text-slate-800">Kategori Produk</h2>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
            @forelse($kategoris as $kategori)
                <a href="{{ route('pembeli.dashboard', ['kategori' => $kategori->id]) }}" class="group relative bg-white rounded-2xl p-4 border border-slate-100 shadow-sm hover:shadow-md hover:border-indigo-600/30 transition-all text-center flex flex-col items-center justify-center gap-3 {{ $kategoriAktif && $kategoriAktif->id == $kategori->id ? 'ring-2 ring-indigo-600' : '' }}">
                    <div class="w-16 h-16 rounded-full bg-slate-50 flex items-center justify-center p-2 group-hover:scale-110 transition-transform">
                        <img src="{{ $kategori->gambar ? asset('storage/kategori/' . basename($kategori->gambar)) : asset('images/default.jpg') }}" alt="{{ $kategori->nama }}" class="w-full h-full object-cover rounded-full">
                    </div>
                    <span class="text-sm font-semibold text-slate-700 group-hover:text-indigo-600 transition">{{ $kategori->nama }}</span>
                </a>
            @empty
                <div class="col-span-full bg-slate-50 rounded-2xl p-8 text-center border border-slate-100">
                    <i class="fas fa-box-open text-3xl text-slate-400 mb-3"></i>
                    <p class="text-slate-500 font-medium">Belum ada kategori tersedia.</p>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Subkategori & Produk Filtered -->
    @if ($kategoriAktif)
        <section class="bg-white rounded-3xl p-6 md:p-8 border border-slate-100 shadow-sm">
            <h3 class="text-xl font-bold font-display text-slate-800 mb-6 flex items-center gap-2">
                <i class="fas fa-folder-open text-amber-500"></i> Eksplorasi: <span class="text-indigo-600">{{ $kategoriAktif->nama }}</span>
            </h3>

            {{-- Subkategori Grid --}}
            @if ($subkategoris->count())
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4 mb-8">
                    @foreach ($subkategoris as $sub)
                        <a href="{{ route('pembeli.dashboard', ['kategori' => $sub->id]) }}" class="group bg-slate-50 rounded-2xl border border-slate-100 overflow-hidden hover:border-indigo-600/30 hover:shadow-md transition-all">
                            <div class="h-24 w-full bg-slate-200 overflow-hidden">
                                <img src="{{ $sub->gambar ? asset('storage/kategori/' . basename($sub->gambar)) : asset('images/default.jpg') }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform" alt="{{ $sub->nama }}" loading="lazy">
                            </div>
                            <div class="p-3 text-center">
                                <h5 class="text-sm font-bold text-slate-800 group-hover:text-indigo-600 transition truncate">{{ $sub->nama }}</h5>
                                <span class="text-xs font-medium text-slate-500 mt-1 block">{{ $sub->produks->count() }} Produk</span>
                            </div>
                        </a>
                    @endforeach
                </div>
            @endif

            {{-- Daftar Produk --}}
            @if ($produks->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($produks as $produk)
                        <div class="bento-card group flex flex-col overflow-hidden bg-white">
                            <!-- Image -->
                            <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                                <img src="{{ $produk->gambar ? asset('storage/' . $produk->gambar) : asset('images/default.jpg') }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @if($produk->kategori)
                                    <span class="absolute top-3 left-3 px-2.5 py-1 bg-white/90 backdrop-blur text-[10px] font-bold uppercase tracking-wider text-indigo-600 rounded-lg shadow-sm">
                                        {{ $produk->kategori->nama }}
                                    </span>
                                @endif
                            </div>
                            <!-- Content -->
                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="font-display font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors line-clamp-2">
                                    <a href="{{ route('pembeli.produk.show', $produk->id) }}">{{ $produk->nama }}</a>
                                </h3>
                                <p class="text-xs text-slate-500 mb-4 line-clamp-2">{{ $produk->deskripsi }}</p>
                                
                                <div class="mt-auto flex items-end justify-between pt-4 border-t border-slate-100">
                                    <div>
                                        <span class="block text-xs text-slate-400 mb-0.5">Harga</span>
                                        <span class="text-lg font-bold text-amber-500">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                                    </div>
                                    <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8 flex justify-center">
                    {{ $produks->links() }}
                </div>
            @else
                <div class="bg-slate-50 rounded-2xl p-8 text-center border border-slate-100">
                    <i class="fas fa-search text-3xl text-slate-400 mb-3"></i>
                    <p class="text-slate-500 font-medium">Tidak ada produk ditemukan di kategori ini.</p>
                </div>
            @endif
        </section>
    @else
        <!-- Produk Terlaris (Only show if no category filter is applied) -->
        @if ($produkTerlaris->count())
        <section>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-2xl font-bold font-display text-slate-800 flex items-center gap-2">
                        <i class="fas fa-fire text-amber-500"></i> Produk Terlaris
                    </h2>
                    <p class="text-sm text-slate-500 mt-1">Jangan lewatkan produk paling diminati bulan ini!</p>
                </div>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach ($produkTerlaris->take(4) as $produk)
                    <div class="bento-card group flex flex-col overflow-hidden bg-white">
                        <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                            <img src="{{ $produk->gambar ? asset('storage/' . $produk->gambar) : asset('images/default.jpg') }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <span class="absolute top-3 left-3 px-2.5 py-1 bg-amber-500 text-[10px] font-bold uppercase tracking-wider text-white rounded-lg shadow-sm">
                                Best Seller
                            </span>
                        </div>
                        <div class="p-5 flex flex-col flex-1">
                            <h3 class="font-display font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors line-clamp-1">
                                <a href="{{ route('pembeli.produk.show', $produk->id) }}">{{ $produk->nama }}</a>
                            </h3>
                            <div class="mt-auto pt-3 flex items-center justify-between">
                                <span class="text-lg font-bold text-indigo-600">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                                <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="text-sm font-semibold text-amber-500 hover:text-indigo-600 transition">Detail <i class="fas fa-chevron-right text-xs"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        <!-- Semua Produk -->
        <section>
            <div class="flex items-center justify-between mb-6">
                <h2 class="text-2xl font-bold font-display text-slate-800">Eksplorasi Katalog</h2>
            </div>
            
            @if ($produks->isNotEmpty())
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    @foreach ($produks as $produk)
                        <div class="bento-card group flex flex-col overflow-hidden bg-white">
                            <!-- Image -->
                            <div class="relative aspect-[4/3] overflow-hidden bg-slate-100">
                                <img src="{{ $produk->gambar ? asset('storage/' . $produk->gambar) : asset('images/default.jpg') }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                @if($produk->kategori)
                                    <span class="absolute top-3 left-3 px-2.5 py-1 bg-white/90 backdrop-blur text-[10px] font-bold uppercase tracking-wider text-indigo-600 rounded-lg shadow-sm">
                                        {{ $produk->kategori->nama }}
                                    </span>
                                @endif
                            </div>
                            <!-- Content -->
                            <div class="p-5 flex flex-col flex-1">
                                <h3 class="font-display font-bold text-slate-800 mb-1 group-hover:text-indigo-600 transition-colors line-clamp-2">
                                    <a href="{{ route('pembeli.produk.show', $produk->id) }}">{{ $produk->nama }}</a>
                                </h3>
                                <p class="text-xs text-slate-500 mb-4 line-clamp-2">{{ $produk->deskripsi }}</p>
                                
                                <div class="mt-auto flex items-end justify-between pt-4 border-t border-slate-100">
                                    <div>
                                        <span class="block text-xs text-slate-400 mb-0.5">Harga</span>
                                        <span class="text-lg font-bold text-amber-500">Rp {{ number_format($produk->harga, 0, ',', '.') }}</span>
                                    </div>
                                    <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        <i class="fas fa-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <div class="mt-8 flex justify-center">
                    {{ $produks->links() }}
                </div>
            @else
                <div class="bg-slate-50 rounded-2xl p-8 text-center border border-slate-100">
                    <i class="fas fa-box-open text-3xl text-slate-400 mb-3"></i>
                    <p class="text-slate-500 font-medium">Belum ada produk tersedia saat ini.</p>
                </div>
            @endif
        </section>
    @endif

</div>
@endsection
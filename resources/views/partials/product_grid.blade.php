@if($produks->count())
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-6">
        @foreach($produks as $produk)
            <div class="bg-white rounded-2xl border border-slate-200/60 flex flex-col group hover:shadow-lg hover:border-indigo-200 transition-all duration-300 relative">
                
                <!-- Discount Badge -->
                @if($produk->diskon && now()->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir))
                    <div class="absolute top-3 left-3 z-10 bg-red-500 text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                        <i class="fas fa-tag"></i> -{{ $produk->diskon->persen_diskon }}%
                    </div>
                @endif

                <!-- Product Image -->
                <div class="relative aspect-square overflow-hidden rounded-t-2xl bg-slate-100">
                    <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="block w-full h-full cursor-pointer">
                        @if($produk->gambar)
                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                <i class="fas fa-image text-4xl"></i>
                            </div>
                        @endif
                    </a>
                    
                    <!-- Quick Action Hover Overlay -->
                    <div class="absolute inset-0 bg-slate-900/10 opacity-0 group-hover:opacity-100 transition-opacity flex items-end justify-center pb-4 gap-2 pointer-events-none">
                        <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="w-10 h-10 rounded-full bg-white text-slate-700 flex items-center justify-center shadow hover:bg-brand-green hover:text-white transition-colors pointer-events-auto" title="Lihat Detail">
                            <i class="far fa-eye"></i>
                        </a>
                        @auth
                            @if(Auth::user()->role === 'pembeli')
                                <form action="{{ route('pembeli.keranjang.store') }}" method="POST" class="inline pointer-events-auto">
                                    @csrf
                                    <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="w-10 h-10 rounded-full bg-white text-slate-700 flex items-center justify-center shadow hover:bg-brand-green hover:text-white transition-colors" title="Tambah ke Keranjang">
                                        <i class="fas fa-shopping-cart"></i>
                                    </button>
                                </form>
                            @endif
                        @else
                            <button type="button" onclick="showLoginPrompt('menambahkan produk ke keranjang')" class="w-10 h-10 rounded-full bg-white text-slate-700 flex items-center justify-center shadow hover:bg-brand-green hover:text-white transition-colors pointer-events-auto" title="Tambah ke Keranjang">
                                <i class="fas fa-shopping-cart"></i>
                            </button>
                        @endauth
                    </div>
                </div>

                <!-- Product Info -->
                <div class="p-4 flex flex-col flex-1">
                    <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="flex-1">
                        <h3 class="font-semibold text-slate-900 text-sm mb-1 line-clamp-2 group-hover:text-indigo-600 transition-colors">
                            {{ $produk->nama }}
                        </h3>
                    </a>
                    
                    <div class="mt-3">
                        @if($produk->diskon && now()->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir))
                            @php
                                $hargaDiskon = $produk->harga - ($produk->harga * ($produk->diskon->persen_diskon / 100));
                            @endphp
                            <div class="flex flex-col">
                                <span class="text-lg font-extrabold text-slate-900">Rp{{ number_format($hargaDiskon, 0, ',', '.') }}</span>
                                <span class="text-xs text-slate-400 line-through">Rp{{ number_format($produk->harga, 0, ',', '.') }}</span>
                            </div>
                        @else
                            <span class="text-lg font-extrabold text-slate-900 block">Rp{{ number_format($produk->harga, 0, ',', '.') }}</span>
                        @endif
                    </div>
                    
                    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between">
                        <div class="flex items-center gap-1 text-[11px] font-semibold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">
                            <i class="fas fa-star text-amber-400"></i>
                            {{ $produk->rating > 0 ? number_format($produk->rating, 1) : '5.0' }}
                        </div>
                        <span class="text-[10px] uppercase font-bold text-slate-400">{{ $produk->umkm->nama_toko ?? 'UMKM' }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="mt-8 w-full ajax-pagination">
        {{ $produks->links() }}
    </div>
@else
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-12 text-center">
        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-box-open text-3xl text-slate-300"></i>
        </div>
        <h3 class="text-lg font-bold text-slate-800 mb-2">Produk tidak ditemukan</h3>
        <p class="text-slate-500 text-sm">Coba sesuaikan filter Anda atau gunakan kata kunci lain untuk mencari produk.</p>
        <button type="button" onclick="resetAllFilters()" class="mt-6 px-6 py-2.5 bg-indigo-600 text-white font-bold rounded-xl text-sm shadow-sm hover:bg-indigo-700 transition">Reset Filter</button>
    </div>
@endif

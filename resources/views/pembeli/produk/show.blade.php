@extends('layouts.public')

@section('title', $produk->nama . ' — Juragan Pelem')
@section('meta_description', Str::limit($produk->deskripsi, 150))

@section('content')
<!-- Breadcrumbs -->
<div class="bg-brand-cream/60 border-b border-slate-100 py-3">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-xs font-semibold text-slate-500">
            <a href="{{ route('landing') }}" class="hover:text-indigo-600 transition">Beranda</a>
            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            <a href="{{ route('kategori') }}" class="hover:text-indigo-600 transition">Kategori</a>
            <i class="fas fa-chevron-right text-[10px] text-slate-300"></i>
            <span class="text-slate-800 font-bold truncate max-w-xs">{{ $produk->nama }}</span>
        </nav>
    </div>
</div>

<main class="py-10 lg:py-14">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-indigo-50 border border-indigo-200 text-indigo-600 rounded-2xl flex items-center gap-3 text-sm font-semibold shadow-sm">
                <i class="fas fa-circle-check text-lg"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center gap-3 text-sm font-semibold shadow-sm">
                <i class="fas fa-circle-exclamation text-lg"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- Product Hero Grid (Image + Purchasing Actions) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-start mb-16">
            
            <!-- Left: Product Image Gallery (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bento-card p-4 bg-white border border-slate-200/80 shadow-sm relative overflow-hidden aspect-square flex items-center justify-center">
                    @if($produk->gambar)
                        <img 
                            src="{{ asset('storage/' . $produk->gambar) }}" 
                            alt="{{ $produk->nama }}" 
                            class="w-full h-full object-cover rounded-2xl"
                            onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');"
                        >
                        <div class="hidden w-full h-full rounded-2xl bg-indigo-50/60 flex flex-col items-center justify-center text-slate-400 gap-2">
                            <i class="fas fa-box-open text-5xl text-indigo-400"></i>
                            <span class="text-xs font-semibold text-slate-500">{{ $produk->nama }}</span>
                        </div>
                    @else
                        <div class="w-full h-full rounded-2xl bg-indigo-50/60 flex flex-col items-center justify-center text-slate-400 gap-2">
                            <i class="fas fa-box-open text-5xl text-indigo-400"></i>
                            <span class="text-xs font-semibold text-slate-500">{{ $produk->nama }}</span>
                        </div>
                    @endif

                    @if($produk->diskon && now()->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir))
                        <span class="absolute top-6 left-6 bg-red-600 text-white font-black text-xs px-3 py-1 rounded-lg shadow-md tracking-wider">
                            DISKON {{ $produk->diskon->persen_diskon }}%
                        </span>
                    @endif
                </div>

                <!-- Guarantee Badges under image -->
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3 bg-brand-cream/60 rounded-xl border border-slate-100 text-center">
                        <i class="fas fa-truck-fast text-indigo-600 text-sm mb-1 block"></i>
                        <p class="text-[11px] font-bold text-slate-700">Kirim 24 Jam</p>
                    </div>
                    <div class="p-3 bg-brand-cream/60 rounded-xl border border-slate-100 text-center">
                        <i class="fas fa-shield-halved text-amber-500 text-sm mb-1 block"></i>
                        <p class="text-[11px] font-bold text-slate-700">Garansi Segar</p>
                    </div>
                    <div class="p-3 bg-brand-cream/60 rounded-xl border border-slate-100 text-center">
                        <i class="fas fa-seedling text-teal-600 text-sm mb-1 block"></i>
                        <p class="text-[11px] font-bold text-slate-700">100% Organik</p>
                    </div>
                </div>
            </div>

            <!-- Right: Product Overview & Buy Box (7 cols) -->
            <div class="lg:col-span-7 space-y-6">
                
                <!-- Category & UMKM Name -->
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600 bg-indigo-50 px-2.5 py-0.5 rounded-md border border-indigo-200">
                            {{ $produk->kategori->nama ?? 'Komoditas Unggulan' }}
                        </span>
                        <span class="text-xs font-semibold text-slate-400">•</span>
                        <span class="text-xs font-semibold text-slate-500">
                            {{ $produk->umkm->nama_toko ?? $produk->user->name ?? 'Mitra Kebun Indramayu' }}
                        </span>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-extrabold text-brand-slate tracking-tight leading-tight">
                        {{ $produk->nama }}
                    </h1>

                    <!-- Rating & Stock summary -->
                    <div class="flex items-center gap-4 mt-3 pb-4 border-b border-slate-100">
                        <div class="flex items-center gap-1.5 bg-amber-50 px-2.5 py-1 rounded-lg border border-amber-200/60 text-xs font-bold text-amber-800">
                            <i class="fas fa-star text-amber-400"></i>
                            <span>{{ number_format($produk->rating ?? 5.0, 1) }}</span>
                            <span class="text-slate-400 font-normal">({{ $ulasan->count() }} ulasan)</span>
                        </div>
                        <span class="text-xs text-slate-400">|</span>
                        <div class="text-xs font-semibold {{ $produk->stok > 0 ? 'text-indigo-600' : 'text-red-500' }}">
                            <i class="fas {{ $produk->stok > 0 ? 'fa-circle-check' : 'fa-circle-xmark' }} mr-1"></i>
                            {{ $produk->stok > 0 ? 'Stok Tersedia (' . $produk->stok . ' item)' : 'Stok Habis' }}
                        </div>
                    </div>
                </div>

                <!-- Price Box -->
                <div class="p-5 bg-brand-cream/50 rounded-2xl border border-slate-200/70">
                    <p class="text-xs text-slate-500 font-semibold mb-1">Harga Produk</p>
                    <div class="flex items-baseline gap-3">
                        <span class="text-3xl sm:text-4xl font-extrabold text-indigo-600 font-display">
                            Rp{{ number_format($produk->harga_setelah_diskon ?? $produk->harga, 0, ',', '.') }}
                        </span>
                        @if(isset($produk->harga_setelah_diskon) && $produk->harga_setelah_diskon < $produk->harga)
                            <span class="text-base text-slate-400 line-through font-semibold">
                                Rp{{ number_format($produk->harga, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Short Description -->
                <div>
                    <h3 class="text-xs uppercase tracking-wider font-bold text-slate-400 mb-2">Deskripsi Produk</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        {{ $produk->deskripsi }}
                    </p>
                </div>

                <!-- Quantity & Purchase Action Buttons -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    
                    @auth
                        @if(Auth::user()->role === 'pembeli')
                            <!-- Form Beli untuk Pembeli yang sudah login -->
                            <form action="{{ route('pembeli.keranjang.store') }}" method="POST" id="cartForm">
                                @csrf
                                <input type="hidden" name="produk_id" value="{{ $produk->id }}">
                                
                                <div class="flex flex-wrap items-center justify-between gap-4 mb-5 p-3.5 bg-slate-50 rounded-2xl border border-slate-200/80">
                                    <div class="flex items-center gap-3">
                                        <span class="text-xs font-bold text-slate-700">Jumlah:</span>
                                        <div class="inline-flex items-center border border-slate-200 rounded-xl bg-white p-1 shadow-sm">
                                            <button type="button" onclick="decrementQty()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-600 font-bold transition">
                                                <i class="fas fa-minus text-xs"></i>
                                            </button>
                                            <input type="number" id="quantityInput" name="quantity" value="1" min="1" max="{{ $produk->stok }}" oninput="updateLiveSubtotal()" class="w-12 text-center text-sm font-bold text-slate-800 outline-none border-none bg-transparent">
                                            <button type="button" onclick="incrementQty({{ $produk->stok }})" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-600 font-bold transition">
                                                <i class="fas fa-plus text-xs"></i>
                                            </button>
                                        </div>
                                        <span class="text-xs text-slate-400">Maks. {{ $produk->stok }} item</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-[10px] uppercase font-bold text-slate-400 block">Subtotal</span>
                                        <span id="detail-subtotal-display" class="text-base font-extrabold text-indigo-600">
                                            Rp{{ number_format($produk->harga_setelah_diskon ?? $produk->harga, 0, ',', '.') }}
                                        </span>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <button type="submit" class="w-full py-3.5 px-6 rounded-xl bg-white border-2 border-indigo-600 text-indigo-600 font-bold text-sm hover:bg-indigo-50 transition flex items-center justify-center gap-2 shadow-sm">
                                        <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                                    </button>
                                    <button type="button" onclick="directBuy({{ $produk->id }})" class="w-full py-3.5 px-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition flex items-center justify-center gap-2 shadow-md shadow-indigo-600/20">
                                        <i class="fas fa-bag-shopping"></i> Beli Sekarang
                                    </button>
                                </div>
                            </form>
                        @else
                            <div class="p-4 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600">
                                <i class="fas fa-info-circle text-indigo-600 mr-1"></i> Anda sedang masuk sebagai <strong>{{ ucfirst(Auth::user()->role) }}</strong>. Gunakan akun pembeli untuk bertransaksi.
                            </div>
                        @endif
                    @else
                        <!-- Guest Mode: Prompt Login Only on Purchase -->
                        <div class="flex items-center gap-4 mb-5">
                            <span class="text-xs font-bold text-slate-700">Jumlah:</span>
                            <div class="inline-flex items-center border border-slate-200 rounded-xl bg-white p-1 shadow-sm">
                                <button type="button" onclick="decrementQty()" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-600 font-bold transition">
                                    <i class="fas fa-minus text-xs"></i>
                                </button>
                                <input type="number" id="quantityInput" value="1" min="1" max="{{ $produk->stok }}" class="w-12 text-center text-sm font-bold text-slate-800 outline-none border-none bg-transparent">
                                <button type="button" onclick="incrementQty({{ $produk->stok }})" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 text-slate-600 font-bold transition">
                                    <i class="fas fa-plus text-xs"></i>
                                </button>
                            </div>
                            <span class="text-xs text-slate-400">Maks. {{ $produk->stok }} item</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <a href="{{ route('login') }}" class="w-full py-3.5 px-6 rounded-xl bg-white border-2 border-indigo-600 text-indigo-600 font-bold text-sm hover:bg-indigo-50 transition flex items-center justify-center gap-2 shadow-sm text-center">
                                <i class="fas fa-cart-plus"></i> Tambah ke Keranjang
                            </a>
                            <a href="{{ route('login') }}" class="w-full py-3.5 px-6 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm transition flex items-center justify-center gap-2 shadow-md shadow-indigo-600/20 text-center">
                                <i class="fas fa-bag-shopping"></i> Beli Sekarang
                            </a>
                        </div>
                        <p class="text-[11px] text-slate-400 text-center">
                            <i class="fas fa-lock text-[10px] mr-1"></i> Anda akan diarahkan untuk login terlebih dahulu sebelum melanjutkan pembayaran.
                        </p>
                    @endauth

                </div>

                <!-- Seller Profile Card -->
                <div class="bento-card p-5 bg-white border border-slate-200/80 mt-6 flex items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-xl shadow-sm shrink-0">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <p class="text-[11px] text-slate-400 font-semibold uppercase">Toko Mitra UMKM</p>
                            <h4 class="font-bold text-sm text-slate-900">{{ $produk->umkm->nama_toko ?? $produk->user->name ?? 'Mitra Juragan Pelem' }}</h4>
                            <p class="text-xs text-slate-500"><i class="fas fa-location-dot text-amber-500 mr-1"></i> {{ $produk->umkm->alamat ?? 'Indramayu, Jawa Barat' }}</p>
                        </div>
                    </div>
                    <span class="text-xs font-bold px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg border border-indigo-200/60 shrink-0">
                        <i class="fas fa-check-circle mr-1"></i> Terverifikasi
                    </span>
                </div>

            </div>
        </div>

        <!-- Product Reviews Section -->
        <section class="py-10 border-t border-slate-200">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h2 class="text-2xl font-extrabold text-brand-slate tracking-tight">Ulasan & Penilaian Pembeli</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Ulasan dari konsumen yang telah membeli produk ini.</p>
                </div>
                <div class="flex items-center gap-2 bg-amber-50 px-3.5 py-2 rounded-xl border border-amber-200 text-amber-900 font-bold text-sm">
                    <i class="fas fa-star text-amber-400"></i>
                    <span>{{ number_format($produk->rating ?? 5.0, 1) }} / 5.0</span>
                </div>
            </div>

            @if($ulasan->isEmpty())
                <div class="text-center py-12 bg-brand-cream/40 rounded-2xl border border-dashed border-slate-200">
                    <i class="fas fa-comments text-slate-300 text-4xl mb-3"></i>
                    <p class="text-sm font-bold text-slate-700">Belum Ada Ulasan</p>
                    <p class="text-xs text-slate-400 mt-1">Jadilah yang pertama memberikan ulasan setelah berbelanja!</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($ulasan as $review)
                        <div class="bento-card p-5 bg-white border border-slate-200/80 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 font-bold text-xs flex items-center justify-center">
                                        {{ strtoupper(substr($review->user->name ?? 'U', 0, 2)) }}
                                    </div>
                                    <div>
                                        <h4 class="text-xs font-bold text-slate-900">{{ $review->user->name ?? 'Pembeli' }}</h4>
                                        <p class="text-[10px] text-slate-400">{{ $review->created_at->format('d M Y') }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center text-amber-400 text-xs">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= ($review->bintang ?? $review->rating ?? 5))
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star text-slate-200"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed italic">
                                "{{ $review->komentar ?? $review->isi ?? 'Produk sangat berkualitas dan memuaskan.' }}"
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </section>

        <!-- Related Products Section -->
        @if(isset($produkTerkait) && $produkTerkait->count())
            <section class="py-12 border-t border-slate-200">
                <div class="flex items-center justify-between mb-8">
                    <div>
                        <p class="text-xs uppercase tracking-wider font-bold text-indigo-600 mb-1">Rekomendasi</p>
                        <h2 class="text-2xl font-extrabold text-brand-slate tracking-tight">Produk Terkait Lainnya</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($produkTerkait as $terkait)
                        <div class="bento-card p-4 bg-white border border-slate-200/80 flex flex-col justify-between group">
                            <div>
                                <div class="aspect-square rounded-2xl overflow-hidden bg-slate-100 relative mb-4 border border-slate-100">
                                    @if($terkait->gambar)
                                        <img src="{{ asset('storage/' . $terkait->gambar) }}" alt="{{ $terkait->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" onerror="this.style.display='none'; this.nextElementSibling.classList.remove('hidden');">
                                        <div class="hidden w-full h-full flex items-center justify-center text-slate-300 bg-slate-50">
                                            <i class="fas fa-box-open text-3xl"></i>
                                        </div>
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                                            <i class="fas fa-box-open text-3xl"></i>
                                        </div>
                                    @endif
                                </div>

                                <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 block mb-1">
                                    {{ $terkait->umkm->nama_toko ?? 'Petani Mitra' }}
                                </span>
                                <h3 class="font-bold text-slate-900 text-sm group-hover:text-indigo-600 transition line-clamp-1 mb-1">
                                    {{ $terkait->nama }}
                                </h3>
                            </div>

                            <div class="pt-3 border-t border-slate-100 mt-2">
                                <p class="text-base font-extrabold text-indigo-600 mb-2">
                                    Rp{{ number_format($terkait->harga_setelah_diskon ?? $terkait->harga, 0, ',', '.') }}
                                </p>
                                <a href="{{ route('pembeli.produk.show', $terkait->id) }}" class="w-full py-2 bg-indigo-50 hover:bg-indigo-600 hover:text-white text-indigo-600 font-bold rounded-xl text-xs flex items-center justify-center gap-1.5 transition">
                                    <i class="fas fa-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

    </div>
</main>

@push('scripts')
<script>
    const unitPrice = {{ (float)($produk->harga_setelah_diskon ?? $produk->harga) }};

    function updateLiveSubtotal() {
        const input = document.getElementById('quantityInput');
        if (!input) return;
        let qty = parseInt(input.value) || 1;
        const max = parseInt(input.getAttribute('max')) || 9999;
        if (qty > max) {
            qty = max;
            input.value = max;
        }
        if (qty < 1) {
            qty = 1;
            input.value = 1;
        }

        const subtotal = unitPrice * qty;
        const display = document.getElementById('detail-subtotal-display');
        if (display) {
            display.textContent = 'Rp' + subtotal.toLocaleString('id-ID');
        }
    }

    function incrementQty(max) {
        const input = document.getElementById('quantityInput');
        if (!input) return;
        let current = parseInt(input.value) || 1;
        if (current < max) {
            input.value = current + 1;
            updateLiveSubtotal();
        }
    }

    function decrementQty() {
        const input = document.getElementById('quantityInput');
        if (!input) return;
        let current = parseInt(input.value) || 1;
        if (current > 1) {
            input.value = current - 1;
            updateLiveSubtotal();
        }
    }

    function directBuy(produkId) {
        const qty = document.getElementById('quantityInput').value || 1;
        window.location.href = "{{ url('/pembeli/order') }}/" + produkId + "/" + qty;
    }
</script>
@endpush
@endsection
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

                <!-- Price Box with Strikethrough Discount (Feature 4) -->
                <div class="p-5 bg-brand-cream/50 rounded-2xl border border-slate-200/70">
                    <p class="text-xs text-slate-500 font-semibold mb-1">Harga Produk</p>
                    <div class="flex items-baseline gap-3">
                        @if($produk->harga_coret && $produk->harga_coret > $produk->harga)
                            <span class="text-3xl sm:text-4xl font-extrabold text-rose-600 font-display">
                                Rp{{ number_format($produk->harga, 0, ',', '.') }}
                            </span>
                            <span class="text-base text-slate-400 line-through font-semibold">
                                Rp{{ number_format($produk->harga_coret, 0, ',', '.') }}
                            </span>
                            <span class="px-2 py-0.5 rounded-lg bg-rose-100 text-rose-700 text-xs font-black">
                                HEMAT {{ $produk->diskon_persen }}%
                            </span>
                        @elseif(isset($produk->harga_setelah_diskon) && $produk->harga_setelah_diskon < $produk->harga)
                            <span class="text-3xl sm:text-4xl font-extrabold text-indigo-600 font-display">
                                Rp{{ number_format($produk->harga_setelah_diskon, 0, ',', '.') }}
                            </span>
                            <span class="text-base text-slate-400 line-through font-semibold">
                                Rp{{ number_format($produk->harga, 0, ',', '.') }}
                            </span>
                        @else
                            <span class="text-3xl sm:text-4xl font-extrabold text-indigo-600 font-display">
                                Rp{{ number_format($produk->harga, 0, ',', '.') }}
                            </span>
                        @endif
                    </div>
                    <div class="mt-2 text-xs text-slate-500 font-medium">
                        <i class="fas fa-weight-scale text-slate-400 mr-1"></i> Berat Bersih: <strong>{{ ($produk->berat_gram ?? 1000) / 1000 }} Kg</strong> / Kemasan
                    </div>
                </div>

                <!-- Short Description -->
                <div>
                    <h3 class="text-xs uppercase tracking-wider font-bold text-slate-400 mb-2">Deskripsi Produk</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">
                        {{ $produk->deskripsi }}
                    </p>
                </div>

                <!-- 🏖️ Store Vacation Mode Alert & Checkout Lock (Feature 3) -->
                @if($produk->umkm && $produk->umkm->is_libur)
                    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 space-y-1.5">
                        <div class="flex items-center gap-2 font-bold text-sm text-amber-800">
                            <i class="fas fa-umbrella-beach text-amber-600 text-base"></i>
                            <span>Toko Sedang Tutup / Libur Panen</span>
                        </div>
                        <p class="text-xs text-amber-700 leading-relaxed">
                            {{ $produk->umkm->libur_pesan ?: 'Kebun / Toko sedang libur sementara waktu untuk persiapan panen berikutnya.' }}
                        </p>
                        @if($produk->umkm->libur_sampai)
                            <div class="text-[11px] font-bold text-amber-800 pt-1">
                                <i class="fas fa-calendar-check mr-1"></i> Buka Kembali: {{ \Carbon\Carbon::parse($produk->umkm->libur_sampai)->format('d F Y') }}
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Quantity & Purchase Action Buttons -->
                <div class="pt-4 border-t border-slate-100 space-y-4">
                    
                    @if($produk->umkm && $produk->umkm->is_libur)
                        <div class="p-4 rounded-xl bg-slate-100 border border-slate-200 text-center text-slate-500 text-xs font-bold">
                            <i class="fas fa-lock mr-1.5"></i> Pemesanan ditutup sementara karena toko sedang dalam mode libur.
                        </div>
                    @else
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
                                                Rp{{ number_format($produk->harga, 0, ',', '.') }}
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
                    @endif

                </div>

                <!-- Seller Profile Card -->
                <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-sm flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold text-lg">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <span class="text-[10px] uppercase font-bold text-slate-400 block">Penjual Terverifikasi</span>
                            <h4 class="font-bold text-slate-800 text-sm">{{ $produk->umkm->nama_toko ?? 'Kebun Mitra' }}</h4>
                            <p class="text-xs text-slate-500">{{ $produk->umkm->alamat ?? 'Indramayu, Jawa Barat' }}</p>
                        </div>
                    </div>
                    @if(Auth::id() !== ($produk->umkm->user_id ?? null))
                        <a href="{{ route('chat.index', ['umkm_id' => $produk->umkm_id]) }}" class="px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold text-xs rounded-xl transition flex items-center gap-1.5">
                            <i class="fas fa-comment-dots"></i> Chat Penjual
                        </a>
                    @endif
                </div>

            </div>
        </div>

        <!-- Ulasan Produk Section (Feature 5: Respon Balasan Toko) -->
        <section class="py-12 border-t border-slate-200">
            <div class="flex items-center justify-between mb-8">
                <div>
                    <p class="text-xs uppercase tracking-wider font-bold text-indigo-600 mb-1">Ulasan Pembeli</p>
                    <h2 class="text-2xl font-extrabold text-brand-slate tracking-tight">Kepuasan Pelanggan</h2>
                </div>
                <div class="flex items-center gap-2">
                    <div class="flex text-amber-400 text-sm">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star"></i>
                        @endfor
                    </div>
                    <span class="font-bold text-slate-800 text-sm">{{ $produk->rating > 0 ? number_format($produk->rating, 1) : '5.0' }} / 5.0</span>
                </div>
            </div>

            @if($ulasan->isEmpty())
                <div class="p-8 text-center bg-white rounded-2xl border border-slate-200/80">
                    <i class="fas fa-comments text-3xl text-slate-300 mb-2"></i>
                    <p class="text-xs text-slate-500">Belum ada ulasan untuk produk ini.</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @foreach($ulasan as $review)
                        <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-indigo-50 text-indigo-600 font-bold flex items-center justify-center text-xs">
                                        {{ substr($review->user->name ?? 'User', 0, 1) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-slate-800 text-xs">{{ $review->user->name ?? 'Pembeli' }}</h4>
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

                            <!-- 💬 Official Seller Reply Bubble (Feature 5) -->
                            @if($review->balasan_penjual)
                                <div class="mt-3 p-3.5 bg-emerald-50/90 rounded-xl border border-emerald-200/70 text-xs text-slate-700 space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-1.5 py-0.5 rounded bg-emerald-600 text-white font-black text-[9px] uppercase tracking-wider">
                                            Respon Toko
                                        </span>
                                        <span class="font-bold text-emerald-950 text-xs">{{ $produk->umkm->nama_toko ?? 'Penjual' }}</span>
                                        <span class="text-[10px] text-slate-400">• {{ $review->balasan_penjual_at ? \Carbon\Carbon::parse($review->balasan_penjual_at)->diffForHumans() : 'baru saja' }}</span>
                                    </div>
                                    <p class="text-slate-700 leading-relaxed pl-1">
                                        {{ $review->balasan_penjual }}
                                    </p>
                                </div>
                            @endif
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
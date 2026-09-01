@extends('layouts.app')

@section('page_title', 'Katalog Produk Toko')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Katalog Produk Toko</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola stok komoditas panen, harga promo diskon coret, dan status tayang secara real-time</p>
        </div>
        <a href="{{ route('penjual.produk.create') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-sm hover:shadow">
            <i class="fas fa-plus"></i>
            Tambah Komoditas Baru
        </a>
    </div>

    @if (session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
        <p class="font-bold text-xs">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Content Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
        
        <!-- Controls: Search & Filter Summary -->
        <div class="p-4 sm:p-5 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-3 bg-slate-50/50">
            <div class="text-xs font-bold text-slate-500 flex items-center gap-2">
                <span>Total: <strong class="text-slate-900">{{ $produks->total() }}</strong> Komoditas</span>
                <span class="w-1 h-1 rounded-full bg-slate-300"></span>
                <span class="text-emerald-600"><i class="fas fa-circle-check"></i> Real-time Stock Sync</span>
            </div>
            
            <div class="relative w-full sm:w-64">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    id="searchProdukInput" 
                    placeholder="Cari nama komoditas..." 
                    class="w-full pl-9 pr-3.5 py-1.5 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none bg-white font-medium"
                >
            </div>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" id="produkGrid">
                @forelse($produks as $produk)
                    <div class="produk-card group bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition duration-300 hover:border-brand-300 flex flex-col h-full relative" data-title="{{ strtolower($produk->nama) }}">
                        
                        <!-- Image Container & Badges -->
                        <div class="relative aspect-video sm:aspect-square overflow-hidden bg-slate-100">
                            @if ($produk->gambar)
                                <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $produk->nama }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            @endif

                            <!-- Discount Badge -->
                            @if ($produk->harga_coret && $produk->harga_coret > $produk->harga)
                                <div class="absolute top-3 left-3 bg-rose-600 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow-sm">
                                    <i class="fas fa-fire mr-1"></i>HEMAT {{ $produk->diskon_persen }}%
                                </div>
                            @elseif ($produk->diskon && now()->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir))
                                <div class="absolute top-3 left-3 bg-rose-600 text-white text-[10px] font-black px-2.5 py-1 rounded-lg shadow-sm">
                                    <i class="fas fa-tag mr-1"></i>{{ $produk->diskon->persen_diskon }}% OFF
                                </div>
                            @endif

                            <!-- Status Active/Inactive Toggle Pill -->
                            <button 
                                type="button" 
                                onclick="toggleProductStatus({{ $produk->id }}, this)" 
                                id="statusBtn-{{ $produk->id }}"
                                class="absolute top-3 right-3 px-2.5 py-1 rounded-lg text-[10px] font-extrabold shadow-sm transition backdrop-blur-md flex items-center gap-1.5 {{ $produk->is_active ? 'bg-emerald-500/90 text-white hover:bg-emerald-600' : 'bg-slate-700/90 text-slate-200 hover:bg-slate-800' }}"
                                title="Klik untuk ubah status tayang"
                            >
                                <span class="w-1.5 h-1.5 rounded-full {{ $produk->is_active ? 'bg-white animate-pulse' : 'bg-rose-400' }}"></span>
                                <span class="status-label">{{ $produk->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </button>
                        </div>

                        <div class="p-4 flex-1 flex flex-col">
                            <!-- Category & Weight -->
                            <div class="flex items-center justify-between text-[10px] font-bold text-slate-400 mb-1">
                                <span>{{ $produk->kategori->nama ?? 'Komoditas Buah' }}</span>
                                <span><i class="fas fa-scale-balanced text-slate-300"></i> {{ ($produk->berat_gram ?? 1000) / 1000 }} Kg / Pack</span>
                            </div>

                            <h3 class="font-bold text-slate-900 text-sm mb-2 line-clamp-2 leading-snug">{{ $produk->nama }}</h3>
                            
                            <!-- Price & Discount Preview -->
                            <div class="mb-3">
                                @if ($produk->harga_coret && $produk->harga_coret > $produk->harga)
                                    <div class="text-[11px] text-slate-400 line-through mb-0.5">
                                        Rp {{ number_format($produk->harga_coret, 0, ',', '.') }}
                                    </div>
                                    <div class="text-base font-extrabold text-rose-600 font-display">
                                        Rp {{ number_format($produk->harga, 0, ',', '.') }}
                                    </div>
                                @elseif ($produk->diskon && now()->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir))
                                    <div class="text-[11px] text-slate-400 line-through mb-0.5">
                                        Rp {{ number_format($produk->harga, 0, ',', '.') }}
                                    </div>
                                    <div class="text-base font-extrabold text-rose-600 font-display">
                                        Rp {{ number_format($produk->harga_setelah_diskon, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-base font-extrabold text-brand-700 font-display">
                                        Rp {{ number_format($produk->harga, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                            <!-- ⚡ QUICK STOCK ADJUSTER SECTION -->
                            <div class="bg-slate-50 p-2.5 rounded-xl border border-slate-100 mb-3 mt-auto">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider">Stok Panen:</span>
                                    <span 
                                        id="stockBadge-{{ $produk->id }}"
                                        class="px-2 py-0.5 rounded text-[10px] font-black {{ $produk->stok < 5 ? 'bg-amber-100 text-amber-800 animate-pulse' : 'bg-emerald-100 text-emerald-800' }}"
                                    >
                                        {{ $produk->stok < 5 ? '⚠️ Menipis' : 'Tersedia' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between gap-2">
                                    <button 
                                        type="button" 
                                        onclick="adjustStock({{ $produk->id }}, -1)" 
                                        class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-300 text-slate-700 font-black text-xs transition flex items-center justify-center shadow-2xs"
                                        title="Kurangi 1 Kg"
                                    >
                                        -
                                    </button>
                                    <span id="stockValue-{{ $produk->id }}" class="font-extrabold text-slate-900 text-xs font-mono">
                                        {{ $produk->stok }} Kg
                                    </span>
                                    <button 
                                        type="button" 
                                        onclick="adjustStock({{ $produk->id }}, 1)" 
                                        class="w-7 h-7 rounded-lg bg-white border border-slate-200 hover:bg-emerald-50 hover:text-emerald-600 hover:border-emerald-300 text-slate-700 font-black text-xs transition flex items-center justify-center shadow-2xs"
                                        title="Tambah 1 Kg"
                                    >
                                        +
                                    </button>
                                    <button 
                                        type="button" 
                                        onclick="adjustStock({{ $produk->id }}, 5)" 
                                        class="px-2 py-1 rounded-lg bg-white border border-slate-200 hover:bg-brand-50 hover:text-brand-600 text-slate-600 font-bold text-[10px] transition shadow-2xs"
                                        title="Tambah 5 Kg"
                                    >
                                        +5
                                    </button>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center gap-1.5 pt-2 border-t border-slate-100">
                                <a href="{{ route('penjual.produk.show', $produk->id) }}" class="flex-1 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-700 text-center rounded-lg text-xs font-bold border border-slate-200 transition" title="Lihat Pratinjau">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('penjual.produk.edit', $produk->id) }}" class="flex-1 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 text-center rounded-lg text-xs font-bold border border-amber-200 transition" title="Edit Komoditas">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('penjual.produk.destroy', $produk->id) }}" method="POST" class="flex-1 inline-flex" onsubmit="return confirm('Yakin mau hapus produk {{ $produk->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-lg text-xs font-bold border border-rose-200 transition" title="Hapus Produk">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-box-open text-3xl text-slate-300"></i>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 mb-1">Belum Ada Komoditas</h3>
                        <p class="text-slate-500 text-xs mb-6 max-w-sm">Mulai jualan dengan menambahkan komoditas mangga pertama Anda di etalase toko ini!</p>
                        <a href="{{ route('penjual.produk.create') }}" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-sm">
                            <i class="fas fa-plus mr-1.5"></i> Tambah Komoditas Pertama
                        </a>
                    </div>
                @endforelse
            </div>

            @if($produks->hasPages())
                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-center">
                    {{ $produks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Quick Toast Feedback Notification -->
<div id="quickToast" class="fixed bottom-6 right-6 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
    <div class="px-4 py-3 rounded-2xl bg-slate-900 text-white shadow-2xl flex items-center gap-3 text-xs font-bold border border-slate-800">
        <i class="fas fa-check-circle text-emerald-400 text-sm" id="toastIcon"></i>
        <span id="toastMessage">Perubahan berhasil disimpan</span>
    </div>
</div>

<script>
    // Live Search Filter
    document.getElementById('searchProdukInput')?.addEventListener('input', function(e) {
        const query = e.target.value.toLowerCase();
        const cards = document.querySelectorAll('.produk-card');
        cards.forEach(card => {
            const title = card.getAttribute('data-title') || '';
            if (title.includes(query)) {
                card.style.display = 'flex';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Toast Function
    function showToast(msg, isSuccess = true) {
        const toast = document.getElementById('quickToast');
        const text = document.getElementById('toastMessage');
        const icon = document.getElementById('toastIcon');
        if (!toast || !text || !icon) return;

        text.textContent = msg;
        icon.className = isSuccess ? 'fas fa-check-circle text-emerald-400 text-sm' : 'fas fa-triangle-exclamation text-rose-400 text-sm';

        toast.classList.remove('translate-y-20', 'opacity-0');
        setTimeout(() => {
            toast.classList.add('translate-y-20', 'opacity-0');
        }, 2500);
    }

    // ⚡ AJAX Quick Stock Adjuster
    function adjustStock(produkId, change) {
        fetch(`/penjual/produk/${produkId}/quick-stock`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ change: change })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const stockVal = document.getElementById(`stockValue-${produkId}`);
                const stockBadge = document.getElementById(`stockBadge-${produkId}`);
                
                if (stockVal) stockVal.textContent = `${data.new_stock} Kg`;
                if (stockBadge) {
                    if (data.is_low) {
                        stockBadge.className = 'px-2 py-0.5 rounded text-[10px] font-black bg-amber-100 text-amber-800 animate-pulse';
                        stockBadge.textContent = '⚠️ Menipis';
                    } else {
                        stockBadge.className = 'px-2 py-0.5 rounded text-[10px] font-black bg-emerald-100 text-emerald-800';
                        stockBadge.textContent = 'Tersedia';
                    }
                }
                showToast(data.message);
            }
        })
        .catch(() => showToast('Gagal mengubah stok komoditas.', false));
    }

    // ⚡ AJAX Toggle Status
    function toggleProductStatus(produkId, btn) {
        fetch(`/penjual/produk/${produkId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const label = btn.querySelector('.status-label');
                const dot = btn.querySelector('span:first-child');
                
                if (data.is_active) {
                    btn.className = 'absolute top-3 right-3 px-2.5 py-1 rounded-lg text-[10px] font-extrabold shadow-sm transition backdrop-blur-md flex items-center gap-1.5 bg-emerald-500/90 text-white hover:bg-emerald-600';
                    if (label) label.textContent = 'Aktif';
                    if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-white animate-pulse';
                } else {
                    btn.className = 'absolute top-3 right-3 px-2.5 py-1 rounded-lg text-[10px] font-extrabold shadow-sm transition backdrop-blur-md flex items-center gap-1.5 bg-slate-700/90 text-slate-200 hover:bg-slate-800';
                    if (label) label.textContent = 'Nonaktif';
                    if (dot) dot.className = 'w-1.5 h-1.5 rounded-full bg-rose-400';
                }
                showToast(data.message);
            }
        })
        .catch(() => showToast('Gagal mengubah status komoditas.', false));
    }
</script>
@endsection

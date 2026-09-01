@extends('layouts.public')

@section('title', 'Beranda Belanja — Juragan Pelem')
@section('meta_description', 'Pusat belanja online mangga segar langsung dari petani Indramayu dan produk olahan UMKM lokal pilihan.')

@section('content')
<!-- Main Buyer Hub -->
<main class="py-8 bg-slate-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">

        <!-- 1. Welcome Hero & Quick Order Hub -->
        <div class="bg-gradient-to-br from-indigo-700 via-indigo-600 to-emerald-700 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden">
            <!-- Background Decorative Blobs -->
            <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
            <div class="absolute right-20 -top-10 w-48 h-48 bg-amber-400/20 rounded-full blur-xl pointer-events-none"></div>

            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 backdrop-blur-md text-amber-300 text-xs font-bold mb-3 border border-white/10">
                        <i class="fas fa-sparkles text-[10px]"></i> Portal Pembeli Resmi
                    </div>
                    <h1 class="text-2xl sm:text-3xl lg:text-4xl font-black font-display tracking-tight text-white">
                        Selamat datang kembali, {{ Auth::user()->name }}! 🥭
                    </h1>
                    <p class="text-indigo-100 text-sm mt-1.5 max-w-xl leading-relaxed">
                        Nikmati mangga manis matang pohon asli Indramayu & dukung UMKM agrobisnis lokal dengan garansi segar 100%.
                    </p>
                </div>

                <!-- Quick Order Status Badges -->
                <div class="grid grid-cols-3 gap-2.5 sm:gap-3 bg-white/10 backdrop-blur-md p-3 rounded-2xl border border-white/15 shrink-0">
                    <a href="{{ route('pembeli.pesanan.status.dikemas') }}" class="p-3 bg-white/10 hover:bg-white/20 rounded-xl text-center transition group">
                        <span class="block text-xl font-extrabold text-white">{{ $orderStats['dikemas'] ?? 0 }}</span>
                        <span class="text-[11px] font-medium text-indigo-100 flex items-center justify-center gap-1 mt-0.5">
                            <i class="fas fa-box text-amber-300 text-[10px]"></i> Dikemas
                        </span>
                    </a>
                    <a href="{{ route('pembeli.pesanan.dikirim') }}" class="p-3 bg-white/10 hover:bg-white/20 rounded-xl text-center transition group relative">
                        <span class="block text-xl font-extrabold text-white">{{ $orderStats['dikirim'] ?? 0 }}</span>
                        <span class="text-[11px] font-medium text-indigo-100 flex items-center justify-center gap-1 mt-0.5">
                            <i class="fas fa-truck-fast text-emerald-300 text-[10px]"></i> Dikirim
                        </span>
                    </a>
                    <a href="{{ route('pembeli.pesanan.index') }}" class="p-3 bg-white/10 hover:bg-white/20 rounded-xl text-center transition group">
                        <span class="block text-xl font-extrabold text-white">{{ $orderStats['diterima'] ?? 0 }}</span>
                        <span class="text-[11px] font-medium text-indigo-100 flex items-center justify-center gap-1 mt-0.5">
                            <i class="fas fa-check-circle text-teal-300 text-[10px]"></i> Selesai
                        </span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 2. Penawaran Spesial Panen (Diskon Unggulan Pekan Ini) -->
        @if(isset($diskonProduks) && $diskonProduks->isNotEmpty())
            <section class="space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full bg-rose-100 text-rose-700 text-xs font-black uppercase tracking-wider">
                                <i class="fas fa-bolt mr-1"></i> Flash Sale Panen
                            </span>
                            <span class="text-xs text-slate-400 font-semibold">• Penawaran Terbatas</span>
                        </div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-slate-900 font-display mt-1">
                            Diskon Unggulan Pekan Ini
                        </h2>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
                    @foreach($diskonProduks as $item)
                        <div class="bento-card group flex flex-col overflow-hidden bg-white relative border border-slate-200/80 shadow-sm hover:shadow-md transition">
                            <!-- Discount Badge -->
                            <div class="absolute top-3 left-3 z-10 bg-rose-600 text-white text-xs font-black px-2.5 py-1 rounded-lg shadow-md flex items-center gap-1">
                                -{{ $item->diskon->persen_diskon }}%
                            </div>

                            <!-- Product Image -->
                            <div class="relative aspect-square overflow-hidden bg-slate-100">
                                <img 
                                    src="{{ $item->gambar ? asset('storage/' . $item->gambar) : asset('images/default.jpg') }}" 
                                    alt="{{ $item->nama }}" 
                                    class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                >
                            </div>

                            <!-- Info -->
                            <div class="p-4 flex flex-col flex-1">
                                <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 mb-1">
                                    {{ $item->umkm->nama_toko ?? 'Petani Indramayu' }}
                                </span>
                                <h3 class="font-bold text-slate-800 text-sm mb-2 line-clamp-1 group-hover:text-indigo-600 transition">
                                    <a href="{{ route('pembeli.produk.show', $item->id) }}">{{ $item->nama }}</a>
                                </h3>

                                <div class="mt-auto pt-2 border-t border-slate-100 flex items-baseline justify-between gap-2">
                                    <div>
                                        <div class="text-base font-black text-rose-600 font-display">
                                            Rp{{ number_format($item->harga_setelah_diskon, 0, ',', '.') }}
                                        </div>
                                        <div class="text-xs text-slate-400 line-through font-semibold">
                                            Rp{{ number_format($item->harga, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    <a href="{{ route('pembeli.produk.show', $item->id) }}" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white rounded-xl text-xs font-bold transition flex items-center gap-1">
                                        Beli <i class="fas fa-arrow-right text-[10px]"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 3. Katalog Produk Terpadu (Consistent with /kategori) -->
        <section class="space-y-6">
            <!-- Header & In-Catalog Search -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 pt-4 border-t border-slate-200/60">
                <div>
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight font-display">
                        Katalog Produk
                    </h2>
                    <p class="text-slate-500 text-sm mt-0.5">
                        Jelajahi seluruh komoditas mangga segar & olahan UMKM Indramayu.
                    </p>
                </div>

                <!-- Realtime Search Input -->
                <div class="w-full md:w-80 relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" 
                        id="catalog-search-input" 
                        value="{{ request('search') }}" 
                        placeholder="Cari langsung di katalog..." 
                        class="w-full bg-white text-sm text-slate-800 pl-10 pr-9 py-2.5 rounded-2xl border border-slate-200 shadow-sm focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 outline-none transition">
                    
                    <button type="button" 
                        id="catalog-search-clear" 
                        class="{{ request('search') ? '' : 'hidden' }} absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </div>
            </div>

            <!-- Two-Column Layout: Sidebar Filter + Product Grid -->
            <div class="flex flex-col lg:flex-row gap-8">
                
                <!-- Left Sidebar Filter -->
                <aside class="w-full lg:w-64 shrink-0">
                    <form id="filter-form" action="{{ route('pembeli.dashboard') }}" method="GET" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-5 sticky top-24">
                        
                        <!-- Hidden fields for search and sort -->
                        <input type="hidden" name="search" id="filter-search" value="{{ request('search') }}">
                        <input type="hidden" name="sort" id="filter-sort" value="{{ request('sort', 'terbaru') }}">
                        
                        <!-- Filter Kategori & Subkategori -->
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center justify-between">
                                Kategori
                                <i class="fas fa-layer-group text-indigo-500 text-xs"></i>
                            </h3>
                            <div class="space-y-3 max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                @foreach($kategoris as $kategori)
                                    <label class="flex items-start gap-3 cursor-pointer group">
                                        <div class="relative flex items-center mt-0.5">
                                            <input type="checkbox" name="kategori[]" value="{{ $kategori->id }}" 
                                                class="category-checkbox peer appearance-none w-4 h-4 border-2 border-slate-300 rounded bg-white checked:bg-indigo-600 checked:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 transition-all cursor-pointer"
                                                {{ in_array($kategori->id, (array)request('kategori', [])) ? 'checked' : '' }}>
                                            <i class="fas fa-check absolute text-[10px] text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none transition-opacity"></i>
                                        </div>
                                        <span class="text-sm text-slate-600 group-hover:text-indigo-600 transition-colors flex-1 font-medium">{{ $kategori->nama }}</span>
                                        <span class="text-xs text-slate-400">({{ $kategori->produks->count() + $kategori->subkategoris->pluck('produks')->flatten()->count() }})</span>
                                    </label>
                                    
                                    @if($kategori->subkategoris->count())
                                        <div class="ml-7 space-y-2 mt-2">
                                            @foreach($kategori->subkategoris as $sub)
                                                <label class="flex items-start gap-3 cursor-pointer group">
                                                    <div class="relative flex items-center mt-0.5">
                                                        <input type="checkbox" name="kategori[]" value="{{ $sub->id }}" 
                                                            class="category-checkbox peer appearance-none w-4 h-4 border-2 border-slate-300 rounded bg-white checked:bg-indigo-600 checked:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 transition-all cursor-pointer"
                                                            {{ in_array($sub->id, (array)request('kategori', [])) ? 'checked' : '' }}>
                                                        <i class="fas fa-check absolute text-[10px] text-white opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 pointer-events-none transition-opacity"></i>
                                                    </div>
                                                    <span class="text-sm text-slate-600 group-hover:text-indigo-600 transition-colors flex-1">{{ $sub->nama }}</span>
                                                    <span class="text-xs text-slate-400">({{ $sub->produks->count() }})</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>

                        <hr class="border-slate-100 my-5">

                        <!-- Filter Harga -->
                        <div class="mb-6">
                            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 flex items-center justify-between">
                                Rentang Harga
                                <i class="fas fa-tags text-indigo-500 text-xs"></i>
                            </h3>
                            <div class="space-y-3">
                                @php
                                    $priceRanges = [
                                        '' => 'Semua Harga',
                                        '0-50000' => 'Di bawah Rp50.000',
                                        '50000-100000' => 'Rp50.000 - Rp100.000',
                                        '100000-250000' => 'Rp100.000 - Rp250.000',
                                        '250000-500000' => 'Rp250.000 - Rp500.000',
                                        '500000-' => 'Di atas Rp500.000',
                                    ];
                                    $currentMin = request('min_harga', '');
                                    $currentMax = request('max_harga', '');
                                    $currentRange = $currentMin . ($currentMax ? '-' . $currentMax : ($currentMin ? '-' : ''));
                                @endphp
                                
                                @foreach($priceRanges as $val => $label)
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <div class="relative flex items-center">
                                            <input type="radio" name="price_range" value="{{ $val }}" 
                                                class="price-radio peer appearance-none w-4 h-4 border-2 border-slate-300 rounded-full bg-white checked:border-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-600/20 transition-all cursor-pointer"
                                                {{ $currentRange == $val || ($val == '' && $currentRange == '') ? 'checked' : '' }}>
                                            <div class="absolute w-2 h-2 bg-indigo-600 rounded-full opacity-0 peer-checked:opacity-100 top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 transition-opacity pointer-events-none"></div>
                                        </div>
                                        <span class="text-sm text-slate-600 group-hover:text-indigo-600 transition-colors font-medium">{{ $label }}</span>
                                    </label>
                                @endforeach
                                
                                <input type="hidden" name="min_harga" id="min_harga" value="{{ request('min_harga') }}">
                                <input type="hidden" name="max_harga" id="max_harga" value="{{ request('max_harga') }}">
                            </div>
                        </div>

                        <div class="pt-2">
                            <button type="button" onclick="resetAllFilters()" class="w-full flex items-center justify-center gap-2 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold rounded-xl text-xs transition-colors">
                                <i class="fas fa-undo"></i> RESET SEMUA FILTER
                            </button>
                        </div>
                    </form>
                </aside>

                <!-- Right Main Content -->
                <div class="flex-1">
                    
                    <!-- Toolbar Sorting & Results Counter -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-3 mb-6 flex flex-col sm:flex-row items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-slate-500 font-medium">Urutkan:</span>
                            <select id="sort-select" onchange="handleSortChange(this.value)" class="text-sm border-0 bg-slate-50 text-slate-700 font-semibold rounded-xl focus:ring-2 focus:ring-indigo-500 cursor-pointer py-1.5 pl-3 pr-8 shadow-sm">
                                <option value="terbaru" {{ request('sort') == 'terbaru' || !request('sort') ? 'selected' : '' }}>Terbaru</option>
                                <option value="termurah" {{ request('sort') == 'termurah' ? 'selected' : '' }}>Harga Termurah</option>
                                <option value="termahal" {{ request('sort') == 'termahal' ? 'selected' : '' }}>Harga Termahal</option>
                            </select>
                        </div>
                        
                        <div class="flex items-center gap-4">
                            <span id="results-count-text" class="text-sm text-slate-500 font-medium">
                                Menampilkan {{ $produks->firstItem() ?? 0 }}-{{ $produks->lastItem() ?? 0 }} dari {{ $produks->total() }} produk
                            </span>
                            
                            <div id="catalog-loading-indicator" class="hidden text-indigo-600 flex items-center gap-1.5 text-xs font-semibold">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>Memuat...</span>
                            </div>
                        </div>
                    </div>

                    <!-- Product Grid Container (Live Updated via AJAX) -->
                    <div id="product-grid-container" class="transition-opacity duration-200">
                        @include('partials.product_grid', ['produks' => $produks])
                    </div>
                    
                </div>
            </div>
        </section>

    </div>
</main>

@push('scripts')
<script>
    let filterDebounceTimer = null;

    function buildFilterQuery() {
        const form = document.getElementById('filter-form');
        const formData = new FormData(form);
        const params = new URLSearchParams();

        // Categories
        const checkedCategories = document.querySelectorAll('.category-checkbox:checked');
        checkedCategories.forEach(cb => {
            params.append('kategori[]', cb.value);
        });

        // Price
        const minHarga = document.getElementById('min_harga').value;
        const maxHarga = document.getElementById('max_harga').value;
        if (minHarga) params.set('min_harga', minHarga);
        if (maxHarga) params.set('max_harga', maxHarga);

        // Search
        const searchVal = document.getElementById('catalog-search-input').value.trim();
        if (searchVal) params.set('search', searchVal);

        // Sort
        const sortVal = document.getElementById('sort-select').value;
        if (sortVal) params.set('sort', sortVal);

        return params;
    }

    function applyFiltersRealtime(updateUrl = true) {
        const params = buildFilterQuery();
        const url = `{{ route('pembeli.dashboard') }}?${params.toString()}`;
        const container = document.getElementById('product-grid-container');
        const loadingIndicator = document.getElementById('catalog-loading-indicator');
        const countText = document.getElementById('results-count-text');

        if (container) container.style.opacity = '0.5';
        if (loadingIndicator) loadingIndicator.classList.remove('hidden');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (container) {
                container.innerHTML = data.html;
                container.style.opacity = '1';
            }
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            if (countText) {
                countText.textContent = `Menampilkan ${data.from}-${data.to} dari ${data.total} produk`;
            }

            if (updateUrl) {
                history.pushState(null, '', url);
            }
        })
        .catch(err => {
            if (container) container.style.opacity = '1';
            if (loadingIndicator) loadingIndicator.classList.add('hidden');
            console.error('Filter fetch error:', err);
        });
    }

    // Realtime in-catalog search input
    const catalogSearchInput = document.getElementById('catalog-search-input');
    const catalogSearchClear = document.getElementById('catalog-search-clear');

    if (catalogSearchInput) {
        catalogSearchInput.addEventListener('input', function () {
            const val = this.value.trim();
            if (catalogSearchClear) {
                if (val) catalogSearchClear.classList.remove('hidden');
                else catalogSearchClear.classList.add('hidden');
            }

            document.getElementById('filter-search').value = val;

            clearTimeout(filterDebounceTimer);
            filterDebounceTimer = setTimeout(() => {
                applyFiltersRealtime();
            }, 300);
        });
    }

    if (catalogSearchClear) {
        catalogSearchClear.addEventListener('click', function () {
            catalogSearchInput.value = '';
            document.getElementById('filter-search').value = '';
            this.classList.add('hidden');
            applyFiltersRealtime();
        });
    }

    // Category checkboxes change
    document.querySelectorAll('.category-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            applyFiltersRealtime();
        });
    });

    // Price range radio change
    document.querySelectorAll('.price-radio').forEach(radio => {
        radio.addEventListener('change', function () {
            const val = this.value;
            const minInput = document.getElementById('min_harga');
            const maxInput = document.getElementById('max_harga');

            if (val === '') {
                minInput.value = '';
                maxInput.value = '';
            } else if (val.endsWith('-')) {
                minInput.value = val.replace('-', '');
                maxInput.value = '';
            } else {
                const parts = val.split('-');
                minInput.value = parts[0];
                maxInput.value = parts[1];
            }

            applyFiltersRealtime();
        });
    });

    // Sort select change
    function handleSortChange(sortVal) {
        document.getElementById('filter-sort').value = sortVal;
        applyFiltersRealtime();
    }

    // Reset All Filters
    function resetAllFilters() {
        document.querySelectorAll('.category-checkbox').forEach(cb => cb.checked = false);
        
        const allPriceRadio = document.querySelector('.price-radio[value=""]');
        if (allPriceRadio) allPriceRadio.checked = true;
        
        document.getElementById('min_harga').value = '';
        document.getElementById('max_harga').value = '';
        
        if (catalogSearchInput) {
            catalogSearchInput.value = '';
            if (catalogSearchClear) catalogSearchClear.classList.add('hidden');
        }
        document.getElementById('filter-search').value = '';
        
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) sortSelect.value = 'terbaru';
        document.getElementById('filter-sort').value = 'terbaru';

        applyFiltersRealtime();
    }

    // Handle AJAX Pagination clicks
    document.addEventListener('click', function (e) {
        const paginationLink = e.target.closest('.ajax-pagination a');
        if (paginationLink) {
            e.preventDefault();
            const targetUrl = paginationLink.getAttribute('href');
            if (targetUrl) {
                const container = document.getElementById('product-grid-container');
                const loadingIndicator = document.getElementById('catalog-loading-indicator');
                const countText = document.getElementById('results-count-text');

                if (container) container.style.opacity = '0.5';
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');

                fetch(targetUrl, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    if (container) {
                        container.innerHTML = data.html;
                        container.style.opacity = '1';
                    }
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    if (countText) {
                        countText.textContent = `Menampilkan ${data.from}-${data.to} dari ${data.total} produk`;
                    }
                    history.pushState(null, '', targetUrl);
                    
                    // Scroll smooth up to catalog header
                    document.querySelector('#product-grid-container').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                })
                .catch(err => {
                    if (container) container.style.opacity = '1';
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    console.error('Pagination fetch error:', err);
                });
            }
        }
    });

    // Handle browser back/forward buttons
    window.addEventListener('popstate', function () {
        location.reload();
    });
</script>
@endpush
@endsection
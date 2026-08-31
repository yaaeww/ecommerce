@extends('layouts.public')

@section('title', 'Kategori Produk — Juragan Pelem')
@section('meta_description', 'Jelajahi seluruh kategori produk mangga segar, olahan makanan, minuman sirup, hingga kerajinan UMKM Indramayu.')

@section('content')
<!-- Content Section -->
<main class="py-10 bg-slate-50/50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Breadcrumb & Header with Live Search -->
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Katalog Produk</h1>
                <p class="text-slate-500 mt-1 text-sm">Temukan berbagai produk unggulan UMKM Indramayu secara instan.</p>
            </div>
            
            <!-- Realtime Search Input in Catalog Page -->
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

        <div class="flex flex-col lg:flex-row gap-8">
            
            <!-- Sidebar Filter -->
            <aside class="w-full lg:w-64 shrink-0">
                <form id="filter-form" action="{{ route('kategori') }}" method="GET" class="bg-white rounded-2xl shadow-sm border border-slate-200/60 p-5 sticky top-24">
                    
                    <!-- Hidden fields for search and sort -->
                    <input type="hidden" name="search" id="filter-search" value="{{ request('search') }}">
                    <input type="hidden" name="sort" id="filter-sort" value="{{ request('sort', 'terbaru') }}">
                    
                    <!-- Filter Kategori -->
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
                                            {{ in_array($kategori->id, request('kategori', [])) ? 'checked' : '' }}>
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
                                                        {{ in_array($sub->id, request('kategori', [])) ? 'checked' : '' }}>
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

            <!-- Main Content -->
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
        const url = `{{ route('kategori') }}?${params.toString()}`;
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
            }, 250);
        });
    }

    if (catalogSearchClear) {
        catalogSearchClear.addEventListener('click', function () {
            catalogSearchInput.value = '';
            catalogSearchClear.classList.add('hidden');
            document.getElementById('filter-search').value = '';
            applyFiltersRealtime();
        });
    }

    // Category Checkboxes
    document.querySelectorAll('.category-checkbox').forEach(cb => {
        cb.addEventListener('change', function () {
            applyFiltersRealtime();
        });
    });

    // Price Radios
    document.querySelectorAll('.price-radio').forEach(rb => {
        rb.addEventListener('change', function () {
            const val = this.value;
            if (!val) {
                document.getElementById('min_harga').value = '';
                document.getElementById('max_harga').value = '';
            } else {
                const parts = val.split('-');
                document.getElementById('min_harga').value = parts[0] || '';
                document.getElementById('max_harga').value = parts[1] || '';
            }
            applyFiltersRealtime();
        });
    });

    // Sort Dropdown
    function handleSortChange(val) {
        document.getElementById('filter-sort').value = val;
        applyFiltersRealtime();
    }

    // Reset All Filters
    function resetAllFilters() {
        document.querySelectorAll('.category-checkbox').forEach(cb => cb.checked = false);
        const allPriceRadio = document.querySelector('.price-radio[value=""]');
        if (allPriceRadio) allPriceRadio.checked = true;
        document.getElementById('min_harga').value = '';
        document.getElementById('max_harga').value = '';
        if (catalogSearchInput) catalogSearchInput.value = '';
        if (catalogSearchClear) catalogSearchClear.classList.add('hidden');
        document.getElementById('filter-search').value = '';
        document.getElementById('sort-select').value = 'terbaru';
        document.getElementById('filter-sort').value = 'terbaru';
        applyFiltersRealtime();
    }

    // Handle AJAX pagination links click
    document.addEventListener('click', function (e) {
        const link = e.target.closest('.ajax-pagination a');
        if (link) {
            e.preventDefault();
            const href = link.getAttribute('href');
            if (href) {
                const container = document.getElementById('product-grid-container');
                const loadingIndicator = document.getElementById('catalog-loading-indicator');
                const countText = document.getElementById('results-count-text');

                if (container) container.style.opacity = '0.5';
                if (loadingIndicator) loadingIndicator.classList.remove('hidden');

                fetch(href, {
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
                        window.scrollTo({ top: 150, behavior: 'smooth' });
                    }
                    if (loadingIndicator) loadingIndicator.classList.add('hidden');
                    if (countText) {
                        countText.textContent = `Menampilkan ${data.from}-${data.to} dari ${data.total} produk`;
                    }
                    history.pushState(null, '', href);
                })
                .catch(() => {
                    window.location.href = href;
                });
            }
        }
    });

    // Handle browser Back / Forward navigation
    window.addEventListener('popstate', function () {
        applyFiltersRealtime(false);
    });
</script>
@endpush
@endsection

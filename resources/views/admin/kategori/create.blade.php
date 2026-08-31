@extends('layouts.app')

@section('page_title', 'Tambah Kategori')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Tambah Kategori Baru</h2>
            <p class="text-xs text-slate-500 mt-0.5">Buat kategori produk baru atau subkategori</p>
        </div>
        <a 
            href="{{ route('admin.kategori.index') }}" 
            class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition shadow-sm"
        >
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-sm">
        <form action="{{ route('admin.kategori.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Nama Kategori -->
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama" 
                    id="nama" 
                    value="{{ old('nama') }}" 
                    required 
                    placeholder="Contoh: Mangga Segar, Olahan Dodol, Souvenir"
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white transition"
                >
                @error('nama')
                    <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parent Kategori (Searchable Custom Dropdown) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kategori Induk (Opsional)
                </label>
                @php
                    $optionsList = $kategoriUtamaFlat ?? $kategoriInduk ?? \App\Models\KategoriProduk::whereNull('parent_id')->get();
                    $currentParentId = old('parent_id');
                    $selectedLabel = '— Tidak ada (Jadikan Kategori Utama) —';
                    foreach ($optionsList as $opt) {
                        if ((string)$opt->id === (string)$currentParentId) {
                            $selectedLabel = $opt->nama;
                            break;
                        }
                    }
                @endphp

                <!-- Hidden Input for Form Submission -->
                <input type="hidden" name="parent_id" id="parent_id_input" value="{{ $currentParentId }}">

                <!-- Custom Dropdown Container -->
                <div class="relative" id="customDropdownContainer">
                    <!-- Dropdown Trigger Button -->
                    <button 
                        type="button" 
                        onclick="toggleCustomDropdown()" 
                        id="dropdownTriggerBtn" 
                        class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 flex items-center justify-between hover:bg-white hover:border-brand-400 focus:outline-none focus:border-brand-500 focus:bg-white transition shadow-sm"
                    >
                        <span class="flex items-center gap-2.5 truncate" id="selectedOptionText">
                            <i class="fas fa-layer-group text-brand-600 text-xs"></i>
                            <span class="truncate">{{ $selectedLabel }}</span>
                        </span>
                        <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" id="dropdownChevronIcon"></i>
                    </button>

                    <!-- Floating Searchable Menu -->
                    <div 
                        id="customDropdownMenu" 
                        class="hidden absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl border border-slate-200 shadow-xl z-50 p-2 space-y-2 animate-in fade-in slide-in-from-top-2 duration-150"
                    >
                        <!-- Live Search Input -->
                        <div class="relative px-1 pt-1">
                            <i class="fas fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                            <input 
                                type="text" 
                                id="dropdownSearchInput" 
                                placeholder="Ketik untuk mencari kategori..." 
                                oninput="filterDropdownOptions(this.value)" 
                                autocomplete="off"
                                class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-semibold text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white transition"
                            >
                        </div>

                        <!-- Options List -->
                        <div class="max-h-60 overflow-y-auto space-y-1 pr-1 customscroll" id="dropdownOptionsList">
                            
                            <!-- Option 1: Root / None -->
                            <div 
                                onclick="selectKategoriOption('', '— Tidak ada (Jadikan Kategori Utama) —')" 
                                class="option-item flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer text-xs font-bold transition {{ empty($currentParentId) ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}"
                                data-label="tidak ada kategori utama root induk"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    <span>— Tidak ada (Jadikan Kategori Utama) —</span>
                                </div>
                                <span class="check-icon {{ empty($currentParentId) ? 'block' : 'hidden' }} text-brand-600">
                                    <i class="fas fa-check text-xs"></i>
                                </span>
                            </div>

                            <!-- List of Options -->
                            @foreach ($optionsList as $item)
                                @php
                                    $isSub = str_contains($item->nama, '--');
                                    $cleanName = trim(str_replace('--', '', $item->nama));
                                    $isSelected = (string)$currentParentId === (string)$item->id;
                                @endphp
                                <div 
                                    onclick="selectKategoriOption('{{ $item->id }}', '{{ addslashes($cleanName) }}')" 
                                    class="option-item flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer text-xs font-semibold transition {{ $isSelected ? 'bg-brand-50 text-brand-700 font-bold' : 'text-slate-700 hover:bg-slate-50' }}"
                                    data-label="{{ strtolower($cleanName) }}"
                                    style="padding-left: {{ $isSub ? '28px' : '12px' }};"
                                >
                                    <div class="flex items-center gap-2 truncate">
                                        @if($isSub)
                                            <i class="fas fa-arrow-turn-down text-slate-300 text-[10px] rotate-[270deg]"></i>
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-slate-100 text-slate-500 border border-slate-200">Sub</span>
                                            <span class="truncate">{{ $cleanName }}</span>
                                        @else
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-brand-50 text-brand-700 border border-brand-200">Induk</span>
                                            <span class="font-bold text-slate-900 truncate">{{ $cleanName }}</span>
                                        @endif
                                    </div>

                                    <span class="check-icon {{ $isSelected ? 'block' : 'hidden' }} text-brand-600 shrink-0 ml-2">
                                        <i class="fas fa-check text-xs"></i>
                                    </span>
                                </div>
                            @endforeach

                            <!-- Empty Search Results Notice -->
                            <div id="emptySearchNotice" class="hidden py-6 text-center text-xs text-slate-400">
                                <i class="fas fa-search mb-1 text-slate-300 text-base block"></i>
                                Kategori tidak ditemukan
                            </div>
                        </div>
                    </div>
                </div>

                @error('parent_id')
                    <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Gambar Kategori -->
            <div>
                <label for="gambar" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Ikon / Foto Kategori <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="file" 
                    name="gambar" 
                    id="gambar" 
                    accept="image/*"
                    required
                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 transition cursor-pointer"
                >
                <p class="text-[11px] text-slate-400 mt-1">Format JPG, PNG, WEBP maksimal 2MB.</p>
                @error('gambar')
                    <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a 
                    href="{{ route('admin.kategori.index') }}" 
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm hover:shadow"
                >
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    function toggleCustomDropdown() {
        const menu = document.getElementById('customDropdownMenu');
        const chevron = document.getElementById('dropdownChevronIcon');
        const searchInput = document.getElementById('dropdownSearchInput');
        
        const isHidden = menu.classList.contains('hidden');
        if (isHidden) {
            menu.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            setTimeout(() => searchInput.focus(), 50);
        } else {
            menu.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    }

    function selectKategoriOption(id, label) {
        document.getElementById('parent_id_input').value = id;
        document.getElementById('selectedOptionText').innerHTML = `
            <i class="fas fa-layer-group text-brand-600 text-xs"></i>
            <span class="truncate">${label}</span>
        `;

        // Update active checkmarks and background
        document.querySelectorAll('#dropdownOptionsList .option-item').forEach(item => {
            item.classList.remove('bg-brand-50', 'text-brand-700', 'font-bold');
            item.classList.add('text-slate-700');
            const check = item.querySelector('.check-icon');
            if (check) check.classList.add('hidden');
        });

        // Close dropdown
        toggleCustomDropdown();
    }

    function filterDropdownOptions(query) {
        const q = query.toLowerCase().trim();
        const items = document.querySelectorAll('#dropdownOptionsList .option-item');
        let visibleCount = 0;

        items.forEach(item => {
            const label = item.getAttribute('data-label') || '';
            if (label.includes(q)) {
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.classList.add('hidden');
            }
        });

        const emptyNotice = document.getElementById('emptySearchNotice');
        if (visibleCount === 0) {
            emptyNotice.classList.remove('hidden');
        } else {
            emptyNotice.classList.add('hidden');
        }
    }

    // Close on outside click
    document.addEventListener('click', function(event) {
        const container = document.getElementById('customDropdownContainer');
        const menu = document.getElementById('customDropdownMenu');
        const chevron = document.getElementById('dropdownChevronIcon');
        if (container && !container.contains(event.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    });
</script>
@endpush
@endsection
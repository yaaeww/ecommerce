@extends('layouts.app')

@section('page_title', 'Edit Kategori')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Edit Kategori</h2>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui nama, hierarki, atau gambar kategori</p>
        </div>
        <a 
            href="{{ route('admin.kategori.index') }}" 
            class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition shadow-sm"
        >
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-sm">
        <form action="{{ route('admin.kategori.update', $kategori->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Nama Kategori -->
            <div>
                <label for="nama" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Kategori <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="nama" 
                    id="nama" 
                    value="{{ old('nama', $kategori->nama) }}" 
                    required 
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white transition"
                >
                @error('nama')
                    <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Parent Kategori (Searchable Custom Dropdown) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Kategori Induk
                </label>
                @php
                    $optionsList = $kategoriUtamaFlat ?? $kategoriInduk ?? \App\Models\KategoriProduk::where('id', '!=', $kategori->id)->get();
                    $currentParentId = old('parent_id', $kategori->parent_id);
                    $selectedLabel = '— Tidak ada (Kategori Utama) —';
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
                                onclick="selectKategoriOption('', '— Tidak ada (Kategori Utama) —')" 
                                class="option-item flex items-center justify-between px-3 py-2.5 rounded-xl cursor-pointer text-xs font-bold transition {{ empty($currentParentId) ? 'bg-brand-50 text-brand-700' : 'text-slate-700 hover:bg-slate-50' }}"
                                data-label="tidak ada kategori utama root induk"
                            >
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-slate-400"></span>
                                    <span>— Tidak ada (Kategori Utama) —</span>
                                </div>
                                <span class="check-icon {{ empty($currentParentId) ? 'block' : 'hidden' }} text-brand-600">
                                    <i class="fas fa-check text-xs"></i>
                                </span>
                            </div>

                            <!-- List of Options -->
                            @foreach ($optionsList as $item)
                                @if ($item->id !== $kategori->id)
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
                                @endif
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

            <!-- Gambar Kategori (Current Preview & Upload New) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Ikon / Foto Kategori
                </label>

                <!-- Current Image Banner if exists -->
                @if ($kategori->gambar_url)
                    <div id="currentImageCard" class="mb-3 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-14 h-14 rounded-xl bg-white border border-slate-200 shadow-2xs overflow-hidden shrink-0">
                                <img src="{{ $kategori->gambar_url }}" class="w-full h-full object-cover" alt="Foto Kategori Saat Ini">
                            </div>
                            <div>
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200">
                                    Gambar Aktif
                                </span>
                                <p class="text-xs font-bold text-slate-800 mt-1 truncate max-w-[200px] sm:max-w-xs">
                                    {{ basename($kategori->gambar) }}
                                </p>
                            </div>
                        </div>
                        <span class="text-[11px] text-slate-400 font-medium hidden sm:inline">Unggah file baru di bawah untuk mengganti</span>
                    </div>
                @endif

                <div class="space-y-3">
                    <!-- Dropzone Container -->
                    <div 
                        id="imageDropzone"
                        onclick="document.getElementById('gambar').click()"
                        class="border-2 border-dashed border-slate-200 hover:border-brand-500 bg-slate-50/60 hover:bg-brand-50/30 rounded-2xl p-6 text-center cursor-pointer transition-all duration-200 group"
                    >
                        <input 
                            type="file" 
                            name="gambar" 
                            id="gambar" 
                            accept="image/png,image/jpeg,image/jpg,image/webp,image/svg+xml"
                            onchange="previewCategoryPhoto(this)"
                            class="hidden"
                        >

                        <!-- Empty State -->
                        <div id="dropzoneEmptyState" class="space-y-2">
                            <div class="w-12 h-12 rounded-2xl bg-white border border-slate-200 shadow-2xs text-brand-600 flex items-center justify-center mx-auto text-lg group-hover:scale-110 group-hover:bg-brand-600 group-hover:text-white transition duration-200">
                                <i class="fas fa-cloud-arrow-up"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-700 group-hover:text-brand-700">
                                    {{ $kategori->gambar_url ? 'Klik atau seret file ke sini untuk mengganti foto' : 'Klik atau seret file gambar ke sini' }}
                                </p>
                                <p class="text-[11px] text-slate-400 mt-0.5">
                                    Format PNG, JPG, WEBP, atau SVG (Maks. 2MB)
                                </p>
                            </div>
                        </div>

                        <!-- Preview State -->
                        <div id="dropzonePreviewState" class="hidden flex items-center justify-center gap-4">
                            <div class="w-16 h-16 rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden flex-shrink-0">
                                <img id="previewImgElement" src="#" alt="Preview" class="w-full h-full object-cover">
                            </div>
                            <div class="text-left min-w-0">
                                <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-brand-50 text-brand-700 border border-brand-200">
                                    Foto Baru Terpilih
                                </span>
                                <p class="text-xs font-bold text-slate-900 truncate mt-1" id="previewFileName">-</p>
                                <p class="text-[10px] text-emerald-600 font-semibold mt-0.5" id="previewFileSize">-</p>
                                <p class="text-[10px] text-brand-600 font-bold mt-1 group-hover:underline">
                                    <i class="fas fa-arrows-rotate mr-1"></i> Klik untuk memilih file lain
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                @error('gambar')
                    <p class="text-rose-500 text-xs mt-1.5 font-semibold">{{ $message }}</p>
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
                    Perbarui Kategori
                </button>
            </div>
        </form>
    </div>

</div>

@push('scripts')
<script>
    // Live Category Image Preview
    function previewCategoryPhoto(input) {
        if (input.files && input.files[0]) {
            const file = input.files[0];
            const reader = new FileReader();

            reader.onload = function(e) {
                document.getElementById('previewImgElement').src = e.target.result;
                document.getElementById('previewFileName').innerText = file.name;
                document.getElementById('previewFileSize').innerText = `${(file.size / 1024).toFixed(1)} KB`;

                document.getElementById('dropzoneEmptyState').classList.add('hidden');
                document.getElementById('dropzonePreviewState').classList.remove('hidden');
            };

            reader.readAsDataURL(file);
        }
    }
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
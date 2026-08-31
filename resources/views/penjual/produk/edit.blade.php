@extends('layouts.app')

@section('page_title', 'Edit Produk')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Edit Produk</h2>
            <p class="text-sm text-slate-500 mt-1">Perbarui informasi katalog produk toko Anda</p>
        </div>
        <a href="{{ route('penjual.produk.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition border border-slate-200">
            <i class="fas fa-arrow-left"></i>
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-rose-50 border border-rose-200">
            <div class="flex items-center gap-2 text-rose-700 font-bold mb-2">
                <i class="fas fa-exclamation-triangle"></i> Terjadi Kesalahan
            </div>
            <ul class="list-disc list-inside text-sm text-rose-600 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('penjual.produk.update', $produk->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden p-6 sm:p-8 space-y-6">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kategori Utama -->
                <div>
                    <label for="kategori_utama" class="block text-sm font-bold text-slate-700 mb-2">Kategori Utama</label>
                    <select id="kategori_utama" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3">
                        <option value="">-- Pilih Kategori Utama --</option>
                        @foreach($kategoriUtamas as $kategori)
                            <option value="{{ $kategori->id }}" {{ $produk->kategori->parent_id == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Subkategori -->
                <div>
                    <label for="subkategori" class="block text-sm font-bold text-slate-700 mb-2">Subkategori <span class="text-rose-500">*</span></label>
                    <select name="kategori_produk_id" id="subkategori" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" required>
                        <option value="">-- Pilih Subkategori --</option>
                        @foreach($subkategoris as $sub)
                            <option value="{{ $sub->id }}" {{ $produk->kategori_produk_id == $sub->id ? 'selected' : '' }}>
                                {{ $sub->nama }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-bold text-slate-700 mb-2">Nama Produk <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="nama" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" value="{{ old('nama', $produk->nama) }}" placeholder="Contoh: Mangga Harum Manis Premium" required>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Produk</label>
                <textarea name="deskripsi" id="deskripsi" rows="5" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" placeholder="Jelaskan detail produk Anda di sini...">{{ old('deskripsi', $produk->deskripsi) }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <!-- Harga -->
                <div>
                    <label for="harga" class="block text-sm font-bold text-slate-700 mb-2">Harga (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="harga" id="harga" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" value="{{ old('harga', $produk->harga) }}" placeholder="0" required>
                </div>
                <!-- Stok -->
                <div>
                    <label for="stok" class="block text-sm font-bold text-slate-700 mb-2">Stok <span class="text-rose-500">*</span></label>
                    <input type="number" name="stok" id="stok" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" value="{{ old('stok', $produk->stok) }}" placeholder="0" required>
                </div>
            </div>

            <!-- Gambar -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Gambar Produk</label>
                
                @if($produk->gambar)
                    <div class="mb-4">
                        <p class="text-xs font-bold text-slate-500 mb-2">Gambar Saat Ini:</p>
                        <img src="{{ asset('storage/' . $produk->gambar) }}" class="max-h-40 rounded-lg shadow-sm border border-slate-200 object-cover" alt="Gambar Produk">
                        <p class="text-[11px] text-slate-400 mt-1">Biarkan kosong jika tidak ingin mengubah gambar.</p>
                    </div>
                @endif
                
                <div class="mt-2 flex justify-center rounded-xl border border-dashed border-slate-300 px-6 py-8 bg-slate-50 relative group hover:bg-slate-100 transition cursor-pointer" id="drop-area">
                    <div class="text-center" id="upload-content">
                        <i class="fas fa-cloud-upload-alt mx-auto h-12 w-12 text-slate-300 group-hover:text-brand-500 transition text-4xl mb-3"></i>
                        <div class="mt-4 flex text-sm leading-6 text-slate-600 justify-center">
                            <label for="gambar" class="relative cursor-pointer rounded-md font-bold text-brand-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-brand-600 focus-within:ring-offset-2 hover:text-brand-500">
                                <span>Pilih Gambar Baru</span>
                                <input id="gambar" name="gambar" type="file" class="sr-only" accept="image/*" onchange="previewImage(event)">
                            </label>
                        </div>
                        <p class="text-xs text-slate-500 mt-1">PNG, JPG, GIF up to 2MB</p>
                    </div>
                    
                    <div id="image-preview-container" class="hidden w-full">
                        <div class="relative inline-block">
                            <img id="preview" src="#" alt="Preview" class="max-h-48 rounded-lg shadow-sm border border-slate-200 object-cover">
                            <button type="button" class="absolute -top-2 -right-2 bg-rose-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs shadow-sm hover:bg-rose-600 transition" onclick="clearImagePreview()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-slate-100 my-6"></div>

            <!-- Diskon Section -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <i class="fas fa-tags text-brand-500"></i>
                    <h3 class="font-bold text-slate-800">Pengaturan Diskon <span class="text-xs font-normal text-slate-500 ml-1">(Opsional)</span></h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="persen_diskon" class="block text-sm font-bold text-slate-700 mb-2">Persen Diskon (%)</label>
                        <input type="number" name="persen_diskon" id="persen_diskon" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" min="0" max="100" value="{{ old('persen_diskon', optional($produk->diskon)->persen_diskon) }}" placeholder="0">
                    </div>
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-bold text-slate-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3 text-slate-700" value="{{ old('tanggal_mulai', optional($produk->diskon)->tanggal_mulai ? \Carbon\Carbon::parse($produk->diskon->tanggal_mulai)->format('Y-m-d') : '') }}">
                    </div>
                    <div>
                        <label for="tanggal_berakhir" class="block text-sm font-bold text-slate-700 mb-2">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" id="tanggal_berakhir" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3 text-slate-700" value="{{ old('tanggal_berakhir', optional($produk->diskon)->tanggal_berakhir ? \Carbon\Carbon::parse($produk->diskon->tanggal_berakhir)->format('Y-m-d') : '') }}">
                    </div>
                </div>
            </div>
            
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button type="submit" class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-xl transition shadow-sm hover:shadow flex items-center gap-2">
                <i class="fas fa-save"></i> Update Produk
            </button>
        </div>

    </form>
</div>

<script>
    const fileInput = document.getElementById('gambar');
    const uploadContent = document.getElementById('upload-content');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImageEl = document.getElementById('preview');

    function previewImage(event) {
        if(event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function() {
                previewImageEl.src = reader.result;
                uploadContent.classList.add('hidden');
                previewContainer.classList.remove('hidden');
                previewContainer.classList.add('flex', 'justify-center');
            };
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    function clearImagePreview() {
        fileInput.value = '';
        previewImageEl.src = '#';
        previewContainer.classList.add('hidden');
        previewContainer.classList.remove('flex', 'justify-center');
        uploadContent.classList.remove('hidden');
    }
    
    // Allow clicking the drop area to trigger file input
    document.getElementById('drop-area').addEventListener('click', function(e) {
        // Prevent triggering if they clicked the clear button or the file input directly
        if(e.target.tagName !== 'BUTTON' && e.target.tagName !== 'I' && e.target.tagName !== 'INPUT' && !previewContainer.classList.contains('flex')) {
            fileInput.click();
        }
    });

    // Dynamic dropdown for subkategori
    document.addEventListener('DOMContentLoaded', function () {
        const kategoriUtamaSelect = document.getElementById('kategori_utama');
        const subkategoriSelect = document.getElementById('subkategori');

        if (kategoriUtamaSelect) {
            kategoriUtamaSelect.addEventListener('change', function () {
                const kategoriId = this.value;
                
                // Clear existing options
                // Keep the default option
                Array.from(subkategoriSelect.options).forEach(opt => {
                    if (opt.value !== '') {
                        opt.style.display = 'none';
                    }
                });

                if (kategoriId) {
                    // Assuming we have parent-child mapping somewhere, 
                    // for now just a placeholder for AJAX/JS logic if implemented
                    // Usually we need an API or data attribute
                }
            });
        }
    });
</script>
@endsection

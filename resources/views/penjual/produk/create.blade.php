@extends('layouts.app')

@section('page_title', 'Tambah Produk')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Tambah Produk Baru</h2>
            <p class="text-sm text-slate-500 mt-1">Tambahkan produk baru ke dalam katalog toko Anda</p>
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

    <form action="{{ route('penjual.produk.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
        @csrf

        <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden p-6 sm:p-8 space-y-6">
            
            <!-- Kategori -->
            <div>
                <label for="kategori_produk_id" class="block text-sm font-extrabold text-slate-700 mb-2">Kategori Produk <span class="text-rose-500">*</span></label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-layer-group text-sm"></i>
                    </div>
                    <select name="kategori_produk_id" id="kategori_produk_id" class="w-full rounded-2xl border-slate-200 bg-slate-50 text-sm font-bold text-slate-800 focus:border-brand-500 focus:ring-brand-500 transition shadow-xs pl-11 pr-10 py-3 cursor-pointer" required>
                        <option value="">-- Pilih Kategori Produk --</option>
                        @foreach($kategoriProduks as $kategori)
                            <option value="{{ $kategori->id }}" {{ old('kategori_produk_id') == $kategori->id ? 'selected' : '' }}>
                                {{ $kategori->nama }}
                            </option>
                            @foreach ($kategori->children as $sub)
                                <option value="{{ $sub->id }}" {{ old('kategori_produk_id') == $sub->id ? 'selected' : '' }}>
                                    — {{ $sub->nama }}
                                </option>
                            @endforeach
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Nama -->
            <div>
                <label for="nama" class="block text-sm font-bold text-slate-700 mb-2">Nama Produk <span class="text-rose-500">*</span></label>
                <input type="text" name="nama" id="nama" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" value="{{ old('nama') }}" placeholder="Contoh: Mangga Harum Manis Premium" required>
            </div>

            <!-- Deskripsi -->
            <div>
                <label for="deskripsi" class="block text-sm font-bold text-slate-700 mb-2">Deskripsi Produk</label>
                <textarea name="deskripsi" id="deskripsi" rows="5" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" placeholder="Jelaskan detail produk Anda di sini...">{{ old('deskripsi') }}</textarea>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Harga -->
                <div>
                    <label for="harga" class="block text-sm font-bold text-slate-700 mb-2">Harga Jual Normal (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="harga" id="harga" min="0" oninput="updateBagiHasil()" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3 font-semibold" value="{{ old('harga') }}" placeholder="Contoh: 45000" required>
                </div>

                <!-- 🏷️ Harga Coret (Diskon Promo) -->
                <div>
                    <label for="harga_coret" class="block text-sm font-bold text-slate-700 mb-2 flex items-center justify-between">
                        <span>Harga Coret (Sebelum Diskon)</span>
                        <span class="text-[10px] text-slate-400 font-normal">Opsional</span>
                    </label>
                    <input type="number" name="harga_coret" id="harga_coret" min="0" oninput="updateDiskonPreview()" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3 font-semibold" value="{{ old('harga_coret') }}" placeholder="Contoh: 55000">
                    <span id="diskonPreviewBadge" class="hidden inline-block mt-1 text-[10px] font-extrabold text-rose-600 bg-rose-50 px-2 py-0.5 rounded-md border border-rose-200"></span>
                </div>

                <!-- Berat Komoditas -->
                <div>
                    <label for="berat_gram" class="block text-sm font-bold text-slate-700 mb-2">Berat Bersih (Gram) <span class="text-rose-500">*</span></label>
                    <input type="number" name="berat_gram" id="berat_gram" min="100" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3 font-semibold" value="{{ old('berat_gram', 1000) }}" placeholder="1000 (1 Kg)" required>
                </div>
            </div>

            <!-- Stok -->
            <div>
                <label for="stok" class="block text-sm font-bold text-slate-700 mb-2">Stok Tersedia (Kemasan/Pack) <span class="text-rose-500">*</span></label>
                <input type="number" name="stok" id="stok" min="0" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" value="{{ old('stok') }}" placeholder="0" required>
            </div>

            <!-- 💰 Real-time Visualisasi Estimasi Pendapatan & Potongan Platform -->
            <div class="p-4 sm:p-5 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div class="flex items-center gap-2">
                        <span class="px-2 py-0.5 rounded bg-brand-50 text-brand-700 text-[10px] font-extrabold uppercase border border-brand-200">
                            Transparansi Bagi Hasil
                        </span>
                        <span class="text-xs font-bold text-slate-700">Estimasi Pendapatan Bersih Per Produk</span>
                    </div>
                    <span class="text-[11px] font-semibold text-slate-500">
                        Potongan Platform Marketplace: <strong class="text-brand-600">{{ $komisiPersen }}%</strong>
                    </span>
                </div>

                <!-- Visual Proportion Bar -->
                <div class="w-full h-2.5 bg-slate-200 rounded-full overflow-hidden flex">
                    <div id="barPenjual" class="bg-emerald-500 h-full transition-all duration-200" style="width: {{ $tokoPersen }}%"></div>
                    <div id="barPlatform" class="bg-indigo-500 h-full transition-all duration-200" style="width: {{ $komisiPersen }}%"></div>
                </div>

                <!-- Breakdown Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 pt-1">
                    <div class="p-3.5 rounded-xl bg-white border border-emerald-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-emerald-700 block">Pendapatan Bersih Anda ({{ $tokoPersen }}%):</span>
                            <span class="text-[10px] text-slate-400">Masuk ke saldo dompet toko</span>
                        </div>
                        <strong class="text-sm sm:text-base font-extrabold text-emerald-700 font-display" id="estimasiBersih">
                            Rp 0
                        </strong>
                    </div>
                    <div class="p-3.5 rounded-xl bg-white border border-indigo-200/80 shadow-xs flex items-center justify-between">
                        <div>
                            <span class="text-[11px] font-bold text-indigo-700 block">Biaya Layanan Platform ({{ $komisiPersen }}%):</span>
                            <span class="text-[10px] text-slate-400">Operasional marketplace</span>
                        </div>
                        <strong class="text-sm sm:text-base font-extrabold text-indigo-700 font-display" id="estimasiKomisi">
                            Rp 0
                        </strong>
                    </div>
                </div>
            </div>

            <!-- Gambar -->
            <div>
                <label class="block text-sm font-bold text-slate-700 mb-2">Gambar Produk</label>
                
                <div class="mt-2 flex justify-center rounded-xl border border-dashed border-slate-300 px-6 py-8 bg-slate-50 relative group hover:bg-slate-100 transition cursor-pointer" id="drop-area">
                    <div class="text-center" id="upload-content">
                        <i class="fas fa-cloud-upload-alt mx-auto h-12 w-12 text-slate-300 group-hover:text-brand-500 transition text-4xl mb-3"></i>
                        <div class="mt-4 flex text-sm leading-6 text-slate-600 justify-center">
                            <label for="gambar" class="relative cursor-pointer rounded-md font-bold text-brand-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-brand-600 focus-within:ring-offset-2 hover:text-brand-500">
                                <span>Upload gambar</span>
                                <input id="gambar" name="gambar" type="file" class="sr-only" accept="image/*" onchange="previewImage(event)">
                            </label>
                            <p class="pl-1">atau drag and drop</p>
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
                        <input type="number" name="persen_diskon" id="persen_diskon" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3" value="{{ old('persen_diskon') }}" min="0" max="100" placeholder="0">
                    </div>
                    <div>
                        <label for="tanggal_mulai" class="block text-sm font-bold text-slate-700 mb-2">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3 text-slate-700" value="{{ old('tanggal_mulai') }}">
                    </div>
                    <div>
                        <label for="tanggal_berakhir" class="block text-sm font-bold text-slate-700 mb-2">Tanggal Berakhir</label>
                        <input type="date" name="tanggal_berakhir" id="tanggal_berakhir" class="w-full rounded-xl border-slate-200 bg-slate-50 text-sm focus:border-brand-500 focus:ring-brand-500 transition shadow-sm px-4 py-3 text-slate-700" value="{{ old('tanggal_berakhir') }}">
                    </div>
                </div>
            </div>
            
        </div>

        <div class="flex justify-end gap-3 mt-6">
            <button type="submit" class="px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-xl transition shadow-sm hover:shadow flex items-center gap-2">
                <i class="fas fa-save"></i> Simpan Produk
            </button>
        </div>

    </form>
</div>

<script>
    const fileInput = document.getElementById('gambar');
    const uploadContent = document.getElementById('upload-content');
    const previewContainer = document.getElementById('image-preview-container');
    const previewImageEl = document.getElementById('preview');

    const komisiPersen = {{ $komisiPersen }};
    const tokoPersen = {{ $tokoPersen }};

    function updateBagiHasil() {
        const harga = parseFloat(document.getElementById('harga').value) || 0;
        const bersih = harga * (tokoPersen / 100);
        const komisi = harga * (komisiPersen / 100);

        document.getElementById('estimasiBersih').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(bersih));
        document.getElementById('estimasiKomisi').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(komisi));
    }

    function updateDiskonPreview() {
        const harga = parseFloat(document.getElementById('harga').value) || 0;
        const hargaCoret = parseFloat(document.getElementById('harga_coret').value) || 0;
        const badge = document.getElementById('diskonPreviewBadge');

        if (hargaCoret > harga && harga > 0) {
            const diskonPersen = Math.round(((hargaCoret - harga) / hargaCoret) * 100);
            badge.innerText = `🔥 Hemat ${diskonPersen}% (Diskon Rp ${(hargaCoret - harga).toLocaleString('id-ID')})`;
            badge.classList.remove('hidden');
        } else {
            badge.classList.add('hidden');
        }
    }

    // Jalankan saat load pertama jika ada old value
    document.addEventListener('DOMContentLoaded', function() {
        updateBagiHasil();
        updateDiskonPreview();
    });

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
</script>
@endsection

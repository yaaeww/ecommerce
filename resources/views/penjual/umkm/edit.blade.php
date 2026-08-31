@extends('layouts.app')

@section('page_title', 'Edit Profil Toko')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Edit Profil Toko</h2>
            <p class="text-sm text-slate-500 mt-1">Perbarui informasi dan logo toko Anda.</p>
        </div>
        <a href="{{ route('penjual.umkm.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition border border-slate-200">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Form Container -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden p-6 sm:p-8">
        <form action="{{ route('penjual.umkm.update', $umkm->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Form Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                
                <!-- Left: Basic Info -->
                <div class="lg:col-span-8 space-y-6">
                    <div>
                        <label for="nama_toko" class="block text-sm font-bold text-slate-900 mb-2">Nama Toko <span class="text-rose-500">*</span></label>
                        <input type="text" id="nama_toko" name="nama_toko" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition @error('nama_toko') border-rose-500 @enderror" value="{{ old('nama_toko', $umkm->nama_toko) }}" required>
                        @error('nama_toko')
                            <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="deskripsi" class="block text-sm font-bold text-slate-900 mb-2">Deskripsi Toko</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition @error('deskripsi') border-rose-500 @enderror">{{ old('deskripsi', $umkm->deskripsi) }}</textarea>
                        @error('deskripsi')
                            <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="alamat" class="block text-sm font-bold text-slate-900 mb-2">Alamat Lengkap <span class="text-rose-500">*</span></label>
                            <input type="text" id="alamat" name="alamat" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition @error('alamat') border-rose-500 @enderror" value="{{ old('alamat', $umkm->alamat) }}" required>
                            @error('alamat')
                                <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="no_telp" class="block text-sm font-bold text-slate-900 mb-2">Nomor Telepon</label>
                            <input type="text" id="no_telp" name="no_telp" class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-900 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition @error('no_telp') border-rose-500 @enderror" value="{{ old('no_telp', $umkm->no_telp) }}">
                            @error('no_telp')
                                <p class="mt-1.5 text-sm text-rose-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Right: Logo Upload -->
                <div class="lg:col-span-4">
                    <label class="block text-sm font-bold text-slate-900 mb-2">Logo Toko</label>
                    <div class="relative group cursor-pointer border-2 border-dashed border-slate-200 bg-slate-50 rounded-2xl p-6 text-center hover:bg-slate-100 hover:border-brand-500 transition h-[250px] flex flex-col justify-center items-center overflow-hidden @error('logo') border-rose-500 @enderror" id="logoUploadArea">
                        
                        <input type="file" id="logo" name="logo" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" accept="image/*" onchange="previewImage(this)">
                        
                        <div id="uploadPlaceholder" class="flex flex-col items-center justify-center {{ $umkm->logo ? 'hidden' : '' }}">
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center text-slate-400 mb-3 shadow-sm group-hover:text-brand-500 group-hover:scale-110 transition">
                                <i class="fas fa-cloud-upload-alt text-2xl"></i>
                            </div>
                            <span class="text-sm font-bold text-slate-700">Ubah Logo</span>
                            <span class="text-xs text-slate-400 mt-1">PNG, JPG up to 2MB</span>
                        </div>

                        <img id="imagePreview" src="{{ $umkm->logo ? asset('storage/' . $umkm->logo) : '#' }}" alt="Preview" class="{{ $umkm->logo ? '' : 'hidden' }} absolute inset-0 w-full h-full object-cover z-0">
                    </div>
                    @error('logo')
                        <p class="mt-1.5 text-sm text-rose-500 text-center">{{ $message }}</p>
                    @enderror
                </div>

            </div>

            <!-- Submit Section -->
            <div class="pt-6 border-t border-slate-100 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl transition shadow-sm hover:shadow">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>

<script>
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            
            reader.onload = function (e) {
                document.getElementById('imagePreview').src = e.target.result;
                document.getElementById('imagePreview').classList.remove('hidden');
                document.getElementById('uploadPlaceholder').classList.add('hidden');
            }
            
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection

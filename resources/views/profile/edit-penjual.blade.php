@extends('layouts.app')

@section('page_title', 'Edit Profil Toko')

@section('content')
<div class="max-w-4xl mx-auto space-y-6 pb-12">
    
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold mb-1">
                <i class="fas fa-edit text-[10px]"></i> Pengaturan Akun
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 font-display">Edit Profil Toko & Penjual</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Perbarui informasi toko, nomor kontak, dan alamat operasional pengiriman kebun.</p>
        </div>
        <a href="{{ route('penjual.profile.show') }}" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-2">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Kembali</span>
        </a>
    </div>

    @if (isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs shadow-xs">
            <div class="flex items-center gap-2 font-bold mb-1">
                <i class="fas fa-exclamation-triangle text-rose-500"></i>
                <span>Terdapat kesalahan pengisian data:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-rose-700 ml-4">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Card -->
    <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-xs rounded-3xl">
        <form action="{{ route('penjual.profile.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PATCH')

            <!-- Section 1: Informasi Toko -->
            <div class="pb-6 border-b border-slate-100">
                <h3 class="text-sm font-extrabold text-slate-900 font-display flex items-center gap-2 mb-4">
                    <i class="fas fa-store text-emerald-600"></i>
                    <span>Informasi Toko & Usaha</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="nama_toko" class="text-xs font-bold text-slate-700">Nama Toko / Kebun <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="nama_toko" 
                            id="nama_toko" 
                            value="{{ old('nama_toko', $umkm->nama_toko ?? $user->name) }}" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition"
                            placeholder="Contoh: Kebun Mangga Gedong Gincu Asli"
                            required
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label for="no_telp" class="text-xs font-bold text-slate-700">Nomor WhatsApp / Telepon <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="no_telp" 
                            id="no_telp" 
                            value="{{ old('no_telp', $umkm->no_telp ?? '') }}" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition"
                            placeholder="Contoh: 081234567890"
                            required
                        >
                    </div>
                </div>

                <div class="mt-5 space-y-1.5">
                    <label for="alamat" class="text-xs font-bold text-slate-700">Alamat Lengkap Kebun / Gudang Pengiriman <span class="text-rose-500">*</span></label>
                    <textarea 
                        name="alamat" 
                        id="alamat" 
                        rows="3" 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition"
                        placeholder="Contoh: Desa Krasak, Blok Gandok, Kec. Jatibarang, Kab. Indramayu"
                        required
                    >{{ old('alamat', $umkm->alamat ?? '') }}</textarea>
                </div>

                <div class="mt-5 space-y-1.5">
                    <label for="deskripsi" class="text-xs font-bold text-slate-700">Deskripsi & Profil Usaha</label>
                    <textarea 
                        name="deskripsi" 
                        id="deskripsi" 
                        rows="3" 
                        class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition"
                        placeholder="Ceritakan sejarah kebun Anda, varietas mangga unggulan, atau jaminan mutu petik matang pohon..."
                    >{{ old('deskripsi', $umkm->deskripsi ?? '') }}</textarea>
                </div>
            </div>

            <!-- Section 2: Penanggung Jawab Akun -->
            <div>
                <h3 class="text-sm font-extrabold text-slate-900 font-display flex items-center gap-2 mb-4">
                    <i class="fas fa-user-circle text-emerald-600"></i>
                    <span>Informasi Akun Penanggung Jawab</span>
                </h3>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label for="name" class="text-xs font-bold text-slate-700">Nama Lengkap Penjual <span class="text-rose-500">*</span></label>
                        <input 
                            type="text" 
                            name="name" 
                            id="name" 
                            value="{{ old('name', $user->name) }}" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-900 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-none transition"
                            required
                        >
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-bold text-slate-400">Email Akun (Terkunci)</label>
                        <input 
                            type="email" 
                            value="{{ $user->email }}" 
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-400 bg-slate-50 cursor-not-allowed" 
                            disabled
                        >
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-6 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('penjual.profile.show') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition shadow-xs flex items-center gap-2">
                    <i class="fas fa-save text-xs"></i>
                    <span>Simpan Perubahan</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
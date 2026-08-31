@extends('layouts.app')

@section('page_title', 'Toko Saya')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Toko Saya</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola profil dan informasi UMKM Anda</p>
        </div>
    </div>

    @if (!$umkm)
        <!-- UMKM Belum Terdaftar -->
        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl p-12 text-center max-w-3xl mx-auto">
            <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-6 transform rotate-3">
                <i class="fas fa-store text-4xl"></i>
            </div>
            <h3 class="text-2xl font-bold text-slate-900 mb-3 font-display">Toko Anda Belum Terdaftar</h3>
            <p class="text-slate-500 mb-8 max-w-md mx-auto leading-relaxed">
                Silakan daftar toko terlebih dahulu agar dapat mulai menambahkan produk dan berjualan di platform kami.
            </p>
            <a href="{{ route('penjual.umkm.create') }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl transition shadow-sm hover:shadow">
                <i class="fas fa-plus-circle"></i>
                Daftar Toko Sekarang
            </a>
        </div>
    @else
        <!-- Profil Toko -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            
            <!-- Left Profile Card -->
            <div class="lg:col-span-1">
                <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6 h-full flex flex-col">
                    <div class="text-center mb-6">
                        <div class="w-32 h-32 mx-auto rounded-full bg-slate-50 border-4 border-white shadow-md flex items-center justify-center mb-4 overflow-hidden relative group">
                            @if ($umkm->logo)
                                <img src="{{ asset('storage/' . $umkm->logo) }}" alt="Logo Toko" class="w-full h-full object-cover">
                            @else
                                <i class="fas fa-store text-4xl text-slate-300"></i>
                            @endif
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 font-display">{{ $umkm->nama_toko }}</h3>
                        <p class="text-sm text-slate-500 mt-1">{{ $umkm->deskripsi ?? 'Tidak ada deskripsi' }}</p>
                    </div>

                    <div class="flex-1">
                        <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4 pb-2 border-b border-slate-100">Informasi Kontak</h4>
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 mb-0.5">Alamat Lengkap</p>
                                    <p class="text-sm text-slate-900 leading-relaxed">{{ $umkm->alamat }}</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 mb-0.5">Nomor Telepon</p>
                                    <p class="text-sm text-slate-900 font-medium">{{ $umkm->no_telp ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center text-slate-400 shrink-0">
                                    <i class="fas fa-tag"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-medium text-slate-500 mb-1">Status Toko</p>
                                    @if($umkm->status == 'approved')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                                            <i class="fas fa-check-circle"></i> Disetujui
                                        </span>
                                    @elseif($umkm->status == 'pending')
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200">
                                            <i class="fas fa-clock"></i> Menunggu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200">
                                            <i class="fas fa-times-circle"></i> Ditolak
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Status Card -->
            <div class="lg:col-span-2">
                <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-8 h-full flex flex-col justify-center text-center">
                    
                    @switch($umkm->status)
                        @case('pending')
                            <div class="max-w-md mx-auto">
                                <div class="w-20 h-20 bg-amber-50 text-amber-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-clock text-4xl animate-pulse"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-2 font-display">Toko Menunggu Persetujuan</h3>
                                <p class="text-slate-500 mb-6 leading-relaxed">
                                    Toko <strong>"{{ $umkm->nama_toko }}"</strong> sedang dalam proses review oleh admin. Harap tunggu konfirmasi lebih lanjut.
                                </p>
                            </div>
                            @break

                        @case('approved')
                            <div class="max-w-lg mx-auto w-full">
                                <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-check-circle text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-6 font-display">Toko Aktif & Berjalan</h3>
                                
                                <div class="grid grid-cols-2 gap-4 mb-8 text-left">
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Produk</p>
                                        <p class="text-2xl font-bold text-slate-900">{{ $umkm->produks()->count() ?? 0 }}</p>
                                    </div>
                                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100">
                                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Total Pesanan</p>
                                        <p class="text-2xl font-bold text-slate-900">0</p>
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
                                    <a href="{{ route('penjual.umkm.edit', $umkm->id) }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition shadow-sm">
                                        <i class="fas fa-edit"></i> Edit Profil Toko
                                    </a>
                                    <a href="{{ route('penjual.produk.index') }}" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl transition shadow-sm">
                                        <i class="fas fa-box"></i> Kelola Produk
                                    </a>
                                </div>
                            </div>
                            @break

                        @case('rejected')
                            <div class="max-w-md mx-auto">
                                <div class="w-20 h-20 bg-rose-50 text-rose-500 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-times-circle text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-2 font-display">Pendaftaran Toko Ditolak</h3>
                                <p class="text-slate-500 mb-6 leading-relaxed">
                                    Mohon maaf, pendaftaran toko Anda tidak dapat disetujui. Silakan periksa kembali dan perbarui data toko Anda.
                                </p>
                                <a href="{{ route('penjual.umkm.edit', $umkm->id) }}" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl transition shadow-sm">
                                    <i class="fas fa-redo"></i> Perbarui Data Toko
                                </a>
                            </div>
                            @break

                        @default
                            <div class="max-w-md mx-auto">
                                <div class="w-20 h-20 bg-slate-50 text-slate-400 rounded-2xl flex items-center justify-center mx-auto mb-6">
                                    <i class="fas fa-question-circle text-4xl"></i>
                                </div>
                                <h3 class="text-xl font-bold text-slate-900 mb-2 font-display">Status Tidak Diketahui</h3>
                                <p class="text-slate-500">Silakan hubungi administrator untuk informasi lebih lanjut.</p>
                            </div>
                    @endswitch

                </div>
            </div>

        </div>
    @endif

</div>
@endsection

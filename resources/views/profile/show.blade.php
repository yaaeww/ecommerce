@extends('layouts.app')

@section('page_title', 'Profil Toko Penjual')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto pb-12">
    
    <!-- Page Header Banner -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold mb-1">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Identitas Toko & Akun
            </div>
            <h2 class="text-2xl font-extrabold text-slate-900 font-display">Profil Toko & Pengaturan</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Kelola identitas kebun, kontak penanggung jawab, dan informasi operasional UMKM Anda.</p>
        </div>

        @if($umkm && $umkm->status === 'approved')
            <div class="flex items-center gap-2.5">
                <a href="{{ route('penjual.profile.edit') }}" class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-xs flex items-center gap-2">
                    <i class="fas fa-edit text-xs"></i>
                    <span>Edit Profil Toko</span>
                </a>
            </div>
        @endif
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center gap-3 text-emerald-800 text-xs font-bold shadow-xs">
            <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center shrink-0">
                <i class="fas fa-check"></i>
            </div>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- ========================================== -->
        <!-- 👤 LEFT CARD: PERSONAL ACCOUNT & AVATAR    -->
        <!-- ========================================== -->
        <div class="lg:col-span-4 space-y-6">
            <div class="card p-6 bg-white border border-slate-200/80 shadow-xs rounded-3xl text-center flex flex-col items-center">
                
                @php
                    $avatarPath = auth()->user()->avatar;
                    $fullPath = 'avatar/' . ltrim($avatarPath, '/');
                    $avatarExists = $avatarPath && Storage::disk('public')->exists($fullPath);
                @endphp

                <!-- Avatar with Upload Button Overlay -->
                <div class="relative group my-2">
                    <div class="w-28 h-28 rounded-full overflow-hidden border-4 border-white shadow-md bg-slate-50 flex items-center justify-center">
                        @if($avatarExists)
                            <img src="{{ asset('storage/' . $fullPath) }}" class="w-full h-full object-cover" alt="{{ $user->name }}">
                        @else
                            <div class="w-full h-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-extrabold text-3xl flex items-center justify-center">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                    </div>

                    <label for="avatarInput" class="absolute bottom-0 right-0 w-9 h-9 rounded-full bg-slate-900 hover:bg-emerald-600 text-white flex items-center justify-center cursor-pointer shadow-md transition-all">
                        <i class="fas fa-camera text-xs"></i>
                    </label>
                </div>

                <form id="avatarForm" action="{{ route('penjual.profile.avatar') }}" method="POST" enctype="multipart/form-data" class="hidden">
                    @csrf
                    <input type="file" name="avatar" id="avatarInput" accept="image/*" onchange="document.getElementById('avatarForm').submit()">
                </form>

                <h3 class="text-base font-extrabold text-slate-900 mt-3 font-display">{{ $user->name }}</h3>
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-bold mt-1.5">
                    <i class="fas fa-store text-emerald-600"></i>
                    <span>Penjual Terdaftar</span>
                </div>

                <div class="w-full border-t border-slate-100 mt-6 pt-5 text-left space-y-3.5 text-xs">
                    <div>
                        <span class="text-slate-400 font-medium block text-[11px] mb-0.5">Email Penanggung Jawab</span>
                        <div class="flex items-center gap-2 font-bold text-slate-800">
                            <i class="fas fa-envelope text-slate-400"></i>
                            <span class="truncate">{{ $user->email }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 font-medium block text-[11px] mb-0.5">Nomor Telepon / WhatsApp</span>
                        <div class="flex items-center gap-2 font-bold text-slate-800">
                            <i class="fas fa-phone text-slate-400"></i>
                            <span>{{ $umkm->no_telp ?? '-' }}</span>
                        </div>
                    </div>

                    <div>
                        <span class="text-slate-400 font-medium block text-[11px] mb-0.5">Tanggal Registrasi Akun</span>
                        <div class="flex items-center gap-2 font-bold text-slate-800">
                            <i class="fas fa-calendar-alt text-slate-400"></i>
                            <span>{{ $user->created_at ? $user->created_at->translatedFormat('d F Y') : '-' }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- ========================================== -->
        <!-- 🏪 RIGHT CARD: STORE DETAILS & LEGALITY    -->
        <!-- ========================================== -->
        <div class="lg:col-span-8 space-y-6">
            <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-xs rounded-3xl">
                
                @if ($umkm)
                    @if ($umkm->status === 'pending')
                        <div class="p-5 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs mb-6 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center shrink-0 text-sm">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-amber-900">Menunggu Verifikasi Admin</h4>
                                <p class="mt-1 text-amber-700 leading-relaxed">Pengajuan toko Anda sedang dalam peninjauan oleh tim kurasi kami. Produk Anda akan aktif setelah disetujui.</p>
                            </div>
                        </div>
                    @elseif ($umkm->status === 'rejected')
                        <div class="p-5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-900 text-xs mb-6 flex items-start gap-3">
                            <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center shrink-0 text-sm">
                                <i class="fas fa-times"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-sm text-rose-900">Pendaftaran Toko Ditolak</h4>
                                <p class="mt-1 text-rose-700 leading-relaxed">Data pengajuan toko belum memenuhi kualifikasi. Silakan perbaiki data toko dan daftarkan kembali.</p>
                                <a href="{{ route('penjual.umkm.create') }}" class="inline-flex items-center gap-1.5 mt-3 font-bold text-rose-700 hover:underline">
                                    <span>Daftarkan Ulang Toko</span>
                                    <i class="fas fa-arrow-right text-[10px]"></i>
                                </a>
                            </div>
                        </div>
                    @endif

                    <div class="flex items-center justify-between pb-5 border-b border-slate-100 mb-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                                <i class="fas fa-store"></i>
                            </div>
                            <div>
                                <h3 class="text-base font-extrabold text-slate-900 font-display">{{ $umkm->nama_toko }}</h3>
                                <p class="text-xs text-slate-400">Unit Usaha Mitra Resmi Indramayu</p>
                            </div>
                        </div>

                        @if($umkm->status === 'approved')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-extrabold border border-emerald-200">
                                <i class="fas fa-check-circle"></i> Mitra Aktif
                            </span>
                        @elseif($umkm->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-700 text-xs font-extrabold border border-amber-200">
                                <i class="fas fa-clock"></i> Pending Review
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-50 text-rose-700 text-xs font-extrabold border border-rose-200">
                                <i class="fas fa-ban"></i> Ditolak
                            </span>
                        @endif
                    </div>

                    <!-- Store Details Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 mb-6 text-xs">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 font-medium block text-[11px] mb-1">Nama Toko / Kebun</span>
                            <p class="font-extrabold text-slate-900 text-sm">{{ $umkm->nama_toko }}</p>
                        </div>

                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="text-slate-400 font-medium block text-[11px] mb-1">Kontak Resmi Toko</span>
                            <p class="font-extrabold text-slate-900 text-sm">{{ $umkm->no_telp ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Store Address -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 mb-5 text-xs">
                        <span class="text-slate-400 font-medium block text-[11px] mb-1.5">
                            <i class="fas fa-map-location-dot mr-1"></i> Alamat Sentra Kebun / Gudang Pengiriman
                        </span>
                        <p class="font-bold text-slate-800 leading-relaxed">{{ $umkm->alamat ?: 'Alamat belum diatur' }}</p>
                    </div>

                    <!-- Store Description -->
                    <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 text-xs">
                        <span class="text-slate-400 font-medium block text-[11px] mb-1.5">
                            <i class="fas fa-align-left mr-1"></i> Deskripsi & Profil Usaha
                        </span>
                        <p class="text-slate-600 leading-relaxed">{{ $umkm->deskripsi ?: 'Tidak ada deskripsi toko.' }}</p>
                    </div>

                @else
                    <!-- Empty Store -->
                    <div class="text-center py-12">
                        <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto mb-4">
                            <i class="fas fa-store-slash"></i>
                        </div>
                        <h4 class="text-base font-extrabold text-slate-900 font-display">Toko Belum Didaftarkan</h4>
                        <p class="text-xs text-slate-500 mt-1 max-w-md mx-auto leading-relaxed mb-6">
                            Lengkapi pendaftaran toko Anda untuk mulai memasarkan produk mangga Indramayu dan menerima pesanan dari seluruh Indonesia.
                        </p>
                        <a href="{{ route('penjual.umkm.create') }}" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-xs inline-flex items-center gap-2">
                            <i class="fas fa-plus"></i>
                            <span>Daftarkan Toko Sekarang</span>
                        </a>
                    </div>
                @endif

            </div>
        </div>

    </div>

</div>
@endsection
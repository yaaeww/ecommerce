@extends('layouts.app')

@section('page_title', 'Detail Toko UMKM')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Profil Toko Mitra</h2>
            <p class="text-xs text-slate-500 mt-0.5">Informasi legalitas, pemilik, dan katalog komoditas UMKM</p>
        </div>
        <a 
            href="{{ route('admin.umkm.index') }}" 
            class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition shadow-sm"
        >
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <!-- Store Profile Card -->
    <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-sm">
        <div class="flex flex-col sm:flex-row items-start gap-6 pb-6 border-b border-slate-100">
            <div class="w-20 h-20 rounded-2xl bg-slate-100 border border-slate-200 p-2 flex items-center justify-center shrink-0">
                @if($umkm->logo && file_exists(public_path('storage/' . $umkm->logo)))
                    <img src="{{ asset('storage/' . $umkm->logo) }}" class="w-full h-full object-contain rounded-xl" alt="{{ $umkm->nama_toko }}">
                @else
                    <i class="fas fa-store text-slate-400 text-3xl"></i>
                @endif
            </div>

            <div class="space-y-2 flex-1">
                <div class="flex flex-wrap items-center gap-3">
                    <h3 class="text-xl font-extrabold text-slate-900 font-display">{{ $umkm->nama_toko }}</h3>
                    @if($umkm->status === 'approved')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                            Disetujui
                        </span>
                    @elseif($umkm->status === 'pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">
                            Menunggu Verifikasi
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-50 text-rose-700 border border-rose-200">
                            Ditolak
                        </span>
                    @endif
                </div>

                <p class="text-xs text-slate-500 leading-relaxed">{{ $umkm->deskripsi ?? 'Belum ada deskripsi toko.' }}</p>

                <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-slate-500 pt-2">
                    <span class="flex items-center gap-1.5"><i class="fas fa-user-tie text-brand-600"></i> {{ $umkm->user->name ?? 'Pemilik' }}</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-envelope text-brand-600"></i> {{ $umkm->user->email ?? '-' }}</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-phone text-brand-600"></i> {{ $umkm->no_telp ?? '-' }}</span>
                    <span class="flex items-center gap-1.5"><i class="fas fa-location-dot text-brand-600"></i> {{ $umkm->alamat ?? 'Indramayu' }}</span>
                </div>
            </div>
        </div>

        <!-- Products Section -->
        <div class="pt-6">
            <div class="flex items-center justify-between mb-4">
                <h4 class="text-sm font-bold uppercase tracking-wider text-slate-700">Daftar Produk Toko Ini</h4>
                <span class="text-xs font-bold text-slate-500">{{ $umkm->produks->count() }} Produk</span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @forelse($umkm->produks as $produk)
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-white border border-slate-200 flex items-center justify-center shrink-0 overflow-hidden">
                                @if($produk->gambar && file_exists(public_path('storage/' . $produk->gambar)))
                                    <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-full h-full object-cover" alt="{{ $produk->nama }}">
                                @else
                                    <i class="fas fa-box-open text-brand-600 text-sm"></i>
                                @endif
                            </div>
                            <div>
                                <h5 class="font-bold text-xs text-slate-900 line-clamp-1">{{ $produk->nama }}</h5>
                                <p class="text-[11px] text-brand-600 font-extrabold">Rp{{ number_format($produk->harga, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <span class="text-[11px] font-semibold text-slate-500 shrink-0">Stok: {{ $produk->stok }}</span>
                    </div>
                @empty
                    <div class="col-span-2 text-center py-8 text-slate-400 text-xs">
                        Toko ini belum menambahkan produk.
                    </div>
                @endforelse
            </div>
        </div>
    </div>

</div>
@endsection
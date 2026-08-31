@extends('layouts.app')

@section('page_title', 'Produk UMKM')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Produk Toko {{ $umkm->nama_toko }}</h2>
            <p class="text-xs text-slate-500 mt-0.5">Pemilik: <span class="font-bold text-slate-700">{{ $umkm->user->name ?? 'Mitra' }}</span> • {{ $umkm->alamat ?? 'Indramayu' }}</p>
        </div>
        <a 
            href="{{ route('admin.umkm.index') }}" 
            class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition shadow-sm self-start"
        >
            <i class="fas fa-arrow-left mr-1"></i> Kembali ke Daftar UMKM
        </a>
    </div>

    <!-- Products Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        @forelse ($products as $produk)
            <div class="card bg-white border border-slate-200/80 shadow-sm overflow-hidden flex flex-col justify-between hover:border-brand-300 transition">
                <div>
                    <div class="h-44 bg-slate-100 flex items-center justify-center overflow-hidden relative">
                        @if ($produk->gambar && file_exists(public_path('storage/' . $produk->gambar)))
                            <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-full h-full object-cover" alt="{{ $produk->nama }}">
                        @else
                            <i class="fas fa-box-open text-brand-600 text-3xl"></i>
                        @endif
                        <span class="absolute top-2.5 right-2.5 px-2 py-0.5 rounded-md bg-white/90 backdrop-blur-sm text-[10px] font-bold text-slate-700 border border-slate-200">
                            Stok: {{ $produk->stok }}
                        </span>
                    </div>

                    <div class="p-4 space-y-2">
                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                            {{ $produk->kategori->nama ?? 'Kategori' }}
                        </span>
                        <h4 class="font-bold text-xs text-slate-900 line-clamp-1">{{ $produk->nama }}</h4>
                        <p class="text-[11px] text-slate-400 line-clamp-2">{{ $produk->deskripsi }}</p>
                    </div>
                </div>

                <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="font-extrabold text-sm text-brand-600">Rp{{ number_format($produk->harga, 0, ',', '.') }}</span>
                    <a href="{{ route('pembeli.produk.show', $produk->id) }}" target="_blank" class="text-xs font-bold text-slate-500 hover:text-brand-600 transition">
                        Detail <i class="fas fa-arrow-up-right-from-square text-[10px]"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full card p-12 bg-white text-center text-slate-400 text-xs">
                <i class="fas fa-boxes-stacked text-3xl mb-2 block"></i>
                Toko ini belum memiliki produk terdaftar.
            </div>
        @endforelse
    </div>

</div>
@endsection
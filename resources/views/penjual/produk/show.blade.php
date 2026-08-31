@extends('layouts.app')

@section('page_title', 'Detail Produk')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Detail Produk</h2>
            <p class="text-sm text-slate-500 mt-1">Informasi lengkap tentang produk Anda</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('penjual.produk.edit', $produk->id) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-sm rounded-xl transition shadow-sm">
                <i class="fas fa-edit"></i> Edit
            </a>
            <a href="{{ route('penjual.produk.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-50 hover:bg-slate-100 text-slate-700 font-bold text-sm rounded-xl transition border border-slate-200">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
        </div>
    </div>

    <!-- Product Info Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Image -->
        <div class="lg:col-span-5">
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden p-3">
                <div class="aspect-square bg-slate-100 rounded-xl overflow-hidden relative">
                    @if($produk->gambar)
                        <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-slate-300">
                            <i class="fas fa-image fa-5x"></i>
                        </div>
                    @endif

                    @php
                        $hargaSetelahDiskon = $produk->harga;
                        $diskon = $produk->diskon;
                        $today = \Carbon\Carbon::now();
                        $hasDiskon = $diskon && $today->between($diskon->tanggal_mulai, $diskon->tanggal_berakhir);

                        if ($hasDiskon) {
                            $hargaSetelahDiskon = round($produk->harga * (1 - ($diskon->persen_diskon / 100)), 2);
                        }
                    @endphp

                    @if($hasDiskon)
                        <div class="absolute top-4 right-4 bg-rose-500 text-white text-sm font-bold px-3 py-1.5 rounded-lg shadow-sm">
                            <i class="fas fa-tag mr-1"></i>{{ $diskon->persen_diskon }}% OFF
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Right: Details -->
        <div class="lg:col-span-7">
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden p-6 sm:p-8 flex flex-col h-full">
                
                <h1 class="text-3xl font-extrabold text-slate-900 font-display mb-2">{{ $produk->nama }}</h1>
                <div class="flex items-center gap-3 mb-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg bg-brand-50 text-brand-700 text-xs font-bold border border-brand-200">
                        <i class="fas fa-tags"></i> {{ $produk->kategoriProduk->nama ?? 'Tidak Ada Kategori' }}
                    </span>
                    <span class="flex items-center gap-1 text-sm text-slate-500 font-medium">
                        <i class="fas fa-star text-amber-400"></i> {{ number_format($produk->rating ?? 0, 1) }} / 5.0
                    </span>
                    <span class="flex items-center gap-1 text-sm text-slate-500 font-medium">
                        <i class="fas fa-eye"></i> {{ $produk->views ?? 0 }} views
                    </span>
                </div>

                <div class="space-y-4 flex-1">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div>
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Harga Produk</p>
                            @if($hasDiskon)
                                <div class="flex items-center gap-3">
                                    <span class="text-2xl font-bold text-rose-600 font-display">Rp{{ number_format($hargaSetelahDiskon, 0, ',', '.') }}</span>
                                    <span class="text-sm text-slate-400 line-through">Rp{{ number_format($produk->harga, 0, ',', '.') }}</span>
                                </div>
                            @else
                                <span class="text-2xl font-bold text-brand-700 font-display">Rp{{ number_format($produk->harga, 0, ',', '.') }}</span>
                            @endif
                        </div>
                        <div class="sm:text-right border-t sm:border-t-0 sm:border-l border-slate-200 pt-4 sm:pt-0 sm:pl-6">
                            <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Stok Tersedia</p>
                            <span class="text-xl font-bold text-slate-900">{{ $produk->stok }} <span class="text-sm font-medium text-slate-500">unit</span></span>
                        </div>
                    </div>

                    @if($hasDiskon)
                        <div class="p-3 rounded-xl bg-rose-50 border border-rose-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-white flex items-center justify-center text-rose-500 shrink-0">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-rose-700">Periode Diskon Berlangsung</p>
                                <p class="text-[11px] text-rose-600">Berlaku dari {{ \Carbon\Carbon::parse($diskon->tanggal_mulai)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($diskon->tanggal_berakhir)->format('d M Y') }}</p>
                            </div>
                        </div>
                    @endif

                    <div class="mt-6">
                        <h3 class="text-sm font-bold text-slate-900 mb-2">Deskripsi Produk</h3>
                        <div class="prose prose-sm prose-slate max-w-none text-slate-600 leading-relaxed bg-white border border-slate-100 rounded-xl p-4">
                            {!! nl2br(e($produk->deskripsi ?? 'Tidak ada deskripsi untuk produk ini.')) !!}
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100 flex gap-3">
                    <form action="{{ route('penjual.produk.destroy', $produk->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus produk ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-sm rounded-xl transition border border-rose-200">
                            <i class="fas fa-trash"></i> Hapus
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- Ulasan Section -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden mt-8">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900"><i class="fas fa-comments text-brand-500 me-2"></i>Ulasan Pelanggan</h3>
            <span class="inline-flex items-center px-2 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">
                {{ $ulasan->count() }} Ulasan
            </span>
        </div>

        <div class="p-6">
            @if($ulasan->count())
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($ulasan as $u)
                        <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 hover:border-slate-200 transition">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-brand-100 text-brand-700 font-bold flex items-center justify-center shrink-0">
                                    {{ strtoupper(substr($u->user->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center justify-between mb-1">
                                        <h6 class="font-bold text-sm text-slate-900">{{ $u->user->name ?? 'Pelanggan' }}</h6>
                                        <span class="text-[11px] text-slate-400">{{ $u->created_at->format('d M Y') }}</span>
                                    </div>
                                    <div class="flex items-center gap-1 mb-2 text-amber-400 text-xs">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <i class="fas fa-star{{ $i <= $u->bintang ? '' : '-half-alt' }}"></i>
                                        @endfor
                                    </div>
                                    <p class="text-sm text-slate-600 leading-relaxed">
                                        {{ $u->ulasan ?? 'Tidak ada teks ulasan.' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 flex flex-col items-center justify-center text-center">
                    <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-comment-slash text-2xl text-slate-300"></i>
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-1">Belum Ada Ulasan</h4>
                    <p class="text-sm text-slate-500">Produk ini belum menerima ulasan dari pelanggan.</p>
                </div>
            @endif
        </div>
    </div>

</div>
@endsection

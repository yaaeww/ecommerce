@extends('layouts.app')

@section('page_title', 'Daftar Produk')

@section('content')
<div class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Daftar Produk Saya</h2>
            <p class="text-sm text-slate-500 mt-1">Kelola katalog produk toko Anda</p>
        </div>
        <a href="{{ route('penjual.produk.create') }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-xl transition shadow-sm hover:shadow">
            <i class="fas fa-plus"></i>
            Tambah Produk
        </a>
    </div>

    @if (session('success'))
    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center gap-3">
        <i class="fas fa-check-circle text-emerald-500 text-xl"></i>
        <p class="font-bold text-sm">{{ session('success') }}</p>
    </div>
    @endif

    <!-- Content Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
        
        <!-- Controls: Search & Filter placeholder if needed -->
        <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
            <div class="text-sm font-bold text-slate-500">
                Total: <span class="text-slate-900">{{ $produks->total() }}</span> produk
            </div>
            <!-- Pagination summary can go here -->
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @forelse($produks as $produk)
                    <div class="group bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition duration-300 hover:border-brand-300 flex flex-col h-full">
                        <div class="relative aspect-video sm:aspect-square overflow-hidden bg-slate-100">
                            @if ($produk->gambar)
                                <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500" alt="{{ $produk->nama }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-300">
                                    <i class="fas fa-image fa-3x"></i>
                                </div>
                            @endif

                            {{-- Badge diskon jika ada --}}
                            @if ($produk->diskon && now()->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir))
                                <div class="absolute top-3 right-3 bg-rose-500 text-white text-xs font-bold px-2 py-1 rounded-lg shadow-sm">
                                    <i class="fas fa-tag mr-1"></i>{{ $produk->diskon->persen_diskon }}% OFF
                                </div>
                            @endif
                        </div>

                        <div class="p-4 flex-1 flex flex-col">
                            <h3 class="font-bold text-slate-900 text-base mb-2 line-clamp-2">{{ $produk->nama }}</h3>
                            
                            <div class="mb-4">
                                @if ($produk->diskon && now()->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir))
                                    <div class="text-xs text-slate-400 line-through mb-0.5">
                                        Rp {{ number_format($produk->harga, 0, ',', '.') }}
                                    </div>
                                    <div class="text-lg font-bold text-rose-600 font-display">
                                        Rp {{ number_format($produk->harga_setelah_diskon, 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-lg font-bold text-brand-700 font-display">
                                        Rp {{ number_format($produk->harga, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>

                            <div class="flex items-center gap-4 text-xs text-slate-500 font-medium mb-4 mt-auto pt-4 border-t border-slate-100">
                                <span class="flex items-center gap-1.5"><i class="fas fa-box text-slate-400"></i> Stok: {{ $produk->stok }}</span>
                                <span class="flex items-center gap-1.5"><i class="fas fa-eye text-slate-400"></i> View: {{ $produk->views ?? 0 }}</span>
                            </div>

                            <div class="flex items-center gap-2 mt-auto">
                                <a href="{{ route('penjual.produk.show', $produk->id) }}" class="flex-1 py-2 bg-slate-50 hover:bg-slate-100 text-slate-700 text-center rounded-xl text-xs font-bold border border-slate-200 transition" title="Detail">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('penjual.produk.edit', $produk->id) }}" class="flex-1 py-2 bg-amber-50 hover:bg-amber-100 text-amber-700 text-center rounded-xl text-xs font-bold border border-amber-200 transition" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('penjual.produk.destroy', $produk->id) }}" method="POST" class="flex-1 inline-flex" onsubmit="return confirm('Yakin mau hapus produk {{ $produk->nama }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-full py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-xl text-xs font-bold border border-rose-200 transition" title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 flex flex-col items-center justify-center text-center">
                        <div class="w-20 h-20 bg-slate-50 rounded-full flex items-center justify-center mb-4">
                            <i class="fas fa-box-open text-3xl text-slate-300"></i>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 mb-1">Belum Ada Produk</h3>
                        <p class="text-slate-500 text-sm mb-6 max-w-sm">Mulai jualan dengan menambahkan produk pertama Anda di toko ini!</p>
                        <a href="{{ route('penjual.produk.create') }}" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-xl transition shadow-sm">
                            <i class="fas fa-plus mr-2"></i> Tambah Produk Pertama
                        </a>
                    </div>
                @endforelse
            </div>

            @if($produks->hasPages())
                <div class="mt-8 pt-6 border-t border-slate-100 flex justify-center">
                    {{ $produks->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

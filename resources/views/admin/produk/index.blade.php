@extends('layouts.app')

@section('page_title', 'Katalog Produk')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Katalog Produk Marketplace</h2>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh produk yang dipublikasikan oleh mitra UMKM dan petani</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-sm">
                Total: <span class="text-brand-600 font-extrabold">{{ $totalProduk }}</span> Produk
            </span>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs text-emerald-800 shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Search & Filter Card -->
    <div class="card p-4 bg-white border border-slate-200/80 shadow-sm rounded-2xl">
        <form method="GET" action="{{ route('admin.produk.index') }}" class="flex flex-col sm:flex-row items-center gap-3">
            <div class="flex-1 w-full relative">
                <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama buah/produk, toko UMKM, atau deskripsi..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500"
                >
            </div>

            <div class="w-full sm:w-64">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-layer-group text-xs"></i>
                    </div>
                    <select name="kategori_id" onchange="this.form.submit()" class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer">
                        <option value="">Semua Kategori Produk</option>
                        @foreach($kategoris as $kat)
                            <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <button type="submit" class="flex-1 sm:flex-none px-5 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-2xl transition shadow-sm cursor-pointer">
                    Filter
                </button>
                @if($search || $kategoriId)
                    <a href="{{ route('admin.produk.index') }}" class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 rounded-2xl transition border border-rose-200" title="Reset">
                        <i class="fas fa-rotate-left text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 font-display">Daftar Komoditas & Produk</h3>
            <span class="text-xs font-bold text-slate-400">{{ $produks->total() }} Produk Ditampilkan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-14">No</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Toko UMKM</th>
                        <th>Harga & Stok</th>
                        <th>Rating</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($produks as $index => $produk)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="text-slate-400 font-bold align-middle">
                                {{ $produks->firstItem() + $index }}
                            </td>
                            <td class="align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 p-0.5 flex items-center justify-center shrink-0 overflow-hidden shadow-xs">
                                        @if($produk->gambar && file_exists(public_path('storage/' . $produk->gambar)))
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-full h-full object-cover rounded-lg" alt="{{ $produk->nama }}">
                                        @else
                                            <i class="fas fa-box-open text-brand-600 text-sm"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-xs text-slate-900 truncate max-w-xs">{{ $produk->nama }}</h4>
                                        <p class="text-[10px] text-slate-400 truncate max-w-xs">{{ $produk->deskripsi }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="inline-block px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $produk->kategori->nama ?? 'Umum' }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <strong class="font-bold text-slate-900 block truncate max-w-[150px]">{{ $produk->umkm->nama_toko ?? 'Petani Mitra' }}</strong>
                                <span class="text-[10px] text-slate-400 block">{{ $produk->umkm->alamat ?? 'Indramayu' }}</span>
                            </td>
                            <td class="align-middle">
                                <p class="font-extrabold text-xs text-slate-900">Rp{{ number_format($produk->harga, 0, ',', '.') }}</p>
                                <span class="text-[10px] font-semibold {{ $produk->stok > 0 ? 'text-slate-500' : 'text-rose-500' }}">
                                    Stok: {{ $produk->stok }}
                                </span>
                            </td>
                            <td class="align-middle">
                                <div class="flex items-center gap-1 text-amber-500 text-xs font-bold">
                                    <i class="fas fa-star text-[10px]"></i>
                                    <span>{{ number_format($produk->rating ?? 5.0, 1) }}</span>
                                </div>
                            </td>
                            <td class="align-middle text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('pembeli.produk.show', $produk->id) }}" target="_blank" class="p-2 text-slate-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="Lihat di Halaman Pembeli">
                                        <i class="fas fa-arrow-up-right-from-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.produk.destroy', $produk->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus produk ini dari marketplace?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Produk">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400 text-xs">
                                <i class="fas fa-boxes-stacked text-3xl text-slate-300 mb-2 block"></i>
                                Tidak ada produk sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produks->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $produks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
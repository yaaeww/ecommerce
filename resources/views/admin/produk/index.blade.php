@extends('layouts.app')

@section('page_title', 'Katalog Produk')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Katalog Produk Marketplace</h2>
            <p class="text-xs text-slate-500 mt-0.5">Seluruh produk yang dipublikasikan oleh mitra UMKM dan petani</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-sm">
                Total: <span class="text-brand-600 font-extrabold">{{ $produks->total() }}</span> Produk
            </span>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-16">No</th>
                        <th>Produk</th>
                        <th>Kategori</th>
                        <th>Toko UMKM</th>
                        <th>Harga & Stok</th>
                        <th>Rating</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($produks as $index => $produk)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="text-xs text-slate-400 font-bold">
                                {{ $produks->firstItem() + $index }}
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 p-1 flex items-center justify-center shrink-0 overflow-hidden">
                                        @if($produk->gambar && file_exists(public_path('storage/' . $produk->gambar)))
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" class="w-full h-full object-cover rounded-lg" alt="{{ $produk->nama }}">
                                        @else
                                            <i class="fas fa-box-open text-brand-600 text-sm"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-xs text-slate-900 line-clamp-1">{{ $produk->nama }}</h4>
                                        <p class="text-[11px] text-slate-400 line-clamp-1 max-w-xs">{{ $produk->deskripsi }}</p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="inline-block px-2.5 py-0.5 rounded-lg text-[10px] font-bold bg-slate-100 text-slate-700 border border-slate-200">
                                    {{ $produk->kategori->nama ?? 'Umum' }}
                                </span>
                            </td>
                            <td>
                                <p class="font-bold text-xs text-slate-800">{{ $produk->umkm->nama_toko ?? 'Petani Mitra' }}</p>
                                <p class="text-[10px] text-slate-400">{{ $produk->umkm->alamat ?? 'Indramayu' }}</p>
                            </td>
                            <td>
                                <p class="font-extrabold text-xs text-slate-900">Rp{{ number_format($produk->harga, 0, ',', '.') }}</p>
                                <span class="text-[11px] font-semibold {{ $produk->stok > 0 ? 'text-slate-500' : 'text-rose-500' }}">
                                    Stok: {{ $produk->stok }}
                                </span>
                            </td>
                            <td>
                                <div class="flex items-center gap-1 text-amber-500 text-xs font-bold">
                                    <i class="fas fa-star text-[10px]"></i>
                                    <span>{{ number_format($produk->rating ?? 5.0, 1) }}</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('pembeli.produk.show', $produk->id) }}" target="_blank" class="p-2 text-slate-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="Lihat di Toko">
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
                                <i class="fas fa-boxes-stacked text-3xl mb-2 block"></i>
                                Belum ada produk di marketplace.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($produks->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $produks->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
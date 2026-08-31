@extends('layouts.app')

@section('page_title', 'Kategori Produk')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Manajemen Kategori Produk</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola taksonomi komoditas mangga, pangan olahan, dan kerajinan UMKM</p>
        </div>
        <div class="flex items-center gap-3">
            <a 
                href="{{ route('admin.kategori.create') }}" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-sm hover:shadow"
            >
                <i class="fas fa-plus text-xs"></i> Tambah Kategori
            </a>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card p-4 sm:p-5 bg-white border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.kategori.index') }}" class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <i class="fas fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama kategori atau subkategori..." 
                    class="w-full pl-9 pr-8 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white transition"
                >
                @if($search)
                    <a href="{{ route('admin.kategori.index', ['filter' => $filter]) }}" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs" title="Hapus pencarian">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>

            <!-- Filter Type Pills / Selector -->
            <div class="flex items-center gap-2 flex-wrap">
                <a 
                    href="{{ route('admin.kategori.index', ['filter' => 'all', 'search' => $search]) }}" 
                    class="px-3 py-2 rounded-xl text-xs font-bold transition {{ $filter === 'all' ? 'bg-brand-50 text-brand-700 border border-brand-200' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}"
                >
                    Semua
                </a>
                <a 
                    href="{{ route('admin.kategori.index', ['filter' => 'induk', 'search' => $search]) }}" 
                    class="px-3 py-2 rounded-xl text-xs font-bold transition {{ $filter === 'induk' ? 'bg-brand-50 text-brand-700 border border-brand-200' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}"
                >
                    Hanya Induk ({{ $totalInduk }})
                </a>
                <a 
                    href="{{ route('admin.kategori.index', ['filter' => 'sub', 'search' => $search]) }}" 
                    class="px-3 py-2 rounded-xl text-xs font-bold transition {{ $filter === 'sub' ? 'bg-brand-50 text-brand-700 border border-brand-200' : 'bg-slate-50 text-slate-600 border border-slate-200 hover:bg-slate-100' }}"
                >
                    Hanya Sub ({{ $totalSub }})
                </a>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-24">Gambar</th>
                        <th>Nama Kategori & Hirarki</th>
                        <th>Induk / Status</th>
                        <th class="w-32 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @php
                        if (!function_exists('renderKategoriRow')) {
                            function renderKategoriRow($kategori, $level = 0)
                            {
                            $indentPx = $level * 28;
                            echo '<tr class="hover:bg-slate-50/70 transition">';
                            
                            // Image / Icon
                            echo '<td>';
                            echo '<div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 overflow-hidden shrink-0">';
                            if ($kategori->gambar && file_exists(public_path('storage/kategori/' . $kategori->gambar))) {
                                echo '<img src="' . asset('storage/kategori/' . $kategori->gambar) . '" class="w-full h-full object-cover" alt="' . e($kategori->nama) . '">';
                            } else {
                                echo '<i class="fas fa-layer-group text-brand-600 text-sm"></i>';
                            }
                            echo '</div>';
                            echo '</td>';

                            // Name with Indentation
                            echo '<td>';
                            echo '<div class="flex items-center gap-2" style="padding-left: ' . $indentPx . 'px;">';
                            if ($level > 0) {
                                echo '<i class="fas fa-arrow-turn-down text-slate-300 text-xs rotate-[270deg]"></i>';
                                echo '<span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Sub</span>';
                                echo '<span class="font-semibold text-xs text-slate-800">' . e($kategori->nama) . '</span>';
                            } else {
                                echo '<span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">Induk</span>';
                                echo '<span class="font-extrabold text-sm text-slate-900">' . e($kategori->nama) . '</span>';
                            }
                            echo '</div>';
                            echo '</td>';

                            // Parent Name
                            echo '<td>';
                            if ($kategori->parent) {
                                echo '<span class="text-xs text-slate-500 font-semibold flex items-center gap-1.5"><i class="fas fa-turn-up text-[10px] text-slate-400 rotate-90"></i>' . e($kategori->parent->nama) . '</span>';
                            } else {
                                echo '<span class="text-[11px] text-slate-400 font-medium">Kategori Utama (' . $kategori->children->count() . ' sub)</span>';
                            }
                            echo '</td>';

                            // Actions
                            echo '<td class="text-center">';
                            echo '<div class="flex items-center justify-center gap-2">';
                            echo '<a href="' . route('admin.kategori.edit', $kategori->id) . '" class="p-2 text-slate-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="Edit">';
                            echo '<i class="fas fa-pen-to-square text-xs"></i>';
                            echo '</a>';
                            echo '<form action="' . route('admin.kategori.destroy', $kategori->id) . '" method="POST" class="inline" onsubmit="return confirm(\'Yakin hapus kategori ' . addslashes($kategori->nama) . '?\')">';
                            echo csrf_field();
                            echo method_field('DELETE');
                            echo '<button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">';
                            echo '<i class="fas fa-trash-can text-xs"></i>';
                            echo '</button>';
                            echo '</form>';
                            echo '</div>';
                            echo '</td>';

                            echo '</tr>';

                            if (isset($kategori->children) && $kategori->children->isNotEmpty()) {
                                foreach ($kategori->children as $child) {
                                    renderKategoriRow($child, $level + 1);
                                }
                            }
                        }
                    }
                    @endphp

                    @forelse ($kategoris as $kategori)
                        @if ($isHierarchical)
                            {!! renderKategoriRow($kategori) !!}
                        @else
                            <tr class="hover:bg-slate-50/70 transition">
                                <td>
                                    <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-400 overflow-hidden shrink-0">
                                        @if ($kategori->gambar && file_exists(public_path('storage/kategori/' . $kategori->gambar)))
                                            <img src="{{ asset('storage/kategori/' . $kategori->gambar) }}" class="w-full h-full object-cover" alt="{{ $kategori->nama }}">
                                        @else
                                            <i class="fas fa-layer-group text-brand-600 text-sm"></i>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        @if($kategori->parent_id)
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600 border border-slate-200">Sub</span>
                                        @else
                                            <span class="inline-block px-1.5 py-0.5 rounded text-[10px] font-bold bg-brand-50 text-brand-700 border border-brand-200">Induk</span>
                                        @endif
                                        <span class="font-bold text-xs text-slate-900">{{ $kategori->nama }}</span>
                                    </div>
                                </td>
                                <td>
                                    @if($kategori->parent)
                                        <span class="text-xs text-slate-500 font-semibold flex items-center gap-1.5">
                                            <i class="fas fa-turn-up text-[10px] text-slate-400 rotate-90"></i>
                                            {{ $kategori->parent->nama }}
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 font-medium">Kategori Utama</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.kategori.edit', $kategori->id) }}" class="p-2 text-slate-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="Edit">
                                            <i class="fas fa-pen-to-square text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.kategori.destroy', $kategori->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin hapus kategori {{ addslashes($kategori->nama) }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus">
                                                <i class="fas fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-12 text-slate-400 text-xs">
                                <i class="fas fa-inbox text-3xl mb-2 block"></i>
                                Tidak ada kategori yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination Bar -->
        @if($kategoris->hasPages())
            <div class="p-4 border-t border-slate-100 flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    Menampilkan <span class="font-bold text-slate-800">{{ $kategoris->firstItem() ?? 0 }}</span> - <span class="font-bold text-slate-800">{{ $kategoris->lastItem() ?? 0 }}</span> dari <span class="font-bold text-slate-800">{{ $kategoris->total() }}</span> entri
                </p>
                <div>
                    {{ $kategoris->links() }}
                </div>
            </div>
        @endif
    </div>

</div>
@endsection
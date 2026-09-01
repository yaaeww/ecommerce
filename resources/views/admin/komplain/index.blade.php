@extends('layouts.app')

@section('page_title', 'Pusat Mediasi Komplain & Garansi Segar')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Pusat Mediasi Komplain & Retur</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola klaim garansi buah segar, investigasi unboxing, dan persetujuan pengembalian dana (refund)</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-rose-50 text-rose-700 text-xs font-bold border border-rose-200">
                <i class="fas fa-shield-halved mr-1"></i> Fresh Guarantee Policy
            </span>
        </div>
    </div>

    <!-- Quick Stats Metric Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="card bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Klaim</p>
                    <h3 class="text-2xl font-extrabold text-slate-900 mt-1 font-display">{{ $totalKomplain }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-lg">
                    <i class="fas fa-list-check"></i>
                </div>
            </div>
        </div>

        <div class="card bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-amber-500 uppercase tracking-wider">Perlu Tindakan</p>
                    <h3 class="text-2xl font-extrabold text-amber-600 mt-1 font-display">{{ $totalDiajukan }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
        </div>

        <div class="card bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-emerald-600 uppercase tracking-wider">Klaim Disetujui</p>
                    <h3 class="text-2xl font-extrabold text-emerald-600 mt-1 font-display">{{ $totalDisetujui }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
        </div>

        <div class="card bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-bold text-rose-500 uppercase tracking-wider">Klaim Ditolak</p>
                    <h3 class="text-2xl font-extrabold text-rose-600 mt-1 font-display">{{ $totalDitolak }}</h3>
                </div>
                <div class="w-11 h-11 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg">
                    <i class="fas fa-times-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter & Table Section -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex flex-col md:flex-row justify-between gap-4 items-center">
            <!-- Filter Status Tab -->
            <div class="flex flex-wrap gap-2">
                @php
                    $tabs = [
                        'semua' => 'Semua',
                        'diajukan' => 'Menunggu Verifikasi',
                        'diproses' => 'Sedang Mediasi',
                        'disetujui' => 'Disetujui (Refund)',
                        'ditolak' => 'Ditolak',
                    ];
                @endphp
                @foreach($tabs as $key => $label)
                    <a href="{{ route('admin.komplain.index', ['status' => $key, 'search' => request('search')]) }}" 
                       class="px-3 py-1.5 rounded-xl text-xs font-bold transition {{ $status === $key ? 'bg-slate-900 text-white shadow-2xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </div>

            <!-- Search Form -->
            <form method="GET" class="w-full md:w-72 relative">
                <input type="hidden" name="status" value="{{ $status }}">
                <input 
                    type="text" 
                    name="search" 
                    value="{{ request('search') }}"
                    placeholder="Cari pembeli, produk, deskripsi..." 
                    class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 border border-slate-200 text-xs focus:bg-white focus:border-brand-500 focus:outline-none"
                >
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </form>
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">ID & Waktu</th>
                        <th class="px-6 py-3.5">Pembeli</th>
                        <th class="px-6 py-3.5">Produk & Toko</th>
                        <th class="px-6 py-3.5">Jenis Kendala</th>
                        <th class="px-6 py-3.5">Solusi Diminta</th>
                        <th class="px-6 py-3.5">Status Mediasi</th>
                        <th class="px-6 py-3.5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($komplains as $k)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4 font-mono">
                                <span class="font-bold text-slate-900">#KMP-{{ $k->id }}</span>
                                <span class="block text-[10px] text-slate-400">{{ $k->created_at->format('d M Y, H:i') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $k->user->name ?? 'Pembeli' }}</div>
                                <span class="text-[11px] text-slate-500">{{ $k->order->phone ?? '-' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $k->order->produk->nama ?? 'Mangga' }}</div>
                                <span class="text-[11px] text-brand-600 font-medium">{{ $k->order->produk->umkm->nama_toko ?? 'Petani Mitra' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-lg bg-slate-100 font-semibold text-slate-700">
                                    {{ $k->label_tipe }}
                                </span>
                            </td>
                            <td class="px-6 py-4 font-extrabold uppercase text-slate-800">
                                {{ $k->solusi_diminta }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border {{ $k->badge_color }}">
                                    {{ $k->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('admin.komplain.show', $k->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold transition">
                                    <i class="fas fa-eye"></i> Investigasi
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-clipboard-check text-3xl mb-2 text-slate-300"></i>
                                <p class="font-bold text-slate-700">Tidak ada pengajuan komplain yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($komplains->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $komplains->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

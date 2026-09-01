@extends('layouts.app')

@section('page_title', 'Riwayat Komplain Garansi Buah')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Riwayat Komplain Garansi Segar</h2>
            <p class="text-xs text-slate-500 mt-1">Daftar permohonan klaim buah rusak, busuk, atau penggantian pesanan</p>
        </div>
        <a href="{{ route('pembeli.pesanan.dikirim') }}" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition">
            <i class="fas fa-box-open mr-1"></i> Pesanan Saya
        </a>
    </div>

    <!-- Table -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">ID Komplain</th>
                        <th class="px-6 py-3.5">Produk & Toko</th>
                        <th class="px-6 py-3.5">Jenis Masalah</th>
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
                                <div class="font-bold text-slate-900">{{ $k->order->produk->nama ?? 'Mangga' }}</div>
                                <span class="text-[11px] text-brand-600">{{ $k->order->produk->umkm->nama_toko ?? 'Petani Mitra' }}</span>
                            </td>
                            <td class="px-6 py-4 font-semibold text-slate-700">
                                {{ $k->label_tipe }}
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
                                <a href="{{ route('pembeli.komplain.show', $k->id) }}" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold transition">
                                    <i class="fas fa-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-slate-400">
                                <i class="fas fa-shield-halved text-3xl mb-2 text-slate-300"></i>
                                <p class="font-bold text-slate-700">Anda belum pernah mengajukan komplain.</p>
                                <p class="text-[11px] text-slate-400 mt-0.5">Seluruh pesanan Anda dikirim dengan garansi buah segar prima.</p>
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

@extends('layouts.app')

@section('page_title', 'Laporan Pendapatan')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Laporan & Rekap Pendapatan</h2>
            <p class="text-xs text-slate-500 mt-0.5">Periode Laporan: <span class="font-bold text-brand-600">{{ $periodeInfo }}</span></p>
        </div>
    </div>

    <!-- Filter Form Card -->
    <div class="card p-6 bg-white border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('admin.pendapatan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Bulan</label>
                <select name="bulan" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500">
                    <option value="">Semua Bulan</option>
                    @foreach($bulanList as $key => $namaBulan)
                        <option value="{{ $key }}" {{ $bulan == $key ? 'selected' : '' }}>
                            {{ $namaBulan }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Tahun</label>
                <select name="tahun" class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500">
                    <option value="">Semua Tahun</option>
                    @foreach($tahunList as $tahunOption)
                        <option value="{{ $tahunOption }}" {{ $tahun == $tahunOption ? 'selected' : '' }}>
                            {{ $tahunOption }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2 pb-2">
                <input 
                    type="checkbox" 
                    id="minggu" 
                    name="minggu" 
                    value="1" 
                    {{ $filterMinggu ? 'checked' : '' }} 
                    class="rounded text-brand-600 focus:ring-brand-500"
                >
                <label for="minggu" class="text-xs font-bold text-slate-700 cursor-pointer">
                    Hanya Minggu Ini
                </label>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition shadow-sm flex items-center justify-center gap-2">
                    <i class="fas fa-filter text-xs"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.pendapatan.index') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- 2 Financial Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
        
        <!-- Card 1: Total GMV -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Penjualan Kotor (GMV)</span>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base">
                    <i class="fas fa-cart-shopping"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
            </p>
            <p class="text-xs font-semibold text-slate-400 mt-2">Akumulasi seluruh pesanan selesai pada periode terpilih</p>
        </div>

        <!-- Card 2: Pendapatan Admin / Komisi Platform -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm border-l-4 border-l-brand-600">
            <div class="flex items-center justify-between mb-4">
                <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Estimasi Komisi Platform (20%)</span>
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-base">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-brand-600 font-display tracking-tight">
                Rp{{ number_format($pendapatanAdmin, 0, ',', '.') }}
            </p>
            <p class="text-xs font-semibold text-slate-400 mt-2">Bagi hasil operasional platform marketplace</p>
        </div>

    </div>

    <!-- Table: Rekap Per Toko -->
    <div class="card bg-white border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-bold text-slate-900">Rekapitulasi Penjualan Per Toko UMKM</h3>
            <span class="text-xs text-slate-400 font-semibold">{{ $rekapPerToko->count() }} Toko Berkontribusi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-16">Peringkat</th>
                        <th>Nama Toko UMKM</th>
                        <th>Total Penjualan Toko</th>
                        <th>Komisi Admin (20%)</th>
                        <th>Pendapatan Bersih Toko (80%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rekapPerToko as $index => $rekap)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="text-center font-bold text-xs">
                                @if($index == 0)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-100 text-amber-800 text-[11px]">🥇</span>
                                @elseif($index == 1)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-slate-200 text-slate-700 text-[11px]">🥈</span>
                                @elseif($index == 2)
                                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-amber-50 text-amber-700 text-[11px]">🥉</span>
                                @else
                                    <span class="text-slate-400">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-store text-slate-400 text-xs"></i>
                                    <span class="font-bold text-xs text-slate-900">{{ $rekap->nama_toko }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="font-extrabold text-xs text-slate-900">
                                    Rp{{ number_format($rekap->total_penjualan, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="font-bold text-xs text-brand-600">
                                    Rp{{ number_format($rekap->total_penjualan * 0.2, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="font-bold text-xs text-emerald-600">
                                    Rp{{ number_format($rekap->total_penjualan * 0.8, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-12 text-slate-400 text-xs">
                                <i class="fas fa-wallet text-3xl mb-2 block"></i>
                                Tidak ada data transaksi selesai pada periode ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
@extends('layouts.app')

@section('page_title', 'Ringkasan Pendapatan')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Ringkasan Pendapatan</h2>
            <p class="text-sm text-slate-500 mt-1">Laporan pendapatan penjualan berdasarkan produk</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('penjual.pendapatan.export.summary.excel', request()->all()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-white font-bold text-sm rounded-xl transition shadow-sm" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Mengekspor...'; setTimeout(() => { this.innerHTML='<i class=\'fas fa-file-excel\'></i> Export Excel'; }, 3000);">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <a href="{{ route('penjual.pendapatan.export.summary.pdf', request()->all()) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm rounded-xl transition shadow-sm" onclick="this.innerHTML='<i class=\'fas fa-spinner fa-spin\'></i> Mengekspor...'; setTimeout(() => { this.innerHTML='<i class=\'fas fa-file-pdf\'></i> Export PDF'; }, 3000);">
                <i class="fas fa-file-pdf"></i> Export PDF
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-4 sm:p-6">
        <form method="GET" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex flex-col sm:flex-row sm:items-center gap-3 w-full sm:w-auto">
                <label for="filter" class="text-sm font-bold text-slate-700 whitespace-nowrap">
                    <i class="fas fa-filter text-slate-400 mr-2"></i>Filter Waktu:
                </label>
                <div class="relative w-full sm:w-48">
                    <select name="filter" id="filter" class="w-full pl-4 pr-10 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-sm font-medium text-slate-700 focus:bg-white focus:border-brand-500 focus:ring-4 focus:ring-brand-500/10 transition appearance-none cursor-pointer" onchange="this.form.submit()">
                        <option value="minggu" {{ request('filter') == 'minggu' ? 'selected' : '' }}>Minggu Ini</option>
                        <option value="bulan" {{ request('filter', 'bulan') == 'bulan' ? 'selected' : '' }}>Bulan Ini</option>
                        <option value="tahun" {{ request('filter') == 'tahun' ? 'selected' : '' }}>Tahun Ini</option>
                    </select>
                    <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                        <i class="fas fa-chevron-down text-xs"></i>
                    </div>
                </div>
            </div>
            
            <div class="flex items-center gap-2 text-sm">
                @php
                    $periodText = [
                        'minggu' => 'Minggu Ini',
                        'bulan' => 'Bulan Ini', 
                        'tahun' => 'Tahun Ini'
                    ][request('filter', 'bulan')];
                @endphp
                <div class="w-8 h-8 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
                    <i class="fas fa-calendar-alt"></i>
                </div>
                <span class="text-slate-500">Menampilkan data: <span class="font-bold text-slate-900">{{ $periodText }}</span></span>
            </div>
        </form>
    </div>

    <!-- Quick Stats & Previous Month Info -->
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <!-- Pendapatan Bulan Lalu -->
        <div class="lg:col-span-1">
            <div class="card bg-slate-50 border border-slate-200/80 shadow-sm rounded-2xl p-6 h-full flex flex-col justify-center">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 rounded-xl bg-slate-200 text-slate-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-history"></i>
                    </div>
                    <h3 class="text-sm font-bold text-slate-600 leading-tight">Pendapatan Bulan Lalu</h3>
                </div>
                <div class="mt-2">
                    <p class="text-2xl font-bold text-slate-900 font-display">
                        {{ isset($totalPendapatanBulanLalu) ? 'Rp ' . number_format($totalPendapatanBulanLalu, 0, ',', '.') : '-' }}
                    </p>
                </div>
            </div>
        </div>

        <!-- Current Period Stats -->
        @if(!$pendapatanPerProduk->isEmpty())
            @php
                $totalPendapatan = $pendapatanPerProduk->sum('total_pendapatan');
                $totalTerjual = $pendapatanPerProduk->sum('total_terjual');
                $totalProduk = $pendapatanPerProduk->count();
            @endphp
            
            <div class="lg:col-span-3 grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 pointer-events-none"></div>
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 relative z-10">Total Pendapatan</h3>
                    <p class="text-3xl font-bold text-emerald-600 font-display relative z-10">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
                </div>
                
                <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-brand-50 rounded-full opacity-50 pointer-events-none"></div>
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 relative z-10">Total Terjual</h3>
                    <p class="text-3xl font-bold text-brand-700 font-display relative z-10">{{ $totalTerjual }} <span class="text-sm text-slate-500 font-medium">unit</span></p>
                </div>

                <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6 relative overflow-hidden">
                    <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 pointer-events-none"></div>
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-2 relative z-10">Produk Terjual</h3>
                    <p class="text-3xl font-bold text-blue-700 font-display relative z-10">{{ $totalProduk }} <span class="text-sm text-slate-500 font-medium">item</span></p>
                </div>
            </div>
        @endif
    </div>

    <!-- Data Table -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden mt-6">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-lg font-bold text-slate-900">Rincian per Produk</h3>
        </div>
        
        @if($pendapatanPerProduk->isEmpty())
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-2xl text-slate-300"></i>
                </div>
                <h4 class="text-base font-bold text-slate-900 mb-1">Belum Ada Pendapatan</h4>
                <p class="text-sm text-slate-500">Belum ada pendapatan dari produk pada periode ini.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 whitespace-nowrap w-16">No</th>
                            <th class="px-6 py-4 whitespace-nowrap">Nama Produk</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Terjual</th>
                            <th class="px-6 py-4 whitespace-nowrap text-right">Total Pendapatan</th>
                            <th class="px-6 py-4 whitespace-nowrap text-center">Sisa Stok</th>
                            <th class="px-6 py-4 whitespace-nowrap text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($pendapatanPerProduk as $index => $item)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="px-6 py-4 font-bold text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-bold text-slate-900">
                                    {{ $item->nama_produk }}
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    {{ $item->total_terjual ?? 0 }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="inline-flex px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg font-bold">
                                        Rp {{ number_format($item->total_pendapatan ?? 0, 0, ',', '.') }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center font-medium">
                                    <span class="{{ $item->stok < 5 ? 'text-rose-600' : 'text-slate-600' }}">
                                        {{ $item->stok }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('penjual.pendapatan.detail', $item->id) }}" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-brand-50 hover:bg-brand-100 text-brand-700 font-bold text-xs rounded-lg transition">
                                        <i class="fas fa-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

</div>
@endsection

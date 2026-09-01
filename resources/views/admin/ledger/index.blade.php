@extends('layouts.app')

@section('page_title', 'Buku Besar Platform & Escrow Ledger')

@section('content')
<div class="max-w-7xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Buku Besar & Monitoring Saldo Escrow</h2>
            <p class="text-xs text-slate-500 mt-1">Transparansi arus kas masuk Midtrans, rekening penampung (Escrow), bagi hasil petani, dan pendapatan komisi {{ $komisiPersen }}%</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                <i class="fas fa-lock text-emerald-600 mr-1"></i> Escrow Protection Active
            </span>
        </div>
    </div>

    <!-- Escrow & Financial Overview 4 Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Kas Masuk Midtrans -->
        <div class="card bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Kas Masuk (Gross)</p>
                    <h3 class="text-xl font-extrabold text-slate-900 mt-1 font-display">Rp {{ number_format($totalGrossInflow, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-base">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-3">Akumulasi seluruh transaksi lunas Midtrans</p>
        </div>

        <!-- Dana Mengendap di Escrow (In-Transit) -->
        <div class="card bg-amber-500 text-white p-5 rounded-2xl shadow-md relative overflow-hidden">
            <div class="relative z-10 flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-extrabold text-amber-100 uppercase tracking-wider">Saldo Mengendap di Escrow</p>
                    <h3 class="text-xl font-extrabold text-white mt-1 font-display">Rp {{ number_format($totalEscrowHolding, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/20 backdrop-blur-xs text-white flex items-center justify-center text-base">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
            <p class="text-[11px] text-amber-100/90 mt-3 relative z-10">Pesanan dalam pengiriman (Belum diterima)</p>
        </div>

        <!-- Total Profit Bersih Platform (20%) -->
        <div class="card bg-slate-900 text-white p-5 rounded-2xl shadow-md">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Profit Bersih Platform ({{ $komisiPersen }}%)</p>
                    <h3 class="text-xl font-extrabold text-emerald-400 mt-1 font-display">Rp {{ number_format($totalPlatformRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-white/10 text-emerald-400 flex items-center justify-center text-base">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-3">+Rp {{ number_format($potensiKomisiEscrow, 0, ',', '.') }} potensi komisi di escrow</p>
        </div>

        <!-- Kewajiban Hutang ke Petani (Siap Tarik) -->
        <div class="card bg-white border border-slate-200/80 p-5 rounded-2xl shadow-sm">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Kewajiban Payout Toko</p>
                    <h3 class="text-xl font-extrabold text-brand-600 mt-1 font-display">Rp {{ number_format($sisaKewajibanToko, 0, ',', '.') }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-base">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-3">Saldo bersih petani yang belum ditarik</p>
        </div>

    </div>

    <!-- Breakdown Saldo per Toko Mitra -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-store text-brand-600"></i> Rekapitulasi Escrow & Saldo per Toko Mitra UMKM
            </h4>
            <span class="text-xs text-slate-400 font-medium">{{ $umkms->count() }} Toko Terdaftar</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3.5">Nama Kebun / Toko</th>
                        <th class="px-6 py-3.5 text-right">Omzet Kotor</th>
                        <th class="px-6 py-3.5 text-right">Saldo Escrow (In-Transit)</th>
                        <th class="px-6 py-3.5 text-right">Hak Bersih Settled ({{ $tokoPersen }}%)</th>
                        <th class="px-6 py-3.5 text-right">Sudah Dicairkan</th>
                        <th class="px-6 py-3.5 text-right">Saldo Siap Tarik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($umkms as $u)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-900">{{ $u->umkm->nama_toko }}</div>
                                <span class="text-[11px] text-slate-400">{{ $u->umkm->user->name ?? '-' }} ({{ $u->umkm->no_telp ?? '-' }})</span>
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-slate-800">
                                Rp {{ number_format($u->gross_total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-amber-600">
                                Rp {{ number_format($u->gross_escrow, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                Rp {{ number_format($u->hak_bersih, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-emerald-600">
                                Rp {{ number_format($u->payout_approved, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-extrabold text-brand-700 font-display">
                                Rp {{ number_format($u->saldo_siap_tarik, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- General Ledger Feed -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <h4 class="text-sm font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-list-ol text-slate-700"></i> Mutasi Transaksi Masuk Terkini
            </h4>
            <span class="text-xs text-slate-400">10 Transaksi Terakhir</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-500 font-bold border-b border-slate-100">
                    <tr>
                        <th class="px-6 py-3">No. Transaksi</th>
                        <th class="px-6 py-3">Pembeli</th>
                        <th class="px-6 py-3">Toko Pengirim</th>
                        <th class="px-6 py-3 text-right">Nilai Transaksi</th>
                        <th class="px-6 py-3 text-right">Hak Petani ({{ $tokoPersen }}%)</th>
                        <th class="px-6 py-3 text-right">Komisi Platform ({{ $komisiPersen }}%)</th>
                        <th class="px-6 py-3 text-center">Status Escrow</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-mono">
                    @foreach($recentOrders as $ro)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3 font-bold text-slate-900 font-sans">
                                #ORD-{{ $ro->id }}
                                <span class="block text-[10px] text-slate-400">{{ $ro->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="px-6 py-3 font-sans text-slate-800">
                                {{ $ro->name }}
                            </td>
                            <td class="px-6 py-3 font-sans text-brand-700 font-semibold">
                                {{ $ro->produk->umkm->nama_toko ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-right font-bold text-slate-900">
                                Rp {{ number_format($ro->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-emerald-700">
                                Rp {{ number_format($ro->total_harga * ($tokoPersen / 100), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-right font-semibold text-slate-600">
                                Rp {{ number_format($ro->total_harga * ($komisiPersen / 100), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3 text-center font-sans">
                                @if($ro->status_pesanan === 'diterima' || $ro->is_escrow_released)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                        Released
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                        In Escrow
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

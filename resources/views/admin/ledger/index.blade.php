@extends('layouts.app')

@section('page_title', 'Buku Besar Platform & Escrow Ledger')

@section('content')
<div class="max-w-7xl mx-auto space-y-8 pb-16">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-brand-50 border border-brand-200 text-brand-700 text-xs font-bold uppercase tracking-wider mb-1.5">
                <i class="fas fa-scale-balanced text-brand-600"></i>
                Buku Besar Finansial & Kliring Escrow
            </div>
            <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Buku Besar & Monitoring Saldo Escrow
            </h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-3xl leading-relaxed">
                Transparansi menyeluruh arus kas masuk Midtrans, rekening penampung (Escrow), pembagian hak panen mitra petani ({{ $tokoPersen }}%), dan pendapatan komisi operasional platform ({{ $komisiPersen }}%).
            </p>
        </div>
        <div class="flex items-center gap-2.5 shrink-0">
            <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200 shadow-xs">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>Escrow Protection Active</span>
            </span>
            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold border border-slate-200 transition shadow-xs flex items-center gap-1.5 cursor-pointer">
                <i class="fas fa-print text-slate-400"></i>
                <span>Cetak Rekap</span>
            </button>
        </div>
    </div>

    <!-- Escrow & Financial Overview 4 Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Kas Masuk Midtrans -->
        <div class="card bg-white border border-slate-200/80 p-5 rounded-3xl shadow-sm hover:border-slate-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Total Kas Masuk (Gross)</span>
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-base border border-blue-100">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-slate-900 mt-2 font-display">
                Rp {{ number_format($totalGrossInflow, 0, ',', '.') }}
            </h3>
            <div class="flex items-center justify-between mt-3 text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                <span>Akumulasi Midtrans SNAP</span>
                <span class="font-bold text-slate-600">100% Tercatat</span>
            </div>
        </div>

        <!-- Dana Mengendap di Escrow (In-Transit) -->
        <div class="card bg-gradient-to-br from-amber-500 to-amber-600 text-white p-5 rounded-3xl shadow-md relative overflow-hidden group">
            <div class="absolute -right-4 -bottom-4 w-24 h-24 bg-white/10 rounded-full blur-xl group-hover:scale-125 transition-transform duration-500"></div>
            <div class="relative z-10 flex items-center justify-between">
                <span class="text-[11px] font-extrabold text-amber-100 uppercase tracking-wider">Saldo Mengendap di Escrow</span>
                <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-xs text-white flex items-center justify-center text-base border border-white/20">
                    <i class="fas fa-hourglass-half"></i>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-white mt-2 font-display relative z-10">
                Rp {{ number_format($totalEscrowHolding, 0, ',', '.') }}
            </h3>
            <div class="flex items-center justify-between mt-3 text-[11px] text-amber-100/90 pt-2 border-t border-white/20 relative z-10">
                <span>Pesanan Dalam Pengiriman</span>
                <span class="font-bold text-white">In-Transit SLA</span>
            </div>
        </div>

        <!-- Total Profit Bersih Platform (20-30%) -->
        <div class="card bg-slate-900 text-white p-5 rounded-3xl shadow-md border border-slate-800">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold text-emerald-400 uppercase tracking-wider">Profit Platform ({{ $komisiPersen }}%)</span>
                <div class="w-10 h-10 rounded-2xl bg-white/10 text-emerald-400 flex items-center justify-center text-base border border-emerald-500/20">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-emerald-400 mt-2 font-display">
                Rp {{ number_format($totalPlatformRevenue, 0, ',', '.') }}
            </h3>
            <div class="flex items-center justify-between mt-3 text-[11px] text-slate-400 pt-2 border-t border-slate-800">
                <span>Potensi di Escrow</span>
                <span class="font-bold text-emerald-300">+Rp {{ number_format($potensiKomisiEscrow, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Kewajiban Hutang ke Petani (Siap Tarik) -->
        <div class="card bg-white border border-slate-200/80 p-5 rounded-3xl shadow-sm hover:border-slate-300 transition">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Kewajiban Payout Toko</span>
                <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-base border border-brand-100">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
            </div>
            <h3 class="text-2xl sm:text-3xl font-black text-brand-600 mt-2 font-display">
                Rp {{ number_format($sisaKewajibanToko, 0, ',', '.') }}
            </h3>
            <div class="flex items-center justify-between mt-3 text-[11px] text-slate-400 pt-2 border-t border-slate-100">
                <span>Hak Petani Siap Tarik</span>
                <a href="{{ route('admin.penarikan.index') }}" class="font-bold text-brand-600 hover:underline">Kelola Payout &rarr;</a>
            </div>
        </div>

    </div>

    <!-- 📊 GRAND ANALYTICS & VISUAL CHART DECK (DIBUKA BESAR) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
        
        <!-- Main Area Chart: Tren Arus Kas & Likuiditas (8 Cols) -->
        <div class="lg:col-span-8 card bg-white border border-slate-200/80 p-6 sm:p-7 rounded-3xl shadow-sm space-y-5 flex flex-col justify-between">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-brand-600 bg-brand-50 px-2.5 py-0.5 rounded-full border border-brand-100">
                        Visualisasi Arus Kas & Kliring
                    </span>
                    <h3 class="text-lg font-extrabold text-slate-900 font-display mt-1">
                        Dinamika Likuiditas & Pergerakan Saldo Transaksi
                    </h3>
                    <p class="text-xs text-slate-400">
                        Perbandingan volume kas masuk (Gross Inflow), hak bersih mitra petani ({{ $tokoPersen }}%), komisi platform, dan pengendapan escrow.
                    </p>
                </div>
                <div class="flex items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-slate-600 bg-slate-100 px-3 py-1.5 rounded-xl">
                        <i class="fas fa-calendar-days text-slate-400 mr-0.5"></i> 6 Bulan Terakhir
                    </span>
                </div>
            </div>

            <!-- Canvas Chart Area -->
            <div class="relative w-full h-[320px] sm:h-[360px] pt-2">
                <canvas id="cashflowTrendChart"></canvas>
            </div>

            <!-- Chart Footer Legend Indicators -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-3 border-t border-slate-100 text-xs">
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-indigo-600 shrink-0"></span>
                    <div>
                        <span class="text-[10px] text-slate-400 block leading-tight">Total Inflow</span>
                        <strong class="text-slate-800 font-extrabold">Rp {{ number_format($totalGrossInflow, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 shrink-0"></span>
                    <div>
                        <span class="text-[10px] text-slate-400 block leading-tight">Hak Petani ({{ $tokoPersen }}%)</span>
                        <strong class="text-slate-800 font-extrabold">Rp {{ number_format($totalHakBersihTokoSettled, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-slate-900 shrink-0"></span>
                    <div>
                        <span class="text-[10px] text-slate-400 block leading-tight">Komisi ({{ $komisiPersen }}%)</span>
                        <strong class="text-slate-800 font-extrabold">Rp {{ number_format($totalPlatformRevenue, 0, ',', '.') }}</strong>
                    </div>
                </div>
                <div class="flex items-center gap-2.5">
                    <span class="w-3 h-3 rounded-full bg-amber-500 shrink-0"></span>
                    <div>
                        <span class="text-[10px] text-slate-400 block leading-tight">Escrow Tertahan</span>
                        <strong class="text-slate-800 font-extrabold">Rp {{ number_format($totalEscrowHolding, 0, ',', '.') }}</strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Donut Chart: Struktur Portofolio & Alokasi Likuiditas (4 Cols) -->
        <div class="lg:col-span-4 card bg-white border border-slate-200/80 p-6 sm:p-7 rounded-3xl shadow-sm space-y-5 flex flex-col justify-between">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full border border-emerald-100">
                    Alokasi Portofolio
                </span>
                <h3 class="text-lg font-extrabold text-slate-900 font-display mt-1">
                    Struktur Distribusi Saldo
                </h3>
                <p class="text-xs text-slate-400">
                    Komposisi pembagian hak modal dan status penjaminan dana saat ini.
                </p>
            </div>

            <!-- Donut Canvas with Center KPI -->
            <div class="relative flex items-center justify-center my-2 h-[220px]">
                <canvas id="escrowAllocationChart"></canvas>
            </div>

            <!-- Structured Percentage Legend -->
            <div class="space-y-2 text-xs pt-2 border-t border-slate-100">
                @php
                    $sumPool = max(1, $totalHakBersihTokoSettled + $totalEscrowHolding + $totalPlatformRevenue);
                    $pctPetani = round(($totalHakBersihTokoSettled / $sumPool) * 100, 1);
                    $pctPlatform = round(($totalPlatformRevenue / $sumPool) * 100, 1);
                    $pctEscrow = round(($totalEscrowHolding / $sumPool) * 100, 1);
                @endphp
                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        <span class="font-bold text-slate-700 text-xs">Hak Mitra Petani</span>
                    </div>
                    <div class="text-right">
                        <span class="font-extrabold text-slate-900 text-xs">{{ $pctPetani }}%</span>
                        <span class="text-[10px] text-slate-400 block font-mono">Rp {{ number_format($totalHakBersihTokoSettled, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                        <span class="font-bold text-slate-700 text-xs">Escrow In-Transit</span>
                    </div>
                    <div class="text-right">
                        <span class="font-extrabold text-slate-900 text-xs">{{ $pctEscrow }}%</span>
                        <span class="text-[10px] text-slate-400 block font-mono">Rp {{ number_format($totalEscrowHolding, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-2 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                        <span class="font-bold text-slate-700 text-xs">Komisi Platform</span>
                    </div>
                    <div class="text-right">
                        <span class="font-extrabold text-slate-900 text-xs">{{ $pctPlatform }}%</span>
                        <span class="text-[10px] text-slate-400 block font-mono">Rp {{ number_format($totalPlatformRevenue, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- 📊 SECONDARY CHART: DISTRIBUSI SALDO PER TOKO MITRA (DIBUKA LEBAR) -->
    <div class="card bg-white border border-slate-200/80 p-6 sm:p-7 rounded-3xl shadow-sm space-y-5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div>
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700 bg-purple-50 px-2.5 py-0.5 rounded-full border border-purple-100">
                    Komparasi Entitas UMKM
                </span>
                <h3 class="text-lg font-extrabold text-slate-900 font-display mt-1">
                    Pemetaan Likuiditas & Performa Finansial per Sentra Mitra
                </h3>
                <p class="text-xs text-slate-400">
                    Visualisasi perbandingan total omzet kotor, saldo mengendap di escrow logistik, dan saldo siap dicairkan (payout) ke rekening petani.
                </p>
            </div>
            <div class="text-right">
                <span class="text-xs font-bold text-slate-500 bg-slate-100 px-3 py-1.5 rounded-xl inline-block">
                    Total {{ count($storeNames) }} Sentra Kebun Aktif
                </span>
            </div>
        </div>

        <div class="relative w-full h-[280px] sm:h-[320px]">
            <canvas id="storePerformanceBarChart"></canvas>
        </div>
    </div>

    <!-- 🏛️ TATA KELOLA & ARSITEKTUR ANALISIS FINANSIAL KOMPREHENSIF (5W1H SECARA ELEGAN & PROFESIONAL) -->
    <div class="space-y-4">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-0.5 rounded-full bg-slate-100 text-slate-700 text-[11px] font-extrabold uppercase tracking-wider mb-1">
                <i class="fas fa-shield-halved text-brand-600"></i>
                Kerangka Tata Kelola & Integritas Finansial
            </div>
            <h3 class="text-xl font-extrabold text-slate-900 font-display tracking-tight">
                Pilar Pengawasan & Protokol Kliring Marketplace
            </h3>
            <p class="text-xs text-slate-500">
                Prinsip arsitektur transparansi permodalan, entitas penerima manfaat, titik penampungan kustodi, siklus SLA, dan mitigasi risiko anti-fraud.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            
            <!-- Pilar 1: Komposisi & Alokasi Modal (What) -->
            <div class="card p-6 bg-white border border-slate-200/80 rounded-3xl shadow-sm space-y-3 hover:border-slate-300 transition">
                <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg border border-indigo-100">
                    <i class="fas fa-coins"></i>
                </div>
                <h4 class="text-sm font-extrabold text-slate-900 font-display">
                    Alokasi Modal & Kebijakan Likuiditas
                </h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Setiap pembayaran pembeli secara otomatis dialokasikan ke dalam dua rekening virtual: <strong>{{ $tokoPersen }}% hak panen bersih mitra petani</strong> dan <strong>{{ $komisiPersen }}% komisi pemeliharaan platform</strong> untuk infrastruktur server, cloud, dan gateway.
                </p>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <span>Proporsi Bagi Hasil</span>
                    <strong class="text-indigo-700 font-bold">{{ $tokoPersen }}% : {{ $komisiPersen }}%</strong>
                </div>
            </div>

            <!-- Pilar 2: Entitas & Pemangku Kepentingan (Who) -->
            <div class="card p-6 bg-white border border-slate-200/80 rounded-3xl shadow-sm space-y-3 hover:border-slate-300 transition">
                <div class="w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg border border-emerald-100">
                    <i class="fas fa-users-viewfinder"></i>
                </div>
                <h4 class="text-sm font-extrabold text-slate-900 font-display">
                    Distribusi Hak & Entitas Penerima
                </h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Integrasi transparan menghubungkan <strong>{{ $umkms->count() }} Sentra UMKM Petani Mangga Indramayu</strong> dengan ribuan pembeli ritel & B2B di bawah pengawasan audit langsung Pemerintah Daerah dan tim Superadmin sentral.
                </p>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <span>Kemitraan Terverifikasi</span>
                    <strong class="text-emerald-700 font-bold">{{ $umkms->count() }} Toko Aktif</strong>
                </div>
            </div>

            <!-- Pilar 3: Titik Penampungan & Rekening Kustodi (Where) -->
            <div class="card p-6 bg-white border border-slate-200/80 rounded-3xl shadow-sm space-y-3 hover:border-slate-300 transition">
                <div class="w-10 h-10 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg border border-blue-100">
                    <i class="fas fa-vault"></i>
                </div>
                <h4 class="text-sm font-extrabold text-slate-900 font-display">
                    Struktur Rekening Kustodi & Jalur Kliring
                </h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Dana kas masuk diproteksi dalam <strong>Rekening Escrow Penampung Terisolasi</strong> via Midtrans Snap berlisensi Bank Indonesia, menjamin saldo tidak tercampur dengan kas operasional sebelum pesanan terverifikasi tuntas.
                </p>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <span>Protokol Keamanan</span>
                    <strong class="text-blue-700 font-bold">BI Escrow Standard</strong>
                </div>
            </div>

            <!-- Pilar 4: Siklus Kliring & Ketepatan Settlement (When) -->
            <div class="card p-6 bg-white border border-slate-200/80 rounded-3xl shadow-sm space-y-3 hover:border-slate-300 transition">
                <div class="w-10 h-10 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg border border-amber-100">
                    <i class="fas fa-clock-rotate-left"></i>
                </div>
                <h4 class="text-sm font-extrabold text-slate-900 font-display">
                    Siklus Kliring & Periode Settlement
                </h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Verifikasi pembayaran berlangsung <strong>instan (&lt;1 menit)</strong>, masa garansi pengiriman barang maksimum <strong>2&times;24 jam pasca tiba</strong>, dan proses verifikasi pencairan dana petani diproses dalam <strong>1&times;24 jam hari kerja</strong>.
                </p>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <span>SLA Verifikasi Payout</span>
                    <strong class="text-amber-700 font-bold">&le; 24 Jam Kerja</strong>
                </div>
            </div>

            <!-- Pilar 5: Proteksi Risiko & Anti-Fraud (Why) -->
            <div class="card p-6 bg-white border border-slate-200/80 rounded-3xl shadow-sm space-y-3 hover:border-slate-300 transition">
                <div class="w-10 h-10 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg border border-rose-100">
                    <i class="fas fa-shield-heart"></i>
                </div>
                <h4 class="text-sm font-extrabold text-slate-900 font-display">
                    Tujuan Tata Kelola & Proteksi Risiko
                </h4>
                <p class="text-xs text-slate-600 leading-relaxed">
                    Mekanisme escrow menjamin <strong>pembeli menerima mangga segar berkualitas asli Indramayu</strong> sebelum dana diteruskan, sekaligus melindungi petani dari risiko pembatalan sepihak dan klaim tidak wajar.
                </p>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <span>Tingkat Resolusi Komplain</span>
                    <strong class="text-rose-700 font-bold">100% Terproteksi</strong>
                </div>
            </div>

            <!-- Pilar 6: Arsitektur Alur Perjalanan Dana (How) -->
            <div class="card p-6 bg-white border border-slate-200/80 rounded-3xl shadow-sm space-y-3 hover:border-slate-300 transition">
                <div class="w-10 h-10 rounded-2xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg border border-teal-100">
                    <i class="fas fa-route"></i>
                </div>
                <h4 class="text-sm font-extrabold text-slate-900 font-display">
                    Arsitektur Alur Perjalanan Dana
                </h4>
                <div class="space-y-1.5 text-[11px] text-slate-600">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-slate-900 text-white flex items-center justify-center text-[9px] font-bold">1</span>
                        <span>Pembeli Checkout & Midtrans Snap Inflow</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-amber-500 text-white flex items-center justify-center text-[9px] font-bold">2</span>
                        <span>Dana Dikunci di Escrow (Status In-Transit)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[9px] font-bold">3</span>
                        <span>Barang Diterima & Saldo Cair ke Petani</span>
                    </div>
                </div>
                <div class="pt-2 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-500 font-mono">
                    <span>Status Otomasi</span>
                    <strong class="text-teal-700 font-bold">End-to-End System</strong>
                </div>
            </div>

        </div>
    </div>

    <!-- Breakdown Saldo per Toko Mitra -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h4 class="text-base font-extrabold text-slate-900 flex items-center gap-2 font-display">
                    <i class="fas fa-store text-brand-600"></i> Rekapitulasi Escrow & Saldo per Toko Mitra UMKM
                </h4>
                <p class="text-xs text-slate-400 mt-0.5">Rincian hak panen bersih yang sudah selesai (Settled), dana dalam perjalanan, dan saldo siap ditarik</p>
            </div>
            <span class="text-xs text-slate-500 font-bold bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                {{ $umkms->count() }} Toko Terdaftar
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-100 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-4">Nama Kebun / Toko</th>
                        <th class="px-6 py-4 text-right">Omzet Kotor</th>
                        <th class="px-6 py-4 text-right">Saldo Escrow (In-Transit)</th>
                        <th class="px-6 py-4 text-right">Hak Bersih Settled ({{ $tokoPersen }}%)</th>
                        <th class="px-6 py-4 text-right">Sudah Dicairkan</th>
                        <th class="px-6 py-4 text-right">Saldo Siap Tarik</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($umkms as $u)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-4">
                                <div class="font-extrabold text-slate-900 text-xs">{{ $u->umkm->nama_toko }}</div>
                                <span class="text-[11px] text-slate-400">{{ $u->umkm->user->name ?? '-' }} ({{ $u->umkm->no_telp ?? '-' }})</span>
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-800">
                                Rp {{ number_format($u->gross_total, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-extrabold text-amber-600">
                                Rp {{ number_format($u->gross_escrow, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-slate-900">
                                Rp {{ number_format($u->hak_bersih, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-semibold text-emerald-600">
                                Rp {{ number_format($u->payout_approved, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-black text-brand-700 font-display text-sm">
                                Rp {{ number_format($u->saldo_siap_tarik, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- General Ledger Feed -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h4 class="text-base font-extrabold text-slate-900 flex items-center gap-2 font-display">
                    <i class="fas fa-list-ol text-slate-700"></i> Mutasi Transaksi Masuk Terkini (General Ledger)
                </h4>
                <p class="text-xs text-slate-400 mt-0.5">Log 10 pesanan terverifikasi Midtrans terakhir yang masuk ke dalam sistem pembukuan</p>
            </div>
            <span class="text-xs text-slate-500 font-bold bg-slate-50 px-3 py-1.5 rounded-xl border border-slate-200">
                10 Transaksi Terakhir
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-100 uppercase tracking-wider text-[10px]">
                    <tr>
                        <th class="px-6 py-4">No. Transaksi</th>
                        <th class="px-6 py-4">Pembeli</th>
                        <th class="px-6 py-4">Toko Pengirim</th>
                        <th class="px-6 py-4 text-right">Nilai Transaksi</th>
                        <th class="px-6 py-4 text-right">Hak Petani ({{ $tokoPersen }}%)</th>
                        <th class="px-6 py-4 text-right">Komisi Platform ({{ $komisiPersen }}%)</th>
                        <th class="px-6 py-4 text-center">Status Escrow</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($recentOrders as $ro)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="px-6 py-3.5 font-bold text-slate-900 font-mono">
                                #ORD-{{ str_pad($ro->id, 5, '0', STR_PAD_LEFT) }}
                                <span class="block text-[10px] text-slate-400 font-sans">{{ $ro->created_at->format('d/m/Y H:i') }} WIB</span>
                            </td>
                            <td class="px-6 py-3.5 font-semibold text-slate-800">
                                {{ $ro->name }}
                            </td>
                            <td class="px-6 py-3.5 text-brand-700 font-bold">
                                {{ $ro->produk->umkm->nama_toko ?? '-' }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-black text-slate-900">
                                Rp {{ number_format($ro->total_harga, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-bold text-emerald-700">
                                Rp {{ number_format($ro->total_harga * ($tokoPersen / 100), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3.5 text-right font-semibold text-slate-600">
                                Rp {{ number_format($ro->total_harga * ($komisiPersen / 100), 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-3.5 text-center">
                                @if($ro->status_pesanan === 'diterima' || $ro->is_escrow_released)
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                        <i class="fas fa-check-circle mr-1 text-[9px]"></i> Settled
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                        <i class="fas fa-clock mr-1 text-[9px]"></i> In Escrow
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400">
                                <i class="fas fa-receipt text-3xl mb-2 text-slate-300 block"></i>
                                Belum ada mutasi transaksi masuk terdata.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- 📊 Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const monthlyLabels = @json($monthlyLabels);
    const monthlyInflows = @json($monthlyInflows);
    const monthlyFarmerShares = @json($monthlyFarmerShares);
    const monthlyPlatformRevenues = @json($monthlyPlatformRevenues);
    const monthlyEscrowHoldings = @json($monthlyEscrowHoldings);

    const storeNames = @json($storeNames);
    const storeGross = @json($storeGross);
    const storeEscrow = @json($storeEscrow);
    const storeReadyWithdraw = @json($storeReadyWithdraw);

    // Number Formatter Helper
    const formatRp = (value) => 'Rp ' + new Intl.NumberFormat('id-ID').format(value);

    // 1. 📈 AREA / SPLINE CHART: Tren Arus Kas & Likuiditas
    const ctxCashflow = document.getElementById('cashflowTrendChart');
    if (ctxCashflow) {
        new Chart(ctxCashflow, {
            type: 'line',
            data: {
                labels: monthlyLabels,
                datasets: [
                    {
                        label: 'Total Kas Masuk (Gross)',
                        data: monthlyInflows,
                        borderColor: '#4f46e5',
                        backgroundColor: 'rgba(79, 70, 229, 0.08)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2.5,
                        pointBackgroundColor: '#4f46e5',
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Hak Bersih Petani ({{ $tokoPersen }}%)',
                        data: monthlyFarmerShares,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.06)',
                        fill: true,
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 3.5,
                    },
                    {
                        label: 'Komisi Platform ({{ $komisiPersen }}%)',
                        data: monthlyPlatformRevenues,
                        borderColor: '#0f172a',
                        borderDash: [4, 4],
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#0f172a',
                        pointRadius: 3,
                    },
                    {
                        label: 'Escrow In-Transit',
                        data: monthlyEscrowHoldings,
                        borderColor: '#f59e0b',
                        tension: 0.4,
                        borderWidth: 2,
                        pointBackgroundColor: '#f59e0b',
                        pointRadius: 3,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '700' },
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        titleFont: { family: 'Plus Jakarta Sans', size: 12, weight: '800' },
                        bodyFont: { family: 'Plus Jakarta Sans', size: 11 },
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + formatRp(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#64748b' }
                    },
                    y: {
                        border: { dash: [4, 4] },
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            color: '#94a3b8',
                            callback: function (val) {
                                if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'K';
                                return 'Rp ' + val;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. 🍩 DONUT CHART: Alokasi Likuiditas Escrow
    const ctxEscrowDonut = document.getElementById('escrowAllocationChart');
    if (ctxEscrowDonut) {
        new Chart(ctxEscrowDonut, {
            type: 'doughnut',
            data: {
                labels: ['Hak Mitra Petani', 'Escrow In-Transit', 'Komisi Platform'],
                datasets: [{
                    data: [
                        {{ (float)$totalHakBersihTokoSettled }},
                        {{ (float)$totalEscrowHolding }},
                        {{ (float)$totalPlatformRevenue }}
                    ],
                    backgroundColor: ['#10b981', '#f59e0b', '#4f46e5'],
                    borderWidth: 3,
                    borderColor: '#ffffff',
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function (context) {
                                return context.label + ': ' + formatRp(context.raw);
                            }
                        }
                    }
                }
            }
        });
    }

    // 3. 📊 BAR CHART: Komparasi Performa & Saldo per Toko Mitra
    const ctxStoreBar = document.getElementById('storePerformanceBarChart');
    if (ctxStoreBar && storeNames.length > 0) {
        new Chart(ctxStoreBar, {
            type: 'bar',
            data: {
                labels: storeNames,
                datasets: [
                    {
                        label: 'Omzet Kotor Terdata',
                        data: storeGross,
                        backgroundColor: 'rgba(79, 70, 229, 0.85)',
                        borderRadius: 8,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Saldo Escrow (In-Transit)',
                        data: storeEscrow,
                        backgroundColor: 'rgba(245, 158, 11, 0.85)',
                        borderRadius: 8,
                        barPercentage: 0.6,
                    },
                    {
                        label: 'Saldo Siap Tarik (Payout)',
                        data: storeReadyWithdraw,
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderRadius: 8,
                        barPercentage: 0.6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            font: { family: 'Plus Jakarta Sans', size: 11, weight: '700' },
                            usePointStyle: true,
                            boxWidth: 8,
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.95)',
                        padding: 12,
                        cornerRadius: 12,
                        callbacks: {
                            label: function (context) {
                                return context.dataset.label + ': ' + formatRp(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Plus Jakarta Sans', size: 11, weight: '600' }, color: '#475569' }
                    },
                    y: {
                        border: { dash: [4, 4] },
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans', size: 10 },
                            color: '#94a3b8',
                            callback: function (val) {
                                if (val >= 1000000) return 'Rp ' + (val / 1000000).toFixed(1) + 'M';
                                if (val >= 1000) return 'Rp ' + (val / 1000).toFixed(0) + 'K';
                                return 'Rp ' + val;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush
@endsection

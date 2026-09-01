@extends('layouts.app')

@section('page_title', 'Laporan & Pengaturan Pendapatan')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Laporan & Pengaturan Bagi Hasil</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Kelola persentase komisi marketplace dan pantau bagi hasil antara platform & toko mitra secara transparan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold">
                <i class="fas fa-percent text-indigo-500"></i>
                Komisi Aktif: <strong class="text-indigo-900">{{ $komisiPersen }}%</strong>
            </span>
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs text-emerald-800 shadow-sm animate-fade-in">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- ========================================================================= -->
    <!-- ⚙️ PENGATURAN KOMISI & SIMULATOR VISUALISASI HARGA JUAL                   -->
    <!-- ========================================================================= -->
    <div class="card p-6 sm:p-7 bg-white border border-slate-200/80 shadow-sm rounded-3xl space-y-6">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6 pb-6 border-b border-slate-100">
            
            <!-- Form Setting Persentase Komisi -->
            <div class="space-y-3 max-w-xl">
                <div class="flex items-center gap-2">
                    <span class="px-2.5 py-0.5 rounded-md bg-brand-50 text-brand-700 text-[10px] font-extrabold uppercase border border-brand-200">
                        Pengaturan Komisi Platform
                    </span>
                </div>
                <h3 class="text-base sm:text-lg font-extrabold text-slate-900 font-display">
                    Atur Persentase Bagi Hasil Platform
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Ubah persentase potongan komisi marketplace dari setiap transaksi penjualan. Seluruh rekap, rincian transparansi pesanan, dan perhitungan bagi hasil mitra akan menyesuaikan secara real-time.
                </p>

                <form method="POST" action="{{ route('admin.pendapatan.update-komisi') }}" class="space-y-3 pt-2">
                    @csrf
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-bold text-slate-700">Preset Cepat:</span>
                        @foreach([5, 10, 15, 20, 25, 30] as $preset)
                            <button 
                                type="button" 
                                onclick="setKomisiInput({{ $preset }})" 
                                class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ (float)$komisiPersen === (float)$preset ? 'bg-brand-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}"
                            >
                                {{ $preset }}%
                            </button>
                        @endforeach
                    </div>

                    <div class="flex items-center gap-3 pt-1">
                        <div class="relative w-36">
                            <input 
                                type="number" 
                                name="komisi_persen" 
                                id="komisiInput"
                                step="0.1" 
                                min="0" 
                                max="100" 
                                value="{{ $komisiPersen }}" 
                                required
                                oninput="updateSimulator()"
                                class="w-full pl-4 pr-9 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-extrabold text-slate-900 focus:outline-none focus:border-brand-500 text-center"
                            >
                            <span class="absolute right-3.5 top-2.5 text-xs font-extrabold text-slate-400">%</span>
                        </div>
                        <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2">
                            <i class="fas fa-floppy-disk"></i>
                            <span>Simpan Persentase</span>
                        </button>
                    </div>
                    @error('komisi_persen')
                        <p class="text-xs text-rose-500 font-semibold">{{ $message }}</p>
                    @enderror
                </form>
            </div>

            <!-- Interactive Live Simulator & Visualisasi Bagi Hasil -->
            <div class="lg:w-96 p-5 rounded-2xl bg-gradient-to-br from-slate-900 to-slate-800 text-white shadow-lg space-y-4 shrink-0">
                <div class="flex items-center justify-between border-b border-slate-700/80 pb-3">
                    <span class="text-[11px] font-extrabold uppercase tracking-wider text-amber-400 flex items-center gap-1.5">
                        <i class="fas fa-calculator"></i> Simulator Bagi Hasil
                    </span>
                    <span class="text-[10px] text-slate-400">Contoh Harga Jual</span>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-[11px] text-slate-400 font-medium">Ketik Nominal Harga Produk (Rp):</label>
                    <input 
                        type="number" 
                        id="simulasiHarga" 
                        value="100000" 
                        step="5000"
                        min="1000"
                        oninput="updateSimulator()"
                        class="w-full px-3.5 py-2 bg-slate-800 border border-slate-700 rounded-xl text-sm font-extrabold text-white focus:outline-none focus:border-brand-400"
                    >
                </div>

                <!-- Visual Colored Proportion Bar -->
                <div class="space-y-1.5 pt-1">
                    <div class="flex items-center justify-between text-[11px] font-bold">
                        <span class="text-emerald-400 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Petani (<span id="simTokoPersen">{{ $tokoPersen }}</span>%)
                        </span>
                        <span class="text-indigo-400 flex items-center gap-1">
                            <span class="w-2 h-2 rounded-full bg-indigo-400"></span> Platform (<span id="simKomisiPersen">{{ $komisiPersen }}</span>%)
                        </span>
                    </div>
                    <div class="w-full h-3 bg-slate-800 rounded-full overflow-hidden flex border border-slate-700/80">
                        <div id="barToko" class="bg-emerald-500 h-full transition-all duration-300" style="width: {{ $tokoPersen }}%"></div>
                        <div id="barPlatform" class="bg-indigo-500 h-full transition-all duration-300" style="width: {{ $komisiPersen }}%"></div>
                    </div>
                </div>

                <!-- Live Nominal Calculation Results -->
                <div class="grid grid-cols-2 gap-3 pt-2 text-xs border-t border-slate-700/80">
                    <div class="p-2.5 rounded-xl bg-slate-800/80 border border-slate-700">
                        <span class="text-[10px] text-slate-400 block">Hak Bersih Petani:</span>
                        <strong class="text-emerald-400 text-xs sm:text-sm font-display block mt-0.5" id="simNominalToko">
                            Rp{{ number_format(100000 * ($tokoPersen / 100), 0, ',', '.') }}
                        </strong>
                    </div>
                    <div class="p-2.5 rounded-xl bg-slate-800/80 border border-slate-700">
                        <span class="text-[10px] text-slate-400 block">Komisi Platform:</span>
                        <strong class="text-indigo-400 text-xs sm:text-sm font-display block mt-0.5" id="simNominalPlatform">
                            Rp{{ number_format(100000 * ($komisiPersen / 100), 0, ',', '.') }}
                        </strong>
                    </div>
                </div>
            </div>

        </div>

        <!-- 📅 Dynamic Calendar Range Filter Form Card -->
        <form id="adminPendapatanFilterForm" method="GET" action="{{ route('admin.pendapatan.index') }}" class="pt-2 space-y-4">
            <input type="hidden" name="period" id="adminPendapatanPeriod" value="{{ $period ?? '' }}">
            <input type="hidden" name="start_date" id="adminPendapatanStartDate" value="{{ $startDateInput ?? '' }}">
            <input type="hidden" name="end_date" id="adminPendapatanEndDate" value="{{ $endDateInput ?? '' }}">

            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <div class="flex flex-col sm:flex-row sm:items-center gap-3 flex-1 flex-wrap">
                    
                    <!-- Dynamic Interactive Calendar Range Input -->
                    <div class="relative min-w-[260px] sm:w-72">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                            <i class="fas fa-calendar-days text-sm"></i>
                        </div>
                        <input 
                            type="text" 
                            id="flatpickrAdminPendapatanCalendar" 
                            placeholder="Pilih Rentang Tanggal Kalender..." 
                            class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer"
                            readonly
                        >
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                            @if(request('period') || request('start_date') || request('bulan') || request('minggu'))
                                <a href="{{ route('admin.pendapatan.index') }}" title="Reset Filter" class="text-slate-400 hover:text-rose-500 transition text-xs">
                                    <i class="fas fa-circle-xmark"></i>
                                </a>
                            @else
                                <i class="fas fa-chevron-down text-[10px] text-slate-400 pointer-events-none"></i>
                            @endif
                        </div>
                    </div>

                    <!-- Quick Filter Period Pills -->
                    <div class="flex items-center gap-1.5 flex-wrap">
                        <button type="button" onclick="setAdminPendapatanPeriod('all')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'all' || (!request('period') && !request('start_date') && !request('bulan') && !request('minggu')) ? 'bg-slate-900 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Semua Waktu
                        </button>
                        <button type="button" onclick="setAdminPendapatanPeriod('today')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'today' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Hari Ini
                        </button>
                        <button type="button" onclick="setAdminPendapatanPeriod('7days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '7days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            7 Hari Terakhir
                        </button>
                        <button type="button" onclick="setAdminPendapatanPeriod('30days')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === '30days' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            30 Hari Terakhir
                        </button>
                        <button type="button" onclick="setAdminPendapatanPeriod('this_month')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_month' || (request('bulan') == date('m') && !request('period') && !request('start_date')) ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Bulan Ini
                        </button>
                        <button type="button" onclick="setAdminPendapatanPeriod('this_year')" class="px-3 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer {{ ($period ?? '') === 'this_year' ? 'bg-emerald-600 text-white shadow-xs' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                            Tahun Ini
                        </button>
                    </div>

                </div>

                <!-- Right: Active Period Badge & Reset Button -->
                <div class="flex items-center justify-between sm:justify-end gap-2.5 pt-2 lg:pt-0 border-t lg:border-t-0 border-slate-100 shrink-0">
                    <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200/80 text-emerald-900 text-xs font-bold">
                        <div class="w-2 h-2 rounded-full bg-emerald-600 animate-pulse"></div>
                        <span class="text-slate-500 font-normal">Rentang:</span>
                        <strong class="font-extrabold text-emerald-800">{{ $periodeInfo }}</strong>
                    </div>

                    @if(request('period') || request('start_date') || request('bulan') || request('minggu'))
                        <a href="{{ route('admin.pendapatan.index') }}" class="px-3 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 text-xs font-bold border border-rose-200 transition flex items-center gap-1.5 shadow-2xs">
                            <i class="fas fa-rotate-left text-[10px]"></i>
                            <span>Reset</span>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    <!-- ========================================================================= -->
    <!-- 📊 3 FINANCIAL SUMMARY CARDS                                              -->
    <!-- ========================================================================= -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        
        <!-- Card 1: Total GMV -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Penjualan Kotor (GMV)</span>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base">
                    <i class="fas fa-cart-shopping"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Rp{{ number_format($totalPendapatan, 0, ',', '.') }}
            </p>
            <p class="text-xs font-semibold text-slate-400 mt-2">Akumulasi seluruh transaksi lunas ({{ $periodeInfo }})</p>
        </div>

        <!-- Card 2: Pendapatan Admin / Komisi Platform -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-brand-600">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">
                    Estimasi Komisi Platform ({{ $komisiPersen }}%)
                </span>
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-base">
                    <i class="fas fa-coins"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-brand-600 font-display tracking-tight">
                Rp{{ number_format($pendapatanAdmin, 0, ',', '.') }}
            </p>
            <p class="text-xs font-semibold text-slate-400 mt-2">Bagi hasil operasional & gateway marketplace</p>
        </div>

        <!-- Card 3: Pendapatan Bersih Mitra Petani UMKM -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-emerald-600">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-emerald-600 uppercase tracking-wider">
                    Hak Omzet Mitra Petani ({{ $tokoPersen }}%)
                </span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-base">
                    <i class="fas fa-store"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-600 font-display tracking-tight">
                Rp{{ number_format($pendapatanMitra, 0, ',', '.') }}
            </p>
            <p class="text-xs font-semibold text-slate-400 mt-2">Diteruskan langsung ke saldo mitra toko/kebun</p>
        </div>

    </div>

    <!-- ========================================================================= -->
    <!-- 📋 TABLE: REKAPITULASI PENJUALAN PER TOKO UMKM                            -->
    <!-- ========================================================================= -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 font-display">Rekapitulasi Penjualan & Pembagian Hasil Per Toko</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar kontribusi omzet bruto, potongan komisi platform, dan hak bersih masing-masing mitra</p>
            </div>
            <span class="text-xs font-bold text-slate-500">{{ $rekapPerToko->count() }} Toko Berkontribusi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-16 text-center">Peringkat</th>
                        <th>Nama Toko UMKM</th>
                        <th>Volume Penjualan</th>
                        <th>Total Omzet Bruto</th>
                        <th>Komisi Platform ({{ $komisiPersen }}%)</th>
                        <th>Hak Bersih Toko ({{ $tokoPersen }}%)</th>
                        <th class="text-center w-36">Proporsi Omzet</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rekapPerToko as $index => $rekap)
                        @php
                            $omzetToko = (float) $rekap->total_penjualan;
                            $komisiToko = $omzetToko * ($komisiPersen / 100);
                            $bersihToko = $omzetToko * ($tokoPersen / 100);
                            $persenKontribusi = $totalPendapatan > 0 ? round(($omzetToko / $totalPendapatan) * 100, 1) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="text-center font-bold text-xs">
                                @if($index == 0)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 text-amber-800 text-xs">🥇</span>
                                @elseif($index == 1)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-slate-200 text-slate-700 text-xs">🥈</span>
                                @elseif($index == 2)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-50 text-amber-700 text-xs">🥉</span>
                                @else
                                    <span class="text-slate-400 font-bold">{{ $index + 1 }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-2">
                                        <i class="fas fa-store text-amber-500 text-xs"></i>
                                        <span class="font-extrabold text-xs text-slate-900">{{ $rekap->nama_toko }}</span>
                                    </div>
                                    <p class="text-[10px] text-slate-400 truncate max-w-[200px]">
                                        <i class="fas fa-map-pin text-[9px] mr-1"></i>{{ $rekap->alamat ?? 'Indramayu' }}
                                    </p>
                                </div>
                            </td>
                            <td>
                                <span class="text-xs font-bold text-slate-700">{{ $rekap->total_transaksi }} Pesanan</span>
                                <span class="text-[10px] text-slate-400 block">({{ $rekap->total_volume ?? 0 }} Pcs/Kg)</span>
                            </td>
                            <td>
                                <span class="font-extrabold text-xs text-slate-900">
                                    Rp{{ number_format($omzetToko, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="font-bold text-xs text-brand-600 bg-brand-50 px-2 py-0.5 rounded-lg border border-brand-200 block w-max">
                                    Rp{{ number_format($komisiToko, 0, ',', '.') }}
                                </span>
                            </td>
                            <td>
                                <span class="font-bold text-xs text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-lg border border-emerald-200 block w-max">
                                    Rp{{ number_format($bersihToko, 0, ',', '.') }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="space-y-1">
                                    <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-brand-600 h-full rounded-full" style="width: {{ $persenKontribusi }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500">{{ $persenKontribusi }}% GMV</span>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400 text-xs">
                                <i class="fas fa-wallet text-3xl text-slate-300 mb-2 block"></i>
                                Tidak ada data penjualan pada periode terpilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    /* Custom High-Contrast Styling for Flatpickr */
    .flatpickr-calendar {
        background: #ffffff !important;
        border-radius: 1.25rem !important;
        border: 1px solid #e2e8f0 !important;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1) !important;
        padding: 0.75rem !important;
        font-family: 'Plus Jakarta Sans', sans-serif !important;
        width: 320px !important;
        color: #0f172a !important;
    }
    .flatpickr-months {
        margin-bottom: 0.5rem !important;
    }
    .flatpickr-months .flatpickr-month {
        color: #0f172a !important;
        fill: #0f172a !important;
        height: 36px !important;
    }
    .flatpickr-current-month {
        font-size: 0.95rem !important;
        font-weight: 800 !important;
        color: #0f172a !important;
        padding-top: 4px !important;
    }
    .flatpickr-current-month .cur-month {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-current-month input.cur-year {
        font-weight: 800 !important;
        color: #0f172a !important;
    }
    .flatpickr-weekdays {
        margin-bottom: 0.5rem !important;
    }
    span.flatpickr-weekday {
        color: #475569 !important;
        font-weight: 800 !important;
        font-size: 0.75rem !important;
    }
    .flatpickr-days {
        width: 100% !important;
    }
    .dayContainer {
        width: 100% !important;
        min-width: 100% !important;
        max-width: 100% !important;
        justify-content: space-around !important;
    }
    .flatpickr-day {
        color: #0f172a !important;
        border-radius: 0.65rem !important;
        font-weight: 700 !important;
        font-size: 0.8rem !important;
        height: 38px !important;
        line-height: 38px !important;
        max-width: 38px !important;
        margin: 2px 0 !important;
        border: 1px solid transparent !important;
    }
    .flatpickr-day:hover {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }
    .flatpickr-day.today {
        border-color: #10b981 !important;
        color: #047857 !important;
        font-weight: 900 !important;
        background: #ecfdf5 !important;
    }
    .flatpickr-day.selected, 
    .flatpickr-day.startRange, 
    .flatpickr-day.endRange {
        background: #059669 !important;
        color: #ffffff !important;
        border-color: #059669 !important;
        font-weight: 800 !important;
    }
    .flatpickr-day.inRange {
        background: #d1fae5 !important;
        border-color: #a7f3d0 !important;
        color: #065f46 !important;
        box-shadow: -5px 0 0 #d1fae5, 5px 0 0 #d1fae5 !important;
    }
    .flatpickr-day.prevMonthDay, 
    .flatpickr-day.nextMonthDay {
        color: #94a3b8 !important;
        font-weight: 500 !important;
    }
    .flatpickr-prev-month svg, 
    .flatpickr-next-month svg {
        fill: #0f172a !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/id.js"></script>
<script>
function setAdminPendapatanPeriod(periodVal) {
    document.getElementById('adminPendapatanPeriod').value = periodVal;
    document.getElementById('adminPendapatanStartDate').value = '';
    document.getElementById('adminPendapatanEndDate').value = '';
    document.getElementById('adminPendapatanFilterForm').submit();
}

document.addEventListener('DOMContentLoaded', function () {
    const calendarEl = document.getElementById('flatpickrAdminPendapatanCalendar');
    const initialStart = "{{ $startDateInput ?? '' }}";
    const initialEnd = "{{ $endDateInput ?? '' }}";
    let defaultDates = [];
    if (initialStart && initialEnd) {
        defaultDates = [initialStart, initialEnd];
    } else if (initialStart) {
        defaultDates = [initialStart];
    }

    if (calendarEl && typeof flatpickr !== 'undefined') {
        flatpickr(calendarEl, {
            mode: "range",
            locale: "id",
            dateFormat: "Y-m-d",
            altInput: true,
            altFormat: "d M Y",
            altInputClass: "w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white text-xs font-extrabold text-slate-800 border border-slate-200 rounded-2xl focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer",
            defaultDate: defaultDates,
            showMonths: 1,
            animate: true,
            onClose: function(selectedDates, dateStr, instance) {
                if (selectedDates.length === 2) {
                    const startStr = instance.formatDate(selectedDates[0], "Y-m-d");
                    const endStr = instance.formatDate(selectedDates[1], "Y-m-d");

                    document.getElementById('adminPendapatanPeriod').value = 'custom';
                    document.getElementById('adminPendapatanStartDate').value = startStr;
                    document.getElementById('adminPendapatanEndDate').value = endStr;
                    document.getElementById('adminPendapatanFilterForm').submit();
                } else if (selectedDates.length === 1) {
                    const singleStr = instance.formatDate(selectedDates[0], "Y-m-d");
                    document.getElementById('adminPendapatanPeriod').value = 'custom';
                    document.getElementById('adminPendapatanStartDate').value = singleStr;
                    document.getElementById('adminPendapatanEndDate').value = singleStr;
                    document.getElementById('adminPendapatanFilterForm').submit();
                }
            }
        });
    }
});

function setKomisiInput(val) {
    document.getElementById('komisiInput').value = val;
    updateSimulator();
}

function updateSimulator() {
    const komisiInput = parseFloat(document.getElementById('komisiInput').value) || 0;
    const hargaInput = parseFloat(document.getElementById('simulasiHarga').value) || 0;

    const komisiPersen = Math.min(Math.max(komisiInput, 0), 100);
    const tokoPersen = 100 - komisiPersen;

    document.getElementById('simKomisiPersen').innerText = komisiPersen;
    document.getElementById('simTokoPersen').innerText = tokoPersen;

    document.getElementById('barPlatform').style.width = komisiPersen + '%';
    document.getElementById('barToko').style.width = tokoPersen + '%';

    const nominalPlatform = (hargaInput * (komisiPersen / 100));
    const nominalToko = (hargaInput * (tokoPersen / 100));

    document.getElementById('simNominalPlatform').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(nominalPlatform));
    document.getElementById('simNominalToko').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(Math.round(nominalToko));
}
</script>
@endpush
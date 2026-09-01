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

        <!-- Filter Form Card -->
        <form method="GET" action="{{ route('admin.pendapatan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end pt-2">
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Filter Bulan</label>
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
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Filter Tahun</label>
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
                <button type="submit" class="w-full py-2.5 px-4 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs transition shadow-sm flex items-center justify-center gap-2">
                    <i class="fas fa-filter text-xs"></i> Terapkan Filter
                </button>
                <a href="{{ route('admin.pendapatan.index') }}" class="p-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs transition" title="Reset">
                    <i class="fas fa-rotate-left"></i>
                </a>
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

@push('scripts')
<script>
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
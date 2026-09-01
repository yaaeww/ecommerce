@extends('layouts.app')

@section('page_title', 'Tracker Pengiriman & SLA Fulfillment')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Tracker Pengiriman & SLA Toko</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Monitor kecepatan respon toko dalam mengemas dan mengirim komoditas buah mangga segar ke pembeli.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($totalOverdue > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold animate-pulse">
                    <i class="fas fa-clock-rotate-left text-rose-500"></i>
                    {{ $totalOverdue }} Pesanan Melewati Batas 24 Jam
                </span>
            @endif
        </div>
    </div>

    <!-- Alert Success -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs text-emerald-800 shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- 4 Metrics KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        
        <!-- Total Lunas -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Perlu Diproses</span>
            <p class="text-2xl font-extrabold text-slate-900 font-display">{{ $totalLunas }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Pesanan lunas terbayar</span>
        </div>

        <!-- Overdue -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-rose-500">
            <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider block mb-1">Overdue (>24 Jam)</span>
            <p class="text-2xl font-extrabold text-rose-600 font-display">{{ $totalOverdue }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Risiko komplain pembeli</span>
        </div>

        <!-- Sedang Dikirim -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-indigo-600">
            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block mb-1">Dalam Pengiriman</span>
            <p class="text-2xl font-extrabold text-indigo-600 font-display">{{ $totalDikirim }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Diserahkan ke kurir</span>
        </div>

        <!-- Selesai Diterima -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-emerald-600">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Selesai / Diterima</span>
            <p class="text-2xl font-extrabold text-emerald-600 font-display">{{ $totalDiterima }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Paket tiba di pembeli</span>
        </div>

    </div>

    <!-- Filter Card -->
    <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
        <form method="GET" action="{{ route('admin.pengiriman.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            
            <div>
                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">Status SLA Fulfillment</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-boxes-packing text-xs"></i>
                    </div>
                    <select name="sla_status" class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer">
                        <option value="semua" {{ $statusFulfillment === 'semua' ? 'selected' : '' }}>Semua Status SLA</option>
                        <option value="overdue" {{ $statusFulfillment === 'overdue' ? 'selected' : '' }}>🔴 Overdue (>24 Jam Belum Dikirim)</option>
                        <option value="dikemas" {{ $statusFulfillment === 'dikemas' ? 'selected' : '' }}>🟡 Sedang Dikemas (On-Track)</option>
                        <option value="dikirim" {{ $statusFulfillment === 'dikirim' ? 'selected' : '' }}>🔵 Sedang Dikirim (Kurir)</option>
                        <option value="diterima" {{ $statusFulfillment === 'diterima' ? 'selected' : '' }}>🟢 Diterima Pembeli</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">Toko / Kebun Mitra</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-store text-xs"></i>
                    </div>
                    <select name="umkm_id" class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer">
                        <option value="">Semua Toko Mitra</option>
                        @foreach($umkms as $umkm)
                            <option value="{{ $umkm->id }}" {{ $umkmId == $umkm->id ? 'selected' : '' }}>{{ $umkm->nama_toko }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">Pencarian Pesanan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-magnifying-glass text-xs"></i>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="No order, resi, pembeli..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs"
                    >
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-2xl transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-filter text-xs"></i>
                    <span>Terapkan</span>
                </button>
                @if(request('sla_status') || request('umkm_id') || request('search'))
                    <a href="{{ route('admin.pengiriman.index') }}" class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-2xl transition border border-rose-200" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @else
                    <a href="{{ route('admin.pengiriman.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition" title="Refresh">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Table: Tracker Pengiriman -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 font-display">Tabel Siklus Pengiriman & Ekspedisi</h3>
            <span class="text-xs font-bold text-slate-400">{{ $orders->total() }} Data Pesanan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th>ID Pesanan & Waktu</th>
                        <th>Komoditas Produk</th>
                        <th>Toko Asal Mitra</th>
                        <th>Tujuan & Penerima</th>
                        <th>Status & SLA Waktu</th>
                        <th>Kurir & No. Resi</th>
                        <th class="text-right">Aksi Resi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($orders as $ord)
                        @php
                            $hoursSincePaid = $ord->created_at->diffInHours(now());
                            $isOverdue = in_array($ord->status_pesanan, ['dikemas', null, '']) && $hoursSincePaid >= 24;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            
                            <!-- ID Pesanan -->
                            <td class="align-middle whitespace-nowrap">
                                <span class="font-extrabold text-slate-900 block font-mono">
                                    #ORD-{{ str_pad($ord->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    Dibayar: {{ $ord->created_at->translatedFormat('d M, H:i') }}
                                </span>
                            </td>

                            <!-- Komoditas Produk -->
                            <td class="align-middle">
                                <strong class="font-bold text-slate-900 block truncate max-w-[160px]">{{ $ord->produk->nama ?? 'Produk' }}</strong>
                                <span class="text-[10px] text-slate-400">{{ $ord->jumlah }} Pcs/Kg • Rp{{ number_format($ord->total_harga, 0, ',', '.') }}</span>
                            </td>

                            <!-- Toko Asal -->
                            <td class="align-middle">
                                <strong class="font-bold text-slate-900 block truncate max-w-[140px]">{{ $ord->produk->umkm->nama_toko ?? 'Kebun Mitra' }}</strong>
                                <span class="text-[10px] text-slate-400 block">{{ $ord->produk->umkm->alamat ?? 'Indramayu' }}</span>
                            </td>

                            <!-- Tujuan & Penerima -->
                            <td class="align-middle">
                                <strong class="font-bold text-slate-900 block">{{ $ord->name ?: ($ord->user->name ?? 'Pembeli') }}</strong>
                                <span class="text-[10px] text-slate-400 block truncate max-w-[160px]">{{ $ord->alamat }}</span>
                            </td>

                            <!-- SLA Waktu -->
                            <td class="align-middle">
                                @if($ord->status_pesanan === 'diterima')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-circle-check mr-1 text-[8px]"></i> Selesai
                                    </span>
                                @elseif($ord->status_pesanan === 'dikirim')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">
                                        <i class="fas fa-truck-fast mr-1 text-[8px]"></i> Di Perjalanan
                                    </span>
                                @elseif($isOverdue)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200 animate-pulse">
                                        <i class="fas fa-triangle-exclamation mr-1 text-[8px]"></i> Overdue ({{ $hoursSincePaid }} Jam)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fas fa-box-open mr-1 text-[8px]"></i> Dikemas ({{ $hoursSincePaid }} Jam)
                                    </span>
                                @endif
                            </td>

                            <!-- Kurir & Resi -->
                            <td class="align-middle">
                                @if($ord->resi_pengiriman)
                                    <span class="font-mono font-bold text-slate-800 block text-[11px]">{{ $ord->resi_pengiriman }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ $ord->kurir ?? 'Ekspedisi' }}</span>
                                @else
                                    <span class="text-[10px] text-slate-400 italic">Belum ada resi</span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="align-middle text-right">
                                <button 
                                    type="button" 
                                    onclick="openResiModal({{ $ord->id }}, '{{ $ord->resi_pengiriman }}', '{{ $ord->kurir }}', '{{ $ord->status_pesanan ?? 'dikemas' }}', '{{ addslashes($ord->catatan_pengiriman ?? '') }}')"
                                    class="px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white font-bold text-xs transition inline-flex items-center gap-1 border border-brand-200 shadow-xs"
                                >
                                    <i class="fas fa-truck text-xs"></i>
                                    <span>Kelola Resi</span>
                                </button>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-slate-400 text-xs">
                                Tidak ada data pengiriman sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($orders->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ========================================================================= -->
<!-- 🚚 MODAL UPDATE RESI & KURIR PENGIRIMAN                                    -->
<!-- ========================================================================= -->
<div id="resiModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-md w-full p-6 space-y-4 animate-scale-in">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-brand-50 text-brand-700 border border-brand-200">
                    Fulfillment Pengiriman
                </span>
                <h3 class="text-base font-extrabold text-slate-900 mt-1" id="modalResiTitle">
                    Kelola Resi Pesanan
                </h3>
            </div>
            <button onclick="closeResiModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <form id="formUpdateResi" method="POST" action="" class="space-y-4 text-xs">
            @csrf
            
            <div>
                <label class="block font-bold text-slate-700 mb-1">Pilih Ekspedisi / Kurir <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="kurir" 
                    id="inputKurir" 
                    list="kurirOptions" 
                    required 
                    placeholder="Misal: J&T Express, SiCepat, Kurir Petani Indramayu..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 focus:outline-none focus:border-brand-500"
                >
                <datalist id="kurirOptions">
                    <option value="J&T Express">
                    <option value="SiCepat Express">
                    <option value="JNE Express">
                    <option value="Pos Indonesia">
                    <option value="Kurir Petani Mandiri (Indramayu)">
                    <option value="Paxel (Fresh Fruit Delivery)">
                </datalist>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor Resi / Bukti Pengiriman <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="resi_pengiriman" 
                    id="inputResi" 
                    required 
                    placeholder="Contoh: JNT-982138912"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-500"
                >
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Status Pengiriman <span class="text-rose-500">*</span></label>
                <select name="status_pesanan" id="selectStatusPesanan" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 focus:outline-none focus:border-brand-500">
                    <option value="dikemas">Sedang Dikemas (Di Kebun)</option>
                    <option value="dikirim">Sedang Dikirim (Diserahkan ke Kurir)</option>
                    <option value="diterima">Telah Diterima (Selesai)</option>
                </select>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea 
                    name="catatan_pengiriman" 
                    id="inputCatatan" 
                    rows="2" 
                    placeholder="Misal: Paket mangga sudah dikemas dengan kardus berlubang aerasi..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-900 focus:outline-none focus:border-brand-500"
                ></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeResiModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl transition shadow-sm">
                    Simpan Resi & Status
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
function openResiModal(orderId, resi, kurir, status, catatan) {
    document.getElementById('modalResiTitle').innerText = `Kelola Resi Pesanan #ORD-${orderId.toString().padStart(5, '0')}`;
    document.getElementById('formUpdateResi').action = `/admin/pengiriman/${orderId}/resi`;
    document.getElementById('inputResi').value = resi || '';
    document.getElementById('inputKurir').value = kurir || '';
    document.getElementById('selectStatusPesanan').value = status || 'dikemas';
    document.getElementById('inputCatatan').value = catatan || '';
    document.getElementById('resiModal').classList.remove('hidden');
}

function closeResiModal() {
    document.getElementById('resiModal').classList.add('hidden');
}
</script>
@endpush

@extends('layouts.app')

@section('page_title', 'Audit & Transparansi Pesanan')

@section('content')
<div class="space-y-8 pb-12">
    
    <!-- Top Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
        <div class="space-y-2 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 border border-indigo-200 text-indigo-700 text-xs font-bold uppercase tracking-wider">
                <i class="fas fa-shield-halved text-indigo-600"></i>
                Pusat Transparansi Transaksi
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display tracking-tight">
                Audit & Log Semua Pesanan Mitra UMKM
            </h1>
            <p class="text-slate-500 text-xs sm:text-sm leading-relaxed">
                Pantau seluruh transaksi komoditas mangga secara transparan: rincian produk yang dibeli, asal toko/kebun petani, profil pembeli, dan status pembayaran Midtrans.
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">
            <button onclick="window.print()" class="px-4 py-2.5 bg-slate-50 hover:bg-slate-100 border border-slate-200 text-slate-700 font-bold text-xs rounded-xl transition flex items-center gap-2">
                <i class="fas fa-print text-slate-400"></i>
                <span>Cetak Rekap</span>
            </button>
        </div>
    </div>

    <!-- 4 Stats Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Pesanan Masuk</span>
                <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-sm">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 font-display">{{ $totalOrders }} <span class="text-xs font-sans text-slate-400 font-normal">Transaksi</span></p>
            <p class="text-[11px] text-slate-400 mt-1">Seluruh riwayat marketplace</p>
        </div>

        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Transaksi Sukses</span>
                <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-emerald-600 font-display">{{ $totalSuccess }} <span class="text-xs font-sans text-slate-400 font-normal">Pesanan</span></p>
            <p class="text-[11px] text-emerald-600 font-semibold mt-1">100% Lunas & Terverifikasi</p>
        </div>

        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Menunggu Pembayaran</span>
                <div class="w-9 h-9 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-sm">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <p class="text-2xl font-extrabold text-amber-600 font-display">{{ $totalPending }} <span class="text-xs font-sans text-slate-400 font-normal">Pesanan</span></p>
            <p class="text-[11px] text-amber-600 font-semibold mt-1">Pending Midtrans SNAP</p>
        </div>

        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Nilai Lunas</span>
                <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-sm">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <p class="text-xl font-extrabold text-slate-900 font-display">Rp{{ number_format($totalNominal, 0, ',', '.') }}</p>
            <p class="text-[11px] text-indigo-600 font-semibold mt-1">Omzet Bruto Terdata</p>
        </div>
    </div>

    <!-- Filter & Search Controls -->
    <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
        <form method="GET" action="{{ route('admin.pesanan.index') }}" class="flex flex-col lg:flex-row items-stretch lg:items-center justify-between gap-4">
            
            <!-- Status Tabs -->
            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'semua'])) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === 'semua' || !$status ? 'bg-slate-900 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua ({{ $totalOrders }})
                </a>
                <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'complete'])) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === 'complete' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                    Sukses Lunas ({{ $totalSuccess }})
                </a>
                <a href="{{ route('admin.pesanan.index', array_merge(request()->except('status', 'page'), ['status' => 'pending'])) }}" class="px-3.5 py-2 rounded-xl text-xs font-bold transition {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-amber-50 text-amber-700 hover:bg-amber-100' }}">
                    Pending ({{ $totalPending }})
                </a>
            </div>

            <!-- Store Dropdown & Search -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                <select name="umkm_id" onchange="this.form.submit()" class="px-3.5 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-700 focus:outline-none focus:border-brand-500">
                    <option value="">Semua Toko Mitra UMKM</option>
                    @foreach($umkms as $u)
                        <option value="{{ $u->id }}" {{ $umkmId == $u->id ? 'selected' : '' }}>
                            {{ $u->nama_toko }}
                        </option>
                    @endforeach
                </select>

                <div class="relative min-w-[240px]">
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Cari pembeli, produk, toko..." 
                        class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-medium focus:outline-none focus:border-brand-500"
                    >
                    <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                </div>

                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-xl text-xs font-bold transition shrink-0">
                    Cari
                </button>
            </div>

        </form>
    </div>

    <!-- Main Transparency Orders Table -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-extrabold text-slate-900 font-display">Tabel Rincian & Transparansi Pesanan</h3>
                <p class="text-xs text-slate-400 mt-0.5">Daftar menyeluruh transaksi marketplace, produk yang dipesan, dan toko mitra pemroses</p>
            </div>
            <span class="text-xs font-bold text-slate-500">{{ $orders->total() }} Transaksi Ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th>ID & Waktu</th>
                        <th>Komoditas & Produk</th>
                        <th>Asal Toko Mitra (Penjual)</th>
                        <th>Pembeli & Tujuan</th>
                        <th>Total & Bagi Hasil</th>
                        <th>Status</th>
                        <th class="text-right">Aksi Transparansi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($orders as $item)
                        @php
                            $produk = $item->produk;
                            $umkm = $produk->umkm ?? null;
                        @endphp
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- ID & Waktu -->
                            <td>
                                <span class="font-extrabold text-xs text-slate-900 block font-mono">
                                    #ORD-{{ str_pad($item->id, 5, '0', STR_PAD_LEFT) }}
                                </span>
                                <span class="text-[11px] text-slate-400 block mt-0.5">
                                    {{ $item->created_at->translatedFormat('d M Y, H:i') }}
                                </span>
                                @if($item->order_id_midtrans)
                                    <span class="inline-block text-[9px] font-mono text-indigo-600 bg-indigo-50 px-1.5 py-0.2 rounded mt-1">
                                        {{ Str::limit($item->order_id_midtrans, 14) }}
                                    </span>
                                @endif
                            </td>

                            <!-- Komoditas & Produk -->
                            <td>
                                <div class="flex items-center gap-3 max-w-xs">
                                    <div class="w-12 h-12 rounded-xl bg-slate-100 border border-slate-200 overflow-hidden shrink-0 shadow-sm">
                                        @if($produk && $produk->gambar)
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300">
                                                <i class="fas fa-box text-sm"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-xs text-slate-900 line-clamp-1">
                                            {{ $produk->nama ?? 'Produk Komoditas' }}
                                        </h4>
                                        <p class="text-[11px] text-slate-500 mt-0.5">
                                            <strong class="text-slate-700">{{ $item->jumlah }} Pcs/Kg</strong> × Rp{{ number_format($produk->harga ?? 0, 0, ',', '.') }}
                                        </p>
                                    </div>
                                </div>
                            </td>

                            <!-- Asal Toko Mitra (Penjual) -->
                            <td>
                                <div class="space-y-0.5">
                                    <div class="flex items-center gap-1.5">
                                        <i class="fas fa-store text-amber-500 text-xs"></i>
                                        <p class="font-extrabold text-xs text-slate-900">{{ $umkm->nama_toko ?? 'Mitra Petani' }}</p>
                                    </div>
                                    <p class="text-[11px] text-slate-500">
                                        Pemilik: <span class="font-medium text-slate-700">{{ $umkm->user->name ?? 'Penjual' }}</span>
                                    </p>
                                    <p class="text-[10px] text-slate-400 truncate max-w-[180px]">
                                        <i class="fas fa-map-pin text-[9px] mr-1"></i>{{ $umkm->alamat ?? 'Kab. Indramayu' }}
                                    </p>
                                </div>
                            </td>

                            <!-- Pembeli & Tujuan -->
                            <td>
                                <div class="space-y-0.5">
                                    <p class="font-bold text-xs text-slate-900">{{ $item->name ?: ($item->user->name ?? 'Pembeli') }}</p>
                                    <p class="text-[11px] text-slate-500">{{ $item->phone ?: '-' }}</p>
                                    <p class="text-[10px] text-slate-400 truncate max-w-[180px]">
                                        {{ $item->alamat ?: 'Indramayu' }}
                                    </p>
                                </div>
                            </td>

                            <!-- Total & Bagi Hasil Transparan -->
                            <td>
                                <p class="text-xs font-extrabold text-slate-900">
                                    Rp{{ number_format($item->total_harga, 0, ',', '.') }}
                                </p>
                                <div class="text-[10px] text-slate-400 mt-0.5 space-y-0.5">
                                    <span class="text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded font-semibold block w-max">
                                        Petani ({{ $tokoPersen }}%): Rp{{ number_format($item->total_harga * ($tokoPersen / 100), 0, ',', '.') }}
                                    </span>
                                    <span class="text-indigo-700 bg-indigo-50 px-1.5 py-0.5 rounded font-semibold block w-max">
                                        Platform ({{ $komisiPersen }}%): Rp{{ number_format($item->total_harga * ($komisiPersen / 100), 0, ',', '.') }}
                                    </span>
                                </div>
                            </td>

                            <!-- Status -->
                            <td>
                                @if($item->status === 'complete')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-circle-check mr-1 text-[9px]"></i> Lunas (Complete)
                                    </span>
                                    <span class="block text-[10px] text-slate-400 mt-1 capitalize font-medium">
                                        Pesanan: {{ str_replace('_', ' ', $item->status_pesanan ?? 'diterima') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fas fa-clock mr-1 text-[9px]"></i> Menunggu Bayar
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button 
                                        type="button" 
                                        onclick="openOrderModal({{ $item->id }})" 
                                        class="px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm border border-brand-200"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                        <span>Detail</span>
                                    </button>
                                    <a 
                                        href="{{ route('admin.pesanan.show', $item->id) }}" 
                                        class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs transition"
                                        title="Buka Halaman Lengkap"
                                    >
                                        <i class="fas fa-arrow-up-right-from-square"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400 text-xs">
                                <i class="fas fa-receipt text-3xl text-slate-300 mb-2 block"></i>
                                Tidak ada data pesanan yang sesuai dengan filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($orders->hasPages())
            <div class="p-6 border-t border-slate-100">
                {{ $orders->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ========================================================================= -->
<!-- 🔍 MODAL DETAIL TRANSPARANSI PESANAN (POP-UP INTERAKTIF)                   -->
<!-- ========================================================================= -->
<div id="orderDetailModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="relative w-full max-w-2xl bg-white rounded-3xl shadow-2xl border border-slate-200 overflow-hidden transform transition-all">
        
        <!-- Modal Header -->
        <div class="p-6 bg-slate-900 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-2xl bg-brand-500/20 text-brand-400 flex items-center justify-center text-lg border border-brand-500/40">
                    <i class="fas fa-receipt"></i>
                </div>
                <div>
                    <h3 class="text-base font-extrabold text-white font-display" id="modalOrderId">Rincian Transparansi Pesanan</h3>
                    <p class="text-xs text-slate-400" id="modalOrderDate">-</p>
                </div>
            </div>
            <button onclick="closeOrderModal()" class="text-slate-400 hover:text-white p-2 rounded-xl hover:bg-slate-800 transition">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <!-- Modal Body (Content Dynamically Loaded) -->
        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto" id="modalBody">
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 text-brand-600"></i>
                <p class="text-xs">Memuat data transparansi pesanan...</p>
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">
                <i class="fas fa-lock text-emerald-500 mr-1"></i> Data diverifikasi oleh Sistem Superadmin
            </span>
            <div class="flex items-center gap-2">
                <button onclick="closeOrderModal()" class="px-4 py-2 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-bold text-xs transition">
                    Tutup
                </button>
                <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs transition flex items-center gap-1.5 shadow-sm">
                    <i class="fas fa-print"></i> Cetak
                </button>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function openOrderModal(orderId) {
    const modal = document.getElementById('orderDetailModal');
    const body = document.getElementById('modalBody');
    modal.classList.remove('hidden');

    body.innerHTML = `
        <div class="text-center py-10 text-slate-400">
            <i class="fas fa-circle-notch fa-spin text-3xl mb-2 text-brand-600"></i>
            <p class="text-xs font-semibold">Mengambil rincian transparansi pesanan...</p>
        </div>
    `;

    fetch(`/admin/pesanan/${orderId}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(json => {
        if (!json.success) throw new Error('Gagal mengambil data');
        const data = json.data;

        document.getElementById('modalOrderId').innerText = `Rincian Pesanan #${data.order_id_midtrans}`;
        document.getElementById('modalOrderDate').innerText = `Waktu Transaksi: ${data.created_at}`;

        const isComplete = data.status === 'complete';

        body.innerHTML = `
            <!-- Status Pill Banner -->
            <div class="p-4 rounded-2xl ${isComplete ? 'bg-emerald-50 border border-emerald-200' : 'bg-amber-50 border border-amber-200'} flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fas ${isComplete ? 'fa-check-circle text-emerald-600' : 'fa-clock text-amber-600'} text-lg"></i>
                    <div>
                        <p class="text-xs font-extrabold ${isComplete ? 'text-emerald-900' : 'text-amber-900'} uppercase">Status: ${data.status}</p>
                        <p class="text-[11px] ${isComplete ? 'text-emerald-700' : 'text-amber-700'}">Fulfillment: ${data.status_pesanan || 'Diterima'}</p>
                    </div>
                </div>
                <span class="text-xs font-extrabold ${isComplete ? 'text-emerald-700 bg-emerald-100' : 'text-amber-700 bg-amber-100'} px-3 py-1 rounded-xl">
                    ${data.total_harga_formatted}
                </span>
            </div>

            <!-- Toko & Petani Asal (Seller Information) -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2">
                <div class="flex items-center justify-between border-b border-slate-200/60 pb-2">
                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-amber-700 bg-amber-100 px-2 py-0.5 rounded">
                        <i class="fas fa-store mr-1"></i> Asal Toko Mitra (Penjual)
                    </span>
                    <span class="text-xs font-bold text-slate-800">${data.toko.nama_toko}</span>
                </div>
                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Nama Petani / Pemilik:</span>
                        <strong class="text-slate-800">${data.toko.pemilik}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">No. Telepon Toko:</span>
                        <strong class="text-slate-800">${data.toko.no_telp}</strong>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 block text-[10px]">Alamat Sentra Kebun:</span>
                        <span class="text-slate-700">${data.toko.alamat}</span>
                    </div>
                </div>
            </div>

            <!-- Produk Komoditas Yang Dipesan -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-indigo-700 bg-indigo-100 px-2 py-0.5 rounded">
                    <i class="fas fa-box-open mr-1"></i> Komoditas Yang Dibeli
                </span>
                <div class="flex items-center gap-3">
                    <img src="${data.produk.gambar_url}" alt="${data.produk.nama}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 shadow-sm">
                    <div class="flex-1">
                        <h4 class="font-extrabold text-xs text-slate-900">${data.produk.nama}</h4>
                        <p class="text-[11px] text-slate-500 mt-0.5">Kategori: <strong class="text-slate-700">${data.produk.kategori}</strong></p>
                        <div class="flex items-center justify-between mt-1 text-xs">
                            <span class="text-slate-600 font-bold">${data.jumlah} Pcs/Kg × ${data.produk.harga_formatted}</span>
                            <span class="font-extrabold text-slate-900">${data.total_harga_formatted}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Profil Pembeli & Destinasi -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-2">
                <span class="text-[10px] font-extrabold uppercase tracking-wider text-purple-700 bg-purple-100 px-2 py-0.5 rounded">
                    <i class="fas fa-user-check mr-1"></i> Data Pembeli & Destinasi Pengiriman
                </span>
                <div class="grid grid-cols-2 gap-2 text-xs pt-1">
                    <div>
                        <span class="text-slate-400 block text-[10px]">Nama Pembeli:</span>
                        <strong class="text-slate-800">${data.pembeli.name}</strong>
                    </div>
                    <div>
                        <span class="text-slate-400 block text-[10px]">Kontak / HP:</span>
                        <strong class="text-slate-800">${data.pembeli.phone}</strong>
                    </div>
                    <div class="col-span-2">
                        <span class="text-slate-400 block text-[10px]">Alamat Tujuan:</span>
                        <span class="text-slate-700">${data.pembeli.alamat}</span>
                    </div>
                </div>
            </div>

            <!-- Transparansi Bagi Hasil -->
            <div class="p-4 rounded-2xl bg-brand-50/70 border border-brand-100 space-y-2 text-xs">
                <div class="flex items-center justify-between">
                    <span class="font-extrabold text-brand-900">Transparansi Finansial Marketplace:</span>
                    <span class="text-brand-700 font-bold">100% Tercatat</span>
                </div>
                <div class="flex items-center justify-between pt-1 border-t border-brand-100 text-[11px]">
                    <span class="text-slate-600">Hak Omzet Mitra Petani (80%):</span>
                    <strong class="text-emerald-700">${data.bagi_hasil.omzet_petani}</strong>
                </div>
                <div class="flex items-center justify-between text-[11px]">
                    <span class="text-slate-600">Komisi Operasional Platform (20%):</span>
                    <strong class="text-indigo-700">${data.bagi_hasil.komisi_platform}</strong>
                </div>
            </div>
        `;
    })
    .catch(err => {
        body.innerHTML = `
            <div class="text-center py-8 text-rose-500">
                <i class="fas fa-triangle-exclamation text-3xl mb-2"></i>
                <p class="text-xs font-bold">Gagal memuat detail pesanan.</p>
            </div>
        `;
    });
}

function closeOrderModal() {
    document.getElementById('orderDetailModal').classList.add('hidden');
}
</script>
@endpush

@extends('layouts.public')

@section('title', 'Pesanan Saya — Juragan Pelem')

@push('styles')
<style>
    .tab-btn.active {
        color: #4f46e5;
        border-color: #4f46e5;
        background-color: #eef2ff;
    }
    .order-card {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .order-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.08), 0 6px 12px -4px rgba(0, 0, 0, 0.04);
    }
</style>
@endpush

@section('content')
<main class="py-8 sm:py-12 bg-slate-50 min-h-screen">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

        <!-- 1. Header & Quick Stat Overview -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-slate-200/80 shadow-sm">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-600 text-2xl shrink-0 shadow-inner">
                        <i class="fas fa-box-archive"></i>
                    </div>
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 font-display tracking-tight">Pesanan Saya</h1>
                            <span class="px-2.5 py-0.5 rounded-full bg-indigo-100 text-indigo-800 font-extrabold text-xs">
                                {{ $orders->count() }} Total Transaksi
                            </span>
                        </div>
                        <p class="text-xs sm:text-sm text-slate-500 mt-1">
                            Pantau proses panen, pengemasan, ekspedisi, hingga konfirmasi penerimaan mangga segar Anda.
                        </p>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="flex items-center gap-3">
                    <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-100 text-center min-w-[100px]">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Aktif</span>
                        <span class="text-base font-black text-amber-600">
                            {{ $orders->whereIn('status_pesanan', ['dikemas', 'dikirim'])->count() }} Pesanan
                        </span>
                    </div>
                    <div class="px-4 py-2.5 rounded-2xl bg-slate-50 border border-slate-100 text-center min-w-[100px]">
                        <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Selesai</span>
                        <span class="text-base font-black text-emerald-600">
                            {{ $orders->where('status_pesanan', 'diterima')->count() }} Sukses
                        </span>
                    </div>
                    <a href="{{ route('kategori') }}" class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition">
                        <i class="fas fa-plus"></i>
                        <span>Belanja Lagi</span>
                    </a>
                </div>
            </div>

            <!-- Notification Alerts -->
            @if(session('success'))
                <div class="mt-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs sm:text-sm flex items-center gap-3 animate-fade-in">
                    <i class="fas fa-check-circle text-emerald-600 text-base shrink-0"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif
            @if(session('error'))
                <div class="mt-6 p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs sm:text-sm flex items-center gap-3 animate-fade-in">
                    <i class="fas fa-triangle-exclamation text-rose-600 text-base shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif
        </div>

        <!-- 2. Dynamic Filter Tabs & Search Bar -->
        <div class="space-y-4">
            <!-- Tabs (Shopee / Tokopedia Style) -->
            <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm overflow-x-auto">
                <div class="flex items-center gap-1.5 min-w-max">
                    @php
                        $countAll = $orders->count();
                        $countPending = $orders->where('status', 'pending')->count();
                        $countDikemas = $orders->where('status', 'complete')->where(function($o){ return in_array($o->status_pesanan, ['dikemas', null]); })->count();
                        $countDikirim = $orders->where('status_pesanan', 'dikirim')->count();
                        $countDiterima = $orders->where('status_pesanan', 'diterima')->count();
                        $countCancel = $orders->where('status', 'cancel')->count();
                    @endphp

                    <button type="button" onclick="switchOrderTab('all')" id="tab-all" class="tab-btn active px-4 py-2.5 rounded-xl font-extrabold text-xs transition flex items-center gap-2 border border-transparent">
                        <i class="fas fa-layer-group text-slate-400"></i>
                        <span>Semua</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] bg-slate-200/70 text-slate-700">{{ $countAll }}</span>
                    </button>

                    <button type="button" onclick="switchOrderTab('pending')" id="tab-pending" class="tab-btn px-4 py-2.5 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-slate-50 font-bold text-xs transition flex items-center gap-2 border border-transparent">
                        <i class="fas fa-clock text-amber-500"></i>
                        <span>Belum Bayar</span>
                        @if($countPending > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-extrabold">{{ $countPending }}</span>
                        @endif
                    </button>

                    <button type="button" onclick="switchOrderTab('dikemas')" id="tab-dikemas" class="tab-btn px-4 py-2.5 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-slate-50 font-bold text-xs transition flex items-center gap-2 border border-transparent">
                        <i class="fas fa-box text-amber-600"></i>
                        <span>Sedang Dikemas</span>
                        @if($countDikemas > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-amber-100 text-amber-800 font-extrabold">{{ $countDikemas }}</span>
                        @endif
                    </button>

                    <button type="button" onclick="switchOrderTab('dikirim')" id="tab-dikirim" class="tab-btn px-4 py-2.5 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-slate-50 font-bold text-xs transition flex items-center gap-2 border border-transparent">
                        <i class="fas fa-truck-fast text-blue-500"></i>
                        <span>Sedang Dikirim</span>
                        @if($countDikirim > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-blue-100 text-blue-800 font-extrabold">{{ $countDikirim }}</span>
                        @endif
                    </button>

                    <button type="button" onclick="switchOrderTab('diterima')" id="tab-diterima" class="tab-btn px-4 py-2.5 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-slate-50 font-bold text-xs transition flex items-center gap-2 border border-transparent">
                        <i class="fas fa-circle-check text-emerald-500"></i>
                        <span>Selesai</span>
                        @if($countDiterima > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-emerald-100 text-emerald-800 font-extrabold">{{ $countDiterima }}</span>
                        @endif
                    </button>

                    <button type="button" onclick="switchOrderTab('cancel')" id="tab-cancel" class="tab-btn px-4 py-2.5 rounded-xl text-slate-600 hover:text-indigo-600 hover:bg-slate-50 font-bold text-xs transition flex items-center gap-2 border border-transparent">
                        <i class="fas fa-ban text-rose-500"></i>
                        <span>Dibatalkan</span>
                        @if($countCancel > 0)
                            <span class="px-2 py-0.5 rounded-full text-[10px] bg-rose-100 text-rose-800 font-extrabold">{{ $countCancel }}</span>
                        @endif
                    </button>
                </div>
            </div>

            <!-- Search & Sort Filter Bar -->
            <div class="flex flex-col sm:flex-row items-center justify-between gap-3">
                <div class="relative w-full sm:w-80">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input 
                        type="text" 
                        id="orderSearchInput" 
                        onkeyup="filterOrderCards()"
                        placeholder="Cari produk, toko, invoice, resi..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 shadow-sm transition"
                    >
                </div>

                <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                    <select id="orderSortSelect" onchange="sortOrderCards()" class="px-3.5 py-2.5 rounded-2xl bg-white border border-slate-200 text-xs font-semibold text-slate-700 shadow-sm focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition">
                        <option value="newest">Waktu Transaksi (Terbaru)</option>
                        <option value="oldest">Waktu Transaksi (Terlama)</option>
                        <option value="highest">Nominal Tertinggi</option>
                        <option value="lowest">Nominal Terendah</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- 3. Dynamic Order Cards Container -->
        <div id="ordersContainer" class="space-y-4">
            @forelse($orders as $order)
                @php
                    // Resolve normalized status category for filter tabs
                    $tabCategory = 'dikemas';
                    if ($order->status === 'pending') {
                        $tabCategory = 'pending';
                    } elseif ($order->status === 'cancel') {
                        $tabCategory = 'cancel';
                    } elseif ($order->status_pesanan === 'dikirim') {
                        $tabCategory = 'dikirim';
                    } elseif ($order->status_pesanan === 'diterima') {
                        $tabCategory = 'diterima';
                    }

                    $cleanStoreName = $order->produk->umkm->nama_toko ?? ($order->produk->user->name ?? 'Kebun Mitra Panen');
                    $orderSearchText = strtolower($order->produk->nama . ' ' . $cleanStoreName . ' ' . $order->order_id_midtrans . ' ' . $order->resi_pengiriman . ' ' . $order->id);
                @endphp

                <div 
                    class="order-card bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden" 
                    data-category="{{ $tabCategory }}"
                    data-search="{{ $orderSearchText }}"
                    data-timestamp="{{ $order->created_at->timestamp }}"
                    data-price="{{ $order->total_harga }}"
                >
                    <!-- Card Top Ribbon (Store Info, Date, Status Pill) -->
                    <div class="px-5 py-4 sm:px-6 bg-slate-50/70 border-b border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-indigo-600 text-xs shadow-xs">
                                <i class="fas fa-store"></i>
                            </div>
                            <div>
                                <div class="flex items-center gap-2">
                                    <h4 class="font-extrabold text-slate-800 text-xs sm:text-sm hover:text-indigo-600 transition">
                                        {{ $cleanStoreName }}
                                    </h4>
                                    @if($order->produk && ($order->produk->user_id || ($order->produk->umkm && $order->produk->umkm->user_id)))
                                        <a href="{{ route('pembeli.chat.index', ['id' => $order->produk->user_id ?? $order->produk->umkm->user_id]) }}" class="text-[11px] text-indigo-600 font-bold hover:underline flex items-center gap-1">
                                            <i class="fas fa-comment-dots"></i> Chat
                                        </a>
                                    @endif
                                </div>
                                <span class="text-[11px] text-slate-400 font-medium">
                                    {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB • No. Pesanan: #{{ $order->id }}
                                </span>
                            </div>
                        </div>

                        <!-- Status Badge -->
                        <div class="flex items-center gap-2">
                            @if($order->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-xs font-black uppercase tracking-wider border border-amber-300">
                                    <i class="fas fa-clock text-amber-600 animate-pulse"></i>
                                    <span>Menunggu Pembayaran</span>
                                </span>
                            @elseif($order->status === 'cancel')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-rose-100 text-rose-800 text-xs font-black uppercase tracking-wider border border-rose-300">
                                    <i class="fas fa-ban text-rose-600"></i>
                                    <span>Dibatalkan</span>
                                </span>
                            @elseif($order->status_pesanan === 'dikirim')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-blue-100 text-blue-800 text-xs font-black uppercase tracking-wider border border-blue-300">
                                    <i class="fas fa-truck-fast text-blue-600"></i>
                                    <span>Dalam Pengiriman</span>
                                </span>
                            @elseif($order->status_pesanan === 'diterima')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-100 text-emerald-800 text-xs font-black uppercase tracking-wider border border-emerald-300">
                                    <i class="fas fa-check-circle text-emerald-600"></i>
                                    <span>Pesanan Selesai</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-amber-50 text-amber-800 text-xs font-black uppercase tracking-wider border border-amber-200">
                                    <i class="fas fa-box-open text-amber-600"></i>
                                    <span>Sedang Dikemas Mitra</span>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Card Body (Product Details) -->
                    <div class="p-5 sm:p-6">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                            
                            <!-- Left: Product Image & Specs -->
                            <div class="flex items-start gap-4">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200/80 relative group">
                                    @if($order->produk && $order->produk->gambar)
                                        <img src="{{ asset('storage/' . $order->produk->gambar) }}" alt="{{ $order->produk->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center text-slate-300 text-lg">
                                            <i class="fas fa-image"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="space-y-1">
                                    <h3 class="font-black text-slate-900 text-sm sm:text-base leading-snug hover:text-indigo-600 transition">
                                        <a href="{{ route('pembeli.invoice.show', $order->id) }}">{{ $order->produk->nama ?? 'Komoditas Mangga' }}</a>
                                    </h3>
                                    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500 font-medium">
                                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[11px] font-semibold">
                                            {{ $order->produk->kategori->nama ?? 'Komoditas Unggulan' }}
                                        </span>
                                        <span>•</span>
                                        <span>{{ $order->jumlah }} pack ({{ (($order->produk->berat_gram ?? 1000) * $order->jumlah) / 1000 }} kg)</span>
                                    </div>
                                    
                                    <!-- Courier & Resi Pill if Shipped -->
                                    @if($order->resi_pengiriman)
                                        <div class="pt-1 flex items-center gap-2 text-xs">
                                            <span class="text-slate-400 font-medium">Resi:</span>
                                            <span class="px-2.5 py-0.5 rounded-lg bg-indigo-50 border border-indigo-100 text-indigo-700 font-mono font-bold text-[11px] flex items-center gap-1.5">
                                                <i class="fas fa-barcode"></i>
                                                {{ $order->resi_pengiriman }}
                                            </span>
                                            <span class="text-slate-400 text-[11px]">({{ $order->kurir ?? 'Kurir Fresh' }})</span>
                                        </div>
                                    @endif

                                    <!-- Complaint / Dispute Status if exists -->
                                    @if($order->komplain)
                                        <div class="pt-1">
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-md bg-orange-100 text-orange-800 text-[11px] font-bold">
                                                <i class="fas fa-shield-halved"></i>
                                                Garansi: {{ ucfirst(str_replace('_', ' ', $order->komplain->status)) }}
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Right: Price & Subtotal -->
                            <div class="text-left sm:text-right border-t sm:border-t-0 pt-3 sm:pt-0 w-full sm:w-auto">
                                <span class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Belanja</span>
                                <span class="text-lg sm:text-xl font-black text-indigo-700 font-display">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </span>
                                <span class="block text-[11px] text-slate-400 font-medium mt-0.5">Sudah termasuk ongkir promo</span>
                            </div>

                        </div>
                    </div>

                    <!-- Card Footer Actions (Contextual per State) -->
                    <div class="px-5 py-3.5 sm:px-6 bg-slate-50/50 border-t border-slate-100 flex flex-wrap items-center justify-between gap-3">
                        
                        <!-- Left Guarantee Seal -->
                        <div class="flex items-center gap-2 text-xs text-slate-500 font-medium">
                            <i class="fas fa-shield-check text-emerald-600"></i>
                            <span class="hidden sm:inline">Garansi Segar 100% Petani Indramayu</span>
                        </div>

                        <!-- Right Action Buttons -->
                        <div class="flex flex-wrap items-center gap-2 ml-auto">

                            <!-- Action: Pending (Bayar & Batalkan) -->
                            @if($order->status === 'pending')
                                <button type="button" onclick="openCancelModal({{ $order->id }}, '{{ $order->produk->nama ?? 'Mangga' }}')" class="px-3.5 py-2 rounded-xl bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold transition">
                                    Batalkan Pesanan
                                </button>
                                <a href="{{ route('pembeli.status.belum-bayar') }}" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-black shadow-sm transition flex items-center gap-1.5">
                                    <i class="fas fa-credit-card"></i>
                                    <span>Bayar Sekarang</span>
                                </a>

                            <!-- Action: Dikemas (Invoice, PDF, Chat, Batal) -->
                            @elseif($order->status === 'complete' && in_array($order->status_pesanan, ['dikemas', null]))
                                <button type="button" onclick="openCancelModal({{ $order->id }}, '{{ $order->produk->nama ?? 'Mangga' }}')" class="px-3 py-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 text-xs font-semibold transition">
                                    Batalkan
                                </button>
                                <a href="{{ route('pembeli.invoice.show', $order->id) }}" class="px-3.5 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                                    <i class="fas fa-file-invoice text-slate-400"></i>
                                    <span>Invoice</span>
                                </a>
                                <a href="{{ route('pembeli.invoice.pdf', $order->id) }}" class="px-3.5 py-2 rounded-xl bg-indigo-50 border border-indigo-100 text-indigo-700 hover:bg-indigo-100 text-xs font-bold transition flex items-center gap-1.5" title="Unduh File PDF">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>PDF</span>
                                </a>

                            <!-- Action: Dikirim (Lacak, Konfirmasi Diterima, Komplain) -->
                            @elseif($order->status_pesanan === 'dikirim')
                                <button type="button" onclick="openTrackingModal('{{ $order->kurir ?? 'Kurir Mitra Panen' }}', '{{ $order->resi_pengiriman ?? 'JP-EXP-8891' }}', '{{ $order->created_at->format('d M Y, H:i') }}', '{{ $cleanStoreName }}', '{{ $order->name }}')" class="px-3.5 py-2 rounded-xl bg-blue-50 border border-blue-200 text-blue-700 hover:bg-blue-100 text-xs font-bold transition flex items-center gap-1.5">
                                    <i class="fas fa-route"></i>
                                    <span>Lacak Ekspedisi</span>
                                </button>

                                <a href="{{ route('pembeli.komplain.create', $order->id) }}" class="px-3 py-2 rounded-xl bg-white border border-orange-200 text-orange-700 hover:bg-orange-50 text-xs font-bold transition">
                                    Garansi Buah
                                </a>

                                <form action="{{ route('pembeli.pesanan.updateStatus', $order->id) }}" method="POST" onsubmit="return confirm('Konfirmasi bahwa buah mangga telah tiba dengan kondisi segar dan baik?')" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-black shadow-sm transition flex items-center gap-1.5">
                                        <i class="fas fa-circle-check"></i>
                                        <span>Konfirmasi Diterima</span>
                                    </button>
                                </form>

                            <!-- Action: Selesai / Diterima (Beli Lagi, Ulasan, Invoice) -->
                            @elseif($order->status_pesanan === 'diterima')
                                <a href="{{ route('pembeli.invoice.show', $order->id) }}" class="px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-bold transition flex items-center gap-1.5">
                                    <i class="fas fa-file-invoice text-slate-400"></i>
                                    <span>Invoice</span>
                                </a>

                                @if($order->ulasan)
                                    <span class="px-3 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold flex items-center gap-1">
                                        <i class="fas fa-star text-amber-500"></i>
                                        <span>{{ $order->ulasan->bintang }}/5 Diulas</span>
                                    </span>
                                @else
                                    <a href="{{ route('pembeli.rating.create', ['order' => $order->id, 'product' => $order->produk_id]) }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-xs transition flex items-center gap-1.5">
                                        <i class="fas fa-star"></i>
                                        <span>Beri Ulasan</span>
                                    </a>
                                @endif

                                @if($order->produk_id)
                                    <a href="{{ route('pembeli.order', ['produk_id' => $order->produk_id]) }}" class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-sm transition flex items-center gap-1.5">
                                        <i class="fas fa-rotate-right"></i>
                                        <span>Beli Lagi</span>
                                    </a>
                                @endif

                            <!-- Action: Dibatalkan (Hapus Riwayat, Beli Lagi) -->
                            @elseif($order->status === 'cancel')
                                @if($order->batal_alasan)
                                    <span class="text-[11px] text-slate-400 italic max-w-xs truncate" title="Alasan: {{ $order->batal_alasan }}">
                                        Alasan: "{{ $order->batal_alasan }}"
                                    </span>
                                @endif
                                <form action="{{ route('pembeli.pesanan.destroy', $order->id) }}" method="POST" onsubmit="return confirm('Hapus pesanan dibatalkan ini dari riwayat?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 text-xs font-semibold transition">
                                        <i class="fas fa-trash-can mr-1"></i> Hapus
                                    </button>
                                </form>
                                @if($order->produk_id)
                                    <a href="{{ route('pembeli.order', ['produk_id' => $order->produk_id]) }}" class="px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold transition">
                                        Beli Produk Ini
                                    </a>
                                @endif
                            @endif

                        </div>
                    </div>
                </div>
            @empty
                <!-- Empty State -->
                <div class="bg-white rounded-3xl border border-slate-200/80 p-12 text-center shadow-sm">
                    <div class="w-20 h-20 rounded-3xl bg-indigo-50 border border-indigo-100 flex items-center justify-center text-indigo-500 text-3xl mx-auto mb-4">
                        <i class="fas fa-basket-shopping"></i>
                    </div>
                    <h3 class="text-lg font-black text-slate-900">Belum Ada Riwayat Pesanan</h3>
                    <p class="text-xs sm:text-sm text-slate-500 mt-1 max-w-md mx-auto">
                        Nikmati manis dan segarnya mangga pilihan langsung dari petani mitra Indramayu hari ini.
                    </p>
                    <div class="mt-6">
                        <a href="{{ route('kategori') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-black shadow-md shadow-indigo-600/20 transition">
                            <i class="fas fa-magnifying-glass"></i>
                            <span>Jelajahi Katalog Mangga Segar</span>
                        </a>
                    </div>
                </div>
            @endforelse

            <!-- Filter No Results Found State -->
            <div id="noResultsState" class="hidden bg-white rounded-3xl border border-slate-200/80 p-12 text-center shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-slate-100 flex items-center justify-center text-slate-400 text-2xl mx-auto mb-3">
                    <i class="fas fa-filter-circle-xmark"></i>
                </div>
                <h4 class="font-bold text-slate-800 text-sm">Tidak Ada Pesanan yang Cocok</h4>
                <p class="text-xs text-slate-500 mt-1">Coba gunakan kata kunci pencarian lain atau ganti tab filter.</p>
                <button type="button" onclick="resetFilters()" class="mt-4 px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition">
                    Reset Filter
                </button>
            </div>
        </div>

    </div>
</main>

<!-- 4. Interactive Modals -->

<!-- Modal: Lacak Ekspedisi Pengiriman -->
<div id="trackingModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6 animate-scale-in">
        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg">
                    <i class="fas fa-truck-fast"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 text-base">Lacak Perjalanan Paket</h4>
                    <p class="text-xs text-slate-400" id="trackingCourierText">Kurir Mitra Fresh</p>
                </div>
            </div>
            <button type="button" onclick="closeTrackingModal()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Tracking Timeline -->
        <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-indigo-100">
            
            <div class="relative">
                <div class="absolute -left-6 top-0 w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-emerald-100 flex items-center justify-center text-[8px] text-white">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-slate-900">Pesanan Dikonfirmasi & Terverifikasi</h5>
                    <p class="text-[11px] text-slate-400 mt-0.5" id="timelineDate1">Waktu Transaksi</p>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -left-6 top-0 w-4 h-4 rounded-full bg-emerald-500 ring-4 ring-emerald-100 flex items-center justify-center text-[8px] text-white">
                    <i class="fas fa-check"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-slate-900">Buah Dipetik & Dikemas di Kebun Mitra</h5>
                    <p class="text-[11px] text-slate-500 mt-0.5" id="timelineStoreText">Sentra Mangga Indramayu</p>
                </div>
            </div>

            <div class="relative">
                <div class="absolute -left-6 top-0 w-4 h-4 rounded-full bg-indigo-600 ring-4 ring-indigo-100 flex items-center justify-center text-[8px] text-white">
                    <i class="fas fa-truck"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-indigo-700">Dalam Pengiriman Menuju Alamat Penerima</h5>
                    <p class="text-[11px] text-slate-500 mt-0.5" id="timelineResiText">No. Resi: JP-EXP-8891</p>
                </div>
            </div>

            <div class="relative opacity-60">
                <div class="absolute -left-6 top-0 w-4 h-4 rounded-full bg-slate-200 flex items-center justify-center text-[8px] text-slate-400">
                    <i class="fas fa-house"></i>
                </div>
                <div>
                    <h5 class="text-xs font-bold text-slate-600">Pesanan Tiba & Siap Dinikmati</h5>
                    <p class="text-[11px] text-slate-400 mt-0.5" id="timelineBuyerText">Tujuan Pengiriman</p>
                </div>
            </div>

        </div>

        <div class="pt-2 flex justify-end">
            <button type="button" onclick="closeTrackingModal()" class="px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition">
                Tutup Pelacakan
            </button>
        </div>
    </div>
</div>

<!-- Modal: Batalkan Pesanan -->
<div id="cancelModal" class="fixed inset-0 z-50 hidden bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-5 animate-scale-in">
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2.5">
                <div class="w-9 h-9 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base">
                    <i class="fas fa-ban"></i>
                </div>
                <div>
                    <h4 class="font-extrabold text-slate-900 text-sm sm:text-base">Batalkan Pesanan</h4>
                    <p class="text-[11px] text-slate-400" id="cancelModalProductName">Mangga</p>
                </div>
            </div>
            <button type="button" onclick="closeCancelModal()" class="w-7 h-7 rounded-full bg-slate-100 text-slate-400 hover:text-slate-600 flex items-center justify-center transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <form id="cancelOrderForm" method="POST" action="">
            @csrf
            <div class="space-y-4">
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-slate-700">Pilih Alasan Pembatalan:</label>
                    <select id="reasonSelect" onchange="handleReasonChange(this)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-xs font-medium text-slate-800 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500">
                        <option value="Ingin mengubah varian komoditas mangga">Ingin mengubah varian komoditas mangga</option>
                        <option value="Salah memasukkan alamat pengiriman">Salah memasukkan alamat pengiriman</option>
                        <option value="Ingin menambah jumlah kuantitas pesanan">Ingin menambah jumlah kuantitas pesanan</option>
                        <option value="Waktu pengiriman tidak sesuai">Waktu pengiriman tidak sesuai</option>
                        <option value="Lainnya">Alasan Lainnya</option>
                    </select>
                </div>

                <div class="space-y-1.5">
                    <label class="block text-xs font-bold text-slate-700">Catatan Tambahan:</label>
                    <textarea 
                        name="batal_alasan" 
                        id="cancelReasonText" 
                        rows="3" 
                        required
                        placeholder="Tuliskan keterangan pembatalan Anda..." 
                        class="w-full p-3 rounded-xl border border-slate-200 text-xs text-slate-800 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500"
                    >Ingin mengubah varian komoditas mangga</textarea>
                </div>

                <p class="text-[11px] text-slate-400 leading-relaxed">
                    <i class="fas fa-circle-info text-slate-400 mr-1"></i> Stok komoditas akan otomatis dikembalikan ke kuota toko mitra petani setelah pembatalan.
                </p>

                <div class="pt-2 flex items-center justify-end gap-2">
                    <button type="button" onclick="closeCancelModal()" class="px-4 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 text-xs font-bold transition">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition">
                        Konfirmasi Pembatalan
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    let activeTab = 'all';

    function switchOrderTab(tab) {
        activeTab = tab;
        
        // Update active class on tab buttons
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('active');
            btn.classList.add('text-slate-600');
        });
        const currentBtn = document.getElementById('tab-' + tab);
        if (currentBtn) {
            currentBtn.classList.add('active');
            currentBtn.classList.remove('text-slate-600');
        }

        filterOrderCards();
    }

    function filterOrderCards() {
        const query = document.getElementById('orderSearchInput').value.toLowerCase().trim();
        const cards = document.querySelectorAll('.order-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const category = card.getAttribute('data-category');
            const searchData = card.getAttribute('data-search') || '';

            const matchesTab = (activeTab === 'all') || (category === activeTab);
            const matchesQuery = !query || searchData.includes(query);

            if (matchesTab && matchesQuery) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        const noResults = document.getElementById('noResultsState');
        if (noResults) {
            noResults.style.display = (visibleCount === 0 && cards.length > 0) ? 'block' : 'none';
        }
    }

    function sortOrderCards() {
        const sortMode = document.getElementById('orderSortSelect').value;
        const container = document.getElementById('ordersContainer');
        const cards = Array.from(document.querySelectorAll('.order-card'));

        cards.sort((a, b) => {
            const timeA = parseInt(a.getAttribute('data-timestamp')) || 0;
            const timeB = parseInt(b.getAttribute('data-timestamp')) || 0;
            const priceA = parseFloat(a.getAttribute('data-price')) || 0;
            const priceB = parseFloat(b.getAttribute('data-price')) || 0;

            if (sortMode === 'newest') return timeB - timeA;
            if (sortMode === 'oldest') return timeA - timeB;
            if (sortMode === 'highest') return priceB - priceA;
            if (sortMode === 'lowest') return priceA - priceB;
            return 0;
        });

        cards.forEach(card => container.appendChild(card));
    }

    function resetFilters() {
        document.getElementById('orderSearchInput').value = '';
        switchOrderTab('all');
    }

    // Modal Helpers
    function openTrackingModal(courier, resi, date, store, buyer) {
        document.getElementById('trackingCourierText').innerText = courier + ' • Resi: ' + resi;
        document.getElementById('timelineDate1').innerText = date + ' WIB';
        document.getElementById('timelineStoreText').innerText = 'Dikemas oleh ' + store + ' (Indramayu)';
        document.getElementById('timelineResiText').innerText = 'No. Resi: ' + resi + ' (' + courier + ')';
        document.getElementById('timelineBuyerText').innerText = 'Tujuan: ' + buyer;
        document.getElementById('trackingModal').classList.remove('hidden');
    }

    function closeTrackingModal() {
        document.getElementById('trackingModal').classList.add('hidden');
    }

    function openCancelModal(orderId, productName) {
        const form = document.getElementById('cancelOrderForm');
        form.action = '/pesanan/' + orderId + '/cancel';
        document.getElementById('cancelModalProductName').innerText = productName;
        document.getElementById('cancelModal').classList.remove('hidden');
    }

    function closeCancelModal() {
        document.getElementById('cancelModal').classList.add('hidden');
    }

    function handleReasonChange(select) {
        const textarea = document.getElementById('cancelReasonText');
        if (select.value !== 'Lainnya') {
            textarea.value = select.value;
        } else {
            textarea.value = '';
            textarea.focus();
        }
    }
</script>
@endpush
@endsection
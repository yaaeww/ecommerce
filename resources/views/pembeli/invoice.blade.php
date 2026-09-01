@extends('layouts.public')

@section('title', 'Invoice Resmi #' . str_pad($order->id, 5, '0', STR_PAD_LEFT) . ' — Juragan Pelem')

@push('styles')
<style>
    @media print {
        /* Sembunyikan elemen navigasi dan tombol saat dicetak */
        header, footer, .no-print, nav, .chat-widget {
            display: none !important;
        }
        body {
            background-color: #ffffff !important;
            color: #000000 !important;
            padding: 0 !important;
        }
        .invoice-card {
            border: none !important;
            box-shadow: none !important;
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }
        .print-shadow-none {
            box-shadow: none !important;
            border: 1px solid #e2e8f0 !important;
        }
    }
</style>
@endpush

@section('content')
<main class="py-8 sm:py-12 bg-slate-100/60 min-h-screen">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

        <!-- Top Action Bar (Back, Print, Chat) -->
        <div class="no-print mb-6 flex flex-wrap items-center justify-between gap-3">
            <a href="{{ route('pembeli.pesanan.index') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 text-xs font-bold shadow-sm hover:bg-slate-50 transition">
                <i class="fas fa-arrow-left text-slate-400"></i>
                <span>Kembali ke Riwayat Pesanan</span>
            </a>

            <div class="flex flex-wrap items-center gap-2.5">
                <button type="button" onclick="window.print()" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-900 text-white text-xs font-bold shadow-sm transition">
                    <i class="fas fa-print"></i>
                    <span>Cetak Faktur</span>
                </button>
                
                <a href="{{ route('pembeli.invoice.pdf', $order->id) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-sm transition">
                    <i class="fas fa-file-pdf"></i>
                    <span>Unduh PDF</span>
                </a>

                @if($order->produk && ($order->produk->user_id || ($order->produk->umkm && $order->produk->umkm->user_id)))
                    <a href="{{ route('pembeli.chat.index', ['id' => $order->produk->user_id ?? $order->produk->umkm->user_id]) }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-sm transition" title="Buka Chat di Aplikasi">
                        <i class="fas fa-comment-dots"></i>
                        <span>Chat Penjual</span>
                    </a>
                @endif

                @if($order->produk && $order->produk->umkm && $order->produk->umkm->nomor_telepon)
                    @php
                        $cleanWa = preg_replace('/[^0-9]/', '', $order->produk->umkm->nomor_telepon);
                        if (str_starts_with($cleanWa, '0')) {
                            $cleanWa = '62' . substr($cleanWa, 1);
                        }
                        $waText = urlencode("Halo " . ($order->produk->umkm->nama_toko ?? 'Juragan Pelem') . ", saya ingin menanyakan pesanan #" . ($order->order_id_midtrans ?: $order->id) . ".");
                    @endphp
                    <a href="https://wa.me/{{ $cleanWa }}?text={{ $waText }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition" title="Chat via WhatsApp">
                        <i class="fab fa-whatsapp"></i>
                        <span>WhatsApp Toko</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- Main Invoice Sheet (A4 Styled Document) -->
        <div class="invoice-card bg-white rounded-3xl border border-slate-200/80 shadow-xl overflow-hidden print-shadow-none">
            
            <!-- 1. Header Banner & Status -->
            <div class="p-6 sm:p-10 border-b border-slate-100 bg-gradient-to-r from-slate-50 via-white to-indigo-50/30">
                <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-6">
                    
                    <!-- Brand Identity -->
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-white border border-slate-200/80 p-2 shadow-sm flex items-center justify-center shrink-0">
                            <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-2xl font-black font-display text-indigo-700 tracking-tight">Juragan<span class="text-amber-500">Pelem</span></span>
                                <span class="px-2 py-0.5 rounded-md bg-indigo-100/80 text-indigo-800 text-[10px] font-extrabold uppercase tracking-wider border border-indigo-200/60">Official Receipt</span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium mt-0.5">Platform Agro-Commerce & Sentra Mangga Indramayu</p>
                            <p class="text-[11px] text-slate-400">www.juraganpelem.com • info@juraganpelem.com</p>
                        </div>
                    </div>

                    <!-- Invoice Numbers & Status Badge -->
                    <div class="text-left sm:text-right space-y-2">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider
                            @if($order->status === 'complete') bg-emerald-100 text-emerald-800 border border-emerald-300
                            @elseif($order->status === 'pending') bg-amber-100 text-amber-800 border border-amber-300
                            @else bg-rose-100 text-rose-800 border border-rose-300 @endif">
                            @if($order->status === 'complete')
                                <i class="fas fa-check-circle text-emerald-600"></i>
                                <span>LUNAS (PAID)</span>
                            @elseif($order->status === 'pending')
                                <i class="fas fa-clock text-amber-600"></i>
                                <span>MENUNGGU PEMBAYARAN</span>
                            @else
                                <i class="fas fa-ban text-rose-600"></i>
                                <span>DIBATALKAN</span>
                            @endif
                        </div>

                        <div>
                            <span class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Nomor Invoice</span>
                            <span class="text-base sm:text-lg font-black text-slate-900 font-mono">
                                #INV/{{ $order->created_at->format('Ymd') }}/JP/{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Dual Party Info Grid (Penjual vs Pembeli) -->
            <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-slate-100 p-6 sm:p-10 border-b border-slate-100 text-sm">
                
                <!-- Left: Info Penjual / Kebun -->
                <div class="md:pr-8 pb-6 md:pb-0 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-indigo-700">
                        <i class="fas fa-store"></i>
                        <span>Diterbitkan Oleh (Mitra Kebun)</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-base">
                            {{ $order->produk->umkm->nama_toko ?? $order->produk->user->name ?? 'Mitra Kebun Indramayu' }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            {{ $order->produk->umkm->alamat ?? 'Sentra Perkebunan Mangga Gedong Gincu, Kab. Indramayu, Jawa Barat' }}
                        </p>
                    </div>
                    <div class="text-xs text-slate-600 space-y-1 pt-1">
                        <div class="flex items-center gap-2">
                            <i class="fab fa-whatsapp text-emerald-600 w-4"></i>
                            <span>{{ $order->produk->umkm->nomor_telepon ?? '0812-3456-7890' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-shield-halved text-amber-500 w-4"></i>
                            <span class="font-semibold text-slate-700">Terverifikasi Mitra Agro Indramayu</span>
                        </div>
                    </div>
                </div>

                <!-- Right: Info Penerima & Pengiriman -->
                <div class="md:pl-8 pt-6 md:pt-0 space-y-3">
                    <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-indigo-700">
                        <i class="fas fa-location-dot"></i>
                        <span>Tujuan Pengiriman (Pembeli)</span>
                    </div>
                    <div>
                        <h4 class="font-extrabold text-slate-800 text-base">
                            {{ $order->name }}
                        </h4>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            {{ $order->alamat }}
                        </p>
                    </div>
                    <div class="text-xs text-slate-600 space-y-1.5 pt-1">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-phone text-slate-400 w-4"></i>
                            <span>{{ $order->phone }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-truck text-indigo-600 w-4"></i>
                            <span class="font-semibold text-slate-700">{{ $order->kurir ?? 'Kurir Mitra Panen Fresh' }}</span>
                            @if($order->resi_pengiriman)
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-700 font-mono font-bold text-[11px]">Resi: {{ $order->resi_pengiriman }}</span>
                            @else
                                <span class="text-slate-400 italic">(Resi dalam proses)</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <i class="fas fa-calendar-day text-slate-400 w-4"></i>
                            <span>Waktu Transaksi: <strong>{{ $order->created_at->translatedFormat('d F Y, H:i') }} WIB</strong></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Rincian Produk / Order Items Table -->
            <div class="p-6 sm:p-10 border-b border-slate-100">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4 flex items-center gap-2">
                    <i class="fas fa-box-open text-indigo-600"></i>
                    <span>Rincian Komoditas & Pembelian</span>
                </h4>

                <div class="overflow-x-auto rounded-2xl border border-slate-200/80">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs font-bold uppercase tracking-wider border-b border-slate-200/80">
                            <tr>
                                <th class="py-3.5 px-4 text-center w-12">No</th>
                                <th class="py-3.5 px-4">Komoditas Produk</th>
                                <th class="py-3.5 px-4 text-right">Harga Satuan</th>
                                <th class="py-3.5 px-4 text-center">Jumlah</th>
                                <th class="py-3.5 px-4 text-right">Total Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr>
                                <td class="py-4 px-4 text-center font-bold text-slate-400">1</td>
                                <td class="py-4 px-4">
                                    <div class="flex items-center gap-3.5">
                                        <div class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden shrink-0 border border-slate-200/60">
                                            @if($order->produk && $order->produk->gambar)
                                                <img src="{{ asset('storage/' . $order->produk->gambar) }}" alt="{{ $order->produk->nama }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300 text-xs">
                                                    <i class="fas fa-image"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h5 class="font-extrabold text-slate-900 leading-tight">{{ $order->produk->nama ?? 'Produk Dihapus' }}</h5>
                                            <div class="flex items-center gap-2 mt-1 text-xs text-slate-500 font-medium">
                                                <span>{{ $order->produk->kategori->nama ?? 'Komoditas Unggulan' }}</span>
                                                <span>•</span>
                                                <span>Berat: {{ ($order->produk->berat_gram ?? 1000) / 1000 }} Kg / kemasan</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-4 text-right font-semibold text-slate-700">
                                    Rp {{ number_format($order->total_harga / max(1, $order->jumlah), 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-4 text-center">
                                    <span class="inline-block px-3 py-1 bg-slate-100 rounded-lg font-bold text-slate-800 text-xs">
                                        {{ $order->jumlah }} pack
                                    </span>
                                </td>
                                <td class="py-4 px-4 text-right font-black text-slate-900 font-display text-base">
                                    Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- 4. Financial Summary & Trust Seals -->
            <div class="p-6 sm:p-10 bg-slate-50/50">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-start">
                    
                    <!-- Left: Fresh Guarantee & Verification Code (7 cols) -->
                    <div class="md:col-span-7 space-y-4">
                        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0 text-lg border border-emerald-200">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <div class="text-xs space-y-1">
                                <h6 class="font-bold text-slate-900">Garansi Segar & Panen Pilihan</h6>
                                <p class="text-slate-500 leading-relaxed">
                                    Produk dipetik langsung dari kebun mitra petani terpercaya di Indramayu dan dikemas dengan standar mutu buah segar.
                                </p>
                            </div>
                        </div>

                        <!-- Midtrans Order Ref -->
                        <div class="p-3.5 rounded-xl bg-indigo-50/60 border border-indigo-100 text-xs space-y-1 font-mono text-slate-600">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Order ID Gateway:</span>
                                <span class="font-bold text-indigo-900">{{ $order->order_id_midtrans }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Status Escrow:</span>
                                <span class="font-bold {{ $order->is_escrow_released ? 'text-emerald-700' : 'text-amber-700' }}">
                                    {{ $order->is_escrow_released ? 'Dana Diteruskan ke Penjual' : 'Dana Aman Tertahan (Escrow)' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Cost Calculation Box (5 cols) -->
                    <div class="md:col-span-5 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                        <div class="flex justify-between text-xs text-slate-500 font-medium">
                            <span>Subtotal Komoditas:</span>
                            <span class="font-bold text-slate-800">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs text-slate-500 font-medium">
                            <span>Biaya Pengiriman:</span>
                            <span class="font-bold text-emerald-600">Gratis / Termasuk Promo</span>
                        </div>
                        <div class="flex justify-between text-xs text-slate-500 font-medium">
                            <span>Biaya Layanan & Asuransi Segar:</span>
                            <span class="font-bold text-slate-800">Rp 0</span>
                        </div>

                        <div class="pt-3 border-t border-slate-200 flex items-baseline justify-between">
                            <div>
                                <span class="block text-xs font-black uppercase tracking-wider text-slate-800">Total Pembayaran</span>
                                <span class="text-[10px] text-slate-400">Sudah termasuk PPN</span>
                            </div>
                            <span class="text-2xl font-black text-indigo-700 font-display">
                                Rp {{ number_format($order->total_harga, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Document Footer & Security Notice -->
            <div class="px-6 py-4 sm:px-10 border-t border-slate-100 bg-white text-center sm:text-left flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] text-slate-400">
                <span>Dokumen ini adalah bukti transaksi digital resmi dari <strong>Juragan Pelem Indramayu</strong>.</span>
                <span>Dicetak pada {{ now()->translatedFormat('d F Y, H:i') }} WIB</span>
            </div>

        </div>

    </div>
</main>
@endsection
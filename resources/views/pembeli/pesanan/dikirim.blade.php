@extends('layouts.app')

@section('page_title', 'Pesanan Dikirim & Selesai')

@section('content')
@php use App\Models\Ulasan; @endphp

<div class="max-w-6xl mx-auto space-y-8">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Pesanan Dikirim & Diterima</h2>
            <p class="text-xs text-slate-500 mt-1">Pantau resi kurir real-time, konfirmasi penerimaan mangga segar, dan klaim garansi buah</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pembeli.komplain.index') }}" class="px-3.5 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs border border-rose-200 transition">
                <i class="fas fa-shield-halved mr-1"></i> Riwayat Komplain Segar
            </a>
            <a href="{{ route('pembeli.pesanan.status.dikemas') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition">
                <i class="fas fa-box mr-1"></i> Pesanan Dikemas
            </a>
        </div>
    </div>

    @php
        $dikirimOrders = $orders->where('status_pesanan', 'dikirim');
        $diterimaOrders = $orders->where('status_pesanan', 'diterima');
    @endphp

    <!-- 🚚 1. PESANAN SEDANG DIKIRIM (In-Transit) -->
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                <i class="fas fa-truck-fast text-blue-500"></i> Sedang Dalam Pengiriman Kurir
                <span class="px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 text-xs font-bold border border-blue-200">
                    {{ $dikirimOrders->count() }} Paket
                </span>
            </h3>
        </div>

        <div class="space-y-4">
            @forelse($dikirimOrders as $order)
                <div class="card bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-7 relative overflow-hidden">
                    <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-blue-500"></div>

                    <!-- Order Header Info -->
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3 pb-4 border-b border-slate-100">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-sm font-display">#ORD-{{ $order->id }}</span>
                                <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-mono font-bold">{{ $order->order_id_midtrans }}</span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-0.5">
                                Dikirim: {{ $order->dikirim_at ? $order->dikirim_at->translatedFormat('d M Y, H:i') : ($order->updated_at ? $order->updated_at->format('d M Y') : '-') }} WIB
                            </p>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-blue-50 text-blue-700 border border-blue-200 uppercase">
                                <i class="fas fa-truck text-xs mr-1"></i> Sedang Dikirim
                            </span>
                        </div>
                    </div>

                    <!-- Resi & Ekspedisi Card -->
                    <div class="my-4 p-4 rounded-xl bg-slate-50 border border-slate-100 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-600 text-white flex items-center justify-center font-bold text-sm">
                                <i class="fas fa-box"></i>
                            </div>
                            <div>
                                <span class="text-[10px] font-extrabold uppercase tracking-wider text-slate-400">Ekspedisi & Nomor Resi</span>
                                <div class="flex items-center gap-2 mt-0.5">
                                    <span class="font-extrabold text-xs text-slate-900 uppercase bg-white px-2 py-0.5 rounded border border-slate-200">
                                        {{ $order->kurir_ekspedisi ?? 'J&T Cargo' }}
                                    </span>
                                    <span class="font-mono font-bold text-xs text-blue-700">{{ $order->no_resi ?? 'Belum terbit' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <!-- Tombol Lacak Resi -->
                            <button 
                                type="button" 
                                onclick="openTrackingModal('{{ $order->id }}', '{{ $order->kurir_ekspedisi ?? 'J&T Cargo' }}', '{{ $order->no_resi ?? 'JP-EXP-99281' }}', '{{ $order->dikirim_at ? $order->dikirim_at->format('d M Y') : date('d M Y') }}')"
                                class="px-3.5 py-2 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 shadow-2xs transition flex items-center gap-1.5"
                            >
                                <i class="fas fa-location-dot text-blue-600"></i> Lacak Paket
                            </button>

                            @if($order->foto_bukti_pengiriman)
                                <a href="{{ asset('storage/' . $order->foto_bukti_pengiriman) }}" target="_blank" class="px-3 py-2 bg-white hover:bg-slate-100 text-slate-700 font-bold text-xs rounded-xl border border-slate-200 transition" title="Lihat Foto Kardus Penjual">
                                    <i class="fas fa-camera text-slate-500 mr-1"></i> Foto Paket
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Item Rincian -->
                    <div class="flex items-center justify-between py-2">
                        <div class="flex items-center gap-3">
                            <img 
                                src="{{ $order->produk && $order->produk->gambar ? asset('storage/' . $order->produk->gambar) : asset('aset/finalisasi logo.png') }}" 
                                alt="Produk"
                                class="w-12 h-12 rounded-xl object-cover border border-slate-200"
                            >
                            <div>
                                <h5 class="font-bold text-xs text-slate-900">{{ $order->produk->nama ?? 'Mangga' }}</h5>
                                <p class="text-[11px] text-slate-500">{{ $order->jumlah }} unit • Toko: <strong class="text-brand-700">{{ $order->produk->umkm->nama_toko ?? 'Petani Mitra' }}</strong></p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-[11px] text-slate-400">Total Tagihan</span>
                            <p class="text-sm font-extrabold text-slate-900 font-display">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                        <div class="text-xs text-slate-500 flex items-center gap-1.5">
                            <i class="fas fa-shield-heart text-emerald-600"></i>
                            <span>Dana Anda aman di penampung (Escrow) hingga paket diterima dengan segar.</span>
                        </div>

                        <div class="flex items-center gap-2 w-full sm:w-auto">
                            <!-- Ajukan Garansi / Komplain -->
                            @if($order->komplain)
                                <a href="{{ route('pembeli.komplain.show', $order->komplain->id) }}" class="px-3.5 py-2 rounded-xl bg-amber-50 text-amber-800 font-bold text-xs border border-amber-200 hover:bg-amber-100 transition">
                                    <i class="fas fa-hourglass-half mr-1"></i> Komplain #{{ $order->komplain->id }}
                                </a>
                            @else
                                <a href="{{ route('pembeli.komplain.create', $order->id) }}" class="px-3.5 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 font-bold text-xs border border-rose-200 transition">
                                    <i class="fas fa-triangle-exclamation mr-1"></i> Klaim Garansi Segar
                                </a>
                            @endif

                            <!-- Konfirmasi Selesai -->
                            <form action="{{ route('pembeli.pesanan.updateStatus', $order->id) }}" method="POST" onsubmit="return confirm('Apakah paket mangga telah Anda terima dengan kondisi segar dan baik?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full sm:w-auto px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-xs transition flex items-center justify-center gap-1.5">
                                    <i class="fas fa-check-circle"></i> Konfirmasi Diterima
                                </button>
                            </form>
                        </div>
                    </div>

                </div>
            @empty
                <div class="card bg-white rounded-2xl border border-slate-200/80 p-8 text-center">
                    <div class="w-12 h-12 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-2 text-slate-400">
                        <i class="fas fa-box-open text-xl"></i>
                    </div>
                    <h4 class="text-sm font-bold text-slate-700">Tidak Ada Pesanan Sedang Dikirim</h4>
                    <p class="text-xs text-slate-400 mt-0.5">Seluruh pesanan Anda sudah selesai atau masih dalam proses panen/packing.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- ✅ 2. PESANAN DITERIMA & SELESAI -->
    <div class="space-y-4 pt-6 border-t border-slate-200">
        <h3 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
            <i class="fas fa-circle-check text-emerald-500"></i> Riwayat Pesanan Selesai
            <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold border border-emerald-200">
                {{ $diterimaOrders->count() }} Selesai
            </span>
        </h3>

        <div class="space-y-4">
            @forelse($diterimaOrders as $order)
                @php
                    $produk = $order->produk;
                    $sudahDinilai = $produk ? Ulasan::where('users_id', auth()->id())->where('orders_id', $order->id)->where('produks_id', $produk->id)->exists() : false;
                @endphp

                <div class="card bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 space-y-4">
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-3 border-b border-slate-100">
                        <div>
                            <span class="font-bold text-slate-900 text-xs font-display">#ORD-{{ $order->id }}</span>
                            <span class="text-[11px] text-slate-400 ml-2">Diterima: {{ $order->diterima_at ? $order->diterima_at->translatedFormat('d M Y, H:i') : $order->updated_at->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200 uppercase">
                                <i class="fas fa-check mr-1"></i> Transaksi Selesai
                            </span>
                            @if($order->komplain)
                                <a href="{{ route('pembeli.komplain.show', $order->komplain->id) }}" class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                                    Komplain #{{ $order->komplain->id }}
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <img 
                                src="{{ $produk && $produk->gambar ? asset('storage/' . $produk->gambar) : asset('aset/finalisasi logo.png') }}" 
                                alt="Produk" 
                                class="w-12 h-12 rounded-xl object-cover border border-slate-200"
                            >
                            <div>
                                <h5 class="font-bold text-xs text-slate-900">{{ $produk->nama ?? 'Mangga' }}</h5>
                                <p class="text-[11px] text-slate-500">{{ $order->jumlah }} unit • Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                            </div>
                        </div>

                        <div>
                            @if(!$sudahDinilai && $produk)
                                <a href="{{ route('pembeli.rating.create', ['order' => $order->id, 'product' => $produk->id]) }}" class="px-3.5 py-1.5 bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs rounded-xl shadow-2xs transition flex items-center gap-1.5">
                                    <i class="fas fa-star"></i> Beri Ulasan
                                </a>
                            @else
                                <span class="px-3 py-1 bg-slate-100 text-slate-600 rounded-lg text-xs font-bold flex items-center gap-1">
                                    <i class="fas fa-star text-amber-400"></i> Ulasan Diberikan
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="card bg-white rounded-2xl border border-slate-200/80 p-8 text-center text-slate-400 text-xs">
                    Belum ada riwayat pesanan yang telah Anda selesaikan.
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- 📦 MODAL LACAK RESI EKSPEDISI LIVE -->
<div id="modalTracking" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-6">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div>
                <h3 class="text-base font-extrabold text-slate-900">Lacak Pengiriman Ekspedisi</h3>
                <p id="trackingCourierInfo" class="text-xs text-slate-500 mt-0.5">J&T Cargo • Resi #JP-001</p>
            </div>
            <button type="button" onclick="document.getElementById('modalTracking').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <!-- Live Step Timeline -->
        <div class="relative pl-6 space-y-6 before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
            
            <div class="relative flex items-start gap-3">
                <div class="absolute -left-6 top-0 w-4 h-4 rounded-full bg-blue-600 ring-4 ring-blue-100"></div>
                <div>
                    <span class="text-[10px] font-bold text-blue-600 uppercase tracking-wider block">Status Terkini</span>
                    <h5 class="text-xs font-extrabold text-slate-900">Paket Sedang Dibawa Kurir Menuju Alamat Penerima</h5>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kurir sedang dalam perjalanan ke alamat tujuan Anda.</p>
                    <span class="text-[10px] text-slate-400 block mt-1">Hari ini • 09:30 WIB</span>
                </div>
            </div>

            <div class="relative flex items-start gap-3">
                <div class="absolute -left-6 top-0 w-4 h-4 rounded-full bg-slate-300"></div>
                <div>
                    <h5 class="text-xs font-bold text-slate-800">Paket Tiba di Sorting Hub Wilayah</h5>
                    <p class="text-[11px] text-slate-500 mt-0.5">Paket lolos sortir fasilitas logistik hub regional.</p>
                    <span id="trackingDateSub" class="text-[10px] text-slate-400 block mt-1">Kemarin • 18:45 WIB</span>
                </div>
            </div>

            <div class="relative flex items-start gap-3">
                <div class="absolute -left-6 top-0 w-4 h-4 rounded-full bg-slate-300"></div>
                <div>
                    <h5 class="text-xs font-bold text-slate-800">Diserahkan Petani ke Agen Ekspedisi Indramayu</h5>
                    <p class="text-[11px] text-slate-500 mt-0.5">Kardus mangga segar telah dipacking dengan aman dan discan resi thermal.</p>
                    <span id="trackingDateInit" class="text-[10px] text-slate-400 block mt-1">Tanggal Kirim</span>
                </div>
            </div>

        </div>

        <div class="pt-4 border-t border-slate-100 flex justify-end">
            <button type="button" onclick="document.getElementById('modalTracking').classList.add('hidden')" class="px-5 py-2 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl shadow-xs">
                Tutup Pelacakan
            </button>
        </div>

    </div>
</div>

<script>
function openTrackingModal(orderId, courier, resi, date) {
    document.getElementById('trackingCourierInfo').innerText = courier.toUpperCase() + ' • No. Resi: ' + resi;
    document.getElementById('trackingDateInit').innerText = date + ' • 14:20 WIB';
    document.getElementById('modalTracking').classList.remove('hidden');
}
</script>
@endsection
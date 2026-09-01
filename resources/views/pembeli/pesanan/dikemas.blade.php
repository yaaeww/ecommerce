@extends('layouts.app')

@section('page_title', 'Pesanan Dikemas')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Pesanan Sedang Dikemas</h2>
            <p class="text-xs text-slate-500 mt-1">Petani mitra sedang memetik buah pilihan, menimbang, dan menyiapkan kardus berlabel resi</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pembeli.pesanan.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-100 text-slate-700 hover:bg-slate-200 font-bold text-xs transition">
                <i class="fas fa-list mr-1"></i> Semua Pesanan
            </a>
            <a href="{{ route('pembeli.status.dikirim') }}" class="px-3.5 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 font-bold text-xs border border-blue-200 transition">
                <i class="fas fa-truck mr-1"></i> Pesanan Dikirim
            </a>
        </div>
    </div>

    <!-- Orders List -->
    <div class="space-y-4">
        @forelse($orders as $order)
            <div class="card bg-white rounded-2xl shadow-sm border border-slate-200/80 p-6 sm:p-7 relative overflow-hidden space-y-4">
                <div class="absolute left-0 top-0 bottom-0 w-1.5 bg-amber-500"></div>

                <!-- Order Header Info -->
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2 pb-3 border-b border-slate-100">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-slate-900 text-sm font-display">#ORD-{{ $order->id }}</span>
                            <span class="px-2 py-0.5 rounded bg-slate-100 text-slate-600 text-[10px] font-mono font-bold">{{ $order->order_id_midtrans }}</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            Pembayaran Lunas • {{ $order->created_at->translatedFormat('d M Y, H:i') }} WIB
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full text-xs font-extrabold bg-amber-50 text-amber-700 border border-amber-200 uppercase">
                            <i class="fas fa-box-archive mr-1"></i> Sedang Dipersiapkan Kebun
                        </span>
                    </div>
                </div>

                <!-- Item Info -->
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 py-2">
                    <div class="flex items-center gap-3.5">
                        <img 
                            src="{{ $order->produk && $order->produk->gambar ? asset('storage/' . $order->produk->gambar) : asset('aset/finalisasi logo.png') }}" 
                            alt="Produk"
                            class="w-14 h-14 rounded-xl object-cover border border-slate-200"
                        >
                        <div>
                            <h4 class="font-bold text-sm text-slate-900">{{ $order->produk->nama ?? 'Mangga' }}</h4>
                            <p class="text-xs text-slate-500 mt-0.5">{{ $order->jumlah }} unit • Kebun: <strong class="text-brand-700">{{ $order->produk->umkm->nama_toko ?? 'Petani Mitra' }}</strong></p>
                            <p class="text-[11px] text-slate-400 mt-0.5"><i class="fas fa-location-dot text-slate-400 mr-1"></i> {{ $order->alamat }}</p>
                        </div>
                    </div>

                    <div class="sm:text-right">
                        <span class="text-xs text-slate-400">Total Pembayaran</span>
                        <p class="text-base font-extrabold text-slate-900 font-display">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Packaging Progress & Action Footer -->
                <div class="pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-3">
                    <div class="flex items-center gap-2 text-xs text-slate-500">
                        <div class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></div>
                        <span>Penjual sedang menyeleksi kematangan buah & packing kardus tebal.</span>
                    </div>

                    <div>
                        <!-- Tombol Batalkan Pesanan (Feature 2) -->
                        <button 
                            type="button" 
                            onclick="openCancelModal('{{ $order->id }}', '{{ $order->produk->nama ?? 'Mangga' }}')"
                            class="px-4 py-2 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-xl border border-rose-200 transition flex items-center gap-1.5"
                        >
                            <i class="fas fa-ban"></i> Batalkan Pesanan
                        </button>
                    </div>
                </div>

            </div>
        @empty
            <div class="card bg-white rounded-2xl border border-slate-200/80 p-12 text-center">
                <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-3 text-slate-400">
                    <i class="fas fa-box-open text-2xl"></i>
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-1">Tidak Ada Pesanan yang Sedang Dikemas</h3>
                <p class="text-xs text-slate-400 max-w-sm mx-auto mb-4">
                    Seluruh pesanan Anda sudah berada di kurir pengiriman atau belum ada pesanan baru.
                </p>
                <a href="{{ route('pembeli.produk.index') }}" class="inline-flex items-center gap-2 px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-xs">
                    <i class="fas fa-bag-shopping"></i> Belanja Mangga Segar
                </a>
            </div>
        @endforelse
    </div>

</div>

<!-- 🛑 MODAL BATALKAN PESANAN -->
<div id="modalCancel" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl space-y-4">
        
        <div class="flex items-center justify-between pb-3 border-b border-slate-100">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center text-sm">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <h3 class="text-base font-extrabold text-slate-900">Batalkan Pesanan</h3>
            </div>
            <button type="button" onclick="document.getElementById('modalCancel').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <form id="formCancelOrder" action="" method="POST" class="space-y-4">
            @csrf

            <p id="cancelOrderPrompt" class="text-xs text-slate-600 leading-relaxed">
                Apakah Anda yakin ingin membatalkan pesanan ini? Kuota stok komoditas akan dikembalikan ke kebun penjual.
            </p>

            <div>
                <label for="batal_alasan" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Alasan Pembatalan <span class="text-rose-500">*</span>
                </label>
                <select name="batal_alasan" id="batal_alasan" required class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none bg-slate-50 font-medium">
                    <option value="Ingin mengubah alamat pengiriman">Ingin mengubah alamat pengiriman</option>
                    <option value="Salah memilih jenis varietas mangga">Salah memilih jenis varietas mangga</option>
                    <option value="Ingin menambah jumlah pesanan / berat">Ingin menambah jumlah pesanan / berat</option>
                    <option value="Menemukan varietas lain yang diinginkan">Menemukan varietas lain yang diinginkan</option>
                    <option value="Alasan lainnya">Alasan lainnya</option>
                </select>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalCancel').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl shadow-xs">
                    Konfirmasi Pembatalan
                </button>
            </div>
        </form>

    </div>
</div>

<script>
function openCancelModal(orderId, productName) {
    document.getElementById('formCancelOrder').action = '/pembeli/pesanan/' + orderId + '/cancel';
    document.getElementById('cancelOrderPrompt').innerHTML = 'Apakah Anda yakin ingin membatalkan pesanan <strong>' + productName + '</strong> (#ORD-' + orderId + ')? Kuota stok akan otomatis dikembalikan ke kebun.';
    document.getElementById('modalCancel').classList.remove('hidden');
}
</script>
@endsection
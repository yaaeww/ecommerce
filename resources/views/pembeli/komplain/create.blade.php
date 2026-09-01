@extends('layouts.app')

@section('page_title', 'Ajukan Garansi Buah Segar (Komplain)')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Breadcrumb & Header -->
    <div class="flex items-center gap-3">
        <a href="{{ route('pembeli.pesanan.dikirim') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 hover:border-slate-300 flex items-center justify-center transition shadow-2xs">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Klaim Garansi Segar / Komplain</h2>
            <p class="text-xs text-slate-500 mt-0.5">Ajukan penggantian atau refund dana jika mangga yang Anda terima busuk / rusak saat pengiriman</p>
        </div>
    </div>

    <!-- Info Banner Garansi Buah -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-emerald-900 to-emerald-800 text-white shadow-lg relative overflow-hidden">
        <div class="relative z-10 flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl bg-white/10 backdrop-blur-xs flex items-center justify-center shrink-0 text-xl text-emerald-300">
                <i class="fas fa-shield-halved"></i>
            </div>
            <div>
                <h4 class="font-extrabold text-base text-white">Jaminan 100% Buah Segar Juragan Pelem</h4>
                <p class="text-xs text-emerald-100/90 leading-relaxed mt-1">
                    Kami menjamin setiap buah mangga dipetik dalam kondisi prima. Jika terjadi kerusakan dalam perjalanan ekspedisi, Anda berhak mendapatkan pengembalian dana penuh atau pengiriman buah pengganti baru dengan menyertakan bukti foto & video unboxing.
                </p>
            </div>
        </div>
    </div>

    <!-- Form Klaim -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6 sm:p-8">
        
        <!-- Ringkasan Produk Pesanan -->
        <div class="p-4 rounded-xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3.5">
                <img 
                    src="{{ $order->produk && $order->produk->gambar ? asset('storage/' . $order->produk->gambar) : asset('aset/finalisasi logo.png') }}" 
                    alt="{{ $order->produk->nama ?? 'Produk' }}"
                    class="w-14 h-14 rounded-xl object-cover border border-slate-200"
                >
                <div>
                    <span class="text-[10px] font-extrabold text-brand-600 bg-brand-50 px-2 py-0.5 rounded uppercase">No. Pesanan #ORD-{{ $order->id }}</span>
                    <h5 class="font-bold text-sm text-slate-900 mt-1">{{ $order->produk->nama ?? 'Mangga' }}</h5>
                    <p class="text-xs text-slate-500">{{ $order->jumlah }} unit • Toko: {{ $order->produk->umkm->nama_toko ?? 'Petani Mitra' }}</p>
                </div>
            </div>
            <div class="sm:text-right">
                <span class="text-xs text-slate-400">Total Transaksi</span>
                <p class="text-base font-extrabold text-slate-900 font-display">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</p>
            </div>
        </div>

        <form action="{{ route('pembeli.komplain.store', $order->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Pilihan Jenis Masalah -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Jenis Kendala <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="relative flex items-center p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition has-checked:border-rose-500 has-checked:bg-rose-50/50">
                        <input type="radio" name="tipe_komplain" value="buah_busuk" class="text-rose-600 focus:ring-rose-500" checked>
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-900">🥭 Buah Busuk / Lewat Matang</span>
                            <span class="block text-[11px] text-slate-500">Daging buah berair/asam berlebih saat tiba</span>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition has-checked:border-rose-500 has-checked:bg-rose-50/50">
                        <input type="radio" name="tipe_komplain" value="kardus_rusak" class="text-rose-600 focus:ring-rose-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-900">📦 Kardus Pecah / Rusak Ekspedisi</span>
                            <span class="block text-[11px] text-slate-500">Kemasan hancur dan menekan buah di dalam</span>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition has-checked:border-rose-500 has-checked:bg-rose-50/50">
                        <input type="radio" name="tipe_komplain" value="berat_kurang" class="text-rose-600 focus:ring-rose-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-900">⚖️ Timbangan / Berat Kurang</span>
                            <span class="block text-[11px] text-slate-500">Total berat kardus tidak sesuai pesanan</span>
                        </div>
                    </label>

                    <label class="relative flex items-center p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 hover:bg-slate-50 cursor-pointer transition has-checked:border-rose-500 has-checked:bg-rose-50/50">
                        <input type="radio" name="tipe_komplain" value="tidak_sesuai" class="text-rose-600 focus:ring-rose-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-900">🔄 Varietas Tidak Sesuai</span>
                            <span class="block text-[11px] text-slate-500">Varietas mangga berbeda dengan katalog</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Opsi Solusi yang Diharapkan -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Solusi Kompensasi yang Anda Harapkan <span class="text-rose-500">*</span>
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <label class="flex items-center p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition has-checked:border-emerald-500 has-checked:bg-emerald-50/50">
                        <input type="radio" name="solusi_diminta" value="refund" class="text-emerald-600 focus:ring-emerald-500" checked>
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-900">💵 Pengembalian Dana (Refund Saldo)</span>
                            <span class="block text-[11px] text-slate-500">Dana ditransfer balik ke rekening/e-wallet Anda</span>
                        </div>
                    </label>
                    <label class="flex items-center p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 cursor-pointer hover:bg-slate-50 transition has-checked:border-brand-500 has-checked:bg-brand-50/50">
                        <input type="radio" name="solusi_diminta" value="ganti_buah" class="text-brand-600 focus:ring-brand-500">
                        <div class="ml-3">
                            <span class="block text-xs font-bold text-slate-900">🥭 Kirim Ulang Buah Baru</span>
                            <span class="block text-[11px] text-slate-500">Petani mengirimkan mangga baru bebas ongkir</span>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Unggah Foto Bukti & Video Unboxing -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Foto Bukti Kondisi Buah / Kardus <span class="text-rose-500">*</span>
                    </label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-brand-500 rounded-xl p-4 text-center transition bg-slate-50/50">
                        <i class="fas fa-camera text-2xl text-slate-400 mb-2"></i>
                        <p class="text-xs font-bold text-slate-700">Pilih Foto Bukti</p>
                        <p class="text-[10px] text-slate-400 mb-3">Format JPG, PNG, WEBP (Maks 5 MB)</p>
                        <input type="file" name="foto_bukti" required accept="image/*" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                        Video Unboxing (Opsional - Sangat Dianjurkan)
                    </label>
                    <div class="border-2 border-dashed border-slate-200 hover:border-brand-500 rounded-xl p-4 text-center transition bg-slate-50/50">
                        <i class="fas fa-video text-2xl text-slate-400 mb-2"></i>
                        <p class="text-xs font-bold text-slate-700">Pilih Video Unboxing</p>
                        <p class="text-[10px] text-slate-400 mb-3">Format MP4, MOV, WEBM (Maks 20 MB)</p>
                        <input type="file" name="video_unboxing" accept="video/*" class="text-xs text-slate-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100">
                    </div>
                </div>
            </div>

            <!-- Deskripsi Rinci -->
            <div>
                <label for="deskripsi" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Jelaskan Detail Masalah / Kerusakan <span class="text-rose-500">*</span>
                </label>
                <textarea 
                    name="deskripsi" 
                    id="deskripsi" 
                    rows="4" 
                    required
                    placeholder="Contoh: Saat kardus dibuka, terdapat 3 buah mangga bagian bawah yang bonyok dan berbau asam karena benturan selama perjalanan ekspedisi..."
                    class="w-full px-4 py-3 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none focus:ring-4 focus:ring-brand-500/10 leading-relaxed"
                >{{ old('deskripsi') }}</textarea>
            </div>

            <!-- Tombol Aksi -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('pembeli.pesanan.dikirim') }}" class="px-5 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition">
                    Batal
                </a>
                <button type="submit" class="px-6 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center gap-2">
                    <i class="fas fa-paper-plane"></i> Kirim Pengajuan Komplain
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

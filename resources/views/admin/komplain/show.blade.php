@extends('layouts.app')

@section('page_title', 'Investigasi Komplain #' . $komplain->id)

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.komplain.index') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shadow-2xs">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-900 font-display">Investigasi Sengketa #KMP-{{ $komplain->id }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Peninjauan barang bukti, riwayat transaksi, dan penetapan putusan mediasi</p>
            </div>
        </div>
        <span class="px-3.5 py-1.5 rounded-full text-xs font-extrabold border uppercase {{ $komplain->badge_color }}">
            {{ $komplain->status }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Kolom Kiri: Detail & Bukti (2 Kolom) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Rincian Pesanan & Laporan -->
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6 space-y-4">
                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-box-open text-brand-600"></i> Rincian Pesanan Yang Disengketakan
                </h4>
                
                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <img 
                            src="{{ $komplain->order->produk && $komplain->order->produk->gambar ? asset('storage/' . $komplain->order->produk->gambar) : asset('aset/finalisasi logo.png') }}" 
                            alt="Produk"
                            class="w-12 h-12 rounded-lg object-cover border border-slate-200"
                        >
                        <div>
                            <h5 class="font-bold text-xs text-slate-900">{{ $komplain->order->produk->nama ?? 'Mangga' }}</h5>
                            <p class="text-[11px] text-slate-500">{{ $komplain->order->jumlah }} unit • Total Tagihan: <strong>Rp {{ number_format($komplain->order->total_harga, 0, ',', '.') }}</strong></p>
                        </div>
                    </div>
                    <span class="text-[10px] font-mono bg-slate-200 px-2 py-1 rounded text-slate-700 font-bold">#ORD-{{ $komplain->order_id }}</span>
                </div>

                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Toko Pengirim:</span>
                        <span class="font-bold text-slate-800">{{ $komplain->order->produk->umkm->nama_toko ?? 'Petani Mitra' }} ({{ $komplain->order->produk->umkm->user->name ?? '-' }})</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Pembeli Penggugat:</span>
                        <span class="font-bold text-slate-800">{{ $komplain->user->name ?? 'Pembeli' }} ({{ $komplain->order->phone }})</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Alasan Komplain:</span>
                        <span class="font-extrabold text-rose-600">{{ $komplain->label_tipe }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Tuntutan Pembeli:</span>
                        <span class="font-extrabold text-brand-700 uppercase">{{ $komplain->solusi_diminta }}</span>
                    </div>
                    <div class="pt-2">
                        <span class="text-slate-500 block mb-1">Deskripsi Masalah dari Pembeli:</span>
                        <div class="p-3 bg-slate-50 rounded-xl text-slate-700 italic border border-slate-100 leading-relaxed">
                            "{{ $komplain->deskripsi }}"
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bukti Foto & Video Unboxing -->
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6 space-y-4">
                <h4 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="fas fa-photo-film text-brand-600"></i> Berkas Barang Bukti Unboxing
                </h4>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @if($komplain->foto_bukti)
                        <div>
                            <span class="text-xs font-bold text-slate-600 block mb-2">Foto Kondisi Buah / Kardus:</span>
                            <a href="{{ asset('storage/' . $komplain->foto_bukti) }}" target="_blank" class="block group relative overflow-hidden rounded-xl border border-slate-200">
                                <img src="{{ asset('storage/' . $komplain->foto_bukti) }}" alt="Foto Bukti" class="w-full h-48 object-cover group-hover:scale-105 transition">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold">
                                    <i class="fas fa-expand mr-1"></i> Perbesar Foto
                                </div>
                            </a>
                        </div>
                    @endif

                    @if($komplain->video_unboxing)
                        <div>
                            <span class="text-xs font-bold text-slate-600 block mb-2">Rekaman Video Unboxing:</span>
                            <video controls class="w-full rounded-xl border border-slate-200 max-h-48 bg-black">
                                <source src="{{ asset('storage/' . $komplain->video_unboxing) }}" type="video/mp4">
                                Browser tidak mendukung pemutaran video.
                            </video>
                        </div>
                    @else
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-100 flex items-center justify-center text-center text-slate-400 text-xs">
                            Tidak ada rekaman video unboxing terlampir.
                        </div>
                    @endif
                </div>
            </div>

        </div>

        <!-- Kolom Kanan: Form Putusan Mediasi Admin (1 Kolom) -->
        <div class="space-y-6">
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6">
                <h4 class="text-sm font-bold text-slate-900 mb-4 flex items-center gap-2">
                    <i class="fas fa-gavel text-slate-800"></i> Penetapan Putusan Mediasi
                </h4>

                <form action="{{ route('admin.komplain.process', $komplain->id) }}" method="POST" class="space-y-4">
                    @csrf

                    <div>
                        <label for="status" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Status Keputusan <span class="text-rose-500">*</span>
                        </label>
                        <select name="status" id="status" class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none bg-slate-50 font-bold">
                            <option value="diproses" {{ $komplain->status === 'diproses' ? 'selected' : '' }}>⏳ Sedang Mediasi / Investigasi</option>
                            <option value="disetujui" {{ $komplain->status === 'disetujui' ? 'selected' : '' }}>✅ Disetujui (Ganti Rugi / Refund)</option>
                            <option value="ditolak" {{ $komplain->status === 'ditolak' ? 'selected' : '' }}>❌ Ditolak (Bukti Tidak Sah)</option>
                            <option value="selesai" {{ $komplain->status === 'selesai' ? 'selected' : '' }}>🏁 Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label for="nominal_refund" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nominal Refund (Rp)
                        </label>
                        <input 
                            type="number" 
                            name="nominal_refund" 
                            id="nominal_refund"
                            value="{{ old('nominal_refund', $komplain->nominal_refund ?? $komplain->order->total_harga) }}"
                            class="w-full px-3 py-2.5 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none font-bold font-display"
                            placeholder="Maks: Rp {{ number_format($komplain->order->total_harga, 0, ',', '.') }}"
                        >
                        <span class="text-[10px] text-slate-400">Total nilai order: Rp {{ number_format($komplain->order->total_harga, 0, ',', '.') }}</span>
                    </div>

                    <div>
                        <label for="catatan_admin" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Catatan Resmi Mediasi <span class="text-rose-500">*</span>
                        </label>
                        <textarea 
                            name="catatan_admin" 
                            id="catatan_admin" 
                            rows="4" 
                            required
                            placeholder="Tuliskan dasar pertimbangan dan instruksi bagi penjual/pembeli..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none"
                        >{{ old('catatan_admin', $komplain->catatan_admin) }}</textarea>
                    </div>

                    <button type="submit" class="w-full py-2.5 bg-slate-900 hover:bg-black text-white font-bold text-xs rounded-xl transition shadow-sm flex items-center justify-center gap-2">
                        <i class="fas fa-check-double"></i> Simpan Putusan Mediasi
                    </button>
                </form>
            </div>
        </div>

    </div>

</div>
@endsection

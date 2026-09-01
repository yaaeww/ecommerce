@extends('layouts.app')

@section('page_title', 'Detail Pesanan')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Detail Pesanan</h2>
            <p class="text-sm text-slate-500 mt-1">Lihat detail dan update status pesanan</p>
        </div>
        <div>
            <a href="{{ route('penjual.pesanan.index') }}" class="inline-flex items-center justify-center gap-2 px-4 py-2 bg-white border border-slate-200 text-slate-700 font-bold text-sm rounded-xl hover:bg-slate-50 transition shadow-sm">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="md:col-span-2 space-y-6">
            <!-- Informasi Pesanan -->
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center shrink-0">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-900">ID Pesanan</h3>
                            <p class="text-xs text-slate-500">Order ID dari Midtrans</p>
                        </div>
                    </div>
                    <div class="px-3 py-1 bg-slate-100 text-slate-700 text-sm font-bold rounded-lg border border-slate-200">
                        {{ $order->order_id_midtrans }}
                    </div>
                </div>
                
                <div class="p-6">
                    <h4 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4 border-b border-slate-100 pb-2">Produk yang Dipesan</h4>
                    <div class="flex items-start gap-4">
                        @if($order->produk->gambar)
                            <div class="w-20 h-20 rounded-xl overflow-hidden shrink-0 border border-slate-200">
                                <img src="{{ asset('storage/' . $order->produk->gambar) }}" alt="{{ $order->produk->nama }}" class="w-full h-full object-cover">
                            </div>
                        @else
                            <div class="w-20 h-20 rounded-xl bg-slate-100 flex items-center justify-center text-slate-400 shrink-0 border border-slate-200">
                                <i class="fas fa-image text-2xl"></i>
                            </div>
                        @endif
                        
                        <div class="flex-1">
                            <h5 class="font-bold text-slate-900 text-lg">{{ $order->produk->nama }}</h5>
                            @if($order->produk->deskripsi)
                                <p class="text-sm text-slate-500 mt-1 line-clamp-2">{{ $order->produk->deskripsi }}</p>
                            @endif
                            <div class="mt-3 flex items-center gap-4 text-sm">
                                <div class="flex items-center gap-1.5 text-slate-600">
                                    <span class="font-medium">Harga:</span>
                                    <span class="font-bold text-slate-900">Rp {{ number_format($order->produk->harga, 0, ',', '.') }}</span>
                                </div>
                                <div class="w-1 h-1 rounded-full bg-slate-300"></div>
                                <div class="flex items-center gap-1.5 text-slate-600">
                                    <span class="font-medium">Jumlah:</span>
                                    <span class="font-bold text-slate-900">{{ $order->jumlah }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                    <span class="font-bold text-slate-700">Total Pembayaran</span>
                    <span class="text-xl font-bold text-brand-700">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Update Status -->
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <i class="fas fa-truck-fast"></i>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900">Status Pengiriman</h3>
                        <p class="text-xs text-slate-500">Perbarui status pengiriman pesanan</p>
                    </div>
                </div>

                <div class="p-6">
                    @if($order->status === 'complete')
                        <form action="{{ route('penjual.pesanan.updateStatus', $order->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PATCH')
                            
                            <div class="space-y-4">
                                <label class="block text-sm font-bold text-slate-700">Pilih Status Baru</label>
                                
                                <div class="grid grid-cols-2 gap-4">
                                    @php
                                        $pengirimanStatus = ['dikemas' => 'Dikemas', 'dikirim' => 'Dikirim'];
                                    @endphp
                                    
                                    @foreach ($pengirimanStatus as $value => $label)
                                        <label class="cursor-pointer relative">
                                            <input type="radio" name="status_pesanan" value="{{ $value }}" class="peer sr-only" {{ old('status_pesanan', $order->status_pesanan) === $value ? 'checked' : '' }}>
                                            <div class="p-4 rounded-xl border-2 border-slate-200 hover:border-brand-200 peer-checked:border-brand-500 peer-checked:bg-brand-50 transition flex items-center gap-3">
                                                <div class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-brand-500 flex items-center justify-center shrink-0">
                                                    <div class="w-2.5 h-2.5 rounded-full bg-brand-500 opacity-0 peer-checked:opacity-100 transition"></div>
                                                </div>
                                                <div>
                                                    <span class="block font-bold text-slate-900">{{ $label }}</span>
                                                    <span class="block text-xs text-slate-500 mt-0.5">
                                                        {{ $value === 'dikemas' ? 'Pesanan sedang dipersiapkan' : 'Pesanan diserahkan ke kurir' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                                
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                                    <div class="space-y-1.5">
                                        <label for="kurir_ekspedisi" class="text-xs font-extrabold text-slate-700 block mb-1">Kurir / Ekspedisi</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                                                <i class="fas fa-truck-fast text-xs"></i>
                                            </div>
                                            <select name="kurir_ekspedisi" id="kurir_ekspedisi" class="w-full pl-10 pr-9 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-hidden bg-slate-50 hover:bg-white focus:bg-white transition shadow-xs cursor-pointer">
                                                <option value="J&T Cargo Fresh Chain" {{ old('kurir_ekspedisi', $order->kurir_ekspedisi) === 'J&T Cargo Fresh Chain' ? 'selected' : '' }}>J&T Cargo Fresh Chain</option>
                                                <option value="JNE Express" {{ old('kurir_ekspedisi', $order->kurir_ekspedisi) === 'JNE Express' ? 'selected' : '' }}>JNE Express</option>
                                                <option value="SiCepat" {{ old('kurir_ekspedisi', $order->kurir_ekspedisi) === 'SiCepat' ? 'selected' : '' }}>SiCepat</option>
                                                <option value="Anteraja" {{ old('kurir_ekspedisi', $order->kurir_ekspedisi) === 'Anteraja' ? 'selected' : '' }}>Anteraja</option>
                                                <option value="Kurir Toko / Pengantaran Langsung" {{ old('kurir_ekspedisi', $order->kurir_ekspedisi) === 'Kurir Toko / Pengantaran Langsung' ? 'selected' : '' }}>Kurir Toko / Pengantaran Langsung</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="space-y-1.5">
                                        <label for="no_resi" class="text-xs font-extrabold text-slate-700 block mb-1">Nomor Resi / Bukti Pengiriman</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                                <i class="fas fa-barcode text-xs"></i>
                                            </div>
                                            <input 
                                                type="text" 
                                                name="no_resi" 
                                                id="no_resi" 
                                                value="{{ old('no_resi', $order->no_resi) }}" 
                                                class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-slate-200 text-xs font-extrabold text-slate-800 focus:border-brand-500 focus:ring-2 focus:ring-emerald-500/20 focus:outline-hidden bg-slate-50 hover:bg-white focus:bg-white transition shadow-xs"
                                                placeholder="Contoh: JT8921829102"
                                            >
                                        </div>
                                    </div>
                                </div>

                                <!-- Upload Foto Bukti Pengiriman (Feature 7) -->
                                <div class="pt-2">
                                    <label class="block text-xs font-bold text-slate-700 mb-1.5 flex items-center justify-between">
                                        <span><i class="fas fa-camera text-slate-400 mr-1"></i> Foto Bukti Paket / Serah Terima (Opsional)</span>
                                        <span class="text-[10px] text-slate-400">JPG, PNG maks 5MB</span>
                                    </label>
                                    
                                    <div class="flex items-center gap-4">
                                        @if($order->foto_bukti_pengiriman)
                                            <div class="relative group shrink-0">
                                                <img src="{{ asset('storage/' . $order->foto_bukti_pengiriman) }}" alt="Bukti Paket" class="w-16 h-16 rounded-xl object-cover border border-slate-200 shadow-xs">
                                                <a href="{{ asset('storage/' . $order->foto_bukti_pengiriman) }}" target="_blank" class="absolute inset-0 bg-black/40 rounded-xl opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </div>
                                        @endif
                                        <input 
                                            type="file" 
                                            name="foto_bukti_pengiriman" 
                                            accept="image/*" 
                                            class="block w-full text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 file:transition cursor-pointer border border-slate-200 rounded-xl p-1"
                                        >
                                    </div>
                                </div>
                                
                                @error('status_pesanan')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                @error('foto_bukti_pengiriman')
                                    <p class="text-sm text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                                
                                <div class="pt-4 flex justify-end">
                                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-sm rounded-xl transition shadow-sm hover:shadow">
                                        <i class="fas fa-save"></i> Simpan Status, Resi & Foto
                                    </button>
                                </div>
                            </div>
                        </form>
                    @else
                        <div class="flex items-start gap-4 p-4 rounded-xl bg-amber-50 text-amber-800 border border-amber-200/50">
                            <i class="fas fa-exclamation-triangle mt-0.5 text-amber-500"></i>
                            <div>
                                <h5 class="font-bold">Tidak Dapat Mengubah Status</h5>
                                <p class="text-sm mt-1 opacity-90">Status pesanan hanya dapat diubah setelah pembayaran selesai (complete). Saat ini status pembayaran adalah <strong class="uppercase">{{ $order->status }}</strong>.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar Content -->
        <div class="space-y-6">
            <!-- Status Badges & Quick Action Printing -->
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900">Status & Cetak Dokumen</h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pembayaran</span>
                        <div class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold uppercase tracking-wider
                            {{ $order->status === 'complete' ? 'bg-emerald-50 text-emerald-700' : ($order->status === 'pending' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700') }}">
                            {{ $order->status }}
                        </div>
                    </div>
                    
                    <div>
                        <span class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Pesanan</span>
                        <div class="inline-flex items-center px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-xs font-bold uppercase tracking-wider">
                            {{ str_replace('_', ' ', $order->status_pesanan) }}
                        </div>
                    </div>
                    
                    @if($order->status === 'complete')
                    <div class="pt-4 border-t border-slate-100 space-y-2.5">
                        <!-- 🏷️ Cetak Label Pengiriman A6 Thermal -->
                        <a 
                            href="{{ route('penjual.pesanan.shipping-label', $order->id) }}" 
                            target="_blank" 
                            class="flex items-center justify-center gap-2 w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl transition shadow-xs"
                        >
                            <i class="fas fa-barcode"></i> Cetak Label Resi (A6)
                        </a>

                        <!-- Lihat Invoice -->
                        <a 
                            href="{{ route('penjual.invoice.show', $order->id) }}" 
                            class="flex items-center justify-center gap-2 w-full py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs rounded-xl transition"
                        >
                            <i class="fas fa-file-invoice"></i> Lihat Faktur Invoice
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Informasi Pembeli -->
            <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
                <div class="p-5 border-b border-slate-100">
                    <h3 class="font-bold text-slate-900 flex items-center gap-2">
                        <i class="fas fa-user text-slate-400"></i> Pembeli
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <span class="block text-xs text-slate-500 mb-0.5">Nama</span>
                        <span class="font-bold text-slate-900">{{ $order->name }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-xs text-slate-500 mb-0.5">No. WhatsApp</span>
                        <span class="font-medium text-slate-700">{{ $order->phone }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-xs text-slate-500 mb-0.5">Waktu Pemesanan</span>
                        <span class="font-medium text-slate-700">{{ $order->created_at->format('d M Y H:i') }}</span>
                    </div>
                    
                    <div>
                        <span class="block text-xs text-slate-500 mb-0.5">Alamat Pengiriman</span>
                        <span class="font-medium text-slate-700 block whitespace-pre-wrap">{{ $order->alamat }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusForm = document.querySelector('form[action*="updateStatus"]');
        if (statusForm) {
            statusForm.addEventListener('submit', function (e) {
                const selectedStatus = document.querySelector('input[name="status_pesanan"]:checked');
                if (selectedStatus) {
                    const statusLabel = selectedStatus.nextElementSibling.querySelector('span.block.font-bold').textContent;
                    if (!confirm(`Apakah Anda yakin ingin mengubah status pesanan menjadi "${statusLabel}"?`)) {
                        e.preventDefault();
                    } else {
                        const submitBtn = this.querySelector('button[type="submit"]');
                        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
                        submitBtn.disabled = true;
                    }
                }
            });
        }
    });
</script>
@endsection

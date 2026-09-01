@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8 text-center sm:text-left">
        <h1 class="text-3xl font-extrabold text-gray-900 flex items-center justify-center sm:justify-start gap-3">
            <i class="fas fa-shopping-bag text-indigo-600"></i>
            Checkout Pesanan Mangga Segar
        </h1>
        <p class="mt-1 text-sm text-gray-500">Lengkapi alamat tujuan pengiriman dan lanjutkan pembayaran aman Midtrans</p>
    </div>

    @if(session('error'))
        <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-md">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-500"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-700">
                        {{ session('error') }}
                    </p>
                </div>
                <div class="ml-auto pl-3">
                    <button type="button" class="text-red-500 hover:bg-red-100 rounded p-1" onclick="this.parentElement.parentElement.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 divide-y lg:divide-y-0 lg:divide-x divide-gray-100">
            
            {{-- Kolom Gambar dan Info Produk --}}
            <div class="lg:col-span-6 p-6 sm:p-8 bg-gray-50/50">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2 pb-4 border-b border-gray-200">
                    <i class="fas fa-box text-indigo-500"></i>
                    Detail Komoditas Pesanan
                </h3>

                <div class="space-y-4 mb-6 max-h-96 overflow-y-auto pr-2">
                    @foreach($items as $item)
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex">
                        <div class="w-28 h-28 bg-gray-100 relative shrink-0">
                            @if ($item->produk->gambar)
                                <img src="{{ asset('storage/' . $item->produk->gambar) }}" alt="{{ $item->produk->nama }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <i class="fas fa-image text-2xl"></i>
                                </div>
                            @endif
                        </div>

                        <div class="p-4 flex-1 flex flex-col justify-between">
                            <div>
                                <h4 class="text-sm font-bold text-gray-900 line-clamp-1">{{ $item->produk->nama }}</h4>
                                <div class="text-xs text-brand-600 font-semibold mt-0.5"><i class="fas fa-store mr-1"></i> {{ $item->produk->umkm->nama_toko ?? 'Petani Mitra' }}</div>
                            </div>
                            <div class="flex items-center justify-between mt-2">
                                <span class="text-xs font-bold text-gray-700 bg-gray-100 px-2 py-0.5 rounded">
                                    {{ $item->jumlah }} x unit
                                </span>
                                <span class="text-sm font-extrabold text-indigo-700">
                                    Rp {{ number_format($item->harga_satuan * $item->jumlah, 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <div class="bg-indigo-50/70 p-5 sm:p-6 border border-indigo-100 rounded-xl space-y-2">
                    <div class="flex items-center justify-between text-xs text-gray-600">
                        <span>Jaminan Garansi Buah Segar:</span>
                        <span class="font-bold text-emerald-700"><i class="fas fa-shield-halved"></i> 100% Proteksi Busuk</span>
                    </div>
                    <div class="flex items-center justify-between pt-2 border-t border-indigo-100/60">
                        <span class="text-sm font-bold text-gray-800">Total Tagihan</span>
                        <span class="text-2xl font-black text-indigo-700 font-display">
                            Rp {{ number_format($total_harga, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Kolom Form Pemesanan & Alamat Tersimpan --}}
            <div class="lg:col-span-6 p-6 sm:p-8 space-y-6">
                
                <div class="flex items-center justify-between pb-4 border-b border-gray-200">
                    <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-location-dot text-indigo-500"></i>
                        Data Alamat Pengiriman
                    </h3>
                    
                    @if(isset($alamats) && $alamats->count() > 0)
                        <button 
                            type="button" 
                            onclick="document.getElementById('modalPilihAlamat').classList.remove('hidden')"
                            class="text-xs font-bold text-indigo-600 hover:text-indigo-800 flex items-center gap-1 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-200"
                        >
                            <i class="fas fa-address-book"></i> Pilih Buku Alamat
                        </button>
                    @endif
                </div>

                @php
                    $defaultName = old('name', $alamatUtama->nama_penerima ?? Auth::user()->name);
                    $defaultPhone = old('phone', $alamatUtama->no_hp ?? '');
                    $defaultAlamat = old('alamat', $alamatUtama ? $alamatUtama->formatted_alamat : Auth::user()->alamat);
                @endphp

                <form action="{{ route('pembeli.checkout') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <input type="hidden" name="is_cart" value="{{ $is_cart ? '1' : '0' }}">
                    @if(!$is_cart)
                        <input type="hidden" name="produk_id" value="{{ $produkId }}">
                        <input type="hidden" name="jumlah" value="{{ $quantity }}">
                    @endif

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Nama Penerima <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="name" id="input_nama_penerima"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white text-xs font-medium"
                            placeholder="Nama penerima paket" required value="{{ $defaultName }}">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            No. WhatsApp / HP Penerima <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="phone" id="input_phone_penerima"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white text-xs font-medium"
                            placeholder="08xxxxxxxxxx" required value="{{ $defaultPhone }}">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1">
                            Alamat Lengkap Tujuan <span class="text-red-500">*</span>
                        </label>
                        <textarea name="alamat" id="input_alamat_lengkap" rows="3"
                            class="block w-full px-3.5 py-2.5 rounded-xl border border-gray-300 bg-gray-50 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 focus:bg-white text-xs font-medium leading-relaxed"
                            placeholder="Jalan, No. Rumah, RT/RW, Kecamatan, Kota/Kabupaten, Kode Pos" required>{{ $defaultAlamat }}</textarea>
                    </div>

                    <div class="pt-4 border-t border-gray-100 space-y-3">
                        <button type="submit" class="w-full flex justify-center items-center px-6 py-3.5 border border-transparent text-sm font-extrabold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 shadow-md transition-all">
                            <i class="fas fa-lock mr-2"></i>
                            Pesan & Lanjut Bayar (Midtrans)
                        </button>
                        <p class="text-center text-[11px] text-gray-400 flex items-center justify-center gap-1.5">
                            <i class="fas fa-shield-alt text-emerald-500"></i>
                            Saldo ditampung di Rekening Escrow resmi hingga mangga diterima segar
                        </p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- 📖 MODAL PILIH DARI BUKU ALAMAT TERSIMPAN -->
@if(isset($alamats) && $alamats->count() > 0)
<div id="modalPilihAlamat" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4 max-h-[85vh] overflow-y-auto">
        <div class="flex items-center justify-between pb-3 border-b border-gray-100">
            <h4 class="text-base font-extrabold text-gray-900">Pilih Dari Buku Alamat</h4>
            <button type="button" onclick="document.getElementById('modalPilihAlamat').classList.add('hidden')" class="w-8 h-8 rounded-full bg-gray-100 text-gray-500 hover:bg-gray-200 flex items-center justify-center">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <div class="space-y-3">
            @foreach($alamats as $alm)
                <div class="p-4 rounded-xl border border-gray-200 hover:border-indigo-500 hover:bg-indigo-50/30 transition cursor-pointer flex flex-col justify-between space-y-2"
                     onclick="selectSavedAddress('{{ addslashes($alm->nama_penerima) }}', '{{ addslashes($alm->no_hp) }}', '{{ addslashes($alm->formatted_alamat) }}')">
                    <div class="flex items-center justify-between">
                        <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-700 text-[10px] font-bold uppercase">{{ $alm->label }}</span>
                        @if($alm->is_utama)
                            <span class="text-[10px] font-extrabold text-indigo-600 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-200">UTAMA</span>
                        @endif
                    </div>
                    <div class="text-xs">
                        <strong class="text-gray-900">{{ $alm->nama_penerima }}</strong> ({{ $alm->no_hp }})
                        <p class="text-gray-600 mt-1 leading-relaxed">{{ $alm->formatted_alamat }}</p>
                    </div>
                    <div class="pt-2 text-right">
                        <span class="text-xs font-bold text-indigo-600 hover:underline">Gunakan Alamat Ini →</span>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<script>
function selectSavedAddress(name, phone, fullAddress) {
    document.getElementById('input_nama_penerima').value = name;
    document.getElementById('input_phone_penerima').value = phone;
    document.getElementById('input_alamat_lengkap').value = fullAddress;
    document.getElementById('modalPilihAlamat').classList.add('hidden');
}
</script>
@endif
@endsection
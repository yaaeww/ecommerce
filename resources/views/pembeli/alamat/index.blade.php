@extends('layouts.app')

@section('page_title', 'Buku Alamat Pengiriman')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-900 font-display">Buku Alamat Pengiriman</h2>
            <p class="text-xs text-slate-500 mt-1">Kelola alamat tujuan kirim mangga segar untuk kemudahan dan kecepatan checkout</p>
        </div>
        <button 
            type="button" 
            onclick="document.getElementById('modalTambahAlamat').classList.remove('hidden')"
            class="inline-flex items-center gap-2 px-4 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-xs"
        >
            <i class="fas fa-plus"></i> Tambah Alamat Baru
        </button>
    </div>

    <!-- Grid Kartu Alamat -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse($alamats as $a)
            <div class="card bg-white border {{ $a->is_utama ? 'border-brand-500 ring-2 ring-brand-500/10' : 'border-slate-200/80' }} rounded-2xl p-6 shadow-sm flex flex-col justify-between space-y-4">
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded-lg bg-slate-100 text-slate-700 font-extrabold text-[10px] uppercase">
                                {{ $a->label }}
                            </span>
                            @if($a->is_utama)
                                <span class="px-2.5 py-0.5 rounded-lg bg-brand-50 text-brand-700 font-extrabold text-[10px] uppercase border border-brand-200">
                                    <i class="fas fa-check-circle mr-1"></i> Utama
                                </span>
                            @endif
                        </div>
                    </div>

                    <h4 class="text-sm font-extrabold text-slate-900">{{ $a->nama_penerima }}</h4>
                    <p class="text-xs text-slate-500 mt-0.5"><i class="fas fa-phone text-slate-400 mr-1"></i> {{ $a->no_hp }}</p>
                    
                    <p class="text-xs text-slate-700 mt-3 leading-relaxed">
                        {{ $a->alamat_lengkap }}
                    </p>
                    <p class="text-[11px] text-slate-400 mt-1">
                        {{ $a->kecamatan ? $a->kecamatan . ', ' : '' }}{{ $a->kota_kabupaten }}, {{ $a->provinsi }} {{ $a->kode_pos ? '(' . $a->kode_pos . ')' : '' }}
                    </p>
                    @if($a->patokan)
                        <p class="text-[11px] text-slate-500 mt-1 italic"><i class="fas fa-map-pin text-brand-500 mr-1"></i> Patokan: {{ $a->patokan }}</p>
                    @endif
                </div>

                <div class="pt-3 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        @if(!$a->is_utama)
                            <form action="{{ route('pembeli.alamat.set-utama', $a->id) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" class="text-xs font-bold text-brand-600 hover:text-brand-700">
                                    Jadikan Utama
                                </button>
                            </form>
                        @endif
                    </div>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('pembeli.alamat.destroy', $a->id) }}" method="POST" onsubmit="return confirm('Hapus alamat ini?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-rose-500 hover:text-rose-700">
                                <i class="fas fa-trash"></i> Hapus
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-2 py-16 card bg-white border border-slate-200/80 rounded-2xl text-center">
                <i class="fas fa-map-location-dot text-4xl text-slate-300 mb-3"></i>
                <h4 class="text-sm font-bold text-slate-800">Belum Ada Alamat Tersimpan</h4>
                <p class="text-xs text-slate-500 max-w-sm mx-auto mt-1">Tambahkan alamat pengiriman Anda agar tidak perlu mengetik ulang saat membeli mangga segar.</p>
                <button 
                    type="button" 
                    onclick="document.getElementById('modalTambahAlamat').classList.remove('hidden')"
                    class="mt-4 inline-flex items-center gap-2 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl transition shadow-xs"
                >
                    <i class="fas fa-plus"></i> Tambah Alamat Pertama
                </button>
            </div>
        @endforelse
    </div>

    <!-- Modal Tambah Alamat Baru -->
    <div id="modalTambahAlamat" class="fixed inset-0 z-50 bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4 hidden">
        <div class="bg-white rounded-3xl max-w-lg w-full p-6 sm:p-8 shadow-2xl space-y-4 max-h-[90vh] overflow-y-auto">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <h3 class="text-base font-extrabold text-slate-900">Tambah Alamat Pengiriman</h3>
                <button type="button" onclick="document.getElementById('modalTambahAlamat').classList.add('hidden')" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>

            <form action="{{ route('pembeli.alamat.store') }}" method="POST" class="space-y-4">
                @csrf

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Label Alamat</label>
                        <input type="text" name="label" required placeholder="Contoh: Rumah / Kantor" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Nama Penerima</label>
                        <input type="text" name="nama_penerima" required value="{{ Auth::user()->name }}" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No. WhatsApp / HP</label>
                        <input type="text" name="no_hp" required placeholder="08xxxxxxxxxx" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kode Pos</label>
                        <input type="text" name="kode_pos" placeholder="452xx" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Provinsi</label>
                        <input type="text" name="provinsi" required value="Jawa Barat" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kota / Kab.</label>
                        <input type="text" name="kota_kabupaten" required value="Kab. Indramayu" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Kecamatan</label>
                        <input type="text" name="kecamatan" placeholder="Sindang" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Alamat Lengkap (Jalan, RT/RW, No. Rumah)</label>
                    <textarea name="alamat_lengkap" rows="3" required placeholder="Jl. Raya Jatibarang No. 12 RT 04 RW 02..." class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Patokan Lokasi (Opsional)</label>
                    <input type="text" name="patokan" placeholder="Dekat Masjid / Cat Pagar Hijau" class="w-full px-3 py-2 rounded-xl border border-slate-200 text-xs focus:border-brand-500 focus:outline-none">
                </div>

                <div class="flex items-center gap-2 pt-2">
                    <input type="checkbox" name="is_utama" id="is_utama_check" value="1" class="rounded text-brand-600 focus:ring-brand-500">
                    <label for="is_utama_check" class="text-xs font-medium text-slate-700 cursor-pointer">Jadikan sebagai alamat pengiriman utama</label>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalTambahAlamat').classList.add('hidden')" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50">
                        Batal
                    </button>
                    <button type="submit" class="px-5 py-2 bg-brand-600 hover:bg-brand-700 text-white font-bold text-xs rounded-xl shadow-xs">
                        Simpan Alamat
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

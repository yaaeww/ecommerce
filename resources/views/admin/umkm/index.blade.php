@extends('layouts.app')

@section('page_title', 'Toko & UMKM')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Manajemen Toko & UMKM</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola verifikasi, status kemitraan toko kebun dan pengrajin</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-sm">
                Total: <span class="text-brand-600">{{ $approvedUmkms->count() + $pendingUmkms->count() + $rejectedUmkms->count() }}</span> Toko
            </span>
        </div>
    </div>

    <!-- Quick Filter Tab Pills -->
    <div class="flex flex-wrap items-center gap-2 border-b border-slate-200 pb-4">
        <button 
            type="button" 
            onclick="switchTab('approved')" 
            id="tabBtn-approved" 
            class="tab-btn active px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-brand-600 text-white shadow-sm"
        >
            <i class="fas fa-check-circle"></i>
            <span>Disetujui</span>
            <span class="px-1.5 py-0.5 rounded-md bg-white/20 text-[10px]">{{ $approvedUmkms->count() }}</span>
        </button>

        <button 
            type="button" 
            onclick="switchTab('pending')" 
            id="tabBtn-pending" 
            class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50"
        >
            <i class="fas fa-clock text-amber-500"></i>
            <span>Menunggu Verifikasi</span>
            @if($pendingUmkms->count() > 0)
                <span class="px-1.5 py-0.5 rounded-md bg-amber-100 text-amber-800 text-[10px] font-black">{{ $pendingUmkms->count() }}</span>
            @endif
        </button>

        <button 
            type="button" 
            onclick="switchTab('rejected')" 
            id="tabBtn-rejected" 
            class="tab-btn px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-2 bg-white text-slate-600 border border-slate-200 hover:bg-slate-50"
        >
            <i class="fas fa-ban text-rose-500"></i>
            <span>Ditolak / Nonaktif</span>
            <span class="px-1.5 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[10px]">{{ $rejectedUmkms->count() }}</span>
        </button>
    </div>

    <!-- TAB 1: APPROVED -->
    <div id="tabContent-approved" class="tab-pane">
        <div class="card bg-white border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full text-left">
                    <thead>
                        <tr>
                            <th>Toko & Pemilik</th>
                            <th>No. Telp</th>
                            <th>Alamat</th>
                            <th>Produk</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($approvedUmkms as $umkm)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 p-1 flex items-center justify-center shrink-0">
                                            @if($umkm->logo && file_exists(public_path('storage/' . $umkm->logo)))
                                                <img src="{{ asset('storage/' . $umkm->logo) }}" class="w-full h-full object-contain rounded-lg" alt="{{ $umkm->nama_toko }}">
                                            @else
                                                <i class="fas fa-store text-slate-400 text-base"></i>
                                            @endif
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-xs text-slate-900">{{ $umkm->nama_toko }}</h4>
                                            <p class="text-[11px] text-slate-400">{{ $umkm->user->name ?? 'Pemilik' }} • {{ $umkm->user->email ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-xs text-slate-700 font-medium">{{ $umkm->no_telp ?? '-' }}</span>
                                </td>
                                <td>
                                    <span class="text-xs text-slate-600 line-clamp-1 max-w-xs">{{ $umkm->alamat ?? 'Indramayu' }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.umkm.products', $umkm->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-brand-50 text-brand-700 text-xs font-bold hover:bg-brand-100 transition">
                                        <i class="fas fa-boxes-stacked text-[10px]"></i>
                                        <span>{{ $umkm->produks->count() }} Produk</span>
                                    </a>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('admin.umkm.show', $umkm->id) }}" class="p-2 text-slate-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="Lihat Detail">
                                            <i class="fas fa-eye text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.umkm.reject', $umkm->id) }}" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan atau tolak UMKM ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Nonaktifkan">
                                                <i class="fas fa-ban text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-12 text-slate-400 text-xs">
                                    Belum ada toko UMKM yang disetujui.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 2: PENDING -->
    <div id="tabContent-pending" class="tab-pane hidden">
        <div class="card bg-white border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full text-left">
                    <thead>
                        <tr>
                            <th>Toko Diajukan</th>
                            <th>Pemilik Akun</th>
                            <th>Alamat & Telp</th>
                            <th class="text-center">Tindakan Verifikasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($pendingUmkms as $umkm)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 border border-amber-200 flex items-center justify-center shrink-0">
                                            <i class="fas fa-clock text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-xs text-slate-900">{{ $umkm->nama_toko }}</h4>
                                            <p class="text-[11px] text-slate-400">{{ $umkm->deskripsi ?? 'Pengajuan toko baru' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <p class="font-bold text-xs text-slate-800">{{ $umkm->user->name ?? 'Penjual' }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $umkm->user->email ?? '' }}</p>
                                </td>
                                <td>
                                    <p class="text-xs text-slate-700">{{ $umkm->alamat ?? '-' }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $umkm->no_telp ?? '-' }}</p>
                                </td>
                                <td class="text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <form action="{{ route('admin.umkm.approve', $umkm->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs transition flex items-center gap-1">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.umkm.reject', $umkm->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak pengajuan UMKM ini?')">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition flex items-center gap-1">
                                                <i class="fas fa-xmark"></i> Tolak
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-12 text-slate-400 text-xs">
                                    <i class="fas fa-circle-check text-3xl text-emerald-400 mb-2 block"></i>
                                    Tidak ada pengajuan toko UMKM baru yang menunggu persetujuan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- TAB 3: REJECTED -->
    <div id="tabContent-rejected" class="tab-pane hidden">
        <div class="card bg-white border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="table w-full text-left">
                    <thead>
                        <tr>
                            <th>Toko Ditolak / Nonaktif</th>
                            <th>Pemilik</th>
                            <th>Alamat</th>
                            <th class="text-center">Aksi Pulihkan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rejectedUmkms as $umkm)
                            <tr class="hover:bg-slate-50/70 transition">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center shrink-0">
                                            <i class="fas fa-store-slash text-sm"></i>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-xs text-slate-700">{{ $umkm->nama_toko }}</h4>
                                            <p class="text-[11px] text-slate-400">{{ $umkm->alamat }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-xs text-slate-700">{{ $umkm->user->name ?? 'Penjual' }}</span>
                                </td>
                                <td>
                                    <span class="text-xs text-slate-500">{{ $umkm->alamat ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <form action="{{ route('admin.umkm.approve', $umkm->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 font-bold text-xs transition">
                                            <i class="fas fa-rotate-left mr-1"></i> Aktifkan Kembali
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-12 text-slate-400 text-xs">
                                    Tidak ada toko yang ditolak.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

@push('scripts')
<script>
    function switchTab(tabName) {
        document.querySelectorAll('.tab-pane').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('.tab-btn').forEach(btn => {
            btn.classList.remove('bg-brand-600', 'text-white', 'shadow-sm');
            btn.classList.add('bg-white', 'text-slate-600', 'border', 'border-slate-200');
        });

        const activeContent = document.getElementById(`tabContent-${tabName}`);
        const activeBtn = document.getElementById(`tabBtn-${tabName}`);

        if (activeContent) activeContent.classList.remove('hidden');
        if (activeBtn) {
            activeBtn.classList.remove('bg-white', 'text-slate-600', 'border', 'border-slate-200');
            activeBtn.classList.add('bg-brand-600', 'text-white', 'shadow-sm');
        }
    }
</script>
@endpush
@endsection
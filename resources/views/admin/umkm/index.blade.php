@extends('layouts.app')

@section('page_title', 'Toko & UMKM')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Manajemen Toko & UMKM</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola verifikasi, status kemitraan toko kebun mangga dan pengrajin</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-sm">
                Total: <span class="text-brand-600 font-extrabold">{{ $totalUmkms }}</span> Toko Mitra
            </span>
        </div>
    </div>

    <!-- Alert Flash -->
    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-center justify-between text-xs text-emerald-800 shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-circle-check text-emerald-600 text-base"></i>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 flex items-center justify-between text-xs text-rose-800 shadow-sm">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-triangle-exclamation text-rose-600 text-base"></i>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700 p-1">
                <i class="fas fa-times"></i>
            </button>
        </div>
    @endif

    <!-- Quick Filter Tab Pills & Search -->
    <div class="card p-4 bg-white border border-slate-200/80 shadow-sm rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        
        <!-- Tab Pills -->
        <div class="flex flex-wrap items-center gap-2">
            <a 
                href="{{ route('admin.umkm.index', array_merge(request()->query(), ['status' => 'all'])) }}" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'all' ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200/60' }}"
            >
                <i class="fas fa-list"></i>
                <span>Semua Toko</span>
                <span class="px-1.5 py-0.5 rounded-md {{ $status === 'all' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }} text-[10px]">{{ $totalUmkms }}</span>
            </a>

            <a 
                href="{{ route('admin.umkm.index', array_merge(request()->query(), ['status' => 'approved'])) }}" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'approved' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200/60' }}"
            >
                <i class="fas fa-check-circle"></i>
                <span>Disetujui</span>
                <span class="px-1.5 py-0.5 rounded-md {{ $status === 'approved' ? 'bg-white/20 text-white' : 'bg-emerald-100 text-emerald-800' }} text-[10px]">{{ $approvedCount }}</span>
            </a>

            <a 
                href="{{ route('admin.umkm.index', array_merge(request()->query(), ['status' => 'pending'])) }}" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'pending' ? 'bg-amber-500 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200/60' }}"
            >
                <i class="fas fa-clock"></i>
                <span>Menunggu Verifikasi</span>
                @if($pendingCount > 0)
                    <span class="px-1.5 py-0.5 rounded-md {{ $status === 'pending' ? 'bg-white/20 text-white' : 'bg-amber-100 text-amber-900' }} text-[10px] font-black">{{ $pendingCount }}</span>
                @endif
            </a>

            <a 
                href="{{ route('admin.umkm.index', array_merge(request()->query(), ['status' => 'rejected'])) }}" 
                class="px-3.5 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $status === 'rejected' ? 'bg-rose-600 text-white shadow-sm' : 'bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200/60' }}"
            >
                <i class="fas fa-ban"></i>
                <span>Ditolak</span>
                <span class="px-1.5 py-0.5 rounded-md {{ $status === 'rejected' ? 'bg-white/20 text-white' : 'bg-slate-200 text-slate-700' }} text-[10px]">{{ $rejectedCount }}</span>
            </a>
        </div>

        <!-- Search Form -->
        <form method="GET" action="{{ route('admin.umkm.index') }}" class="flex items-center gap-2 w-full sm:w-72">
            <input type="hidden" name="status" value="{{ $status }}">
            <div class="relative flex-1">
                <i class="fas fa-search absolute left-3 top-2.5 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari toko, pemilik, alamat..." 
                    class="w-full pl-8 pr-3 py-1.5 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500"
                >
            </div>
            <button type="submit" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.umkm.index', ['status' => $status]) }}" class="p-1.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition" title="Reset">
                    <i class="fas fa-rotate-left text-xs"></i>
                </a>
            @endif
        </form>

    </div>

    <!-- Table Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 font-display">Daftar Toko & Kebun Mitra</h3>
            <span class="text-xs font-bold text-slate-400">{{ $umkms->total() }} Toko Ditampilkan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-14">No</th>
                        <th>Toko & Pemilik</th>
                        <th>No. Telp</th>
                        <th>Alamat</th>
                        <th>Produk</th>
                        <th class="text-center w-28">Status</th>
                        <th class="text-right w-36">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($umkms as $index => $umkm)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="text-slate-400 font-bold align-middle">
                                {{ $umkms->firstItem() + $index }}
                            </td>
                            
                            <!-- Toko & Pemilik -->
                            <td class="align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200 p-1 flex items-center justify-center shrink-0 shadow-xs">
                                        @if($umkm->logo && file_exists(public_path('storage/' . $umkm->logo)))
                                            <img src="{{ asset('storage/' . $umkm->logo) }}" class="w-full h-full object-contain rounded-lg" alt="{{ $umkm->nama_toko }}">
                                        @else
                                            <i class="fas fa-store text-slate-400 text-base"></i>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <h4 class="font-extrabold text-xs text-slate-900 truncate">{{ $umkm->nama_toko }}</h4>
                                        <p class="text-[10px] text-slate-400 truncate">{{ $umkm->user->name ?? 'Pemilik' }} • {{ $umkm->user->email ?? '' }}</p>
                                    </div>
                                </div>
                            </td>

                            <!-- Telp -->
                            <td class="align-middle">
                                <span class="font-medium text-slate-700">{{ $umkm->no_telp ?? '-' }}</span>
                            </td>

                            <!-- Alamat -->
                            <td class="align-middle">
                                <span class="text-slate-600 block truncate max-w-[180px]">{{ $umkm->alamat ?? 'Indramayu' }}</span>
                            </td>

                            <!-- Produk -->
                            <td class="align-middle">
                                <a href="{{ route('admin.umkm.products', $umkm->id) }}" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-brand-50 text-brand-700 font-bold hover:bg-brand-100 transition border border-brand-200/60">
                                    <i class="fas fa-boxes-stacked text-[10px]"></i>
                                    <span>{{ $umkm->produks->count() }} Produk</span>
                                </a>
                            </td>

                            <!-- Status -->
                            <td class="align-middle text-center">
                                @if($umkm->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-circle-check mr-1 text-[8px]"></i> Disetujui
                                    </span>
                                @elseif($umkm->status === 'pending')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        <i class="fas fa-clock mr-1 text-[8px]"></i> Pending
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fas fa-ban mr-1 text-[8px]"></i> Ditolak
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi -->
                            <td class="align-middle text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.umkm.show', $umkm->id) }}" class="p-2 text-slate-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="Lihat Detail">
                                        <i class="fas fa-eye text-xs"></i>
                                    </a>

                                    @if($umkm->status === 'pending')
                                        <form action="{{ route('admin.umkm.approve', $umkm->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] transition" title="Setujui Toko">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.umkm.reject', $umkm->id) }}" method="POST" class="inline" onsubmit="return confirm('Tolak pendaftaran UMKM ini?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-600 hover:bg-rose-700 text-white font-bold text-[11px] transition" title="Tolak Toko">
                                                <i class="fas fa-xmark"></i>
                                            </button>
                                        </form>
                                    @elseif($umkm->status === 'approved')
                                        <form action="{{ route('admin.umkm.reject', $umkm->id) }}" method="POST" class="inline" onsubmit="return confirm('Nonaktifkan toko UMKM ini?')">
                                            @csrf
                                            <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Nonaktifkan">
                                                <i class="fas fa-ban text-xs"></i>
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.umkm.approve', $umkm->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 font-bold text-[11px] transition" title="Aktifkan Kembali">
                                                <i class="fas fa-rotate-left mr-1"></i> Pulihkan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400 text-xs">
                                <i class="fas fa-store-slash text-3xl text-slate-300 mb-2 block"></i>
                                Tidak ada data toko sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($umkms->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $umkms->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
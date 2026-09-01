@extends('layouts.app')

@section('page_title', 'Akun Penjual')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Manajemen Akun Penjual</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar pengguna dengan hak akses penjual / mitra UMKM dan perkebunan</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-sm">
                Total: <span class="text-brand-600 font-extrabold">{{ $totalPenjual }}</span> Akun Penjual
            </span>
        </div>
    </div>

    <!-- Alert Success -->
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

    <!-- Search & Filter Card -->
    <div class="card p-4 bg-white border border-slate-200/80 shadow-sm rounded-2xl">
        <form method="GET" action="{{ route('admin.penjual.index') }}" class="flex items-center gap-3">
            <div class="flex-1 relative">
                <i class="fas fa-search absolute left-3.5 top-3 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari nama penjual, email, nama toko, atau alamat..." 
                    class="w-full pl-9 pr-4 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500"
                >
            </div>
            <button type="submit" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition">
                Cari
            </button>
            @if($search)
                <a href="{{ route('admin.penjual.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-xl transition" title="Reset">
                    <i class="fas fa-rotate-left text-xs"></i>
                </a>
            @endif
        </form>
    </div>

    <!-- Table Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 font-display">Daftar Akun Mitra Penjual</h3>
            <span class="text-xs font-bold text-slate-400">{{ $penjual->total() }} Data Ditampilkan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-14">No</th>
                        <th>Nama & Profil</th>
                        <th>Email Akun</th>
                        <th>Toko UMKM Binaan</th>
                        <th>Bergabung Sejak</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($penjual as $index => $user)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="text-slate-400 font-bold align-middle">
                                {{ $penjual->firstItem() + $index }}
                            </td>
                            <td class="align-middle">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-brand-50 border border-brand-200 text-brand-600 font-extrabold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-xs text-slate-900">{{ $user->name }}</h4>
                                        <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-600">
                                            Penjual
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td class="align-middle">
                                <span class="font-medium text-slate-700">{{ $user->email }}</span>
                            </td>
                            <td class="align-middle">
                                @if($user->umkm)
                                    <span class="font-bold text-brand-600 flex items-center gap-1.5">
                                        <i class="fas fa-store text-xs"></i>
                                        {{ $user->umkm->nama_toko }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Belum buat toko</span>
                                @endif
                            </td>
                            <td class="align-middle">
                                <span class="text-slate-500">{{ $user->created_at ? $user->created_at->translatedFormat('d M Y') : '-' }}</span>
                            </td>
                            <td class="align-middle text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('admin.penjual.edit', $user->id) }}" class="p-2 text-slate-500 hover:text-brand-600 hover:bg-brand-50 rounded-lg transition" title="Edit Akun">
                                        <i class="fas fa-pen-to-square text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.penjual.destroy', $user->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus akun penjual {{ addslashes($user->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition" title="Hapus Akun">
                                            <i class="fas fa-trash-can text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400 text-xs">
                                <i class="fas fa-users text-3xl text-slate-300 mb-2 block"></i>
                                Tidak ada akun penjual yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penjual->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $penjual->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
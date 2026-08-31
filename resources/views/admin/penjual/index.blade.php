@extends('layouts.app')

@section('page_title', 'Akun Penjual')

@section('content')
<div class="space-y-6">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Manajemen Akun Penjual</h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar pengguna dengan hak akses penjual / mitra UMKM</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-xs font-bold text-slate-700 shadow-sm">
                Total: <span class="text-brand-600 font-extrabold">{{ $penjual->count() }}</span> Akun
            </span>
        </div>
    </div>

    <!-- Table Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-16">No</th>
                        <th>Nama & Profil</th>
                        <th>Email Akun</th>
                        <th>Toko UMKM Binaan</th>
                        <th>Bergabung Sejak</th>
                        <th class="text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($penjual as $index => $user)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="text-xs text-slate-400 font-bold">
                                {{ $index + 1 }}
                            </td>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-brand-50 border border-brand-200 text-brand-600 font-bold text-xs flex items-center justify-center shrink-0 shadow-sm">
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
                            <td>
                                <span class="text-xs font-semibold text-slate-700">{{ $user->email }}</span>
                            </td>
                            <td>
                                @if($user->umkm)
                                    <span class="font-bold text-xs text-brand-600 flex items-center gap-1.5">
                                        <i class="fas fa-store text-xs"></i>
                                        {{ $user->umkm->nama_toko }}
                                    </span>
                                @else
                                    <span class="text-xs text-slate-400 italic">Belum buat toko</span>
                                @endif
                            </td>
                            <td>
                                <span class="text-xs text-slate-500">{{ $user->created_at ? $user->created_at->format('d M Y') : '-' }}</span>
                            </td>
                            <td class="text-center">
                                <div class="flex items-center justify-center gap-2">
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
                                <i class="fas fa-users text-3xl mb-2 block"></i>
                                Belum ada akun penjual terdaftar.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
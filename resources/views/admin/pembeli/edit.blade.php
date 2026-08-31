@extends('layouts.app')

@section('page_title', 'Edit Akun Pembeli')

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight font-display">Edit Akun Pembeli</h2>
            <p class="text-xs text-slate-500 mt-0.5">Perbarui nama dan alamat email pengguna pembeli</p>
        </div>
        <a 
            href="{{ route('admin.pembeli.index') }}" 
            class="px-3.5 py-2 text-xs font-bold text-slate-600 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl transition shadow-sm"
        >
            <i class="fas fa-arrow-left mr-1"></i> Kembali
        </a>
    </div>

    <div class="card p-6 sm:p-8 bg-white border border-slate-200/80 shadow-sm">
        <form action="{{ route('admin.pembeli.update', $pembeli->id) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="name" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Nama Lengkap <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="text" 
                    name="name" 
                    id="name" 
                    value="{{ old('name', $pembeli->name) }}" 
                    required 
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white transition"
                >
                @error('name')
                    <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">
                    Alamat Email <span class="text-rose-500">*</span>
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email', $pembeli->email) }}" 
                    required 
                    class="w-full px-4 py-3 rounded-xl bg-slate-50 border border-slate-200 text-xs font-semibold text-slate-800 focus:outline-none focus:border-brand-500 focus:bg-white transition"
                >
                @error('email')
                    <p class="text-rose-500 text-xs mt-1 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a 
                    href="{{ route('admin.pembeli.index') }}" 
                    class="px-5 py-2.5 rounded-xl text-xs font-bold text-slate-600 bg-slate-100 hover:bg-slate-200 transition"
                >
                    Batal
                </a>
                <button 
                    type="submit" 
                    class="px-6 py-2.5 rounded-xl text-xs font-bold text-white bg-brand-600 hover:bg-brand-700 transition shadow-sm hover:shadow"
                >
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
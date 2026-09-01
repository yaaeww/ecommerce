@extends('layouts.app')

@section('page_title', 'Pusat Moderasi Ulasan & Sentimen')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Pusat Moderasi Ulasan & Sentimen</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Pantau feedback pembeli, evaluasi kualitas komoditas buah dari kebun mitra, serta moderasi komentar ulasan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($krisisCount > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold">
                    <i class="fas fa-triangle-exclamation text-amber-500"></i>
                    {{ $krisisCount }} Ulasan Bintang 1-2 (Perlu Evaluasi)
                </span>
            @endif
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

    <!-- Rating Distribution & Sentiment Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        
        <!-- Rating Summary Card -->
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl flex flex-col justify-between">
            <div>
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-2">Rata-Rata Kepuasan Konsumen</span>
                <div class="flex items-baseline gap-3">
                    <span class="text-4xl font-extrabold text-slate-900 font-display">{{ number_format($avgRating, 1) }}</span>
                    <div class="text-amber-400 text-sm flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= round($avgRating) ? 'text-amber-400' : 'text-slate-200' }}"></i>
                        @endfor
                    </div>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-4 border-t border-slate-100 pt-3">
                Total <strong>{{ $totalUlasan }}</strong> ulasan dari pembeli terverifikasi
            </p>
        </div>

        <!-- Star Breakdown Progress Bars -->
        <div class="lg:col-span-2 card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl space-y-2.5">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider block mb-1">Distribusi Bintang Sentimen</span>
            
            @foreach([5, 4, 3, 2, 1] as $star)
                @php
                    $count = $starCounts[$star];
                    $pct = $totalUlasan > 0 ? round(($count / $totalUlasan) * 100, 1) : 0;
                @endphp
                <div class="flex items-center gap-3 text-xs">
                    <span class="w-12 font-bold text-slate-700 flex items-center gap-1">
                        {{ $star }} <i class="fas fa-star text-amber-400 text-[10px]"></i>
                    </span>
                    <div class="flex-1 bg-slate-100 h-2.5 rounded-full overflow-hidden">
                        <div class="h-full rounded-full {{ $star >= 4 ? 'bg-emerald-500' : ($star == 3 ? 'bg-amber-400' : 'bg-rose-500') }}" style="width: {{ $pct }}%"></div>
                    </div>
                    <span class="w-16 text-right font-extrabold text-slate-800 text-[11px]">{{ $count }} ({{ $pct }}%)</span>
                </div>
            @endforeach
        </div>

    </div>

    <!-- Filter Card -->
    <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-2xl">
        <form method="GET" action="{{ route('admin.ulasan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Rating Bintang</label>
                <select name="rating" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500">
                    <option value="">Semua Bintang</option>
                    <option value="5" {{ $rating == 5 ? 'selected' : '' }}>⭐⭐⭐⭐⭐ (5 Bintang)</option>
                    <option value="4" {{ $rating == 4 ? 'selected' : '' }}>⭐⭐⭐⭐ (4 Bintang)</option>
                    <option value="3" {{ $rating == 3 ? 'selected' : '' }}>⭐⭐⭐ (3 Bintang)</option>
                    <option value="2" {{ $rating == 2 ? 'selected' : '' }}>⭐⭐ (2 Bintang - Negatif)</option>
                    <option value="1" {{ $rating == 1 ? 'selected' : '' }}>⭐ (1 Bintang - Sangat Buruk)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Moderasi</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500">
                    <option value="semua" {{ $statusModerasi === 'semua' ? 'selected' : '' }}>Semua Status</option>
                    <option value="published" {{ $statusModerasi === 'published' ? 'selected' : '' }}>🟢 Diterbitkan (Published)</option>
                    <option value="hidden" {{ $statusModerasi === 'hidden' ? 'selected' : '' }}>🔴 Disembunyikan (Hidden/Spam)</option>
                    <option value="flagged" {{ $statusModerasi === 'flagged' ? 'selected' : '' }}>🟡 Ditandai (Flagged)</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Toko / Kebun Mitra</label>
                <select name="umkm_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500">
                    <option value="">Semua Toko Mitra</option>
                    @foreach($umkms as $umkm)
                        <option value="{{ $umkm->id }}" {{ $umkmId == $umkm->id ? 'selected' : '' }}>{{ $umkm->nama_toko }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.ulasan.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition" title="Reset">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Table: Ulasan Pembeli -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 font-display">Daftar Feedback & Ulasan Konsumen</h3>
            <span class="text-xs font-bold text-slate-400">{{ $ulasans->total() }} Ulasan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-28">Rating</th>
                        <th>Komoditas Produk & Toko</th>
                        <th>Pembeli</th>
                        <th>Isi Ulasan & Komentar</th>
                        <th class="text-center w-28">Status Moderasi</th>
                        <th class="text-right w-36">Aksi Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($ulasans as $ul)
                        <tr class="hover:bg-slate-50/70 transition">
                            
                            <!-- Rating -->
                            <td class="align-top whitespace-nowrap">
                                <div class="flex items-center gap-1 text-amber-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star {{ $i <= $ul->bintang ? 'text-amber-400' : 'text-slate-200' }} text-xs"></i>
                                    @endfor
                                </div>
                                <span class="text-[10px] text-slate-400 block mt-1">
                                    {{ $ul->created_at->translatedFormat('d M Y') }}
                                </span>
                            </td>

                            <!-- Produk & Toko -->
                            <td class="align-top">
                                <strong class="font-extrabold text-slate-900 block truncate max-w-[170px]">{{ $ul->produk->nama ?? 'Produk' }}</strong>
                                <span class="text-[10px] text-brand-600 font-semibold block mt-0.5">
                                    <i class="fas fa-store mr-1"></i>{{ $ul->produk->umkm->nama_toko ?? 'Kebun Mitra' }}
                                </span>
                            </td>

                            <!-- Pembeli -->
                            <td class="align-top">
                                <strong class="font-bold text-slate-900 block">{{ $ul->user->name ?? 'Pembeli' }}</strong>
                                <span class="text-[10px] text-slate-400 block truncate">{{ $ul->user->email ?? '-' }}</span>
                            </td>

                            <!-- Isi Ulasan -->
                            <td class="align-top max-w-sm">
                                <p class="text-slate-700 leading-relaxed font-medium">
                                    "{{ $ul->ulasan }}"
                                </p>
                                @if($ul->catatan_moderasi)
                                    <p class="text-[10px] text-rose-600 bg-rose-50 p-1.5 rounded-lg mt-1 font-semibold">
                                        <i class="fas fa-note-sticky mr-1"></i>Catatan Admin: {{ $ul->catatan_moderasi }}
                                    </p>
                                @endif
                            </td>

                            <!-- Status Moderasi -->
                            <td class="align-top text-center">
                                @if($ul->status_moderasi === 'published')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-eye mr-1 text-[8px]"></i> Published
                                    </span>
                                @elseif($ul->status_moderasi === 'hidden')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fas fa-eye-slash mr-1 text-[8px]"></i> Hidden
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                        <i class="fas fa-flag mr-1 text-[8px]"></i> Flagged
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Moderasi -->
                            <td class="align-top text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($ul->status_moderasi === 'published')
                                        <form method="POST" action="{{ route('admin.ulasan.moderate', $ul->id) }}">
                                            @csrf
                                            <input type="hidden" name="status_moderasi" value="hidden">
                                            <input type="hidden" name="catatan_moderasi" value="Disembunyikan oleh Superadmin">
                                            <button type="submit" onclick="return confirm('Sembunyikan ulasan ini dari publik?')" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white font-bold text-[11px] transition border border-rose-200" title="Sembunyikan">
                                                <i class="fas fa-eye-slash text-xs mr-1"></i> Hide
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.ulasan.moderate', $ul->id) }}">
                                            @csrf
                                            <input type="hidden" name="status_moderasi" value="published">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white font-bold text-[11px] transition border border-emerald-200" title="Terbitkan">
                                                <i class="fas fa-eye text-xs mr-1"></i> Publish
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400 text-xs">
                                Belum ada data ulasan sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ulasans->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $ulasans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

@extends('layouts.app')

@section('page_title', 'Status Komplain #' . $komplain->id)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">

    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('pembeli.pesanan.dikirim') }}" class="w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-600 hover:text-slate-900 flex items-center justify-center transition shadow-2xs">
                <i class="fas fa-arrow-left text-sm"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-900 font-display">Investigasi Komplain #{{ $komplain->id }}</h2>
                <p class="text-xs text-slate-500 mt-0.5">Status mediasi & peninjauan Garansi Segar Juragan Pelem</p>
            </div>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-extrabold border uppercase {{ $komplain->badge_color }}">
            {{ $komplain->status }}
        </span>
    </div>

    <!-- Status Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl p-6 space-y-6">
        
        <!-- Status Box -->
        <div class="p-4 rounded-xl {{ $komplain->status === 'disetujui' ? 'bg-emerald-50 border border-emerald-200' : ($komplain->status === 'ditolak' ? 'bg-rose-50 border border-rose-200' : 'bg-amber-50 border border-amber-200') }}">
            <div class="flex items-start gap-3">
                <i class="fas {{ $komplain->status === 'disetujui' ? 'fa-check-circle text-emerald-600' : ($komplain->status === 'ditolak' ? 'fa-times-circle text-rose-600' : 'fa-hourglass-half text-amber-600') }} text-lg mt-0.5"></i>
                <div class="space-y-1">
                    <h5 class="font-bold text-sm text-slate-900">
                        @if($komplain->status === 'diajukan')
                            Komplain Sedang Dalam Antrean Verifikasi
                        @elseif($komplain->status === 'diproses')
                            Tim Customer Care Sedang Meninjau Bukti & Koordinasi Toko
                        @elseif($komplain->status === 'disetujui')
                            Komplain Disetujui! Solusi: {{ strtoupper($komplain->solusi_diminta) }}
                        @elseif($komplain->status === 'ditolak')
                            Komplain Ditolak
                        @else
                            Komplain Selesai
                        @endif
                    </h5>
                    <p class="text-xs text-slate-600 leading-relaxed">
                        {{ $komplain->catatan_admin ?? 'Bukti foto dan video unboxing Anda telah tersimpan di sistem. Admin akan memverifikasi kelayakan klaim garansi dalam 1x24 jam kerja.' }}
                    </p>
                    @if($komplain->nominal_refund)
                        <div class="pt-2">
                            <span class="text-xs font-bold text-emerald-800 bg-emerald-100/80 px-2.5 py-1 rounded-lg">
                                Nominal Refund: Rp {{ number_format($komplain->nominal_refund, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Rincian Masalah -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
            <div>
                <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Rincian Laporan</h6>
                <dl class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <dt class="text-slate-500">Jenis Kendala:</dt>
                        <dd class="font-bold text-slate-800">{{ $komplain->label_tipe }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <dt class="text-slate-500">Solusi Diminta:</dt>
                        <dd class="font-bold text-brand-700 uppercase">{{ $komplain->solusi_diminta }}</dd>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-50">
                        <dt class="text-slate-500">Waktu Pengajuan:</dt>
                        <dd class="text-slate-700">{{ $komplain->created_at->translatedFormat('d M Y, H:i') }} WIB</dd>
                    </div>
                    <div class="py-1">
                        <dt class="text-slate-500 mb-1">Deskripsi Pembeli:</dt>
                        <dd class="p-3 bg-slate-50 rounded-xl text-slate-700 italic leading-relaxed border border-slate-100">
                            "{{ $komplain->deskripsi }}"
                        </dd>
                    </div>
                </dl>
            </div>

            <div>
                <h6 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Bukti Foto & Video</h6>
                <div class="space-y-3">
                    @if($komplain->foto_bukti)
                        <div>
                            <span class="text-[11px] font-medium text-slate-500 block mb-1">Foto Bukti Kerusakan:</span>
                            <a href="{{ asset('storage/' . $komplain->foto_bukti) }}" target="_blank" class="block group relative overflow-hidden rounded-xl border border-slate-200">
                                <img src="{{ asset('storage/' . $komplain->foto_bukti) }}" alt="Bukti Foto" class="w-full h-40 object-cover group-hover:scale-105 transition duration-300">
                                <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-xs font-bold gap-1.5">
                                    <i class="fas fa-search-plus"></i> Lihat Foto Penuh
                                </div>
                            </a>
                        </div>
                    @endif

                    @if($komplain->video_unboxing)
                        <div class="pt-2">
                            <span class="text-[11px] font-medium text-slate-500 block mb-1">Video Unboxing:</span>
                            <video controls class="w-full rounded-xl border border-slate-200 max-h-48 bg-black">
                                <source src="{{ asset('storage/' . $komplain->video_unboxing) }}" type="video/mp4">
                                Browser Anda tidak mendukung pemutaran video.
                            </video>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

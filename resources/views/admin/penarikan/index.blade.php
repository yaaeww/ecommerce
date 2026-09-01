@extends('layouts.app')

@section('page_title', 'Manajemen Pencairan Saldo (Payout Mitra)')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Manajemen Pencairan Saldo (Payout)</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Kelola permohonan penarikan dana hak bagi hasil toko mitra, verifikasi rekening, dan unggah bukti transfer perbankan.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($totalPending > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold animate-pulse">
                    <i class="fas fa-clock text-amber-500"></i>
                    {{ $totalPending }} Permohonan Menunggu Verifikasi
                </span>
            @endif
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

    <!-- 4 Metrics KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        
        <!-- Total Pengajuan -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-1">Total Pengajuan</span>
            <p class="text-2xl font-extrabold text-slate-900 font-display">{{ $totalPengajuan }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Permohonan penarikan dana</span>
        </div>

        <!-- Pending -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-amber-500">
            <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block mb-1">Menunggu Transfer</span>
            <p class="text-2xl font-extrabold text-amber-600 font-display">{{ $totalPending }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Perlu verifikasi & pembayaran</span>
        </div>

        <!-- Disetujui / Ditransfer -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-emerald-600">
            <span class="text-[11px] font-bold text-emerald-600 uppercase tracking-wider block mb-1">Total Dana Dicairkan</span>
            <p class="text-2xl font-extrabold text-emerald-600 font-display">Rp{{ number_format($totalDisetujui, 0, ',', '.') }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Sukses ditransfer ke mitra</span>
        </div>

        <!-- Ditolak -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-rose-500">
            <span class="text-[11px] font-bold text-rose-600 uppercase tracking-wider block mb-1">Ditolak / Batal</span>
            <p class="text-2xl font-extrabold text-rose-600 font-display">{{ $totalDitolak }}</p>
            <span class="text-[10px] text-slate-400 mt-1 block">Data rekening tidak cocok</span>
        </div>

    </div>

    <!-- Filter Card -->
    <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
        <form method="GET" action="{{ route('admin.penarikan.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            
            <div>
                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">Status Payout</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-hand-holding-dollar text-xs"></i>
                    </div>
                    <select name="status" class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer">
                        <option value="semua" {{ $status === 'semua' ? 'selected' : '' }}>Semua Status Payout</option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>🟡 Menunggu Verifikasi (Pending)</option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>🟢 Disetujui / Selesai Transfer</option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>🔴 Ditolak</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">Toko / Kebun Mitra</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-store text-xs"></i>
                    </div>
                    <select name="umkm_id" class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer">
                        <option value="">Semua Toko Mitra</option>
                        @foreach($umkms as $umkm)
                            <option value="{{ $umkm->id }}" {{ $umkmId == $umkm->id ? 'selected' : '' }}>{{ $umkm->nama_toko }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">Pencarian Rekening</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i class="fas fa-magnifying-glass text-xs"></i>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="No rek, nama bank, atas nama..." 
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs"
                    >
                </div>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2.5 px-4 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-2xl transition shadow-sm flex items-center justify-center gap-2 cursor-pointer">
                    <i class="fas fa-filter text-xs"></i>
                    <span>Terapkan</span>
                </button>
                @if($status !== 'semua' || $umkmId || $search)
                    <a href="{{ route('admin.penarikan.index') }}" class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-700 font-bold text-xs rounded-2xl transition border border-rose-200" title="Reset Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @else
                    <a href="{{ route('admin.penarikan.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition" title="Refresh">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>

        </form>
    </div>

    <!-- Table: Permohonan Penarikan Saldo -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 font-display">Daftar Permohonan Pencairan Saldo</h3>
            <span class="text-xs font-bold text-slate-400">{{ $penarikans->total() }} Pengajuan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th>Toko / Kebun Mitra</th>
                        <th>Nominal Penarikan</th>
                        <th>Rekening Tujuan</th>
                        <th>Waktu Pengajuan</th>
                        <th class="text-center w-28">Status</th>
                        <th class="text-right w-44">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($penarikans as $p)
                        <tr class="hover:bg-slate-50/70 transition">
                            
                            <!-- Toko -->
                            <td class="align-middle">
                                <strong class="font-extrabold text-slate-900 block">{{ $p->umkm->nama_toko ?? 'Kebun Mitra' }}</strong>
                                <span class="text-[10px] text-slate-400 block">{{ $p->umkm->user->name ?? 'Petani' }} • {{ $p->umkm->user->phone ?? $p->umkm->no_telp }}</span>
                            </td>

                            <!-- Nominal -->
                            <td class="align-middle">
                                <strong class="font-extrabold text-sm text-slate-900 font-display block">
                                    Rp{{ number_format($p->jumlah, 0, ',', '.') }}
                                </strong>
                            </td>

                            <!-- Rekening -->
                            <td class="align-middle">
                                <span class="px-2 py-0.5 rounded-md bg-slate-100 font-bold text-slate-800 text-[10px] uppercase border border-slate-200">
                                    {{ $p->nama_bank }}
                                </span>
                                <strong class="font-mono text-xs text-slate-900 block mt-1">{{ $p->nomor_rekening }}</strong>
                                <span class="text-[10px] text-slate-500">a.n {{ $p->atas_nama }}</span>
                            </td>

                            <!-- Waktu -->
                            <td class="align-middle text-slate-500 text-[11px]">
                                <span>{{ $p->created_at->translatedFormat('d M Y, H:i') }}</span>
                                @if($p->processed_at)
                                    <span class="text-[10px] text-slate-400 block">Diproses: {{ $p->processed_at->translatedFormat('d M, H:i') }}</span>
                                @endif
                            </td>

                            <!-- Status -->
                            <td class="align-middle text-center">
                                @if($p->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-circle-check mr-1 text-[8px]"></i> Disetujui
                                    </span>
                                @elseif($p->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fas fa-times-circle mr-1 text-[8px]"></i> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        <i class="fas fa-clock mr-1 text-[8px]"></i> Pending
                                    </span>
                                @endif
                            </td>

                            <!-- Aksi Verifikasi -->
                            <td class="align-middle text-right whitespace-nowrap">
                                @if($p->status === 'pending')
                                    <div class="flex items-center justify-end gap-1.5">
                                        <button 
                                            type="button" 
                                            onclick="openApproveModal({{ $p->id }}, '{{ $p->umkm->nama_toko }}', '{{ number_format($p->jumlah, 0, ',', '.') }}', '{{ $p->nama_bank }}', '{{ $p->nomor_rekening }}', '{{ $p->atas_nama }}')"
                                            class="px-2.5 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-600 text-emerald-700 hover:text-white font-bold text-xs transition border border-emerald-200 shadow-xs"
                                        >
                                            <i class="fas fa-check text-[10px] mr-1"></i> Transfer
                                        </button>
                                        <button 
                                            type="button" 
                                            onclick="openRejectModal({{ $p->id }}, '{{ $p->umkm->nama_toko }}')"
                                            class="px-2.5 py-1.5 rounded-xl bg-rose-50 hover:bg-rose-600 text-rose-700 hover:text-white font-bold text-xs transition border border-rose-200 shadow-xs"
                                        >
                                            <i class="fas fa-times text-[10px] mr-1"></i> Tolak
                                        </button>
                                    </div>
                                @elseif($p->status === 'approved' && $p->bukti_transfer)
                                    <a 
                                        href="{{ asset('storage/' . $p->bukti_transfer) }}" 
                                        target="_blank" 
                                        class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-50 text-slate-700 hover:text-brand-600 font-bold text-xs transition inline-flex items-center gap-1 border border-slate-200"
                                    >
                                        <i class="fas fa-receipt text-xs"></i>
                                        <span>Bukti Transfer</span>
                                    </a>
                                @else
                                    <span class="text-[10px] text-slate-400" title="{{ $p->catatan_admin }}">
                                        {{ Str::limit($p->catatan_admin, 20) }}
                                    </span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400 text-xs">
                                Tidak ada data permohonan penarikan dana sesuai filter.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($penarikans->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $penarikans->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ========================================================================= -->
<!-- 💳 MODAL APPROVE & UPLOAD BUKTI TRANSFER                                   -->
<!-- ========================================================================= -->
<div id="approveModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-md w-full p-6 space-y-4 animate-scale-in">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-emerald-50 text-emerald-700 border border-emerald-200">
                    Konfirmasi Transfer Dana
                </span>
                <h3 class="text-base font-extrabold text-slate-900 mt-1" id="approveTitle">
                    Persetujuan Pencairan Saldo
                </h3>
            </div>
            <button onclick="closeApproveModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <div id="rekeningDetailBox" class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200 text-xs space-y-1">
            <!-- Dinamis JS -->
        </div>

        <form id="formApprove" method="POST" action="" enctype="multipart/form-data" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">Unggah Struk / Bukti Transfer Bank <span class="text-rose-500">*</span></label>
                <input 
                    type="file" 
                    name="bukti_transfer" 
                    required 
                    accept="image/*,application/pdf"
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-800 focus:outline-none focus:border-brand-500"
                >
                <span class="text-[10px] text-slate-400 mt-0.5 block">Format: JPG, PNG, PDF maks 3MB</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Catatan Admin (Opsional)</label>
                <input 
                    type="text" 
                    name="catatan_admin" 
                    placeholder="Misal: Berhasil ditransfer via Internet Banking BCA..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-900 focus:outline-none focus:border-brand-500"
                >
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeApproveModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-check"></i>
                    <span>Setujui & Selesaikan</span>
                </button>
            </div>
        </form>

    </div>
</div>

<!-- ========================================================================= -->
<!-- ❌ MODAL REJECT PENARIKAN                                                  -->
<!-- ========================================================================= -->
<div id="rejectModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-md w-full p-6 space-y-4 animate-scale-in">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-rose-50 text-rose-700 border border-rose-200">
                    Tolak Permohonan Payout
                </span>
                <h3 class="text-base font-extrabold text-slate-900 mt-1" id="rejectTitle">
                    Tolak Penarikan Saldo
                </h3>
            </div>
            <button onclick="closeRejectModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <form id="formReject" method="POST" action="" class="space-y-4 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">Alasan Penolakan <span class="text-rose-500">*</span></label>
                <textarea 
                    name="catatan_admin" 
                    rows="3" 
                    required 
                    placeholder="Contoh: Nomor rekening tidak valid / nama pemilik berbeda dengan identitas toko..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-medium text-slate-900 focus:outline-none focus:border-brand-500"
                ></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeRejectModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-rose-600 hover:bg-rose-700 text-white font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-ban"></i>
                    <span>Tolak Permohonan</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
function openApproveModal(id, namaToko, nominal, bank, rek, an) {
    document.getElementById('approveTitle').innerText = `Transfer Rp ${nominal}`;
    document.getElementById('formApprove').action = `/admin/penarikan/${id}/approve`;
    document.getElementById('rekeningDetailBox').innerHTML = `
        <p class="font-bold text-slate-900">${namaToko}</p>
        <p class="text-slate-600">Bank: <strong>${bank}</strong> • Rek: <strong class="font-mono text-slate-900">${rek}</strong></p>
        <p class="text-slate-600">Atas Nama: <strong>${an}</strong></p>
    `;
    document.getElementById('approveModal').classList.remove('hidden');
}

function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
}

function openRejectModal(id, namaToko) {
    document.getElementById('rejectTitle').innerText = `Tolak Permohonan ${namaToko}`;
    document.getElementById('formReject').action = `/admin/penarikan/${id}/reject`;
    document.getElementById('rejectModal').classList.remove('hidden');
}

function closeRejectModal() {
    document.getElementById('rejectModal').classList.add('hidden');
}
</script>
@endpush

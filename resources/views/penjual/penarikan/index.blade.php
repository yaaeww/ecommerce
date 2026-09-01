@extends('layouts.app')

@section('page_title', 'Pencairan Saldo & Bagi Hasil Kebun')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Pencairan Saldo Toko</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Tarik hak bersih bagi hasil penjualan mangga ({{ $tokoPersen }}%) langsung ke rekening bank Anda.
            </p>
        </div>
        <div>
            <button 
                type="button" 
                onclick="openTarikModal()" 
                {{ $saldoTersedia < 50000 ? 'disabled' : '' }}
                class="px-5 py-2.5 rounded-2xl bg-brand-600 hover:bg-brand-700 disabled:bg-slate-300 disabled:cursor-not-allowed text-white font-bold text-xs transition shadow-sm flex items-center gap-2"
            >
                <i class="fas fa-money-bill-transfer"></i>
                <span>Tarik Saldo Tersedia</span>
            </button>
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
    @if(isset($errors) && $errors->any())
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-xs text-rose-800 space-y-1 shadow-sm">
            <div class="flex items-center gap-2 font-bold">
                <i class="fas fa-triangle-exclamation text-rose-600"></i>
                <span>Periksa kembali input permohonan Anda:</span>
            </div>
            <ul class="list-disc list-inside pl-4 text-[11px]">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- 5 Saldo Cards (Distributed Escrow Overview) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Saldo Siap Ditarik (Settled) -->
        <div class="card p-5 bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-500/20 rounded-3xl">
            <span class="text-[11px] font-bold uppercase tracking-wider block opacity-90 mb-1">Saldo Siap Ditarik (Settled)</span>
            <p class="text-2xl sm:text-3xl font-extrabold font-display">
                Rp{{ number_format($saldoTersedia, 0, ',', '.') }}
            </p>
            <span class="text-[10px] opacity-80 mt-1 block">Pesanan selesai • Min. tarik Rp 50.000</span>
        </div>

        <!-- Saldo Tertahan di Escrow (In-Transit) -->
        <div class="card p-5 bg-white border border-amber-200/80 shadow-sm rounded-3xl border-l-4 border-l-amber-500">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-amber-600 uppercase tracking-wider block mb-1">Saldo Tertahan (Escrow)</span>
                <i class="fas fa-lock text-amber-400 text-xs"></i>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 font-display">
                Rp{{ number_format($hakBersihEscrow ?? 0, 0, ',', '.') }}
            </p>
            <span class="text-[10px] text-slate-400 mt-1 block">Pesanan sedang dipacking/dikirim kurir</span>
        </div>

        <!-- Sedang Diproses Admin -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-indigo-500">
            <span class="text-[11px] font-bold text-indigo-600 uppercase tracking-wider block mb-1">Sedang Antre Transfer</span>
            <p class="text-2xl font-extrabold text-slate-900 font-display">
                Rp{{ number_format($totalDitarikPending, 0, ',', '.') }}
            </p>
            <span class="text-[10px] text-slate-400 mt-1 block">Menunggu verifikasi admin</span>
        </div>

        <!-- Berhasil Dicairkan -->
        <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-brand-600">
            <span class="text-[11px] font-bold text-brand-600 uppercase tracking-wider block mb-1">Total Sudah Dicairkan</span>
            <p class="text-2xl font-extrabold text-brand-600 font-display">
                Rp{{ number_format($totalDitarikApproved, 0, ',', '.') }}
            </p>
            <span class="text-[10px] text-slate-400 mt-1 block">Telah masuk ke rekening bank</span>
        </div>

    </div>

    <!-- Table: Riwayat Penarikan Dana Toko -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 font-display">Riwayat Permohonan Pencairan Dana</h3>
            <span class="text-xs font-bold text-slate-400">{{ $riwayatPenarikan->total() }} Transaksi</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th>Waktu Pengajuan</th>
                        <th>Nominal Penarikan</th>
                        <th>Rekening Penerima</th>
                        <th class="text-center">Status</th>
                        <th>Catatan / Keterangan</th>
                        <th class="text-right">Bukti Transfer</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($riwayatPenarikan as $rw)
                        <tr class="hover:bg-slate-50/70 transition">
                            <td class="align-middle text-slate-600">
                                <strong class="font-bold text-slate-900 block">{{ $rw->created_at->translatedFormat('d M Y') }}</strong>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $rw->created_at->format('H:i') }} WIB</span>
                            </td>

                            <td class="align-middle">
                                <strong class="font-extrabold text-sm text-slate-900 font-display">
                                    Rp{{ number_format($rw->jumlah, 0, ',', '.') }}
                                </strong>
                            </td>

                            <td class="align-middle">
                                <span class="px-2 py-0.5 rounded bg-slate-100 font-bold text-slate-700 text-[10px] uppercase border border-slate-200">
                                    {{ $rw->nama_bank }}
                                </span>
                                <span class="font-mono font-bold text-slate-800 block mt-0.5">{{ $rw->nomor_rekening }}</span>
                                <span class="text-[10px] text-slate-400">a.n {{ $rw->atas_nama }}</span>
                            </td>

                            <td class="align-middle text-center">
                                @if($rw->status === 'approved')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">
                                        <i class="fas fa-circle-check mr-1 text-[8px]"></i> Berhasil Ditransfer
                                    </span>
                                @elseif($rw->status === 'rejected')
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                        <i class="fas fa-times-circle mr-1 text-[8px]"></i> Ditolak
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200 animate-pulse">
                                        <i class="fas fa-clock mr-1 text-[8px]"></i> Menunggu Verifikasi
                                    </span>
                                @endif
                            </td>

                            <td class="align-middle max-w-xs text-slate-600 text-[11px]">
                                {{ $rw->catatan_admin ?: '-' }}
                            </td>

                            <td class="align-middle text-right">
                                @if($rw->bukti_transfer)
                                    <a 
                                        href="{{ asset('storage/' . $rw->bukti_transfer) }}" 
                                        target="_blank" 
                                        class="px-3 py-1.5 rounded-xl bg-brand-50 hover:bg-brand-600 text-brand-600 hover:text-white font-bold text-xs transition inline-flex items-center gap-1 border border-brand-200"
                                    >
                                        <i class="fas fa-receipt text-xs"></i>
                                        <span>Lihat Struk</span>
                                    </a>
                                @else
                                    <span class="text-slate-300 italic text-[11px]">Belum ada struk</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-slate-400 text-xs">
                                Belum ada riwayat permohonan penarikan saldo.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($riwayatPenarikan->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $riwayatPenarikan->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ========================================================================= -->
<!-- 💳 MODAL PENGAJUAN PENARIKAN SALDO                                         -->
<!-- ========================================================================= -->
<div id="tarikModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-md w-full p-6 space-y-4 animate-scale-in">
        
        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
            <div>
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-brand-50 text-brand-700 border border-brand-200">
                    Formulir Payout
                </span>
                <h3 class="text-base font-extrabold text-slate-900 mt-1">
                    Tarik Saldo Toko Mitra
                </h3>
            </div>
            <button onclick="closeTarikModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <div class="p-3.5 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-800">
            <span class="text-[10px] font-bold block uppercase tracking-wider text-emerald-600">Saldo Maksimal yang Dapat Ditarik:</span>
            <p class="text-xl font-extrabold font-display text-emerald-700">Rp {{ number_format($saldoTersedia, 0, ',', '.') }}</p>
        </div>

        <form method="POST" action="{{ route('penjual.penarikan.store') }}" class="space-y-3.5 text-xs">
            @csrf

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nominal yang Ingin Ditarik (Rp) <span class="text-rose-500">*</span></label>
                <input 
                    type="number" 
                    name="jumlah" 
                    min="50000" 
                    max="{{ $saldoTersedia }}" 
                    required 
                    placeholder="Contoh: 150000"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-500 text-sm"
                >
                <span class="text-[10px] text-slate-400 mt-0.5 block">Minimal penarikan Rp 50.000</span>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Bank / E-Wallet <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="nama_bank" 
                    list="bankOptions" 
                    required 
                    placeholder="Pilih atau ketik (BCA, BRI, Mandiri, BNI, Dana, GoPay...)"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 focus:outline-none focus:border-brand-500"
                >
                <datalist id="bankOptions">
                    <option value="BCA">
                    <option value="BRI">
                    <option value="Mandiri">
                    <option value="BNI">
                    <option value="BSI">
                    <option value="Dana">
                    <option value="GoPay">
                    <option value="OVO">
                </datalist>
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nomor Rekening / No. E-Wallet <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="nomor_rekening" 
                    required 
                    placeholder="Contoh: 8831920192"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-mono font-bold text-slate-900 focus:outline-none focus:border-brand-500"
                >
            </div>

            <div>
                <label class="block font-bold text-slate-700 mb-1">Nama Pemilik Rekening (Sesuai Buku Tabungan) <span class="text-rose-500">*</span></label>
                <input 
                    type="text" 
                    name="atas_nama" 
                    required 
                    placeholder="Contoh: Budi Santoso"
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl font-bold text-slate-900 focus:outline-none focus:border-brand-500"
                >
            </div>

            <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeTarikModal()" class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-bold rounded-xl transition shadow-sm flex items-center gap-1.5">
                    <i class="fas fa-paper-plane"></i>
                    <span>Ajukan Penarikan</span>
                </button>
            </div>
        </form>

    </div>
</div>
@endsection

@push('scripts')
<script>
function openTarikModal() {
    document.getElementById('tarikModal').classList.remove('hidden');
}

function closeTarikModal() {
    document.getElementById('tarikModal').classList.add('hidden');
}
</script>
@endpush

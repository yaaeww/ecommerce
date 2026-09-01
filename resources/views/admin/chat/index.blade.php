@extends('layouts.app')

@section('page_title', 'Moderasi Chat & Anti-Fraud Hub')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Moderasi Chat & Anti-Fraud Hub</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Pantau percakapan pembeli & toko mitra, deteksi potensi transaksi di luar sistem, serta mediasi sengketa barang.
            </p>
        </div>
        <div class="flex items-center gap-2">
            @if($totalFlaggedConversations > 0)
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold animate-pulse">
                    <i class="fas fa-shield-halved text-rose-500"></i>
                    {{ $totalFlaggedConversations }} Percakapan Terindikasi Risiko
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

    <!-- 3 Metrics KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Room Diskusi</span>
                <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center text-base">
                    <i class="fas fa-comments"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-slate-900 font-display tracking-tight">{{ $totalRoom }}</p>
            <p class="text-xs text-slate-400 mt-1.5">Pasangan pembeli & toko yang terhubung</p>
        </div>

        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-brand-600">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-brand-600 uppercase tracking-wider">Total Pesan Terkirim</span>
                <div class="w-10 h-10 rounded-xl bg-brand-50 text-brand-600 flex items-center justify-center text-base">
                    <i class="fas fa-paper-plane"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-brand-600 font-display tracking-tight">{{ number_format($totalAllChat, 0, ',', '.') }}</p>
            <p class="text-xs text-slate-400 mt-1.5">Akumulasi interaksi live di platform</p>
        </div>

        <div class="card p-6 bg-white border border-slate-200/80 shadow-sm rounded-3xl border-l-4 border-l-rose-500">
            <div class="flex items-center justify-between mb-3">
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Terdeteksi Sensitif (Anti-Fraud)</span>
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-base">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
            </div>
            <p class="text-3xl font-extrabold text-rose-600 font-display tracking-tight">{{ $totalFlaggedConversations }}</p>
            <p class="text-xs text-slate-400 mt-1.5">Memuat kata kunci nomor WA / transfer luar</p>
        </div>
    </div>

    <!-- Filter & Search Card -->
    <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-2xl">
        <form method="GET" action="{{ route('admin.chat.index') }}" class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex-1 w-full relative">
                <i class="fas fa-search absolute left-4 top-3.5 text-slate-400 text-xs"></i>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Cari pembeli, nama toko, atau potongan isi pesan..." 
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500"
                >
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <label class="flex items-center gap-2 text-xs font-bold text-slate-700 cursor-pointer select-none">
                    <input 
                        type="checkbox" 
                        name="risk_only" 
                        value="1" 
                        {{ $filterRisk ? 'checked' : '' }} 
                        onchange="this.form.submit()" 
                        class="rounded text-rose-600 focus:ring-rose-500"
                    >
                    <span>Hanya yang Berisiko Anti-Fraud</span>
                </label>

                <button type="submit" class="px-4 py-2.5 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Filter
                </button>

                <a href="{{ route('admin.chat.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition" title="Reset">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- Table: Room Percakapan Aktif -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 font-display">Daftar Kanal Diskusi & Monitoring</h3>
            <span class="text-xs font-bold text-slate-400">{{ count($conversations) }} Sesi Percakapan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-12 text-center">Status</th>
                        <th>Toko UMKM (Penjual)</th>
                        <th>Akun Pembeli</th>
                        <th>Pesan Terakhir</th>
                        <th class="text-center w-28">Volume Chat</th>
                        <th class="text-right w-36">Aksi Inspeksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($conversations as $conv)
                        <tr class="hover:bg-slate-50/70 transition">
                            <!-- Status Fraud -->
                            <td class="text-center align-middle">
                                @if($conv->has_risk)
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-rose-100 text-rose-700" title="Terdeteksi {{ $conv->flagged_count }} pesan sensitif">
                                        <i class="fas fa-triangle-exclamation text-xs"></i>
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 text-emerald-700" title="Aman (Clean)">
                                        <i class="fas fa-circle-check text-xs"></i>
                                    </span>
                                @endif
                            </td>

                            <!-- Toko / Penjual -->
                            <td class="align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-700 flex items-center justify-center text-xs font-extrabold shrink-0 border border-amber-200">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <strong class="font-extrabold text-slate-900 block truncate">{{ $conv->seller->umkm->nama_toko ?? $conv->seller->name }}</strong>
                                        <span class="text-[10px] text-slate-400 block">{{ $conv->seller->name }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Pembeli -->
                            <td class="align-middle">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-indigo-50 text-indigo-700 flex items-center justify-center text-xs font-extrabold shrink-0 border border-indigo-200">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="min-w-0">
                                        <strong class="font-bold text-slate-900 block truncate">{{ $conv->buyer->name }}</strong>
                                        <span class="text-[10px] text-slate-400 block truncate">{{ $conv->buyer->email }}</span>
                                    </div>
                                </div>
                            </td>

                            <!-- Pesan Terakhir -->
                            <td class="align-middle max-w-xs">
                                <p class="text-slate-700 font-medium truncate">
                                    {{ $conv->last_message->message ?? '-' }}
                                </p>
                                <span class="text-[10px] text-slate-400 block mt-0.5">
                                    {{ $conv->last_message ? $conv->last_message->created_at->diffForHumans() : '' }}
                                </span>
                            </td>

                            <!-- Volume Chat -->
                            <td class="align-middle text-center">
                                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 font-extrabold text-[11px]">
                                    {{ $conv->total_messages }} Pesan
                                </span>
                            </td>

                            <!-- Aksi -->
                            <td class="align-middle text-right">
                                <button 
                                    type="button" 
                                    onclick="openChatTranscript({{ $conv->user_a->id }}, {{ $conv->user_b->id }})" 
                                    class="px-3.5 py-2 rounded-xl {{ $conv->has_risk ? 'bg-rose-50 text-rose-700 border border-rose-200 hover:bg-rose-600 hover:text-white' : 'bg-brand-50 text-brand-600 border border-brand-200 hover:bg-brand-600 hover:text-white' }} font-bold text-xs transition inline-flex items-center gap-1.5 shadow-xs"
                                >
                                    <i class="fas fa-eye text-xs"></i>
                                    <span>Inspeksi</span>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-12 text-slate-400 text-xs">
                                <i class="fas fa-comments text-3xl text-slate-300 mb-2 block"></i>
                                Tidak ada percakapan ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- ========================================================================= -->
<!-- 🔍 MODAL TRANSCRIPT CHAT INSPECTOR                                        -->
<!-- ========================================================================= -->
<div id="chatModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-xs flex items-center justify-center p-4">
    <div class="relative bg-white rounded-3xl shadow-2xl border border-slate-200 max-w-2xl w-full max-h-[85vh] flex flex-col overflow-hidden animate-scale-in">
        
        <!-- Modal Header -->
        <div class="p-5 sm:px-6 border-b border-slate-100 bg-slate-50/80 flex items-center justify-between shrink-0">
            <div>
                <span class="text-[10px] font-extrabold uppercase px-2 py-0.5 rounded bg-brand-50 text-brand-700 border border-brand-200">
                    Transkrip Sengketa & Moderasi
                </span>
                <h3 class="text-sm sm:text-base font-extrabold text-slate-900 mt-1" id="modalChatTitle">
                    Memuat Percakapan...
                </h3>
            </div>
            <button onclick="closeChatModal()" class="w-8 h-8 rounded-full bg-slate-200/80 hover:bg-slate-300 text-slate-600 flex items-center justify-center transition">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>

        <!-- Chat Bubble Container -->
        <div id="modalChatBody" class="p-6 space-y-4 overflow-y-auto flex-1 bg-slate-50/40 text-xs">
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2 text-brand-600 block"></i>
                Mengambil log pesan...
            </div>
        </div>

        <!-- Modal Footer -->
        <div class="p-4 border-t border-slate-100 bg-white flex items-center justify-between text-xs text-slate-400 shrink-0">
            <span class="flex items-center gap-1 text-[11px]">
                <i class="fas fa-shield-halved text-emerald-600"></i> Terenkripsi & Tercatat dalam Audit Trail
            </span>
            <button type="button" onclick="closeChatModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl transition">
                Tutup
            </button>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
function openChatTranscript(userA, userB) {
    const modal = document.getElementById('chatModal');
    const body = document.getElementById('modalChatBody');
    const title = document.getElementById('modalChatTitle');

    modal.classList.remove('hidden');
    body.innerHTML = `
        <div class="text-center py-12 text-slate-400">
            <i class="fas fa-spinner fa-spin text-2xl mb-2 text-brand-600 block"></i>
            Mengambil transkrip percakapan...
        </div>
    `;

    fetch(`/admin/chat-monitoring/${userA}/${userB}`)
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                title.innerText = `${data.user_a.name} (${data.user_a.toko || data.user_a.role}) ↔ ${data.user_b.name} (${data.user_b.toko || data.user_b.role})`;
                
                if(data.messages.length === 0) {
                    body.innerHTML = '<p class="text-center text-slate-400 py-8">Belum ada pesan dalam room ini.</p>';
                    return;
                }

                let html = '';
                data.messages.forEach(msg => {
                    const isSenderA = msg.sender_id == data.user_a.id;
                    const senderName = isSenderA ? data.user_a.name : data.user_b.name;
                    const senderRole = isSenderA ? (data.user_a.toko || data.user_a.role) : (data.user_b.toko || data.user_b.role);

                    html += `
                        <div class="flex flex-col ${isSenderA ? 'items-start' : 'items-end'}">
                            <span class="text-[10px] font-bold text-slate-400 mb-1 px-1">
                                ${senderName} (${senderRole}) • ${msg.created_at}
                            </span>
                            <div class="max-w-md p-3.5 rounded-2xl ${msg.is_flagged ? 'bg-rose-50 border-2 border-rose-300 text-rose-900 shadow-sm' : (isSenderA ? 'bg-white border border-slate-200 text-slate-800' : 'bg-brand-600 text-white')}">
                                ${msg.is_flagged ? `
                                    <div class="flex items-center gap-1 text-[10px] font-extrabold text-rose-700 mb-1">
                                        <i class="fas fa-triangle-exclamation"></i>
                                        Terdeteksi Kata Kunci: <span class="bg-rose-200 px-1 rounded">${msg.matched_keywords.join(', ')}</span>
                                    </div>
                                ` : ''}
                                <p class="text-xs leading-relaxed break-words">${msg.message}</p>
                            </div>
                        </div>
                    `;
                });

                body.innerHTML = html;
                body.scrollTop = body.scrollHeight;
            }
        })
        .catch(err => {
            body.innerHTML = '<p class="text-center text-rose-500 py-8">Gagal memuat transkrip percakapan.</p>';
        });
}

function closeChatModal() {
    document.getElementById('chatModal').classList.add('hidden');
}
</script>
@endpush

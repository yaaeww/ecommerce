@extends('layouts.app')

@section('page_title', 'Audit Trail & Log Aktivitas Sistem')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Audit Trail & Log Aktivitas</h2>
            <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                Rekam jejak komprehensif seluruh aksi kritis di sistem (perubahan komisi, payout, persetujuan toko, moderasi, dll).
            </p>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 border border-slate-200 text-slate-700 text-xs font-bold">
                <i class="fas fa-shield-halved text-emerald-600"></i>
                Total {{ number_format($totalLogs, 0, ',', '.') }} Peristiwa Tercatat
            </span>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-2xl">
        <form method="GET" action="{{ route('admin.activity-log.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 items-end">
            
            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Tindakan (Action)</label>
                <select name="action" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500">
                    <option value="semua">Semua Tindakan</option>
                    @foreach($availableActions as $act)
                        <option value="{{ $act }}" {{ $action === $act ? 'selected' : '' }}>{{ $act }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pengguna / Eksekutor</label>
                <select name="user_id" class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500">
                    <option value="">Semua Pengguna</option>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ $userId == $u->id ? 'selected' : '' }}>{{ $u->name }} ({{ ucfirst($u->role) }})</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pencarian Deskripsi / IP</label>
                <input 
                    type="text" 
                    name="search" 
                    value="{{ $search }}" 
                    placeholder="Kata kunci keterangan atau IP..." 
                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-xs font-bold text-slate-800 focus:outline-none focus:border-brand-500"
                >
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-4 bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs rounded-xl transition shadow-sm">
                    Terapkan Filter
                </button>
                <a href="{{ route('admin.activity-log.index') }}" class="p-2 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition" title="Reset">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Table: Activity Logs -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-3xl overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <h3 class="text-base font-extrabold text-slate-900 font-display">Daftar Kronologis Jejak Audit Sistem</h3>
            <span class="text-xs font-bold text-slate-400">{{ $logs->total() }} Log Ditemukan</span>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-left">
                <thead>
                    <tr>
                        <th class="w-36">Waktu Kejadian</th>
                        <th>Aktor / Pengguna</th>
                        <th>Tindakan (Action)</th>
                        <th>Keterangan / Detail Mutasi</th>
                        <th>Alamat IP & User Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($logs as $log)
                        <tr class="hover:bg-slate-50/70 transition">
                            
                            <!-- Waktu -->
                            <td class="align-top whitespace-nowrap text-slate-500">
                                <strong class="font-bold text-slate-800 block">{{ $log->created_at->translatedFormat('d M Y') }}</strong>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $log->created_at->format('H:i:s') }} ({{ $log->created_at->diffForHumans() }})</span>
                            </td>

                            <!-- Aktor -->
                            <td class="align-top whitespace-nowrap">
                                @if($log->user)
                                    <strong class="font-bold text-slate-900 block">{{ $log->user->name }}</strong>
                                    <span class="text-[10px] uppercase font-extrabold px-1.5 py-0.5 rounded {{ $log->user->role === 'admin' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : 'bg-slate-100 text-slate-600' }}">
                                        {{ $log->user->role }}
                                    </span>
                                @else
                                    <span class="text-slate-400 italic">Sistem Otomatis</span>
                                @endif
                            </td>

                            <!-- Action Badge -->
                            <td class="align-top whitespace-nowrap">
                                @php
                                    $badgeClass = match($log->action) {
                                        'UPDATE_KOMISI' => 'bg-purple-50 text-purple-700 border-purple-200',
                                        'APPROVE_PAYOUT' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'REJECT_PAYOUT' => 'bg-rose-50 text-rose-700 border-rose-200',
                                        'REQUEST_PAYOUT' => 'bg-amber-50 text-amber-700 border-amber-200',
                                        'MODERASI_ULASAN' => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'INSPECT_CHAT' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        'UPDATE_PENGIRIMAN' => 'bg-teal-50 text-teal-700 border-teal-200',
                                        default => 'bg-slate-100 text-slate-700 border-slate-200',
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded-md font-mono text-[10px] font-extrabold border {{ $badgeClass }}">
                                    {{ $log->action }}
                                </span>
                            </td>

                            <!-- Deskripsi -->
                            <td class="align-top max-w-md">
                                <p class="text-slate-800 leading-relaxed font-medium">
                                    {{ $log->description }}
                                </p>
                            </td>

                            <!-- IP & UA -->
                            <td class="align-top whitespace-nowrap text-[11px] text-slate-400">
                                <span class="font-mono text-slate-600 block">{{ $log->ip_address ?? '127.0.0.1' }}</span>
                                <span class="text-[10px] text-slate-400 block truncate max-w-[150px]" title="{{ $log->user_agent }}">
                                    {{ Str::limit($log->user_agent, 25) }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-10 text-slate-400 text-xs">
                                Belum ada catatan aktivitas log.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                {{ $logs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection

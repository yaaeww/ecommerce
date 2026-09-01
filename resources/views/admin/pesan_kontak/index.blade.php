@extends('layouts.app')

@section('page_title', 'Kotak Masuk Pesan & Layanan Kontak')

@section('content')
<div class="space-y-6 pb-12">
    
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-lg shadow-sm border border-brand-100">
                    <i class="fas fa-envelope-open-text"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-extrabold text-slate-900 tracking-tight font-display">Pesan Masuk & Layanan Kontak</h2>
                    <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                        Kelola seluruh formulir pertanyaan, permohonan kemitraan UMKM, pesanan partai besar, dan aduan publik.
                    </p>
                </div>
            </div>
        </div>
        
        @if($stats['belum_dibaca'] > 0)
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs font-bold shadow-sm">
                    <span class="flex h-2 w-2 relative">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                    </span>
                    <span>{{ $stats['belum_dibaca'] }} Pesan Baru Menunggu Tanggapan</span>
                </span>
            </div>
        @endif
    </div>

    <!-- Metric Cards Grid (4 Columns) -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Pesan -->
        <a href="{{ route('admin.pesan-kontak.index') }}" class="p-5 bg-white border border-slate-200/80 hover:border-slate-300 rounded-2xl shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Masuk</span>
                <div class="w-8 h-8 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                    <i class="fas fa-inbox"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-slate-900 font-display mt-2">{{ number_format($stats['total'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Semua riwayat pertanyaan</p>
        </a>

        <!-- Card 2: Belum Dibaca -->
        <a href="{{ route('admin.pesan-kontak.index', ['status' => 'belum_dibaca']) }}" class="p-5 bg-white border {{ $statusFilter === 'belum_dibaca' ? 'border-rose-300 ring-2 ring-rose-100' : 'border-slate-200/80' }} hover:border-rose-300 rounded-2xl shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Belum Dibaca</span>
                <div class="w-8 h-8 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                    <i class="fas fa-envelope"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-rose-600 font-display mt-2">{{ number_format($stats['belum_dibaca'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Perlu perhatian admin</p>
        </a>

        <!-- Card 3: Kemitraan & B2B -->
        <a href="{{ route('admin.pesan-kontak.index', ['kategori' => 'partai_besar']) }}" class="p-5 bg-white border border-slate-200/80 hover:border-amber-300 rounded-2xl shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-amber-600 uppercase tracking-wider">Mitra & B2B</span>
                <div class="w-8 h-8 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                    <i class="fas fa-handshake"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-amber-600 font-display mt-2">{{ number_format($stats['kerjasama'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Peluang kemitraan / partai besar</p>
        </a>

        <!-- Card 4: Dibalas / Selesai -->
        <a href="{{ route('admin.pesan-kontak.index', ['status' => 'dibalas']) }}" class="p-5 bg-white border {{ $statusFilter === 'dibalas' ? 'border-emerald-300 ring-2 ring-emerald-100' : 'border-slate-200/80' }} hover:border-emerald-300 rounded-2xl shadow-sm transition group">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold text-emerald-700 uppercase tracking-wider">Sudah Dibalas</span>
                <div class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-xs group-hover:scale-105 transition-transform">
                    <i class="fas fa-check-double"></i>
                </div>
            </div>
            <p class="text-2xl sm:text-3xl font-extrabold text-emerald-700 font-display mt-2">{{ number_format($stats['dibalas'], 0, ',', '.') }}</p>
            <p class="text-[11px] text-slate-400 mt-0.5">Tanggapan selesai</p>
        </a>
    </div>

    <!-- Filter & Search Bar -->
    <div class="card p-5 bg-white border border-slate-200/80 shadow-sm rounded-2xl">
        <form method="GET" action="{{ route('admin.pesan-kontak.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
            
            <!-- Search Query (5 cols) -->
            <div class="sm:col-span-5">
                <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1.5">Pencarian Kata Kunci</label>
                <div class="relative">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Cari nama, email, subjek, no. HP, atau isi pesan..." 
                        class="w-full bg-slate-50 border border-slate-200 focus:border-brand-500 rounded-xl text-xs font-semibold text-slate-800 pl-9 pr-3 py-2.5 outline-none transition"
                    >
                </div>
            </div>

            <!-- Filter Status (3 cols) -->
            <div class="sm:col-span-3">
                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">Status Pesan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-inbox text-xs"></i>
                    </div>
                    <select name="status" class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer">
                        <option value="all" {{ $statusFilter === 'all' ? 'selected' : '' }}>Semua Status Pesan</option>
                        <option value="belum_dibaca" {{ $statusFilter === 'belum_dibaca' ? 'selected' : '' }}>🔴 Belum Dibaca</option>
                        <option value="dibaca" {{ $statusFilter === 'dibaca' ? 'selected' : '' }}>🟡 Sudah Dibaca</option>
                        <option value="dibalas" {{ $statusFilter === 'dibalas' ? 'selected' : '' }}>🟢 Sudah Dibalas</option>
                        <option value="diarsipkan" {{ $statusFilter === 'diarsipkan' ? 'selected' : '' }}>⚪ Diarsipkan</option>
                    </select>
                </div>
            </div>

            <!-- Filter Kategori (3 cols) -->
            <div class="sm:col-span-3">
                <label class="block text-[11px] font-extrabold text-slate-700 uppercase tracking-wider mb-2">Kategori Pesan</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-emerald-600">
                        <i class="fas fa-tags text-xs"></i>
                    </div>
                    <select name="kategori" class="w-full pl-10 pr-9 py-2.5 bg-slate-50 hover:bg-white focus:bg-white border border-slate-200 rounded-2xl text-xs font-extrabold text-slate-800 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 focus:outline-hidden transition shadow-xs cursor-pointer">
                        <option value="all" {{ $kategoriFilter === 'all' ? 'selected' : '' }}>Semua Kategori</option>
                        <option value="pertanyaan_umum" {{ $kategoriFilter === 'pertanyaan_umum' ? 'selected' : '' }}>💬 Pertanyaan Umum</option>
                        <option value="kerjasama_umkm" {{ $kategoriFilter === 'kerjasama_umkm' ? 'selected' : '' }}>🤝 Kerjasama Mitra UMKM</option>
                        <option value="partai_besar" {{ $kategoriFilter === 'partai_besar' ? 'selected' : '' }}>📦 Partai Besar / B2B</option>
                        <option value="kendala_transaksi" {{ $kategoriFilter === 'kendala_transaksi' ? 'selected' : '' }}>⚠️ Kendala Transaksi</option>
                        <option value="masukan" {{ $kategoriFilter === 'masukan' ? 'selected' : '' }}>💡 Masukan & Saran</option>
                    </select>
                </div>
            </div>

            <!-- Action Buttons (1 col) -->
            <div class="sm:col-span-1 flex items-center gap-1.5">
                <button type="submit" class="w-full py-2.5 px-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs rounded-2xl transition flex items-center justify-center shadow-sm cursor-pointer" title="Terapkan Filter">
                    <i class="fas fa-filter text-xs"></i>
                </button>
                <a href="{{ route('admin.pesan-kontak.index') }}" class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-2xl transition flex items-center justify-center" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>

        </form>
    </div>

    <!-- Bulk Action Toolbar (When Selected) -->
    <div id="bulkActionToolbar" class="hidden p-3.5 bg-slate-900 text-white rounded-2xl flex flex-wrap items-center justify-between gap-3 shadow-lg transition-all">
        <div class="flex items-center gap-3">
            <span id="bulkSelectedCountText" class="text-xs font-bold bg-brand-600 px-2.5 py-1 rounded-lg">0 pesan terpilih</span>
            <span class="text-xs text-slate-300">Pilih tindakan massal:</span>
        </div>
        <div class="flex items-center gap-2">
            <button type="button" onclick="submitBulkAction('mark_read')" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition">
                <i class="fas fa-envelope-open mr-1"></i> Tandai Dibaca
            </button>
            <button type="button" onclick="submitBulkAction('archive')" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-bold rounded-xl transition">
                <i class="fas fa-box-archive mr-1"></i> Arsipkan
            </button>
            <button type="button" onclick="submitBulkAction('delete')" class="px-3 py-1.5 bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold rounded-xl transition">
                <i class="fas fa-trash mr-1"></i> Hapus
            </button>
        </div>
    </div>

    <!-- Inbox List Table Card -->
    <div class="card bg-white border border-slate-200/80 shadow-sm rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 border-b border-slate-200 text-[10px] font-extrabold uppercase tracking-wider text-slate-500">
                        <th class="py-3.5 px-4 w-10 text-center">
                            <input 
                                type="checkbox" 
                                id="selectAllCheckbox"
                                onchange="toggleSelectAll(this)"
                                class="rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-4 w-4 cursor-pointer"
                            >
                        </th>
                        <th class="py-3.5 px-4">Pengirim & Kontak</th>
                        <th class="py-3.5 px-4">Kategori & Subjek</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4">Waktu Masuk</th>
                        <th class="py-3.5 px-4 text-center w-28">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs">
                    @forelse($pesanList as $item)
                        <tr class="hover:bg-slate-50/60 transition {{ $item->status === 'belum_dibaca' ? 'bg-amber-50/20 font-medium' : '' }}" id="row-message-{{ $item->id }}">
                            <!-- Checkbox -->
                            <td class="py-3.5 px-4 text-center">
                                <input 
                                    type="checkbox" 
                                    value="{{ $item->id }}" 
                                    name="pesan_checkbox"
                                    onchange="handleRowCheckboxChange()"
                                    class="row-checkbox rounded border-slate-300 text-brand-600 focus:ring-brand-500 h-4 w-4 cursor-pointer"
                                >
                            </td>

                            <!-- Pengirim -->
                            <td class="py-3.5 px-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center font-bold text-xs flex-shrink-0 border border-slate-200">
                                        {{ strtoupper(substr($item->nama, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="flex items-center gap-1.5">
                                            <p class="font-bold text-slate-900 truncate {{ $item->status === 'belum_dibaca' ? 'font-extrabold' : '' }}">{{ $item->nama }}</p>
                                            @if($item->user_id)
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-emerald-50 text-emerald-700 border border-emerald-200" title="Akun Terdaftar ({{ $item->user->role ?? 'User' }})">
                                                    {{ ucfirst($item->user->role ?? 'User') }}
                                                </span>
                                            @else
                                                <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-slate-100 text-slate-500" title="Pengunjung Tamu">
                                                    Tamu
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-[11px] text-slate-500 truncate">{{ $item->email }}</p>
                                        @if($item->no_telepon)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $item->no_telepon) }}" target="_blank" class="text-[10px] text-emerald-600 hover:underline flex items-center gap-1 mt-0.5">
                                                <i class="fab fa-whatsapp text-[11px]"></i> {{ $item->no_telepon }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            <!-- Kategori & Subjek -->
                            <td class="py-3.5 px-4">
                                <div class="max-w-md">
                                    <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 border border-slate-200/80 mb-1">
                                        {{ $item->kategori_label }}
                                    </span>
                                    <p class="font-bold text-slate-900 truncate {{ $item->status === 'belum_dibaca' ? 'text-brand-900' : '' }}">{{ $item->subjek }}</p>
                                    <p class="text-[11px] text-slate-500 line-clamp-1 mt-0.5">{{ Str::limit($item->pesan, 80) }}</p>
                                </div>
                            </td>

                            <!-- Status Badge -->
                            <td class="py-3.5 px-4" id="status-badge-container-{{ $item->id }}">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $item->status_badge['bg'] }}">
                                    <span class="h-1.5 w-1.5 rounded-full {{ $item->status_badge['dot'] }}"></span>
                                    <span>{{ $item->status_badge['label'] }}</span>
                                </span>
                                @if($item->balasan_admin)
                                    <p class="text-[10px] text-emerald-600 font-semibold mt-1">
                                        <i class="fas fa-reply text-[9px] mr-0.5"></i> Dibalas admin
                                    </p>
                                @endif
                            </td>

                            <!-- Waktu Masuk -->
                            <td class="py-3.5 px-4 whitespace-nowrap text-slate-500 text-[11px]">
                                <p class="font-bold text-slate-800">{{ $item->created_at->translatedFormat('d M Y') }}</p>
                                <p class="text-[10px] text-slate-400">{{ $item->created_at->format('H:i') }} WIB ({{ $item->created_at->diffForHumans() }})</p>
                            </td>

                            <!-- Aksi -->
                            <td class="py-3.5 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <!-- View & Reply Button -->
                                    <button 
                                        type="button"
                                        onclick="openDetailModal({{ $item->id }})"
                                        class="p-2 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-600 transition" 
                                        title="Buka & Tanggapi Pesan"
                                    >
                                        <i class="fas fa-eye text-xs"></i>
                                    </button>

                                    <!-- Delete Button -->
                                    <form method="POST" action="{{ route('admin.pesan-kontak.destroy', $item->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pesan ini?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 transition" title="Hapus Pesan">
                                            <i class="fas fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center">
                                <div class="w-16 h-16 rounded-2xl bg-slate-50 text-slate-300 flex items-center justify-center mx-auto mb-3 text-2xl border border-slate-100">
                                    <i class="fas fa-inbox"></i>
                                </div>
                                <h4 class="text-sm font-bold text-slate-700">Tidak ada pesan kontak ditemukan</h4>
                                <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto">
                                    Belum ada pesan yang cocok dengan filter atau kata kunci pencarian saat ini.
                                </p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pesanList->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pesanList->links() }}
            </div>
        @endif
    </div>

    <!-- Hidden Bulk Action Form -->
    <form id="bulk-action-form" method="POST" action="{{ route('admin.pesan-kontak.bulk-action') }}" style="display: none;">
        @csrf
        <input type="hidden" name="action" id="bulk-action-input">
        <div id="bulk-action-hidden-inputs"></div>
    </form>

    <!-- 🔍 Detailed View & Reply Modal (Pure Vanilla JS) -->
    <div 
        id="detailPesanModal"
        class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-200"
        onclick="if(event.target === this) closeDetailModal()"
    >
        <div 
            class="bg-white rounded-3xl max-w-2xl w-full border border-slate-200 shadow-2xl overflow-hidden my-8 transform transition-all"
        >
            <!-- Modal Header -->
            <div class="p-6 bg-slate-50/80 border-b border-slate-200/80 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-brand-50 text-brand-600 flex items-center justify-center text-base border border-brand-100">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 font-display">Detail Pesan Masuk</h3>
                        <p class="text-xs text-slate-500" id="modalSubtitle">Memuat detail...</p>
                    </div>
                </div>
                <button 
                    type="button" 
                    onclick="closeDetailModal()" 
                    class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full hover:bg-slate-100 flex items-center justify-center transition"
                    aria-label="Tutup"
                >
                    <i class="fas fa-times text-sm"></i>
                </button>
            </div>

            <!-- Modal Body: Loading State -->
            <div id="modalLoadingState" class="py-16 text-center text-slate-400">
                <i class="fas fa-spinner fa-spin text-3xl text-brand-600 mb-3"></i>
                <p class="text-xs font-semibold text-slate-600">Memuat rincian pesan...</p>
            </div>

            <!-- Modal Body: Content -->
            <div id="modalLoadedContent" class="hidden p-6 sm:p-8 max-h-[70vh] overflow-y-auto space-y-6">
                
                <!-- Sender Profile Card -->
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-3.5">
                        <div class="w-12 h-12 rounded-2xl bg-brand-600 text-white flex items-center justify-center font-bold text-lg shadow-sm" id="modalSenderInitial">
                            U
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="text-sm font-bold text-slate-900" id="modalSenderName">-</h4>
                                <span id="modalSenderRoleBadge" class="text-[9px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700">
                                    Tamu
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-mono mt-0.5" id="modalSenderEmail">-</p>
                        </div>
                    </div>

                    <!-- Fast Action (WhatsApp & Email) -->
                    <div class="flex items-center gap-2">
                        <a 
                            id="modalWaBtn"
                            href="#" 
                            target="_blank"
                            class="px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold rounded-xl transition flex items-center gap-1.5 shadow-sm"
                        >
                            <i class="fab fa-whatsapp"></i> Chat WA
                        </a>
                        <a 
                            id="modalEmailBtn"
                            href="#" 
                            class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-bold rounded-xl transition flex items-center gap-1.5"
                        >
                            <i class="fas fa-envelope"></i> Email
                        </a>
                    </div>
                </div>

                <!-- Subject & Message Details -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <span id="modalCategoryBadge" class="text-[10px] font-bold px-2.5 py-1 rounded-lg bg-indigo-50 text-indigo-700 border border-indigo-100">
                            -
                        </span>
                        <div class="flex items-center gap-2">
                            <span class="text-[11px] text-slate-500 font-medium">Ubah Status:</span>
                            <select 
                                id="modalStatusSelect"
                                onchange="changeModalStatus(this.value)" 
                                class="px-2.5 py-1 bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-700 focus:outline-none focus:border-brand-500 cursor-pointer shadow-xs"
                            >
                                <option value="belum_dibaca">🔴 Belum Dibaca</option>
                                <option value="dibaca">🟡 Sudah Dibaca</option>
                                <option value="dibalas">🟢 Sudah Dibalas</option>
                                <option value="diarsipkan">⚪ Diarsipkan</option>
                            </select>
                        </div>
                    </div>

                    <h4 class="text-base font-extrabold text-slate-900 tracking-tight" id="modalSubject">-</h4>
                    
                    <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-200 text-xs text-slate-700 leading-relaxed whitespace-pre-line font-sans" id="modalMessageText">
                        -
                    </div>

                    <!-- Meta Information (IP & Device) -->
                    <div class="flex flex-wrap items-center gap-4 text-[10px] text-slate-400 pt-1 font-mono">
                        <span id="modalIpInfo"><i class="fas fa-network-wired mr-1"></i> IP: -</span>
                        <span id="modalDevice"><i class="fas fa-laptop mr-1"></i> Perangkat: Web Browser</span>
                    </div>
                </div>

                <hr class="border-slate-100">

                <!-- Admin Response Section -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 flex items-center gap-1.5">
                            <i class="fas fa-reply-all text-brand-600"></i> Tanggapan & Tindak Lanjut Admin
                        </h4>
                        <span id="modalRepliedStatusBadge" class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500">
                            Belum Dibalas
                        </span>
                    </div>

                    <!-- Previous Reply Notice (If Exists) -->
                    <div id="modalPreviousReplyCard" class="hidden p-3.5 bg-emerald-50/80 border border-emerald-200 rounded-2xl text-xs space-y-1">
                        <div class="flex items-center justify-between text-[11px] font-bold text-emerald-800">
                            <span id="modalAdminName"><i class="fas fa-user-shield mr-1"></i> Ditanggapi oleh Admin</span>
                            <span id="modalRepliedAt" class="text-[10px] text-emerald-600 font-normal">-</span>
                        </div>
                        <p class="text-slate-700 text-xs whitespace-pre-line pt-1" id="modalPreviousReplyText"></p>
                    </div>

                    <!-- Reply Input Box -->
                    <div class="space-y-2">
                        <label class="block text-[11px] font-semibold text-slate-600">
                            Catatan Balasan Internal / Hasil Komunikasi:
                        </label>
                        <textarea 
                            id="modalReplyTextarea"
                            rows="3" 
                            placeholder="Tuliskan ringkasan hasil tindak lanjut pesan ini (misal: penawaran harga 2 ton telah dikirim ke WA/Email pelanggan)..."
                            class="w-full p-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-normal text-slate-800 focus:outline-none focus:border-brand-500 transition resize-none"
                        ></textarea>
                        <p class="text-[10px] text-slate-400 italic">
                            *Menyimpan tanggapan akan otomatis mengubah status pesan menjadi "Sudah Dibalas".
                        </p>
                    </div>

                    <!-- Submit Response Button -->
                    <div class="flex items-center justify-end">
                        <button 
                            type="button" 
                            id="modalSubmitReplyBtn"
                            onclick="submitModalReply()" 
                            class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 active:scale-95 text-white font-bold text-xs rounded-xl shadow transition duration-200 flex items-center gap-2"
                        >
                            <i class="fas fa-floppy-disk text-xs"></i>
                            <span>Simpan & Tandai Selesai</span>
                        </button>
                    </div>
                </div>

            </div>

            <!-- Modal Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end">
                <button 
                    type="button"
                    onclick="closeDetailModal()" 
                    class="px-5 py-2.5 bg-slate-200 hover:bg-slate-300 active:bg-slate-400 text-slate-700 text-xs font-bold rounded-xl transition cursor-pointer"
                >
                    Tutup
                </button>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let currentActivePesanId = null;
    let currentActivePesanData = null;

    // 🌟 Open & Populate Message Modal
    async function openDetailModal(id) {
        currentActivePesanId = id;
        currentActivePesanData = null;

        const modal = document.getElementById('detailPesanModal');
        const loading = document.getElementById('modalLoadingState');
        const content = document.getElementById('modalLoadedContent');

        if (!modal) return;

        // Show modal and loading state
        modal.classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        if (loading) loading.classList.remove('hidden');
        if (content) content.classList.add('hidden');

        try {
            const res = await fetch(`{{ url('admin/pesan-kontak') }}/${id}`);
            const json = await res.json();

            if (json.success && json.data) {
                const data = json.data;
                currentActivePesanData = data;

                // Subtitle
                document.getElementById('modalSubtitle').innerText = `ID Pesan #${data.id} — Diterima pada ${data.created_at_formatted}`;

                // Sender Info
                const initial = data.nama ? data.nama.charAt(0).toUpperCase() : 'U';
                document.getElementById('modalSenderInitial').innerText = initial;
                document.getElementById('modalSenderName').innerText = data.nama || '-';
                document.getElementById('modalSenderEmail').innerText = data.email || '-';

                // Role badge
                const roleBadge = document.getElementById('modalSenderRoleBadge');
                if (roleBadge) {
                    if (data.is_registered_user) {
                        roleBadge.className = 'text-[9px] font-bold px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200';
                        roleBadge.innerText = `Akun ${data.user_role ? data.user_role.toUpperCase() : 'USER'}`;
                    } else {
                        roleBadge.className = 'text-[9px] font-bold px-2 py-0.5 rounded-full bg-slate-200 text-slate-700';
                        roleBadge.innerText = 'Tamu';
                    }
                }

                // WhatsApp & Email Buttons
                const waBtn = document.getElementById('modalWaBtn');
                if (waBtn) {
                    if (data.no_telepon) {
                        const cleanPhone = data.no_telepon.replace(/[^0-9]/g, '');
                        waBtn.href = `https://wa.me/${cleanPhone}?text=Halo%20${encodeURIComponent(data.nama)},%20menanggapi%20pertanyaan%20Anda%20di%20Juragan%20Pelem...`;
                        waBtn.classList.remove('hidden');
                    } else {
                        waBtn.classList.add('hidden');
                    }
                }

                const emailBtn = document.getElementById('modalEmailBtn');
                if (emailBtn) {
                    emailBtn.href = `mailto:${data.email}?subject=Re:%20${encodeURIComponent(data.subjek || 'Pertanyaan Juragan Pelem')}`;
                }

                // Category & Subject
                document.getElementById('modalCategoryBadge').innerText = data.kategori_label || 'Pertanyaan Umum';
                document.getElementById('modalSubject').innerText = data.subjek || '-';
                document.getElementById('modalMessageText').innerText = data.pesan || '-';

                // Meta IP
                document.getElementById('modalIpInfo').innerHTML = `<i class="fas fa-network-wired mr-1"></i> IP: ${data.ip_address || '127.0.0.1'}`;

                // Status select
                const statusSelect = document.getElementById('modalStatusSelect');
                if (statusSelect) statusSelect.value = data.status;

                // Reply info
                const prevCard = document.getElementById('modalPreviousReplyCard');
                const prevText = document.getElementById('modalPreviousReplyText');
                const adminName = document.getElementById('modalAdminName');
                const repliedAt = document.getElementById('modalRepliedAt');
                const replyBadge = document.getElementById('modalRepliedStatusBadge');
                const replyTextarea = document.getElementById('modalReplyTextarea');

                if (data.balasan_admin) {
                    if (prevCard) prevCard.classList.remove('hidden');
                    if (prevText) prevText.innerText = data.balasan_admin;
                    if (adminName) adminName.innerHTML = `<i class="fas fa-user-shield mr-1"></i> Ditanggapi oleh ${data.admin_nama || 'Admin'}`;
                    if (repliedAt) repliedAt.innerText = data.dibalas_pada ? `${data.dibalas_pada} WIB` : '';
                    if (replyBadge) {
                        replyBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
                        replyBadge.innerText = 'Sudah Dibalas';
                    }
                    if (replyTextarea) replyTextarea.value = data.balasan_admin;
                } else {
                    if (prevCard) prevCard.classList.add('hidden');
                    if (replyBadge) {
                        replyBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-slate-100 text-slate-500';
                        replyBadge.innerText = 'Belum Dibalas';
                    }
                    if (replyTextarea) replyTextarea.value = '';
                }

                // Show loaded content
                if (loading) loading.classList.add('hidden');
                if (content) content.classList.remove('hidden');

            } else {
                alert('Gagal memuat detail pesan.');
                closeDetailModal();
            }
        } catch (err) {
            console.error('Error fetching message details:', err);
            alert('Terjadi kesalahan saat memuat pesan.');
            closeDetailModal();
        }
    }

    // 🌟 Close Modal
    function closeDetailModal() {
        const modal = document.getElementById('detailPesanModal');
        if (modal) {
            modal.classList.add('hidden');
            document.body.classList.remove('overflow-hidden');
        }
    }

    // Close on Escape Key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeDetailModal();
        }
    });

    // 🌟 Update Status via Dropdown
    async function changeModalStatus(newStatus) {
        if (!currentActivePesanId) return;

        try {
            const res = await fetch(`{{ url('admin/pesan-kontak') }}/${currentActivePesanId}/status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: newStatus })
            });
            const json = await res.json();
            if (json.success) {
                // Update row in background table if exists
                const badgeContainer = document.getElementById(`status-badge-container-${currentActivePesanId}`);
                if (badgeContainer && json.status_badge) {
                    badgeContainer.innerHTML = `
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border ${json.status_badge.bg}">
                            <span class="h-1.5 w-1.5 rounded-full ${json.status_badge.dot}"></span>
                            <span>${json.status_badge.label}</span>
                        </span>
                    `;
                }
            }
        } catch (err) {
            alert('Gagal memperbarui status pesan.');
        }
    }

    // 🌟 Submit Response / Reply
    async function submitModalReply() {
        if (!currentActivePesanId) return;
        const textarea = document.getElementById('modalReplyTextarea');
        const replyText = textarea ? textarea.value.trim() : '';

        if (!replyText) {
            alert('Silakan tuliskan catatan tanggapan balasan terlebih dahulu.');
            return;
        }

        const btn = document.getElementById('modalSubmitReplyBtn');
        const originalHtml = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-xs"></i> Menyimpan...';
        }

        try {
            const res = await fetch(`{{ url('admin/pesan-kontak') }}/${currentActivePesanId}/reply`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ balasan_admin: replyText })
            });
            const json = await res.json();

            if (json.success) {
                alert('Tanggapan berhasil disimpan dan status pesan diubah menjadi Dibalas!');
                
                // Update modal view
                const prevCard = document.getElementById('modalPreviousReplyCard');
                const prevText = document.getElementById('modalPreviousReplyText');
                const adminName = document.getElementById('modalAdminName');
                const repliedAt = document.getElementById('modalRepliedAt');
                const replyBadge = document.getElementById('modalRepliedStatusBadge');
                const statusSelect = document.getElementById('modalStatusSelect');

                if (prevCard) prevCard.classList.remove('hidden');
                if (prevText) prevText.innerText = json.data.balasan_admin;
                if (adminName) adminName.innerHTML = `<i class="fas fa-user-shield mr-1"></i> Ditanggapi oleh ${json.data.admin_nama || 'Admin'}`;
                if (repliedAt) repliedAt.innerText = json.data.dibalas_pada ? `${json.data.dibalas_pada} WIB` : 'Baru saja';
                if (replyBadge) {
                    replyBadge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-800';
                    replyBadge.innerText = 'Sudah Dibalas';
                }
                if (statusSelect) statusSelect.value = 'dibalas';

                // Update row in table
                const badgeContainer = document.getElementById(`status-badge-container-${currentActivePesanId}`);
                if (badgeContainer && json.data.status_badge) {
                    badgeContainer.innerHTML = `
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] font-bold border ${json.data.status_badge.bg}">
                            <span class="h-1.5 w-1.5 rounded-full ${json.data.status_badge.dot}"></span>
                            <span>${json.data.status_badge.label}</span>
                        </span>
                        <p class="text-[10px] text-emerald-600 font-semibold mt-1">
                            <i class="fas fa-reply text-[9px] mr-0.5"></i> Dibalas admin
                        </p>
                    `;
                }

            } else {
                alert(json.message || 'Gagal menyimpan tanggapan.');
            }
        } catch (err) {
            console.error('Error submitting reply:', err);
            alert('Terjadi kesalahan saat menyimpan tanggapan.');
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = originalHtml;
            }
        }
    }

    // 🌟 Bulk Action Management
    function getSelectedIds() {
        const checkboxes = document.querySelectorAll('.row-checkbox:checked');
        return Array.from(checkboxes).map(cb => cb.value);
    }

    function toggleSelectAll(selectAll) {
        const checkboxes = document.querySelectorAll('.row-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = selectAll.checked;
        });
        handleRowCheckboxChange();
    }

    function handleRowCheckboxChange() {
        const selected = getSelectedIds();
        const toolbar = document.getElementById('bulkActionToolbar');
        const countText = document.getElementById('bulkSelectedCountText');
        const selectAll = document.getElementById('selectAllCheckbox');
        const totalRows = document.querySelectorAll('.row-checkbox').length;

        if (countText) countText.innerText = `${selected.length} pesan terpilih`;

        if (selected.length > 0) {
            if (toolbar) {
                toolbar.classList.remove('hidden');
                toolbar.classList.add('flex');
            }
        } else {
            if (toolbar) {
                toolbar.classList.add('hidden');
                toolbar.classList.remove('flex');
            }
        }

        if (selectAll) {
            selectAll.checked = totalRows > 0 && selected.length === totalRows;
        }
    }

    function submitBulkAction(action) {
        const selected = getSelectedIds();
        if (selected.length === 0) {
            alert('Pilih setidaknya satu pesan terlebih dahulu.');
            return;
        }

        let confirmMsg = `Jalankan aksi massal pada ${selected.length} pesan?`;
        if (action === 'delete') confirmMsg = `YAKIN ingin menghapus permanen ${selected.length} pesan terpilih? Tindakan ini tidak dapat dibatalkan!`;

        if (!confirm(confirmMsg)) return;

        const form = document.getElementById('bulk-action-form');
        const inputAction = document.getElementById('bulk-action-input');
        const container = document.getElementById('bulk-action-hidden-inputs');

        inputAction.value = action;
        container.innerHTML = '';

        selected.forEach(id => {
            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'ids[]';
            hidden.value = id;
            container.appendChild(hidden);
        });

        form.submit();
    }
</script>
@endpush

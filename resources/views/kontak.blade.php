@extends('layouts.public')

@section('title', 'Hubungi Kami — Layanan Bantuan & Kemitraan UMKM Juragan Pelem')
@section('meta_description', 'Punya pertanyaan tentang buah mangga, kendala pesanan, pasokan partai besar, atau ingin bermitra sebagai UMKM? Hubungi tim Juragan Pelem di Indramayu.')

@section('content')
<!-- Ambient Atmospheric Gradients -->
<div class="relative overflow-hidden">
    <div class="absolute -top-32 -right-32 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute top-1/3 -left-32 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <!-- 1. Hero Section -->
    <section class="pt-12 pb-10 sm:pt-16 sm:pb-12 bg-gradient-to-b from-white via-brand-cream/60 to-brand-cream border-b border-slate-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="max-w-3xl space-y-4">
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-brand-slate leading-tight font-display">
                    Kami Siap Mendengarkan <br>
                    <span class="text-emerald-700">Setiap Kebutuhan & Cerita Anda</span>
                </h1>

                <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                    Baik Anda ingin berkonsultasi mengenai pesanan buah mangga segar, pasokan partai besar / B2B, kendala pengiriman, atau ingin mendaftarkan produk UMKM lokal Anda ke platform, tim operasional kami di Indramayu siap membantu dengan sepenuh hati.
                </p>

                <!-- Response Latency Pill -->
                <div class="flex flex-wrap items-center gap-4 pt-2 text-xs font-semibold text-slate-500">
                    <div class="flex items-center gap-1.5 text-emerald-700 bg-white px-3 py-1.5 rounded-xl border border-emerald-100 shadow-sm">
                        <i class="fas fa-bolt text-amber-500"></i> Rata-rata respon < 15 Menit pada Jam Kerja
                    </div>
                    <div class="flex items-center gap-1.5 text-slate-600 bg-white px-3 py-1.5 rounded-xl border border-slate-200/80 shadow-sm">
                        <i class="fas fa-clock text-slate-400"></i> Operasional: Senin - Minggu (08:00 - 20:00 WIB)
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. Main Interactive Bento & Form Section -->
    <section class="py-12 sm:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            @if(session('success'))
                <div class="mb-8 p-4 sm:p-5 rounded-2xl bg-emerald-50 border border-emerald-200 flex items-start gap-4 shadow-sm animate-fade-in">
                    <div class="w-10 h-10 rounded-xl bg-emerald-600 text-white flex items-center justify-center flex-shrink-0 shadow-sm">
                        <i class="fas fa-check text-lg"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-emerald-900">Pesan Terkirim dengan Sukses!</h4>
                        <p class="text-xs sm:text-sm text-emerald-700 mt-0.5 leading-relaxed">{{ session('success') }}</p>
                    </div>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start" x-data="contactFormHandler()">
                
                <!-- Left Column: Interactive Contact Form (7 cols) -->
                <div class="lg:col-span-7 bg-white rounded-3xl p-6 sm:p-8 md:p-10 border border-slate-200/80 shadow-sm relative overflow-hidden">
                    <div class="flex items-center justify-between pb-6 border-b border-slate-100">
                        <div>
                            <h2 class="text-xl sm:text-2xl font-bold font-display text-brand-slate">Kirim Pesan Langsung</h2>
                            <p class="text-xs sm:text-sm text-slate-500 mt-1">Formulir terintegrasi langsung ke panel admin & tim penanganan kami.</p>
                        </div>
                        <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-lg border border-emerald-100">
                            <i class="fas fa-paper-plane"></i>
                        </div>
                    </div>

                    <form action="{{ route('kontak.store') }}" method="POST" class="mt-6 space-y-6" @submit="handleSubmit">
                        @csrf

                        <!-- Anti-Bot Honeypot -->
                        <div style="display: none !important;">
                            <input type="text" name="website_hp_check" tabindex="-1" autocomplete="off">
                        </div>

                        <!-- 1. Category Selector Pills -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2.5">
                                Keperluan / Kategori Pesan <span class="text-rose-500">*</span>
                            </label>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                                <template x-for="cat in categories" :key="cat.id">
                                    <button 
                                        type="button"
                                        @click="selectedCategory = cat.id"
                                        :class="selectedCategory === cat.id ? 'border-emerald-600 bg-emerald-50/70 text-emerald-900 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200 bg-slate-50/50 text-slate-700 hover:border-slate-300 hover:bg-slate-50'"
                                        class="flex items-center gap-3 p-3 rounded-xl border text-left transition duration-200"
                                    >
                                        <div 
                                            :class="selectedCategory === cat.id ? 'bg-emerald-600 text-white' : 'bg-white text-slate-400 border border-slate-200'"
                                            class="w-7 h-7 rounded-lg flex items-center justify-center text-xs flex-shrink-0 transition-colors"
                                        >
                                            <i :class="cat.icon"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-xs font-bold truncate" x-text="cat.title"></p>
                                            <p class="text-[10px] text-slate-500 truncate" x-text="cat.desc"></p>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            <input type="hidden" name="kategori" :value="selectedCategory">

                            <!-- Dynamic Helper Hint based on selected category -->
                            <div class="mt-3 p-3 rounded-xl bg-amber-50/80 border border-amber-200/80 flex items-start gap-2.5 text-xs text-amber-900">
                                <i class="fas fa-lightbulb text-amber-600 mt-0.5 text-xs flex-shrink-0"></i>
                                <p x-text="currentCategoryHint" class="leading-relaxed"></p>
                            </div>
                        </div>

                        <!-- 2. Sender Details (Name, Email, Phone) -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="nama" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                    Nama Lengkap <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-user absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input 
                                        type="text" 
                                        id="nama" 
                                        name="nama" 
                                        value="{{ old('nama', $user->name ?? '') }}"
                                        required 
                                        placeholder="Contoh: Budi Santoso"
                                        class="w-full bg-slate-50 focus:bg-white text-xs sm:text-sm text-slate-800 pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                    >
                                </div>
                                @error('nama')
                                    <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                    Alamat Email <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-envelope absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input 
                                        type="email" 
                                        id="email" 
                                        name="email" 
                                        value="{{ old('email', $user->email ?? '') }}"
                                        required 
                                        placeholder="nama@email.com"
                                        class="w-full bg-slate-50 focus:bg-white text-xs sm:text-sm text-slate-800 pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                    >
                                </div>
                                @error('email')
                                    <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 3. Phone & Subject -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="no_telepon" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                    No. WhatsApp / Telepon <span class="text-slate-400 font-normal">(Opsional)</span>
                                </label>
                                <div class="relative">
                                    <i class="fab fa-whatsapp absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input 
                                        type="text" 
                                        id="no_telepon" 
                                        name="no_telepon" 
                                        value="{{ old('no_telepon', $user->phone ?? '') }}"
                                        placeholder="0812xxxxxxxx"
                                        class="w-full bg-slate-50 focus:bg-white text-xs sm:text-sm text-slate-800 pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                    >
                                </div>
                            </div>

                            <div>
                                <label for="subjek" class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">
                                    Subjek Pesan <span class="text-rose-500">*</span>
                                </label>
                                <div class="relative">
                                    <i class="fas fa-heading absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                                    <input 
                                        type="text" 
                                        id="subjek" 
                                        name="subjek" 
                                        value="{{ old('subjek') }}"
                                        required 
                                        placeholder="Contoh: Tanya stok Mangga Gedong Gincu 50 Kg"
                                        class="w-full bg-slate-50 focus:bg-white text-xs sm:text-sm text-slate-800 pl-9 pr-3.5 py-2.5 rounded-xl border border-slate-200 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none transition"
                                    >
                                </div>
                                @error('subjek')
                                    <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- 4. Message Content -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="pesan" class="block text-xs font-bold uppercase tracking-wider text-slate-500">
                                    Isi Pesan / Pertanyaan <span class="text-rose-500">*</span>
                                </label>
                                <span class="text-[11px] text-slate-400" x-text="`${messageLength} / 5000 karakter`"></span>
                            </div>
                            <textarea 
                                id="pesan" 
                                name="pesan" 
                                rows="5" 
                                required
                                x-model="messageText"
                                placeholder="Tuliskan secara lengkap detail pertanyaan, jumlah kebutuhan, atau kendala yang ingin Anda konsultasikan..."
                                class="w-full bg-slate-50 focus:bg-white text-xs sm:text-sm text-slate-800 p-3.5 rounded-2xl border border-slate-200 focus:border-emerald-600 focus:ring-2 focus:ring-emerald-500/20 outline-none transition leading-relaxed"
                            >{{ old('pesan') }}</textarea>
                            @error('pesan')
                                <p class="text-rose-500 text-[11px] font-semibold mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- 5. Submit Button -->
                        <div class="pt-2">
                            <button 
                                type="submit" 
                                :disabled="isSubmitting"
                                class="w-full py-3.5 px-6 rounded-2xl bg-emerald-700 hover:bg-emerald-800 active:scale-[0.99] text-white font-bold text-sm shadow-md hover:shadow-lg transition duration-200 flex items-center justify-center gap-2 disabled:opacity-50"
                            >
                                <template x-if="!isSubmitting">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-paper-plane text-xs"></i>
                                        <span>Kirim Pesan Sekarang</span>
                                    </span>
                                </template>
                                <template x-if="isSubmitting">
                                    <span class="flex items-center gap-2">
                                        <i class="fas fa-spinner fa-spin text-sm"></i>
                                        <span>Mengirim pesan ke server...</span>
                                    </span>
                                </template>
                            </button>
                            <p class="text-[11px] text-center text-slate-400 mt-2.5">
                                <i class="fas fa-shield-alt text-emerald-600 mr-1"></i> Data dan privasi Anda terenkripsi aman sesuai standar privasi Juragan Pelem.
                            </p>
                        </div>
                    </form>
                </div>

                <!-- Right Column: Operational HQ, Direct Channels & Guarantee Bento (5 cols) -->
                <div class="lg:col-span-5 space-y-5">
                    
                    <!-- Card 1: Direct Support Channels -->
                    <div class="bento-card p-6 sm:p-7 bg-brand-slate text-white relative overflow-hidden">
                        <div class="absolute -bottom-10 -right-10 w-40 h-40 bg-emerald-600/20 rounded-full blur-2xl pointer-events-none"></div>

                        <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-400 block mb-1">Respon Cepat</span>
                        <h3 class="text-lg font-bold font-display text-white mb-4">Saluran Bantuan Langsung</h3>

                        <div class="space-y-4">
                            <!-- WhatsApp Shortcut -->
                            <a 
                                href="https://wa.me/6281234567890?text=Halo%20Admin%20Juragan%20Pelem,%20saya%20ingin%20bertanya%20seputar%20produk%20dan%20layanan" 
                                target="_blank"
                                class="flex items-center gap-3.5 p-3 rounded-2xl bg-white/10 hover:bg-emerald-600/30 border border-white/10 transition group"
                            >
                                <div class="w-10 h-10 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-lg flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center justify-between">
                                        <p class="text-xs font-bold text-white">WhatsApp Tim CS</p>
                                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-500/30 text-emerald-300">Online</span>
                                    </div>
                                    <p class="text-xs text-slate-300 font-mono mt-0.5">0812-3456-7890</p>
                                </div>
                                <i class="fas fa-arrow-up-right-from-square text-xs text-slate-400 group-hover:text-emerald-400 transition"></i>
                            </a>

                            <!-- Email Support -->
                            <a 
                                href="mailto:halo@juraganpelem.id" 
                                class="flex items-center gap-3.5 p-3 rounded-2xl bg-white/10 hover:bg-white/15 border border-white/10 transition group"
                            >
                                <div class="w-10 h-10 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base flex-shrink-0 group-hover:scale-105 transition-transform">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-bold text-white">Email Resmi Kemitraan</p>
                                    <p class="text-xs text-slate-300 font-mono mt-0.5">halo@juraganpelem.id</p>
                                </div>
                                <i class="fas fa-arrow-up-right-from-square text-xs text-slate-400 group-hover:text-amber-400 transition"></i>
                            </a>

                            <!-- Phone Hotline -->
                            <div class="flex items-center gap-3.5 p-3 rounded-2xl bg-white/5 border border-white/5">
                                <div class="w-10 h-10 rounded-xl bg-slate-700 text-slate-200 flex items-center justify-center text-base flex-shrink-0">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-white">Sentra Informasi Agrikultur</p>
                                    <p class="text-xs text-slate-300 font-mono mt-0.5">(0234) 567-8901</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2: Physical HQ & Logistic Warehouse Bento -->
                    <div class="bento-card p-6 sm:p-7 bg-white border border-slate-200/80 shadow-sm">
                        <div class="flex items-center gap-3 mb-4">
                            <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center text-base flex-shrink-0">
                                <i class="fas fa-location-dot"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-brand-slate">Sentra Logistik & Sortir Mangga</h3>
                                <p class="text-[11px] text-slate-500">Kabupaten Indramayu, Jawa Barat</p>
                            </div>
                        </div>

                        <p class="text-xs text-slate-600 leading-relaxed">
                            Jl. Raya Krasak No. 45, Kompleks Sentra Distribusi Agrobisnis, Kecamatan Jatibarang, Kabupaten Indramayu, Jawa Barat 45273.
                        </p>

                        <div class="mt-4 pt-4 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-slate-500">
                                <i class="fas fa-truck-ramp-box text-emerald-600 mr-1"></i> Titik Hub Ekspedisi Utama
                            </span>
                            <a 
                                href="https://maps.google.com/?q=Jatibarang+Indramayu" 
                                target="_blank" 
                                class="text-xs font-bold text-emerald-700 hover:text-emerald-800 flex items-center gap-1 hover:underline"
                            >
                                Buka Google Maps <i class="fas fa-chevron-right text-[10px]"></i>
                            </a>
                        </div>
                    </div>

                    <!-- Card 3: Trust & Protection Guarantee Badge -->
                    <div class="bento-card p-6 bg-gradient-to-br from-emerald-50/80 to-teal-50/50 border border-emerald-200/70">
                        <h4 class="text-xs font-extrabold uppercase tracking-wider text-emerald-900 mb-3 flex items-center gap-2">
                            <i class="fas fa-award text-amber-500 text-sm"></i> Komitmen Layanan Juragan Pelem
                        </h4>
                        <div class="space-y-2.5 text-xs text-slate-700">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 text-xs"></i>
                                <p><strong>Garansi 100% Ganti Baru:</strong> Jika buah tiba dalam kondisi busuk atau rusak di perjalanan.</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 text-xs"></i>
                                <p><strong>Transparansi Pembayaran:</strong> Dana aman dalam escrow sebelum pesanan Anda konfirmasi diterima.</p>
                            </div>
                            <div class="flex items-start gap-2">
                                <i class="fas fa-check-circle text-emerald-600 mt-0.5 text-xs"></i>
                                <p><strong>Pemberdayaan Nyata:</strong> Transaksi Anda langsung membantu kesejahteraan petani & UMKM lokal.</p>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>

    <!-- 3. FAQ Accordion Section (Fast Resolution) -->
    <section class="py-14 bg-white border-t border-slate-200/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-10">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-700 block mb-1">Solusi Cepat</span>
                <h2 class="text-2xl sm:text-3xl font-extrabold font-display text-brand-slate">Pertanyaan Sering Diajukan (FAQ)</h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Cari jawaban cepat atas hal-hal yang sering ditanyakan pelanggan dan mitra kami.</p>
            </div>

            <div class="space-y-3" x-data="{ openFaq: null }">
                
                <!-- FAQ 1 -->
                <div class="rounded-2xl border border-slate-200/80 overflow-hidden transition">
                    <button 
                        @click="openFaq = (openFaq === 1 ? null : 1)"
                        class="w-full p-4 sm:p-5 text-left bg-slate-50/50 hover:bg-slate-50 flex items-center justify-between gap-4 font-bold text-xs sm:text-sm text-slate-800"
                    >
                        <span>Bagaimana cara mengajukan klaim garansi jika buah mangga rusak saat pengiriman?</span>
                        <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="openFaq === 1 ? 'rotate-180 text-emerald-600' : ''"></i>
                    </button>
                    <div x-show="openFaq === 1" x-collapse style="display: none;" class="p-4 sm:p-5 pt-0 bg-slate-50/50 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                        Cukup foto bukti buah yang rusak bersama label resi paket dalam kurun waktu 1x24 jam setelah paket diterima, lalu buka menu <strong>Riwayat Pesanan &gt; Ajukan Komplain Garansi</strong> atau kirim pesan langsung melalui WhatsApp CS kami. Tim kami akan segera mengirimkan buah pengganti atau memproses pengembalian saldo escrow.
                    </div>
                </div>

                <!-- FAQ 2 -->
                <div class="rounded-2xl border border-slate-200/80 overflow-hidden transition">
                    <button 
                        @click="openFaq = (openFaq === 2 ? null : 2)"
                        class="w-full p-4 sm:p-5 text-left bg-slate-50/50 hover:bg-slate-50 flex items-center justify-between gap-4 font-bold text-xs sm:text-sm text-slate-800"
                    >
                        <span>Apakah Juragan Pelem melayani pemesanan partai besar (B2B/Catering)?</span>
                        <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="openFaq === 2 ? 'rotate-180 text-emerald-600' : ''"></i>
                    </button>
                    <div x-show="openFaq === 2" x-collapse style="display: none;" class="p-4 sm:p-5 pt-0 bg-slate-50/50 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                        Ya! Kami melayani pasokan grosir dan partai besar mulai dari 50 kg hingga skala tonase untuk kebutuhan supermarket, katering hotel, dan industri makanan dengan harga khusus langsung dari kebun. Pilih kategori <strong>Pesanan Partai Besar / B2B</strong> pada formulir di atas untuk penawaran resmi.
                    </div>
                </div>

                <!-- FAQ 3 -->
                <div class="rounded-2xl border border-slate-200/80 overflow-hidden transition">
                    <button 
                        @click="openFaq = (openFaq === 3 ? null : 3)"
                        class="w-full p-4 sm:p-5 text-left bg-slate-50/50 hover:bg-slate-50 flex items-center justify-between gap-4 font-bold text-xs sm:text-sm text-slate-800"
                    >
                        <span>Bagaimana cara UMKM kuliner atau pengrajin Indramayu bergabung?</span>
                        <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="openFaq === 3 ? 'rotate-180 text-emerald-600' : ''"></i>
                    </button>
                    <div x-show="openFaq === 3" x-collapse style="display: none;" class="p-4 sm:p-5 pt-0 bg-slate-50/50 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                        Anda dapat mendaftar langsung secara online dengan memilih peran <strong>Penjual (Mitra UMKM)</strong> pada menu Pendaftaran Akun, atau hubungi kami melalui formulir kontak dengan kategori <strong>Kerjasama Mitra UMKM</strong> untuk pendampingan tim inkubasi kami.
                    </div>
                </div>

                <!-- FAQ 4 -->
                <div class="rounded-2xl border border-slate-200/80 overflow-hidden transition">
                    <button 
                        @click="openFaq = (openFaq === 4 ? null : 4)"
                        class="w-full p-4 sm:p-5 text-left bg-slate-50/50 hover:bg-slate-50 flex items-center justify-between gap-4 font-bold text-xs sm:text-sm text-slate-800"
                    >
                        <span>Ekspedisi apa saja yang digunakan untuk menjaga kesegaran buah mangga?</span>
                        <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" :class="openFaq === 4 ? 'rotate-180 text-emerald-600' : ''"></i>
                    </button>
                    <div x-show="openFaq === 4" x-collapse style="display: none;" class="p-4 sm:p-5 pt-0 bg-slate-50/50 text-xs sm:text-sm text-slate-600 leading-relaxed border-t border-slate-100">
                        Kami bekerjasama dengan layanan kurir kilat express (Next Day & Regular) serta kemasan berongga khusus dengan jaring pelindung buah (*fruit net*) dan kardus *heavy-duty* anti-benturan sehingga buah tiba tetap segar dan harum.
                    </div>
                </div>

            </div>
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
    function contactFormHandler() {
        return {
            selectedCategory: "{{ old('kategori', 'pertanyaan_umum') }}",
            messageText: @json(old('pesan', '')),
            isSubmitting: false,
            categories: [
                { id: 'pertanyaan_umum', title: 'Pertanyaan Umum', desc: 'Produk & cara belanja', icon: 'fas fa-comment-dots' },
                { id: 'kerjasama_umkm', title: 'Kerjasama Mitra UMKM', desc: 'Buka toko & pasokan', icon: 'fas fa-handshake' },
                { id: 'partai_besar', title: 'Partai Besar / B2B', desc: 'Grosir & tonase mangga', icon: 'fas fa-boxes-stacked' },
                { id: 'kendala_transaksi', title: 'Kendala Transaksi', desc: 'Pembayaran & pesanan', icon: 'fas fa-circle-exclamation' },
                { id: 'masukan', title: 'Masukan & Saran', desc: 'Ide perbaikan platform', icon: 'fas fa-lightbulb' }
            ],
            get currentCategoryHint() {
                switch(this.selectedCategory) {
                    case 'kerjasama_umkm':
                        return 'Tips: Sertakan jenis produk olahan, nama usaha, dan kapasitas produksi Anda agar tim kurasi UMKM kami dapat menindaklanjuti lebih cepat.';
                    case 'partai_besar':
                        return 'Tips: Cantumkan estimasi tonase/kilogram, varietas mangga (Gedong Gincu/Harum Manis/Simanalagi), serta jadwal waktu pengiriman yang diinginkan.';
                    case 'kendala_transaksi':
                        return 'Tips: Cantumkan Nomor Invoice atau ID Pesanan Midtrans Anda untuk mempermudah pengecekan langsung pada sistem escrow.';
                    case 'masukan':
                        return 'Kami sangat menghargai setiap masukan membangun untuk memajukan ekosistem petani & UMKM lokal Indramayu.';
                    default:
                        return 'Tim kami akan membalas pesan Anda ke alamat email atau nomor WhatsApp yang Anda cantumkan di bawah ini.';
                }
            },
            get messageLength() {
                return (this.messageText || '').length;
            },
            handleSubmit() {
                this.isSubmitting = true;
            }
        }
    }
</script>
@endpush

<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Juragan Pelem — Marketplace Resmi Mangga & UMKM Indramayu')</title>
    <meta name="description" content="@yield('meta_description', 'Platform digital penghubung langsung petani mangga Indramayu dan UMKM lokal dengan konsumen seluruh Indonesia.')">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800;900&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- Icons & Scripts -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'brand-green': '#1B4D3E',
                        'brand-green-dark': '#12352A',
                        'brand-green-light': '#2D6A4F',
                        'brand-amber': '#d97706',
                        'indigo-600': '#1B4D3E',
                        'indigo-700': '#12352A',
                        'indigo-500': '#2D6A4F',
                        'amber-500': '#d97706',
                        'yellow-500': '#f59e0b',
                        'brand-cream': '#FBF9F5',
                        'brand-slate': '#0f172a',
                    },
                    fontFamily: {
                        'display': ['Outfit', 'sans-serif'],
                        'sans': ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        h1, h2, h3, h4, .font-display { font-family: 'Outfit', sans-serif; }
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
        }
        .bento-card {
            background: #ffffff;
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: 1.5rem;
            transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        }
        .bento-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 20px 35px -15px rgba(0, 0, 0, 0.07);
            border-color: rgba(45, 106, 79, 0.3);
        }
    </style>
    @stack('styles')
</head>
<body class="bg-brand-cream text-slate-800 antialiased selection:bg-indigo-600 selection:text-white flex flex-col min-h-screen pb-20 md:pb-0">

    <!-- Navigation Bar -->
    <header class="sticky top-0 z-40 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            <!-- Brand Logo -->
            <a href="{{ route('landing') }}" class="flex items-center gap-3 group shrink-0">
                <div class="w-10 h-10 sm:w-11 sm:h-11 rounded-xl bg-white shadow-sm border border-slate-100 p-1.5 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                </div>
                <div>
                    <span class="text-xl sm:text-2xl font-bold font-display text-indigo-600 tracking-tight">Juragan<span class="text-amber-500">Pelem</span></span>
                    <span class="block text-[9px] sm:text-[10px] tracking-wider uppercase font-semibold text-slate-400 -mt-1">Agro-Commerce Indramayu</span>
                </div>
            </a>

            <!-- Search Bar in Header (Desktop) -->
            <div class="hidden lg:block relative flex-1 max-w-md mx-6" id="desktop-search-container">
                <form action="{{ route('kategori') }}" method="GET" id="desktop-search-form" class="relative w-full">
                    <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
                    <input type="text" 
                        name="search" 
                        id="desktop-search-input"
                        value="{{ request('search') }}" 
                        autocomplete="off"
                        placeholder="Cari Mangga Gedong, Dodol, Sirup, Batik..." 
                        class="w-full bg-slate-100/80 hover:bg-white focus:bg-white text-sm text-slate-800 pl-10 pr-9 py-2.5 rounded-xl border border-transparent focus:border-indigo-600 focus:ring-2 focus:ring-indigo-600/20 outline-none transition">
                    
                    <button type="button" id="desktop-search-clear" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 text-xs">
                        <i class="fas fa-times-circle"></i>
                    </button>
                </form>

                <!-- Realtime Autocomplete Dropdown -->
                <div id="desktop-search-dropdown" class="hidden absolute left-0 right-0 top-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-200/80 p-3 z-50 overflow-hidden backdrop-blur-xl">
                    <div id="search-loading" class="hidden py-6 text-center text-slate-400 text-xs flex items-center justify-center gap-2">
                        <i class="fas fa-spinner fa-spin text-indigo-600 text-sm"></i>
                        <span>Mencari produk...</span>
                    </div>
                    <div id="search-content" class="space-y-3">
                        <!-- Dynamic Results will be injected here -->
                    </div>
                </div>
            </div>

            <!-- Nav Links (Desktop) -->
            <nav class="hidden md:flex items-center gap-7 text-sm font-semibold text-slate-600">
                <a href="{{ route('landing') }}" class="{{ request()->routeIs('landing') ? 'text-indigo-600' : 'hover:text-indigo-600' }} transition">Beranda</a>
                <a href="{{ route('kategori') }}" class="{{ request()->routeIs('kategori') ? 'text-indigo-600' : 'hover:text-indigo-600' }} transition">Kategori</a>
                <a href="{{ route('tentang') }}" class="{{ request()->routeIs('tentang') ? 'text-indigo-600' : 'hover:text-indigo-600' }} transition">Tentang Kami</a>
            </nav>

            <!-- Desktop User Auth Actions -->
            <div class="hidden md:flex items-center gap-3">
                @auth
                    @if(Auth::user()->role === 'pembeli')
                        <!-- Chat Internal -->
                        <a href="{{ route('pembeli.chat.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-indigo-600 hover:text-white transition group relative" aria-label="Chat Penjual">
                            <i class="fas fa-comments group-hover:scale-110 transition-transform"></i>
                            @if(isset($jumlahChatBaru) && $jumlahChatBaru > 0)
                                <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white shadow-sm ring-2 ring-white">
                                    {{ $jumlahChatBaru }}
                                </span>
                            @endif
                        </a>

                        <!-- Notification -->
                        <a href="{{ route('pembeli.pesanan.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-indigo-600 hover:text-white transition group relative" aria-label="Notifikasi Pesanan">
                            <i class="fas fa-truck-fast group-hover:scale-110 transition-transform"></i>
                            @if(isset($notifikasiDikirim) && $notifikasiDikirim->count() > 0)
                                <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white shadow-sm ring-2 ring-white">
                                    {{ $notifikasiDikirim->count() }}
                                </span>
                            @endif
                        </a>

                        <!-- Cart -->
                        <a href="{{ route('pembeli.keranjang.index') }}" class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600 hover:bg-indigo-600 hover:text-white transition group relative" aria-label="Keranjang Belanja">
                            <i class="fas fa-shopping-cart group-hover:scale-110 transition-transform"></i>
                            @if(isset($totalKeranjang) && $totalKeranjang > 0)
                                <span class="absolute -top-1.5 -right-1.5 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white shadow-sm ring-2 ring-white">
                                    {{ $totalKeranjang }}
                                </span>
                            @endif
                        </a>

                        <!-- Profile Dropdown -->
                        <div class="relative ms-2" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 focus:outline-none">
                                <img src="{{ Auth::user()->profile_photo_url ? asset('storage/' . Auth::user()->profile_photo_url) : asset('images/default-avatar.png') }}" alt="{{ Auth::user()->name }}" class="w-10 h-10 rounded-xl object-cover border-2 border-white shadow-sm hover:border-indigo-600 transition">
                            </button>
                            
                            <!-- Dropdown Menu -->
                            <div x-show="open" style="display: none;" class="absolute right-0 mt-3 w-56 bg-white rounded-xl shadow-lg border border-slate-100 py-2 z-50 transform origin-top-right transition-all">
                                <div class="px-4 py-3 border-b border-slate-100">
                                    <p class="text-sm font-semibold text-slate-800">{{ Auth::user()->name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ Auth::user()->email }}</p>
                                </div>
                                <a href="{{ route('pembeli.dashboard') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition"><i class="fas fa-home w-5 mr-2 text-slate-400"></i> Beranda Belanja</a>
                                <a href="{{ route('pembeli.profile.show') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition"><i class="fas fa-user-circle w-5 mr-2 text-slate-400"></i> Profil Saya</a>
                                <a href="{{ route('pembeli.pesanan.index') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50 hover:text-indigo-600 transition"><i class="fas fa-clipboard-list w-5 mr-2 text-slate-400"></i> Riwayat Pesanan</a>
                                <div class="border-t border-slate-100 my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                        <i class="fas fa-sign-out-alt w-5 mr-2 text-red-400"></i> Keluar
                                    </button>
                                </form>
                            </div>
                        </div>

                    @else
                        <!-- Dashboard Button for admin/penjual -->
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition shadow-sm hover:shadow">
                            <i class="fas fa-chart-pie text-xs"></i> Dashboard
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-4 py-2 text-sm font-semibold text-slate-700 hover:text-indigo-600 transition">
                        Masuk
                    </a>
                    <a href="{{ route('register') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition shadow-sm hover:shadow">
                        Daftar Akun
                    </a>
                @endauth
            </div>

            <!-- Mobile Top Quick Actions (Search & Cart) -->
            <div class="flex md:hidden items-center gap-2">
                <a href="{{ route('kategori') }}" class="w-9 h-9 rounded-xl bg-slate-100/90 flex items-center justify-center text-slate-600 hover:text-indigo-600 transition" aria-label="Cari Produk">
                    <i class="fas fa-search text-xs"></i>
                </a>
                
                <a href="{{ route('pembeli.keranjang.index') }}" class="w-9 h-9 rounded-xl bg-slate-100/90 flex items-center justify-center text-slate-600 hover:text-indigo-600 transition relative" aria-label="Keranjang">
                    <i class="fas fa-shopping-cart text-xs"></i>
                    @if(isset($totalKeranjang) && $totalKeranjang > 0)
                        <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[9px] font-bold text-white shadow-sm ring-2 ring-white">
                            {{ $totalKeranjang }}
                        </span>
                    @endif
                </a>
            </div>
        </div>
    </header>

    <!-- Main Page Content -->
    <div class="flex-1">
        @yield('content')
    </div>

    <!-- Comprehensive Modern Footer -->
    <footer class="bg-brand-slate text-slate-300 py-16 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 pb-12 border-b border-slate-800">
                <!-- Col 1: Brand Info (4 cols) -->
                <div class="md:col-span-4 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-white p-1 flex items-center justify-center">
                            <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto">
                        </div>
                        <span class="text-2xl font-bold font-display text-white tracking-tight">Juragan<span class="text-amber-500">Pelem</span></span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed">
                        Platform digital marketplace agrikultur terintegrasi pertama di Kabupaten Indramayu, menghubungkan langsung petani mangga dan pelaku UMKM dengan konsumen seluruh Indonesia.
                    </p>
                    <div class="flex items-center gap-3 pt-2">
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-400 hover:text-white flex items-center justify-center transition"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-400 hover:text-white flex items-center justify-center transition"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-400 hover:text-white flex items-center justify-center transition"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="w-9 h-9 rounded-lg bg-slate-800 hover:bg-indigo-600 text-slate-400 hover:text-white flex items-center justify-center transition"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Col 2: Navigation (2 cols) -->
                <div class="md:col-span-2 space-y-3">
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider">Jelajahi</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="{{ route('landing') }}" class="hover:text-emerald-400 transition">Beranda</a></li>
                        <li><a href="{{ route('kategori') }}" class="hover:text-emerald-400 transition">Kategori Produk</a></li>
                        <li><a href="{{ route('landing') }}#produk" class="hover:text-emerald-400 transition">Katalog Terlaris</a></li>
                        <li><a href="{{ route('landing') }}#ekosistem" class="hover:text-emerald-400 transition">Standar Mutu</a></li>
                        <li><a href="{{ route('tentang') }}" class="hover:text-emerald-400 transition">Tentang Kami</a></li>
                    </ul>
                </div>

                <!-- Col 3: Legal & Support (3 cols) -->
                <div class="md:col-span-3 space-y-3">
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider">Bantuan & Regulasi</h4>
                    <ul class="space-y-2 text-sm text-slate-400">
                        <li><a href="#" class="hover:text-emerald-400 transition">Kebijakan Garansi 100%</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Panduan Mitra Petani & UMKM</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Syarat & Ketentuan Layanan</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition">Kebijakan Privasi Data</a></li>
                    </ul>
                </div>

                <!-- Col 4: Hubungi Kami (3 cols) -->
                <div class="md:col-span-3 space-y-3">
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider">Sentra Logistik</h4>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        <i class="fas fa-location-dot text-amber-500 mr-1"></i> Jl. Raya Krasak No. 45, Kec. Jatibarang, Kabupaten Indramayu, Jawa Barat 45273
                    </p>
                    <p class="text-xs text-slate-400">
                        <i class="fas fa-envelope text-amber-500 mr-1"></i> halo@juraganpelem.id
                    </p>
                    <p class="text-xs text-slate-400">
                        <i class="fas fa-headset text-amber-500 mr-1"></i> CS: 0812-3456-7890 (08:00 - 17:00 WIB)
                    </p>
                </div>
            </div>

            <!-- Bottom Row: Copyright & Payments -->
            <div class="pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <p>&copy; {{ date('Y') }} Juragan Pelem Inc. Hak Cipta Dilindungi Undang-Undang. Dibangun dengan bangga di Indramayu.</p>
                <div class="flex items-center gap-3 text-slate-400 text-sm">
                    <i class="fab fa-cc-visa" title="Visa"></i>
                    <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                    <span class="font-bold text-xs bg-slate-800 px-2 py-0.5 rounded text-slate-300">QRIS</span>
                    <span class="font-bold text-xs bg-slate-800 px-2 py-0.5 rounded text-slate-300">Midtrans</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Mobile App Bottom Navigation Bar (Dock Menu) -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-xl border-t border-slate-200/80 shadow-[0_-4px_25px_rgba(0,0,0,0.08)] px-2 py-1.5 transition-all">
        <div class="max-w-md mx-auto grid grid-cols-5 items-center gap-1">
            
            <!-- 1. Beranda -->
            <a href="{{ route('landing') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all {{ request()->routeIs('landing') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <div class="relative flex items-center justify-center w-7 h-7 {{ request()->routeIs('landing') ? 'bg-indigo-50 rounded-lg text-indigo-600' : '' }}">
                    <i class="fas fa-house text-[16px]"></i>
                </div>
                <span class="text-[10px] tracking-tight mt-0.5 {{ request()->routeIs('landing') ? 'font-bold text-indigo-600' : 'font-medium' }}">Beranda</span>
            </a>

            <!-- 2. Kategori -->
            <a href="{{ route('kategori') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all {{ request()->routeIs('kategori') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                <div class="relative flex items-center justify-center w-7 h-7 {{ request()->routeIs('kategori') ? 'bg-indigo-50 rounded-lg text-indigo-600' : '' }}">
                    <i class="fas fa-layer-group text-[16px]"></i>
                </div>
                <span class="text-[10px] tracking-tight mt-0.5 {{ request()->routeIs('kategori') ? 'font-bold text-indigo-600' : 'font-medium' }}">Kategori</span>
            </a>

            <!-- 3. Pesanan -->
            @auth
                @if(Auth::user()->role === 'pembeli')
                    <a href="{{ route('pembeli.pesanan.index') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all {{ request()->routeIs('pembeli.pesanan.*') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                        <div class="relative flex items-center justify-center w-7 h-7 {{ request()->routeIs('pembeli.pesanan.*') ? 'bg-indigo-50 rounded-lg text-indigo-600' : '' }}">
                            <i class="fas fa-truck-fast text-[16px]"></i>
                            @if(isset($notifikasiDikirim) && $notifikasiDikirim->count() > 0)
                                <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[8px] font-bold text-white shadow-sm ring-2 ring-white">
                                    {{ $notifikasiDikirim->count() }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] tracking-tight mt-0.5 {{ request()->routeIs('pembeli.pesanan.*') ? 'font-bold text-indigo-600' : 'font-medium' }}">Pesanan</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all {{ request()->routeIs('dashboard') || request()->routeIs('*.dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                        <div class="relative flex items-center justify-center w-7 h-7 {{ request()->routeIs('dashboard') ? 'bg-indigo-50 rounded-lg text-indigo-600' : '' }}">
                            <i class="fas fa-chart-pie text-[16px]"></i>
                        </div>
                        <span class="text-[10px] tracking-tight mt-0.5 font-medium">Panel</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all text-slate-500 hover:text-slate-800">
                    <div class="relative flex items-center justify-center w-7 h-7">
                        <i class="fas fa-truck-fast text-[16px]"></i>
                    </div>
                    <span class="text-[10px] tracking-tight mt-0.5 font-medium">Pesanan</span>
                </a>
            @endauth

            <!-- 4. Chat -->
            @auth
                @if(Auth::user()->role === 'pembeli')
                    <a href="{{ route('pembeli.chat.index') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all {{ request()->routeIs('pembeli.chat.*') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                        <div class="relative flex items-center justify-center w-7 h-7 {{ request()->routeIs('pembeli.chat.*') ? 'bg-indigo-50 rounded-lg text-indigo-600' : '' }}">
                            <i class="fas fa-comments text-[16px]"></i>
                            @if(isset($jumlahChatBaru) && $jumlahChatBaru > 0)
                                <span class="absolute -top-1 -right-1 flex h-4 w-4 items-center justify-center rounded-full bg-red-500 text-[8px] font-bold text-white shadow-sm ring-2 ring-white">
                                    {{ $jumlahChatBaru }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] tracking-tight mt-0.5 {{ request()->routeIs('pembeli.chat.*') ? 'font-bold text-indigo-600' : 'font-medium' }}">Chat</span>
                    </a>
                @else
                    <a href="{{ route('tentang') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all {{ request()->routeIs('tentang') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                        <div class="relative flex items-center justify-center w-7 h-7 {{ request()->routeIs('tentang') ? 'bg-indigo-50 rounded-lg text-indigo-600' : '' }}">
                            <i class="fas fa-info-circle text-[16px]"></i>
                        </div>
                        <span class="text-[10px] tracking-tight mt-0.5 font-medium">Tentang</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all text-slate-500 hover:text-slate-800">
                    <div class="relative flex items-center justify-center w-7 h-7">
                        <i class="fas fa-comments text-[16px]"></i>
                    </div>
                    <span class="text-[10px] tracking-tight mt-0.5 font-medium">Chat</span>
                </a>
            @endauth

            <!-- 5. Akun / Profil -->
            @auth
                @if(Auth::user()->role === 'pembeli')
                    <a href="{{ route('pembeli.profile.show') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all {{ request()->routeIs('pembeli.profile.*') || request()->routeIs('pembeli.dashboard') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                        <div class="relative flex items-center justify-center w-7 h-7">
                            @if(Auth::user()->profile_photo_url)
                                <img src="{{ asset('storage/' . Auth::user()->profile_photo_url) }}" alt="{{ Auth::user()->name }}" class="w-6 h-6 rounded-full object-cover border {{ request()->routeIs('pembeli.profile.*') ? 'border-indigo-600 ring-2 ring-indigo-200' : 'border-slate-300' }}">
                            @else
                                <div class="w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-[10px] {{ request()->routeIs('pembeli.profile.*') ? 'ring-2 ring-indigo-300' : '' }}">
                                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <span class="text-[10px] tracking-tight mt-0.5 {{ request()->routeIs('pembeli.profile.*') ? 'font-bold text-indigo-600' : 'font-medium' }}">Akun</span>
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all text-slate-500 hover:text-slate-800">
                        <div class="relative flex items-center justify-center w-7 h-7">
                            <i class="fas fa-user-shield text-[16px]"></i>
                        </div>
                        <span class="text-[10px] tracking-tight mt-0.5 font-medium">Admin</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center justify-center py-1 px-1 rounded-xl transition-all {{ request()->routeIs('login') || request()->routeIs('register') ? 'text-indigo-600 font-bold' : 'text-slate-500 hover:text-slate-800' }}">
                    <div class="relative flex items-center justify-center w-7 h-7 {{ request()->routeIs('login') ? 'bg-indigo-50 rounded-lg text-indigo-600' : '' }}">
                        <i class="fas fa-user text-[16px]"></i>
                    </div>
                    <span class="text-[10px] tracking-tight mt-0.5 {{ request()->routeIs('login') ? 'font-bold text-indigo-600' : 'font-medium' }}">Masuk</span>
                </a>
            @endauth

        </div>
    </nav>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('desktop-search-input');
            const searchDropdown = document.getElementById('desktop-search-dropdown');
            const searchLoading = document.getElementById('search-loading');
            const searchContent = document.getElementById('search-content');
            const searchClear = document.getElementById('desktop-search-clear');
            const searchContainer = document.getElementById('desktop-search-container');
            const liveSearchUrl = "{{ route('api.search.live') }}";

            let searchTimeout = null;

            if (!searchInput || !searchDropdown) return;

            function closeDropdown() {
                searchDropdown.classList.add('hidden');
            }

            function openDropdown() {
                searchDropdown.classList.remove('hidden');
            }

            if (searchClear) {
                searchClear.addEventListener('click', function () {
                    searchInput.value = '';
                    searchClear.classList.add('hidden');
                    closeDropdown();
                    searchInput.focus();
                });
            }

            // Realtime Debounced Input
            searchInput.addEventListener('input', function () {
                const query = searchInput.value.trim();

                if (query.length > 0) {
                    if (searchClear) searchClear.classList.remove('hidden');
                } else {
                    if (searchClear) searchClear.classList.add('hidden');
                    closeDropdown();
                    return;
                }

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    fetchSearchResults(query);
                }, 200);
            });

            searchInput.addEventListener('focus', function () {
                if (searchInput.value.trim().length > 0) {
                    openDropdown();
                }
            });

            document.addEventListener('click', function (e) {
                if (searchContainer && !searchContainer.contains(e.target)) {
                    closeDropdown();
                }
            });

            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    closeDropdown();
                }
            });

            function fetchSearchResults(query) {
                openDropdown();
                searchLoading.classList.remove('hidden');
                searchContent.innerHTML = '';

                fetch(`${liveSearchUrl}?q=${encodeURIComponent(query)}`)
                    .then(res => res.json())
                    .then(data => {
                        searchLoading.classList.add('hidden');
                        renderSearchResults(data, query);
                    })
                    .catch(() => {
                        searchLoading.classList.add('hidden');
                        searchContent.innerHTML = `
                            <div class="py-4 text-center text-xs text-slate-400">
                                <i class="fas fa-exclamation-circle text-amber-500 mr-1"></i> Gagal memuat hasil pencarian.
                            </div>
                        `;
                    });
            }

            function renderSearchResults(data, query) {
                let html = '';

                // 1. Categories
                if (data.categories && data.categories.length > 0) {
                    html += `
                        <div class="border-b border-slate-100 pb-2.5">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-1.5 px-1">Kategori Terkait</span>
                            <div class="flex flex-wrap gap-1.5">
                                ${data.categories.map(c => `
                                    <a href="${c.url}" class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1 bg-slate-100 hover:bg-indigo-50 hover:text-indigo-600 rounded-lg text-slate-700 transition">
                                        <i class="fas fa-tag text-[10px] text-slate-400"></i> ${c.nama}
                                    </a>
                                `).join('')}
                            </div>
                        </div>
                    `;
                }

                // 2. Products
                if (data.products && data.products.length > 0) {
                    html += `
                        <div>
                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 block mb-2 px-1">Produk Unggulan (${data.total})</span>
                            <div class="space-y-1.5">
                                ${data.products.map(p => `
                                    <a href="${p.url}" class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 transition group">
                                        <div class="w-11 h-11 rounded-xl bg-slate-100 border border-slate-200/80 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                            ${p.gambar_url ? `
                                                <img src="${p.gambar_url}" alt="${p.nama}" class="w-full h-full object-cover">
                                            ` : `
                                                <i class="fas fa-image text-slate-400 text-sm"></i>
                                            `}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-xs font-bold text-slate-800 group-hover:text-indigo-600 truncate transition">
                                                ${p.nama}
                                            </h4>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-xs font-extrabold text-indigo-600">${p.harga_final}</span>
                                                ${p.has_diskon ? `
                                                    <span class="text-[10px] text-slate-400 line-through">${p.harga}</span>
                                                    <span class="text-[9px] font-black text-red-600 bg-red-50 px-1 rounded">- ${p.persen_diskon}%</span>
                                                ` : ''}
                                            </div>
                                        </div>
                                        <span class="text-[10px] font-semibold text-slate-400 group-hover:text-indigo-600 transition flex-shrink-0">
                                            ${p.toko} <i class="fas fa-chevron-right text-[9px] ml-1"></i>
                                        </span>
                                    </a>
                                `).join('')}
                            </div>
                        </div>
                    `;

                    // See all results CTA button
                    html += `
                        <div class="pt-2 border-t border-slate-100">
                            <a href="${data.all_results_url}" class="w-full py-2.5 px-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 rounded-xl text-xs font-bold flex items-center justify-center gap-2 transition">
                                <i class="fas fa-search text-[11px]"></i> Lihat Semua ${data.total} Hasil untuk "${query}"
                            </a>
                        </div>
                    `;
                } else if (data.categories.length === 0) {
                    html += `
                        <div class="py-8 text-center px-4">
                            <div class="w-10 h-10 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-2 text-slate-400">
                                <i class="fas fa-box-open text-base"></i>
                            </div>
                            <p class="text-xs font-bold text-slate-700 mb-0.5">Produk tidak ditemukan</p>
                            <p class="text-[11px] text-slate-400">Tidak ada produk yang cocok dengan kata kunci "<strong>${query}</strong>"</p>
                        </div>
                    `;
                }

                searchContent.innerHTML = html;
            }
        });
    </script>

    @stack('scripts')
</body>
</html>

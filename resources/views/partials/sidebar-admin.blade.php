<!-- Backdrop for mobile -->
<div 
    id="adminSidebarOverlay" 
    onclick="toggleAdminSidebar()" 
    class="hidden fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-sm lg:hidden transition-opacity"
></div>

<!-- Left Sidebar -->
<aside 
    id="adminSidebar" 
    class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200/80 flex flex-col justify-between transition-transform duration-300 ease-in-out -translate-x-full lg:translate-x-0 lg:static lg:inset-auto"
>
    <!-- Top Brand Logo -->
    <div>
        <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 p-1.5 flex items-center justify-center group-hover:scale-105 transition-transform shadow-sm">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                </div>
                <div>
                    <span class="text-lg font-bold font-display text-slate-900 tracking-tight">Juragan<span class="text-brand-600">Pelem</span></span>
                    <span class="block text-[9px] tracking-wider uppercase font-extrabold text-brand-600 -mt-0.5">Admin Central</span>
                </div>
            </a>

            <button 
                type="button" 
                onclick="toggleAdminSidebar()" 
                class="lg:hidden text-slate-400 hover:text-slate-600"
            >
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-6 overflow-y-auto max-h-[calc(100vh-10rem)]">
            
            <!-- Group: Menu Utama -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Menu Utama
                </p>
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a 
                        href="{{ route('admin.dashboard') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.dashboard') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-chart-pie w-4 text-sm {{ request()->routeIs('admin.dashboard') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Kategori -->
                    <a 
                        href="{{ route('admin.kategori.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.kategori.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-layer-group w-4 text-sm {{ request()->routeIs('admin.kategori.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Kategori Produk</span>
                    </a>

                    <!-- Toko & UMKM -->
                    <a 
                        href="{{ route('admin.umkm.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.umkm.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-store w-4 text-sm {{ request()->routeIs('admin.umkm.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Toko & UMKM</span>
                    </a>

                    <!-- Produk -->
                    <a 
                        href="{{ route('admin.produk.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.produk.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-boxes-stacked w-4 text-sm {{ request()->routeIs('admin.produk.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Katalog Produk</span>
                    </a>

                    <!-- Pendapatan -->
                    <a 
                        href="{{ route('admin.pendapatan.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.pendapatan.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-wallet w-4 text-sm {{ request()->routeIs('admin.pendapatan.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Pendapatan</span>
                    </a>
                </div>
            </div>

            <!-- Group: Manajemen Pengguna -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Manajemen Pengguna
                </p>
                <div class="space-y-1">
                    <!-- Akun Penjual -->
                    <a 
                        href="{{ route('admin.penjual.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.penjual.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-user-tie w-4 text-sm {{ request()->routeIs('admin.penjual.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Akun Penjual</span>
                    </a>

                    <!-- Akun Pembeli -->
                    <a 
                        href="{{ route('admin.pembeli.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('admin.pembeli.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-users w-4 text-sm {{ request()->routeIs('admin.pembeli.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Akun Pembeli</span>
                    </a>
                </div>
            </div>

        </nav>
    </div>

    <!-- Bottom Logout & Admin Card -->
    <div class="p-4 border-t border-slate-100">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button 
                type="submit" 
                class="w-full py-2.5 px-3.5 rounded-xl bg-slate-50 hover:bg-rose-50 text-slate-600 hover:text-rose-600 font-bold text-xs flex items-center justify-center gap-2 border border-slate-200/70 hover:border-rose-200 transition shadow-sm"
            >
                <i class="fas fa-arrow-right-from-bracket"></i>
                <span>Keluar (Logout)</span>
            </button>
        </form>
    </div>
</aside>
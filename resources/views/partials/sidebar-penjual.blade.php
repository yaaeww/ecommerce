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
            <a href="{{ route('penjual.dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-200 p-1.5 flex items-center justify-center group-hover:scale-105 transition-transform shadow-sm">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                </div>
                <div>
                    <span class="text-lg font-bold font-display text-slate-900 tracking-tight">Juragan<span class="text-brand-600">Pelem</span></span>
                    <span class="block text-[9px] tracking-wider uppercase font-extrabold text-brand-600 -mt-0.5">Penjual Panel</span>
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
            
            <!-- Group: Utama -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Menu Utama
                </p>
                <div class="space-y-1">
                    <!-- Dashboard -->
                    <a 
                        href="{{ route('penjual.dashboard') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('penjual.dashboard') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-chart-pie w-4 text-sm {{ request()->routeIs('penjual.dashboard') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Dashboard</span>
                    </a>

                    <!-- Toko & UMKM -->
                    <a 
                        href="{{ route('penjual.umkm.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('penjual.umkm.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-store w-4 text-sm {{ request()->routeIs('penjual.umkm.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Toko Saya</span>
                    </a>

                    <!-- Produk -->
                    <a 
                        href="{{ route('penjual.produk.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('penjual.produk.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-boxes-stacked w-4 text-sm {{ request()->routeIs('penjual.produk.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Produk Toko</span>
                    </a>

                    <!-- Pesanan -->
                    <a 
                        href="{{ route('penjual.pesanan.index') }}" 
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('penjual.pesanan.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-cart-shopping w-4 text-sm {{ request()->routeIs('penjual.pesanan.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                            <span>Pesanan Masuk</span>
                        </div>
                        @php
                            $jumlahNotif = (isset($notifPesananComplete) ? $notifPesananComplete->count() : 0) + (isset($notifStatusPesanan) ? $notifStatusPesanan->count() : 0);
                        @endphp
                        @if($jumlahNotif > 0)
                            <span class="px-2 py-0.5 rounded-full bg-rose-100 text-rose-600 text-[10px] font-bold">{{ $jumlahNotif }}</span>
                        @endif
                    </a>

                    <!-- Pendapatan -->
                    <a 
                        href="{{ route('penjual.pendapatan.index') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('penjual.pendapatan.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-wallet w-4 text-sm {{ request()->routeIs('penjual.pendapatan.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Pendapatan</span>
                    </a>
                </div>
            </div>

            <!-- Group: Komunikasi -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">
                    Komunikasi & Pengaturan
                </p>
                <div class="space-y-1">
                    <!-- Chat -->
                    <a 
                        href="{{ route('penjual.chat.index') }}" 
                        class="flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('penjual.chat.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <div class="flex items-center gap-3">
                            <i class="fas fa-messages w-4 text-sm {{ request()->routeIs('penjual.chat.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                            <span>Chat Pembeli</span>
                        </div>
                        @php
                            $jumlahChatBaru = \App\Models\Chat::where('receiver_id', Auth::id())
                                ->where('is_ai', 0)
                                ->where('is_read', false)
                                ->count();
                        @endphp
                        @if($jumlahChatBaru > 0)
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-600 text-[10px] font-bold">{{ $jumlahChatBaru }}</span>
                        @endif
                    </a>

                    <!-- Profile -->
                    <a 
                        href="{{ route('penjual.profile.show') }}" 
                        class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-bold transition {{ request()->routeIs('penjual.profile.*') ? 'bg-brand-50 text-brand-600 border border-brand-200/60 shadow-sm' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}"
                    >
                        <i class="fas fa-user-circle w-4 text-sm {{ request()->routeIs('penjual.profile.*') ? 'text-brand-600' : 'text-slate-400' }}"></i>
                        <span>Profil Saya</span>
                    </a>
                </div>
            </div>

            @if(isset($notifPesananComplete) && $notifPesananComplete->count() > 0)
            <!-- Mini Notification List (optional) -->
            <div>
                <p class="px-3 text-[11px] font-bold uppercase tracking-wider text-emerald-500 mb-2">
                    Pesanan Baru
                </p>
                <div class="space-y-1">
                    @foreach($notifPesananComplete->take(3) as $order)
                    <a href="{{ route('penjual.pesanan.index') }}" class="block px-3 py-2 rounded-lg hover:bg-slate-50 transition">
                        <p class="text-[11px] font-medium text-slate-700 truncate"><span class="font-bold text-slate-900">{{ $order->name }}</span> ({{ $order->jumlah }}x)</p>
                        <p class="text-[9px] text-slate-400">{{ $order->created_at->diffForHumans() }}</p>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

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

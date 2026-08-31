<header class="sticky top-0 z-30 flex items-center justify-between h-20 px-6 sm:px-8 bg-white border-b border-slate-200/80 shadow-sm backdrop-blur-md">
    <!-- Left: Mobile Menu Toggle & Title -->
    <div class="flex items-center gap-4">
        <button 
            type="button" 
            onclick="toggleAdminSidebar()" 
            class="lg:hidden p-2.5 rounded-xl text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition focus:outline-none"
            aria-label="Toggle Sidebar"
        >
            <i class="fas fa-bars text-lg"></i>
        </button>

        <div>
            <h1 class="text-lg sm:text-xl font-extrabold text-slate-900 tracking-tight font-display">
                @yield('page_title', 'Dashboard')
            </h1>
            <p class="text-[11px] text-slate-400 font-medium hidden sm:block">
                Sistem Manajemen Agro-Commerce & UMKM Indramayu
            </p>
        </div>
    </div>

    <!-- Right: Quick Actions & User Dropdown -->
    <div class="flex items-center gap-3 sm:gap-4">
        
        <!-- View Storefront Button -->
        <a 
            href="{{ route('landing') }}" 
            target="_blank" 
            class="hidden md:inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition shadow-sm"
        >
            <i class="fas fa-arrow-up-right-from-square text-[10px] text-brand-600"></i>
            <span>Lihat Website</span>
        </a>

        <!-- Divider -->
        <div class="h-6 w-px bg-slate-200 hidden sm:block"></div>

        <!-- User Profile Dropdown -->
        @if(Auth::check())
            <div class="relative" id="userDropdownContainer">
                <button 
                    type="button" 
                    onclick="toggleUserDropdown()" 
                    class="flex items-center gap-3 p-1.5 pl-3 pr-2.5 rounded-2xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition focus:outline-none"
                >
                    <div class="text-right hidden sm:block">
                        <span class="block text-xs font-bold text-slate-800">{{ Auth::user()->name }}</span>
                        <span class="block text-[10px] font-semibold text-brand-600 uppercase tracking-wider">
                            {{ ucfirst(Auth::user()->role) }}
                        </span>
                    </div>

                    <div class="w-10 h-10 rounded-xl bg-brand-50 border border-brand-200 text-brand-600 flex items-center justify-center font-bold text-sm shadow-sm">
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    </div>

                    <i class="fas fa-chevron-down text-slate-400 text-xs transition-transform duration-200" id="dropdownChevron"></i>
                </button>

                <!-- Dropdown Menu -->
                <div 
                    id="userDropdownMenu" 
                    class="hidden absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-200/80 py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-150"
                >
                    <div class="px-4 py-3 border-b border-slate-100">
                        <p class="text-xs font-bold text-slate-900">{{ Auth::user()->name }}</p>
                        <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                    </div>

                    <div class="py-1">
                        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                            <i class="fas fa-chart-pie text-slate-400 w-4"></i> Dashboard
                        </a>
                        <a href="{{ route('admin.penjual.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                            <i class="fas fa-users text-slate-400 w-4"></i> Kelola Pengguna
                        </a>
                    </div>

                    <div class="border-t border-slate-100 pt-1">
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs font-bold text-rose-600 hover:bg-rose-50 transition text-left">
                                <i class="fas fa-arrow-right-from-bracket w-4"></i> Keluar (Logout)
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endif

    </div>
</header>

<script>
    function toggleUserDropdown() {
        const menu = document.getElementById('userDropdownMenu');
        const chevron = document.getElementById('dropdownChevron');
        menu.classList.toggle('hidden');
        chevron.classList.toggle('rotate-180');
    }

    // Close dropdown on outside click
    document.addEventListener('click', function(event) {
        const container = document.getElementById('userDropdownContainer');
        const menu = document.getElementById('userDropdownMenu');
        const chevron = document.getElementById('dropdownChevron');
        if (container && !container.contains(event.target) && menu && !menu.classList.contains('hidden')) {
            menu.classList.add('hidden');
            chevron.classList.remove('rotate-180');
        }
    });

    function toggleAdminSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('adminSidebarOverlay');
        if (sidebar) {
            sidebar.classList.toggle('-translate-x-full');
            if (overlay) overlay.classList.toggle('hidden');
        }
    }
</script>
@php
    $role = Auth::user()->role ?? 'guest';
    $systemNotifications = collect();
    $actionRequiredCount = 0;
    $unreadTotalCount = 0;
    
    if ($role === 'admin') {
        try {
            $systemNotifications = \App\Http\Controllers\Admin\AdminNotificationController::getSystemNotifications();
            $actionRequiredCount = $systemNotifications->where('is_critical', true)->where('is_unread', true)->count();
            $unreadTotalCount = $systemNotifications->where('is_unread', true)->count();
        } catch (\Throwable $e) {
            $systemNotifications = collect();
        }
    } elseif ($role === 'penjual') {
        try {
            $systemNotifications = \App\Http\Controllers\Penjual\PenjualNotificationController::getSellerNotifications();
            $actionRequiredCount = $systemNotifications->where('is_critical', true)->where('is_unread', true)->count();
            $unreadTotalCount = $systemNotifications->where('is_unread', true)->count();
        } catch (\Throwable $e) {
            $systemNotifications = collect();
        }
    }
@endphp

<header class="sticky top-0 z-30 flex items-center justify-between h-20 px-6 sm:px-8 bg-white/95 border-b border-slate-200/80 shadow-xs backdrop-blur-md">
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
                Sistem Manajemen Agro-Commerce & Pengawasan Transparansi Indramayu
            </p>
        </div>
    </div>

    <!-- Right: Quick Actions, Notifications & User Dropdown -->
    <div class="flex items-center gap-2.5 sm:gap-3.5">
        
        <!-- View Storefront Button -->
        <a 
            href="{{ route('landing') }}" 
            target="_blank" 
            class="hidden md:inline-flex items-center gap-2 px-3.5 py-2 text-xs font-bold text-slate-600 bg-slate-50 hover:bg-slate-100 border border-slate-200 rounded-xl transition shadow-xs"
        >
            <i class="fas fa-arrow-up-right-from-square text-[10px] text-brand-600"></i>
            <span>Lihat Website</span>
        </a>

        <!-- ========================================================================= -->
        <!-- 🔔 NOTIFICATION CENTER (SUPERADMIN & PENJUAL)                            -->
        <!-- ========================================================================= -->
        @if(in_array($role, ['admin', 'penjual']))
            <div class="relative" id="notifDropdownContainer">
                <button 
                    type="button" 
                    onclick="toggleNotifDropdown()" 
                    class="relative p-2.5 rounded-2xl text-slate-600 hover:text-slate-900 hover:bg-slate-100 border border-transparent hover:border-slate-200 transition focus:outline-none"
                    aria-label="Notifikasi Sistem"
                    title="{{ $role === 'penjual' ? 'Notifikasi Toko & Pesanan' : 'Notifikasi & Kejadian Sistem' }}"
                >
                    <i class="fas fa-bell text-base text-slate-600"></i>
                    
                    <!-- Notification Badge Counter -->
                    <span 
                        id="notifBadge" 
                        class="{{ $unreadTotalCount > 0 ? 'flex' : 'hidden' }} absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-black items-center justify-center text-white {{ $actionRequiredCount > 0 ? 'bg-rose-600 animate-pulse ring-2 ring-white' : 'bg-brand-600 ring-2 ring-white' }}"
                    >
                        {{ $actionRequiredCount > 0 ? $actionRequiredCount : $unreadTotalCount }}
                    </span>
                </button>

                <!-- Dropdown Menu Card -->
                <div 
                    id="notifDropdownMenu" 
                    class="hidden absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-3xl shadow-2xl border border-slate-200/90 py-0 z-50 overflow-hidden transform transition-all duration-200"
                >
                    <!-- Header -->
                    <div class="p-4 bg-slate-900 text-white flex items-center justify-between">
                        <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-xl bg-white/10 flex items-center justify-center text-amber-400">
                                <i class="fas fa-bell text-xs"></i>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-xs tracking-tight">
                                    {{ $role === 'penjual' ? 'Pusat Notifikasi Toko' : 'Pusat Notifikasi & Audit' }}
                                </h4>
                                <p class="text-[10px] text-slate-400" id="notifSubtitle">
                                    {{ $actionRequiredCount > 0 ? $actionRequiredCount . ' kejadian butuh tindakan segera' : ($unreadTotalCount > 0 ? $unreadTotalCount . ' notifikasi baru' : 'Semua notifikasi sudah dibaca') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-1.5">
                            <button 
                                type="button" 
                                onclick="fetchLatestNotifications(true)" 
                                class="p-1.5 rounded-lg bg-white/10 hover:bg-white/20 text-slate-300 hover:text-white transition text-xs" 
                                title="Segarkan Notifikasi"
                            >
                                <i class="fas fa-rotate" id="notifRefreshIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Filter Tabs & Mark All As Read Bar -->
                    <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50 border-b border-slate-100 text-[11px] font-bold">
                        <div class="flex items-center gap-1.5">
                            <button 
                                type="button" 
                                onclick="filterNotifTab('all')" 
                                id="notifTab-all" 
                                class="notif-tab-btn active px-2.5 py-1 rounded-lg bg-brand-600 text-white text-[10px]"
                            >
                                Semua (<span id="notifCount-all">{{ $systemNotifications->count() }}</span>)
                            </button>
                            <button 
                                type="button" 
                                onclick="filterNotifTab('action')" 
                                id="notifTab-action" 
                                class="notif-tab-btn px-2.5 py-1 rounded-lg bg-white text-slate-600 border border-slate-200 text-[10px]"
                            >
                                Perlu Tindakan (<span id="notifCount-action">{{ $actionRequiredCount }}</span>)
                            </button>
                        </div>

                        <!-- 🔘 TOMBOL TANDAIN SUDAH DIBACA -->
                        <button 
                            type="button" 
                            onclick="markAllNotificationsAsRead()" 
                            id="btnMarkAllRead"
                            class="text-[10px] font-bold text-slate-500 hover:text-brand-600 hover:bg-brand-50 px-2 py-1 rounded-lg transition flex items-center gap-1 border border-transparent hover:border-brand-200"
                            title="Tandai semua notifikasi telah dibaca"
                        >
                            <i class="fas fa-check-double text-[9px] text-brand-600"></i>
                            <span>Tandai Dibaca</span>
                        </button>
                    </div>

                    <!-- Notification Items Container -->
                    <div class="max-h-80 overflow-y-auto divide-y divide-slate-100" id="notifListContainer">
                        @forelse($systemNotifications as $notif)
                            <a 
                                href="{{ $notif['url'] }}" 
                                class="notif-item block p-3.5 hover:bg-slate-50 transition group {{ $notif['is_unread'] ? ($notif['is_critical'] ? 'bg-rose-50/30' : 'bg-brand-50/20') : 'opacity-80' }}"
                                data-category="{{ $notif['is_critical'] ? 'action' : 'info' }}"
                                data-unread="{{ $notif['is_unread'] ? '1' : '0' }}"
                            >
                                <div class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-xl {{ $notif['bg_light'] }} {{ $notif['badge_text'] }} border {{ $notif['border'] }} flex items-center justify-center shrink-0 mt-0.5 shadow-2xs">
                                        <i class="{{ $notif['icon'] }} text-xs"></i>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center justify-between gap-1 mb-0.5">
                                            <div class="flex items-center gap-1.5 truncate">
                                                <h5 class="text-xs font-bold text-slate-900 group-hover:text-brand-600 transition truncate">
                                                    {{ $notif['title'] }}
                                                </h5>
                                                @if($notif['is_unread'])
                                                    <span class="w-1.5 h-1.5 rounded-full bg-brand-600 unread-dot"></span>
                                                @endif
                                            </div>
                                            <span class="text-[9px] font-semibold text-slate-400 shrink-0">
                                                {{ $notif['time'] }}
                                            </span>
                                        </div>
                                        <p class="text-[11px] text-slate-500 leading-snug line-clamp-2">
                                            {{ $notif['description'] }}
                                        </p>
                                    </div>
                                </div>
                            </a>
                        @empty
                            <div class="p-8 text-center text-slate-400 text-xs" id="emptyNotifPlaceholder">
                                <i class="fas fa-circle-check text-3xl text-emerald-400 mb-2 block"></i>
                                {{ $role === 'penjual' ? 'Belum ada notifikasi baru untuk toko Anda.' : 'Tidak ada notifikasi tertunda saat ini.' }}
                            </div>
                        @endforelse
                    </div>

                    <!-- Footer Link -->
                    <div class="p-3 bg-slate-50 border-t border-slate-100 text-center flex items-center justify-between px-4">
                        @if($role === 'penjual')
                            <a 
                                href="{{ route('penjual.pesanan.index') }}" 
                                class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1.5"
                            >
                                <i class="fas fa-cart-shopping text-[10px]"></i>
                                <span>Kelola Pesanan Masuk</span>
                            </a>
                            <span class="text-[10px] font-bold text-emerald-600 flex items-center gap-1">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-ping"></span> Live Toko
                            </span>
                        @else
                            <a 
                                href="{{ route('admin.activity-log.index') }}" 
                                class="text-xs font-bold text-brand-600 hover:text-brand-700 flex items-center gap-1.5"
                            >
                                <i class="fas fa-shield-halved text-[10px]"></i>
                                <span>Buka Seluruh Audit Log</span>
                            </a>
                            <span class="text-[10px] font-bold text-slate-400">Live System</span>
                        @endif
                    </div>

                </div>
            </div>
        @endif

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

                    <div class="w-10 h-10 rounded-xl bg-brand-50 border border-brand-200 text-brand-600 flex items-center justify-center font-bold text-sm shadow-xs">
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
                        @if(Auth::user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <i class="fas fa-chart-pie text-slate-400 w-4"></i> Dashboard
                            </a>
                            <a href="{{ route('admin.ledger.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <i class="fas fa-book-journal-whills text-slate-400 w-4"></i> Buku Besar Escrow
                            </a>
                            <a href="{{ route('admin.komplain.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <i class="fas fa-shield-heart text-slate-400 w-4"></i> Mediasi Komplain
                            </a>
                            <a href="{{ route('admin.activity-log.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <i class="fas fa-shield-halved text-slate-400 w-4"></i> Log Aktivitas
                            </a>
                        @elseif(Auth::user()->role === 'penjual')
                            <a href="{{ route('penjual.dashboard') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <i class="fas fa-store text-slate-400 w-4"></i> Panel Toko
                            </a>
                            <a href="{{ route('penjual.penarikan.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <i class="fas fa-wallet text-slate-400 w-4"></i> Pencairan Saldo
                            </a>
                        @else
                            <a href="{{ route('pembeli.pesanan.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <i class="fas fa-bag-shopping text-slate-400 w-4"></i> Pesanan Saya
                            </a>
                            <a href="{{ route('pembeli.alamat.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <i class="fas fa-address-book text-slate-400 w-4"></i> Buku Alamat
                            </a>
                            <a href="{{ route('pembeli.komplain.index') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs font-semibold text-slate-700 hover:bg-slate-50 hover:text-brand-600 transition">
                                <i class="fas fa-shield-halved text-slate-400 w-4"></i> Garansi & Komplain
                            </a>
                        @endif
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
        if (menu) menu.classList.toggle('hidden');
        if (chevron) chevron.classList.toggle('rotate-180');

        // Close notif dropdown if open
        const notifMenu = document.getElementById('notifDropdownMenu');
        if (notifMenu && !notifMenu.classList.contains('hidden')) {
            notifMenu.classList.add('hidden');
        }
    }

    function toggleNotifDropdown() {
        const notifMenu = document.getElementById('notifDropdownMenu');
        if (notifMenu) notifMenu.classList.toggle('hidden');

        // Close user dropdown if open
        const userMenu = document.getElementById('userDropdownMenu');
        const chevron = document.getElementById('dropdownChevron');
        if (userMenu && !userMenu.classList.contains('hidden')) {
            userMenu.classList.add('hidden');
            if (userChevron) userChevron.classList.remove('rotate-180');
        }

        // Fetch fresh notifications whenever opened
        if (notifMenu && !notifMenu.classList.contains('hidden')) {
            fetchLatestNotifications();
        }
    }

    function filterNotifTab(tab) {
        const allBtn = document.getElementById('notifTab-all');
        const actionBtn = document.getElementById('notifTab-action');
        const items = document.querySelectorAll('.notif-item');

        if (tab === 'all') {
            allBtn.className = 'notif-tab-btn active px-2.5 py-1 rounded-lg bg-brand-600 text-white text-[10px]';
            actionBtn.className = 'notif-tab-btn px-2.5 py-1 rounded-lg bg-white text-slate-600 border border-slate-200 text-[10px]';
            items.forEach(el => el.classList.remove('hidden'));
        } else {
            actionBtn.className = 'notif-tab-btn active px-2.5 py-1 rounded-lg bg-rose-600 text-white text-[10px]';
            allBtn.className = 'notif-tab-btn px-2.5 py-1 rounded-lg bg-white text-slate-600 border border-slate-200 text-[10px]';
            items.forEach(el => {
                if (el.getAttribute('data-category') === 'action') {
                    el.classList.remove('hidden');
                } else {
                    el.classList.add('hidden');
                }
            });
        }
    }

    const notifUnreadUrl = "{{ $role === 'penjual' ? route('penjual.notifications.unread') : ($role === 'admin' ? route('admin.notifications.unread') : '') }}";
    const notifMarkReadUrl = "{{ $role === 'penjual' ? route('penjual.notifications.mark-read') : ($role === 'admin' ? route('admin.notifications.mark-read') : '') }}";

    // 🔘 Eksekusi "Tandai Semua Sudah Dibaca"
    function markAllNotificationsAsRead() {
        if (!notifMarkReadUrl) return;

        const btn = document.getElementById('btnMarkAllRead');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin text-[9px]"></i> Menyimpan...';
            btn.disabled = true;
        }

        fetch(notifMarkReadUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                // Sembunyikan badge
                const badge = document.getElementById('notifBadge');
                if (badge) badge.classList.add('hidden');

                // Hilangkan dot unread dan styling aktif
                document.querySelectorAll('.unread-dot').forEach(el => el.remove());
                document.querySelectorAll('.notif-item').forEach(el => {
                    el.classList.remove('bg-rose-50/30', 'bg-brand-50/20');
                    el.classList.add('opacity-80');
                    el.setAttribute('data-unread', '0');
                });

                const subtitle = document.getElementById('notifSubtitle');
                if (subtitle) subtitle.textContent = 'Semua notifikasi sudah dibaca';

                const countAction = document.getElementById('notifCount-action');
                if (countAction) countAction.textContent = '0';
            }
        })
        .catch(() => {})
        .finally(() => {
            if (btn) {
                btn.innerHTML = '<i class="fas fa-check-double text-[9px] text-emerald-600"></i> <span class="text-emerald-700">Sudah Dibaca</span>';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-check-double text-[9px] text-brand-600"></i> <span>Tandai Dibaca</span>';
                    btn.disabled = false;
                }, 2000);
            }
        });
    }

    function fetchLatestNotifications(isManual = false) {
        if (!notifUnreadUrl) return;

        const refreshIcon = document.getElementById('notifRefreshIcon');
        if (refreshIcon && isManual) refreshIcon.classList.add('fa-spin');

        fetch(notifUnreadUrl)
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const badge = document.getElementById('notifBadge');
                    const subtitle = document.getElementById('notifSubtitle');
                    const countAll = document.getElementById('notifCount-all');
                    const countAction = document.getElementById('notifCount-action');

                    if (countAll) countAll.textContent = res.total_count;
                    if (countAction) countAction.textContent = res.action_required_count;

                    if (badge) {
                        if (res.action_required_count > 0) {
                            badge.textContent = res.action_required_count;
                            badge.className = 'flex absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-black items-center justify-center text-white bg-rose-600 animate-pulse ring-2 ring-white';
                        } else if (res.total_unread_count > 0) {
                            badge.textContent = res.total_unread_count;
                            badge.className = 'flex absolute -top-0.5 -right-0.5 min-w-[18px] h-[18px] px-1 rounded-full text-[10px] font-black items-center justify-center text-white bg-brand-600 ring-2 ring-white';
                        } else {
                            badge.classList.add('hidden');
                        }
                    }

                    if (subtitle) {
                        subtitle.textContent = res.action_required_count > 0 
                            ? `${res.action_required_count} kejadian butuh tindakan segera`
                            : (res.total_unread_count > 0 ? `${res.total_unread_count} notifikasi baru` : 'Semua notifikasi sudah dibaca');
                    }
                }
            })
            .catch(() => {})
            .finally(() => {
                if (refreshIcon && isManual) {
                    setTimeout(() => refreshIcon.classList.remove('fa-spin'), 600);
                }
            });
    }

    // Auto-poll notifications every 30 seconds
    setInterval(() => {
        if ("{{ $role }}" === 'admin' || "{{ $role }}" === 'penjual') {
            fetchLatestNotifications();
        }
    }, 30000);

    // Close dropdown on outside click
    document.addEventListener('click', function(event) {
        const userContainer = document.getElementById('userDropdownContainer');
        const userMenu = document.getElementById('userDropdownMenu');
        const userChevron = document.getElementById('dropdownChevron');

        if (userContainer && !userContainer.contains(event.target) && userMenu && !userMenu.classList.contains('hidden')) {
            userMenu.classList.add('hidden');
            if (userChevron) userChevron.classList.remove('rotate-180');
        }

        const notifContainer = document.getElementById('notifDropdownContainer');
        const notifMenu = document.getElementById('notifDropdownMenu');
        if (notifContainer && !notifContainer.contains(event.target) && notifMenu && !notifMenu.classList.contains('hidden')) {
            notifMenu.classList.add('hidden');
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
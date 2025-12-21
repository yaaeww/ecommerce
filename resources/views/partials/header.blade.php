<div class="header">
    <div class="header-left">
        <div class="menu-icon bi bi-list"></div>
        <div class="header-search">
            <h4 class="text-capitalize header-title">
                @yield('title', 'Dashboard')
            </h4>
        </div>
    </div>

    <!-- User Dropdown - POSISI KANAN POJOK -->
    @if(Auth::check())
        <div class="user-info-dropdown">
            <div class="dropdown">
                <a class="dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                    <div class="user-avatar">
                        @if(Auth::user()->role === 'admin')
                            <i class="bi bi-shield-fill-check" style="color: var(--gold); font-size: 1.5rem;"></i>
                        @else
                            <i class="bi bi-person-circle" style="color: var(--gold); font-size: 1.5rem;"></i>
                        @endif
                    </div>
                    <span class="user-name text-white">
                        {{ Auth::user()->name }}
                        @if(Auth::user()->role === 'admin')
                            <span class="admin-badge">Admin</span>
                        @endif
                    </span>
                    <i class="bi bi-chevron-down ms-1" style="color: var(--gold);"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right">
                    @if(Auth::user()->role !== 'admin')
                        <a class="dropdown-item" href="{{ route('penjual.profile.show') }}">
                            <i class="bi bi-person me-2"></i> Profil Saya
                        </a>
                        <div class="dropdown-divider"></div>
                    @else
                        <a class="dropdown-item" href="{{ route('admin.dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Admin Dashboard
                        </a>
                        <div class="dropdown-divider"></div>
                    @endif
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="dropdown-item text-danger" type="submit">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    :root {
        --dark-blue: #0a1628;
        --medium-blue: #1a3a5f;
        --light-blue: #2a4a7f;
        --gold: #ffd700;
        --gold-light: #ffed4e;
        --gold-dark: #d4af37;
        --admin-color: #ff6b6b;
        --admin-light: #ff8e8e;
    }

    /* Header Styling - FLEX LAYOUT */
    .header {
        background: linear-gradient(125deg, var(--dark-blue) 0%, var(--medium-blue) 100%) !important;
        border-bottom: 2px solid var(--gold);
        box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
        backdrop-filter: blur(10px);
        position: sticky;
        top: 0;
        z-index: 999;
        padding: 15px 30px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 70px;
        width: 100%;
        box-sizing: border-box;
        padding-left: 20%;
    }

    /* Header Left */
    .header-left {
        display: flex;
        align-items: center;
        flex-shrink: 0;
    }

    /* Menu Icon */
    .menu-icon {
        color: var(--gold);
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-right: 20px;
    }

    .menu-icon:hover {
        color: var(--gold-light);
        transform: scale(1.1);
    }

    /* Header Title */
    .header-title {
        font-weight: 700;
        font-size: 1.8rem;
        background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
        margin: 0;
        white-space: nowrap;
    }

    /* User Info Dropdown - POSISI KANAN MUTLAK */
    .user-info-dropdown {
        margin-left: auto;
        flex-shrink: 0;
    }

    .user-info-dropdown .dropdown {
        position: relative;
    }

    .user-info-dropdown .dropdown-toggle {
        display: flex;
        align-items: center;
        text-decoration: none;
        padding: 0px 0px;
        border-radius: 12px;
        transition: all 0.3s ease;
        border: 1px solid rgba(255, 215, 0, 0.3);
        background: rgba(255, 215, 0, 0.1);
        white-space: nowrap;
        gap: 8px;
    }

    .user-info-dropdown .dropdown-toggle:hover {
        background: rgba(255, 215, 0, 0.2);
        border-color: var(--gold);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
    }

    .user-name {
        color: var(--gold);
        font-weight: 600;
        font-size: 1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    /* Admin Badge */
    .admin-badge {
        background: linear-gradient(135deg, var(--admin-color) 0%, var(--admin-light) 100%);
        color: white;
        font-size: 0.7rem;
        padding: 2px 8px;
        border-radius: 10px;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        animation: pulse-admin 2s infinite;
    }

    @keyframes pulse-admin {

        0%,
        100% {
            box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
        }

        50% {
            box-shadow: 0 2px 15px rgba(255, 107, 107, 0.5);
        }
    }

    .user-avatar {
        display: flex;
        align-items: center;
    }

    /* Dropdown Menu */
    .dropdown-menu {
        background: linear-gradient(135deg, var(--dark-blue) 0%, var(--medium-blue) 100%);
        border: 2px solid var(--gold);
        border-radius: 12px;
        box-shadow: 0 8px 30px rgba(255, 215, 0, 0.3);
        backdrop-filter: blur(10px);
        padding: 10px 0;
        min-width: 200px;
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 5px;
        z-index: 1000;
        display: none;
    }

    .dropdown-menu.show {
        display: block;
        animation: dropdownFadeIn 0.3s ease;
    }

    @keyframes dropdownFadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .dropdown-item {
        color: #e0e0e0;
        padding: 10px 20px;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        font-weight: 500;
        text-decoration: none;
        border: none;
        background: none;
        width: 100%;
        text-align: left;
        cursor: pointer;
    }

    .dropdown-item:hover {
        color: var(--gold);
        background: rgba(255, 215, 0, 0.1);
        padding-left: 25px;
    }

    .dropdown-item.text-danger {
        color: #ff6b6b;
    }

    .dropdown-item.text-danger:hover {
        color: #ff4757 !important;
        background: rgba(255, 71, 87, 0.1);
    }

    .dropdown-divider {
        border-color: rgba(255, 215, 0, 0.3);
        margin: 8px 0;
    }

    /* Header Search */
    .header-search {
        display: flex;
        align-items: center;
    }

    /* RESPONSIVE DESIGN - OPTIMIZED UNTUK SEMUA UKURAN */
    @media (max-width: 1200px) {
        .header {
            padding: 15px 25px;
        }

        .header-title {
            font-size: 1.6rem;
        }
    }

    @media (max-width: 992px) {
        .header {
            padding: 12px 20px;
        }

        .header-title {
            font-size: 1.4rem;
        }

        .user-name {
            font-size: 0.9rem;
        }

        .user-info-dropdown .dropdown-toggle {
            padding: 8px 14px;
        }

        .admin-badge {
            font-size: 0.65rem;
            padding: 2px 6px;
        }
    }

    @media (max-width: 768px) {
        .header {
            padding: 10px 15px;
            min-height: 70px;
        }

        .header-title {
            font-size: 1.3rem;
        }

        .menu-icon {
            font-size: 1.3rem;
            margin-right: 15px;
        }

        /* Sembunyikan nama user dan badge admin di mobile, tampilkan hanya icon */
        .user-name {
            display: none;
        }

        .user-info-dropdown .dropdown-toggle {
            padding: 8px 12px;
        }

        .dropdown-menu {
            min-width: 180px;
            right: 0;
        }
    }

    @media (max-width: 576px) {
        .header {
            padding: 8px 12px;
        }

        .header-title {
            font-size: 1.2rem;
        }

        .menu-icon {
            font-size: 1.2rem;
            margin-right: 12px;
        }

        .user-info-dropdown .dropdown-toggle {
            padding: 6px 10px;
        }

        .user-avatar i {
            font-size: 1.3rem;
        }

        .dropdown-menu {
            min-width: 160px;
        }
    }

    @media (max-width: 400px) {
        .header {
            padding: 6px 10px;
        }

        .header-title {
            font-size: 1.1rem;
        }

        .menu-icon {
            font-size: 1.1rem;
            margin-right: 10px;
        }

        .user-info-dropdown .dropdown-toggle {
            padding: 5px 8px;
        }
    }

    /* EXTRA SMALL DEVICES */
    @media (max-width: 320px) {
        .header {
            padding: 5px 8px;
        }

        .header-title {
            font-size: 1rem;
        }

        .menu-icon {
            font-size: 1rem;
            margin-right: 8px;
        }

        .user-info-dropdown .dropdown-toggle {
            padding: 4px 6px;
        }

        .dropdown-menu {
            min-width: 140px;
        }
    }

    /* Animasi untuk header */
    .header {
        animation: slideDown 0.5s ease-out;
    }

    @keyframes slideDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }

        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    /* Sparkle Effect */
    .header-sparkle {
        position: absolute;
        width: 100%;
        height: 100%;
        pointer-events: none;
        overflow: hidden;
        top: 0;
        left: 0;
    }

    @keyframes headerSparkleFloat {

        0%,
        100% {
            transform: translateY(0) scale(1);
            opacity: 0;
        }

        50% {
            transform: translateY(-10px) scale(1.2);
            opacity: 1;
        }
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Dropdown functionality
        const userDropdown = document.querySelector('.user-info-dropdown .dropdown-toggle');
        if (userDropdown) {
            userDropdown.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const dropdownMenu = this.nextElementSibling;
                dropdownMenu.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.user-info-dropdown')) {
                    const dropdownMenu = document.querySelector('.user-info-dropdown .dropdown-menu');
                    if (dropdownMenu) {
                        dropdownMenu.classList.remove('show');
                    }
                }
            });

            // Close dropdown when pressing Escape key
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    const dropdownMenu = document.querySelector('.user-info-dropdown .dropdown-menu');
                    if (dropdownMenu) {
                        dropdownMenu.classList.remove('show');
                    }
                }
            });
        }

        // Menu icon functionality (untuk toggle sidebar)
        const menuIcon = document.querySelector('.menu-icon');
        if (menuIcon) {
            menuIcon.addEventListener('click', function () {
                const sidebar = document.querySelector('.left-side-bar');
                if (sidebar) {
                    sidebar.classList.toggle('show');
                }
            });
        }

        // Prevent dropdown close when clicking inside dropdown
        const dropdownMenu = document.querySelector('.dropdown-menu');
        if (dropdownMenu) {
            dropdownMenu.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }

        // Sparkle animation
        function createHeaderSparkle() {
            const header = document.querySelector('.header');
            if (!header) return;

            const sparkle = document.createElement('div');
            sparkle.style.position = 'absolute';
            sparkle.style.width = '3px';
            sparkle.style.height = '3px';
            sparkle.style.background = '#ffd700';
            sparkle.style.borderRadius = '50%';
            sparkle.style.boxShadow = '0 0 8px #ffd700';
            sparkle.style.left = Math.random() * 100 + '%';
            sparkle.style.top = Math.random() * 100 + '%';
            sparkle.style.animation = 'headerSparkleFloat 1.5s forwards';
            sparkle.style.pointerEvents = 'none';
            sparkle.style.zIndex = '1';

            header.appendChild(sparkle);

            setTimeout(() => {
                if (sparkle.parentNode) {
                    sparkle.parentNode.removeChild(sparkle);
                }
            }, 1500);
        }

        // Start sparkle animation setiap 800ms
        setInterval(createHeaderSparkle, 800);
    });
</script>
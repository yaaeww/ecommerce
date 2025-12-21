@php
    $role = Auth::user()->role ?? 'guest';
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Dashboard')</title>

    <!-- DeskApp CSS -->
    <link rel="stylesheet" href="{{ asset('deskapp/vendors/styles/core.css') }}">
    <link rel="stylesheet" href="{{ asset('deskapp/vendors/styles/icon-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('deskapp/vendors/styles/style.css') }}">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --dark-blue: #0a1628;
            --medium-blue: #1a3a5f;
            --light-blue: #2a4a7f;
            --gold: #ffd700;
            --gold-light: #ffed4e;
            --gold-dark: #d4af37;
        }

        /* TEMA UTAMA */
        body {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--medium-blue) 100%);
            color: #e0e0e0;
        }

        /* HEADER STYLING */
        .header {
            background: rgba(10, 22, 40, 0.95) !important;
            backdrop-filter: blur(10px);
            border-bottom: 2px solid var(--gold);
            box-shadow: 0 4px 20px rgba(255, 215, 0, 0.2);
        }

        .navbar-logo {
            height: 45px;
            filter: brightness(1.2);
        }

        .navbar-brand {
            color: var(--gold) !important;
            font-weight: 700;
            font-size: 1.5rem;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* SIDEBAR STYLING */
        .left-sidebar {
            background: rgba(10, 22, 40, 0.95) !important;
            border-right: 2px solid var(--gold);
        }

        .sidebar-menu>li>a {
            color: #e0e0e0 !important;
            font-weight: 500;
            padding: 12px 20px;
            border-radius: 6px;
            margin: 5px 10px;
            transition: all 0.3s ease;
        }

        .sidebar-menu>li>a:hover,
        .sidebar-menu>li.active>a {
            color: var(--gold) !important;
            background: rgba(255, 215, 0, 0.1) !important;
            transform: translateY(-2px);
        }

        .sidebar-menu>li>a i {
            color: var(--gold) !important;
            margin-right: 10px;
        }

        /* MAIN CONTENT */
        .main-container {
            background: transparent;
        }

        .page-header {
            background: rgba(26, 58, 95, 0.8);
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 2rem;
            border: 2px solid rgba(255, 215, 0, 0.3);
            backdrop-filter: blur(10px);
        }

        .page-title {
            font-weight: 700;
            font-size: 2rem;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
            margin: 0;
        }

        /* CARD STYLING */
        .card {
            background: linear-gradient(135deg, rgba(26, 58, 95, 0.9) 0%, rgba(42, 74, 127, 0.9) 100%);
            border: 2px solid rgba(255, 215, 0, 0.3);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s ease;
            backdrop-filter: blur(10px);
        }

        .card:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 15px 40px rgba(255, 215, 0, 0.4);
            border-color: var(--gold);
        }

        .card-title {
            color: var(--gold) !important;
            font-weight: 600;
            font-size: 1.2rem;
        }

        /* BUTTON STYLING */
        .btn-primary {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            border: none;
            color: var(--dark-blue);
            font-weight: 700;
            padding: 10px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 25px rgba(255, 215, 0, 0.5);
            background: linear-gradient(135deg, var(--gold-light) 0%, var(--gold) 100%);
            color: var(--dark-blue);
        }

        .btn-outline-primary {
            background: transparent;
            border: 2px solid var(--gold);
            color: var(--gold);
            font-weight: 600;
            padding: 8px 24px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background: var(--gold);
            color: var(--dark-blue);
            transform: scale(1.05);
            box-shadow: 0 0 20px rgba(255, 215, 0, 0.5);
        }

        /* TABLE STYLING */
        .table {
            background: rgba(26, 58, 95, 0.8);
            border-radius: 10px;
            overflow: hidden;
            backdrop-filter: blur(10px);
        }

        .table th {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            color: var(--dark-blue) !important;
            font-weight: 700;
            border: none;
            padding: 15px;
        }

        .table td {
            color: #e0e0e0;
            padding: 12px 15px;
            border-color: rgba(255, 215, 0, 0.2);
        }

        /* RIGHT SIDEBAR THEME SETTINGS */
        .right-sidebar {
            background: rgba(10, 22, 40, 0.95) !important;
            backdrop-filter: blur(10px);
            border-left: 2px solid var(--gold);
        }

        .sidebar-btn-group .btn.active {
            border-color: var(--gold) !important;
            background: var(--gold) !important;
            color: var(--dark-blue) !important;
        }

        /* FORM STYLING */
        .form-control {
            background: rgba(26, 58, 95, 0.6);
            border: 2px solid rgba(255, 215, 0, 0.3);
            color: #e0e0e0;
            border-radius: 8px;
            padding: 10px 15px;
        }

        .form-control:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
            background: rgba(26, 58, 95, 0.8);
            color: #e0e0e0;
        }

        /* FOOTER STYLING */
        footer {
            background: linear-gradient(135deg, var(--dark-blue) 0%, rgba(26, 58, 95, 0.95) 100%);
            padding: 2rem 0;
            border-top: 3px solid var(--gold);
            box-shadow: 0 -4px 20px rgba(255, 215, 0, 0.2);
            color: var(--gold);
        }

        /* SCROLLBAR STYLING */
        ::-webkit-scrollbar {
            width: 12px;
        }

        ::-webkit-scrollbar-track {
            background: var(--dark-blue);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            border-radius: 6px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--gold-light);
        }

        /* SPARKLE ANIMATION */
        .sparkle {
            position: absolute;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .sparkle::before,
        .sparkle::after {
            content: '';
            position: absolute;
            width: 3px;
            height: 3px;
            background: var(--gold);
            border-radius: 50%;
            box-shadow: 0 0 10px var(--gold);
            animation: sparkleFloat 3s infinite;
        }

        @keyframes sparkleFloat {

            0%,
            100% {
                transform: translateY(0) scale(1);
                opacity: 0;
            }

            50% {
                transform: translateY(-30px) scale(1.5);
                opacity: 1;
            }
        }

        /* FLOATING ANIMATION */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-10px);
            }
        }

        .floating {
            animation: float 3s ease-in-out infinite;
        }
    </style>

    <!-- Stack untuk style tambahan dari view -->
    @stack('style')
</head>

<body id="body">
    @include('partials.header')
    @includeIf('partials.sidebar-' . $role)

    <div class="main-container">
        <div class="pd-ltr-20">
            <div class="page-header d-flex justify-content-between align-items-center mb-4">
                <h4 class="page-title">
                    @yield('title', 'Dashboard')
                </h4>
                <div class="sparkle"></div>
            </div>

            @yield('content')
        </div>
    </div>

    @include('partials.footer')

    <!-- DeskApp Scripts -->
    <script src="{{ asset('deskapp/vendors/scripts/core.js') }}"></script>
    <script src="{{ asset('deskapp/vendors/scripts/script.min.js') }}"></script>
    <script src="{{ asset('deskapp/vendors/scripts/process.js') }}"></script>
    <script src="{{ asset('deskapp/vendors/scripts/layout-settings.js') }}"></script>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const body = document.getElementById('body');
            
            const toggleBtn = document.querySelector('[data-toggle="right-sidebar"]');
            const closeBtn = document.querySelector('[data-toggle="right-sidebar-close"]');
            const sidebar = document.querySelector('.right-sidebar');

            // Sidebar toggle
            toggleBtn?.addEventListener('click', () => {
                sidebar?.classList.toggle('right-sidebar-visible');
            });
            closeBtn?.addEventListener('click', () => {
                sidebar?.classList.remove('right-sidebar-visible');
            });

            // Add sparkle animation
            setInterval(() => {
                const sparkle = document.createElement('div');
                sparkle.style.position = 'absolute';
                sparkle.style.width = '4px';
                sparkle.style.height = '4px';
                sparkle.style.background = '#ffd700';
                sparkle.style.borderRadius = '50%';
                sparkle.style.boxShadow = '0 0 10px #ffd700';
                sparkle.style.left = Math.random() * 100 + '%';
                sparkle.style.top = Math.random() * 100 + '%';
                sparkle.style.animation = 'sparkleFloat 2s forwards';
                sparkle.style.pointerEvents = 'none';
                document.querySelector('.sparkle').appendChild(sparkle);

                setTimeout(() => sparkle.remove(), 2000);
            }, 1000);
        });
    </script>

    <!-- Stack untuk script tambahan dari view -->
    @stack('scripts')
</body>

</html>
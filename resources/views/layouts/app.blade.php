@php
    $role = Auth::user()->role ?? 'guest';
@endphp
<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-50">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page_title', 'Superadmin Dashboard') — Juragan Pelem</title>

    <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        display: ['"Outfit"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5', // Primary Royal Indigo
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        },
                        slate: {
                            850: '#151f32',
                            900: '#0f172a',
                        }
                    }
                }
            }
        }
    </script>

    <!-- Bootstrap 5 & FontAwesome for Legacy Compatibility -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
        }

        /* Clean Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Override Bootstrap Conflicts */
        a {
            text-decoration: none;
        }
        .card {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 1rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        }
        .table {
            --bs-table-bg: transparent;
            color: #334155;
            margin-bottom: 0;
        }
        .table thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e2e8f0;
            padding: 0.875rem 1rem;
        }
        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f1f5f9;
            vertical-align: middle;
            font-size: 0.875rem;
        }
        .table tbody tr:hover {
            background-color: #f8fafc;
        }
    </style>

    @stack('style')
</head>

<body class="h-full flex flex-col min-h-screen antialiased bg-slate-50 text-slate-800">
    <div class="flex h-screen overflow-hidden">
        
        <!-- Sidebar Navigation -->
        @includeIf('partials.sidebar-' . $role)

        <!-- Main Content Area -->
        <div class="relative flex flex-col flex-1 overflow-y-auto overflow-x-hidden bg-slate-50">
            
            <!-- Modern Header Bar -->
            @include('partials.header')

            <!-- Page Body -->
            <main class="flex-1 p-6 sm:p-8 lg:p-10 max-w-7xl w-full mx-auto">
                <!-- Flash Alert Messages -->
                @if(session('success'))
                    <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-2xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-circle-check text-emerald-600 text-lg"></i>
                            <span class="text-xs sm:text-sm font-semibold">{{ session('success') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700">
                            <i class="fas fa-xmark text-sm"></i>
                        </button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-6 p-4 bg-rose-50 border border-rose-200 text-rose-800 rounded-2xl flex items-center justify-between shadow-sm">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-circle-exclamation text-rose-600 text-lg"></i>
                            <span class="text-xs sm:text-sm font-semibold">{{ session('error') }}</span>
                        </div>
                        <button type="button" onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-700">
                            <i class="fas fa-xmark text-sm"></i>
                        </button>
                    </div>
                @endif

                @yield('content')
            </main>

            <!-- Footer -->
            @include('partials.footer')
        </div>

    </div>

    <!-- Bootstrap 5 Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- 🔍 Universal Real-Time Search Handler (No Enter Needed) -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const searchInputs = document.querySelectorAll('input[name="search"], input[type="search"], .realtime-search');
            
            searchInputs.forEach(input => {
                let debounceTimer = null;
                const form = input.closest('form');

                // If URL has search query and this input has value, restore focus to end of text
                const urlParams = new URLSearchParams(window.location.search);
                if (urlParams.has('search') && urlParams.get('search') === input.value && input.value.trim() !== '') {
                    input.focus();
                    const val = input.value;
                    input.value = '';
                    input.value = val;
                }

                input.addEventListener('input', function () {
                    const query = this.value.toLowerCase().trim();

                    // 1. Instant client-side filtering on current table rows
                    const table = document.querySelector('.table tbody');
                    if (table) {
                        const rows = table.querySelectorAll('tr');
                        rows.forEach(row => {
                            // Don't filter empty-state row if it's the only one
                            if (row.querySelector('td[colspan]')) return;

                            const rowText = row.innerText.toLowerCase();
                            if (query === '' || rowText.includes(query)) {
                                row.style.display = '';
                            } else {
                                row.style.display = 'none';
                            }
                        });
                    }

                    // 2. Debounced server-side query (450ms)
                    if (form && form.method.toUpperCase() === 'GET') {
                        clearTimeout(debounceTimer);
                        debounceTimer = setTimeout(() => {
                            form.submit();
                        }, 450);
                    }
                });

                // Prevent standard form submission on Enter if already searching live
                input.addEventListener('keydown', function (e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        if (form) {
                            clearTimeout(debounceTimer);
                            form.submit();
                        }
                    }
                });
            });
        });
    </script>

    @stack('scripts')
</body>

</html>
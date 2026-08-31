<!DOCTYPE html>
<html lang="id" class="h-full bg-brand-cream">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Akun — Juragan Pelem | Marketplace Mangga & UMKM Indramayu</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
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
                        'brand-green': '#1B4D3E',
                        'brand-green-dark': '#12352A',
                        'brand-green-light': '#2D6A4F',
                        'brand-amber': '#E88D14',
                        'brand-amber-light': '#F3A638',
                        'brand-cream': '#FAFAF7',
                        'brand-slate': '#1E293B',
                    }
                }
            }
        }
    </script>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAFAF7;
        }
        .role-card.active {
            border-color: #1B4D3E;
            background-color: rgba(27, 77, 62, 0.04);
            box-shadow: 0 4px 12px rgba(27, 77, 62, 0.08);
        }
        .role-card.active .role-check {
            display: flex;
        }
    </style>
</head>

<body class="h-full flex min-h-[100dvh]">
    <div class="flex-1 flex flex-col lg:flex-row min-h-full">
        
        <!-- Left Side: Brand Showcase Panel (Visible on LG screens) -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-brand-green-dark via-brand-green to-emerald-900 p-12 text-white flex-col justify-between relative overflow-hidden">
            <!-- Subtle Radial Glows -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-emerald-500/10 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 bg-brand-amber/10 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Top Header in Panel -->
            <div class="relative z-10">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-3 group">
                    <div class="w-11 h-11 rounded-xl bg-white shadow-sm p-1.5 flex items-center justify-center group-hover:scale-105 transition-transform">
                        <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                    </div>
                    <div>
                        <span class="text-2xl font-bold font-display text-white tracking-tight">Juragan<span class="text-brand-amber">Pelem</span></span>
                        <span class="block text-[10px] tracking-wider uppercase font-semibold text-emerald-200/80 -mt-1">Agro-Commerce Indramayu</span>
                    </div>
                </a>
            </div>

            <!-- Central Hero Narrative -->
            <div class="space-y-6 relative z-10 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur border border-white/20 text-emerald-200 text-xs font-bold uppercase tracking-wider">
                    <i class="fas fa-seedling text-brand-amber"></i> Bergabung Bersama Kami
                </div>

                <h1 class="text-4xl sm:text-5xl font-extrabold font-display leading-tight tracking-tight text-white">
                    Mulai Belanja atau <br>
                    <span class="text-amber-300">Buka Toko UMKM</span> Anda.
                </h1>

                <p class="text-emerald-100/90 text-sm leading-relaxed">
                    Satu akun untuk ribuan produk pertanian lokal berkualitas. Rasakan kemudahan transaksi aman, pengiriman terpercaya, dan dukungan nyata untuk petani Indramayu.
                </p>

                <!-- Benefits List -->
                <div class="space-y-3 pt-2">
                    <div class="flex items-center gap-3 text-xs text-emerald-100">
                        <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-[10px]">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Pilihan akun Pembeli untuk konsumsi atau Penjual untuk pemilik toko UMKM</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-emerald-100">
                        <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-[10px]">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Garansi kualitas segar dengan pengemasan tahan benturan</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-emerald-100">
                        <div class="w-5 h-5 rounded-full bg-emerald-500/20 text-emerald-300 flex items-center justify-center text-[10px]">
                            <i class="fas fa-check"></i>
                        </div>
                        <span>Dukungan pembayaran digital terintegrasi instan</span>
                    </div>
                </div>
            </div>

            <!-- Bottom Note -->
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-xs text-emerald-200/80">
                <i class="fas fa-users text-brand-amber mr-2"></i> Lebih dari <strong>50+ UMKM & Kelompok Tani</strong> telah terdaftar dan aktif melayani pesanan.
            </div>

        </div>

        <!-- Right Side: Clean Auth Form -->
        <div class="flex-1 flex flex-col justify-between p-6 sm:p-12 lg:p-16 bg-brand-cream overflow-y-auto">
            
            <!-- Top Nav Back Link -->
            <div class="flex items-center justify-between">
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-brand-green transition">
                    <i class="fas fa-arrow-left"></i> Kembali ke Beranda
                </a>
                
                <!-- Mobile Brand Logo (Visible only on mobile/tablet) -->
                <a href="{{ route('landing') }}" class="flex lg:hidden items-center gap-2">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-8 w-auto">
                    <span class="font-display font-bold text-brand-green text-lg">Juragan<span class="text-brand-amber">Pelem</span></span>
                </a>
            </div>

            <!-- Form Container -->
            <div class="max-w-md w-full mx-auto my-8">
                
                <!-- Header Title -->
                <div class="mb-6">
                    <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight font-display">
                        Buat Akun Baru
                    </h2>
                    <p class="text-sm text-slate-500 mt-1.5">
                        Pilih peran Anda dan lengkapi formulir di bawah ini.
                    </p>
                </div>

                <!-- Validation Alerts -->
                @if ($errors->any())
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs font-medium shadow-sm">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <i class="fas fa-triangle-exclamation text-red-500"></i> Mohon Periksa Input:
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-4">
                    @csrf

                    <!-- Interactive Role Selector Cards -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">Pilih Peran Akun</label>
                        <input type="hidden" name="role" id="roleInput" value="{{ old('role', 'pembeli') }}">
                        
                        <div class="grid grid-cols-2 gap-3">
                            <!-- Option 1: Pembeli -->
                            <div 
                                id="roleCardPembeli" 
                                onclick="selectRole('pembeli')" 
                                class="role-card cursor-pointer p-4 rounded-2xl border-2 border-slate-200 bg-white transition relative flex flex-col justify-between {{ old('role', 'pembeli') === 'pembeli' ? 'active' : '' }}"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-brand-green flex items-center justify-center text-lg">
                                        <i class="fas fa-bag-shopping"></i>
                                    </div>
                                    <div class="role-check hidden w-5 h-5 rounded-full bg-brand-green text-white items-center justify-center text-[10px]">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h4 class="font-bold text-xs text-brand-slate">Pembeli</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Belanja mangga & olahan</p>
                                </div>
                            </div>

                            <!-- Option 2: Penjual -->
                            <div 
                                id="roleCardPenjual" 
                                onclick="selectRole('penjual')" 
                                class="role-card cursor-pointer p-4 rounded-2xl border-2 border-slate-200 bg-white transition relative flex flex-col justify-between {{ old('role') === 'penjual' ? 'active' : '' }}"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="w-10 h-10 rounded-xl bg-amber-50 text-brand-amber flex items-center justify-center text-lg">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="role-check hidden w-5 h-5 rounded-full bg-brand-green text-white items-center justify-center text-[10px]">
                                        <i class="fas fa-check"></i>
                                    </div>
                                </div>
                                <div class="mt-3">
                                    <h4 class="font-bold text-xs text-brand-slate">Penjual / UMKM</h4>
                                    <p class="text-[11px] text-slate-400 mt-0.5">Buka toko & jual panen</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Name Input -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-user text-sm"></i>
                            </div>
                            <input 
                                id="name" 
                                type="text" 
                                name="name" 
                                value="{{ old('name') }}" 
                                required 
                                autofocus 
                                placeholder="Nama lengkap Anda"
                                class="w-full bg-white text-slate-800 text-sm pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 outline-none transition shadow-sm"
                            >
                        </div>
                    </div>

                    <!-- Email Input -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-envelope text-sm"></i>
                            </div>
                            <input 
                                id="email" 
                                type="email" 
                                name="email" 
                                value="{{ old('email') }}" 
                                required 
                                placeholder="nama@email.com"
                                class="w-full bg-white text-slate-800 text-sm pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 outline-none transition shadow-sm"
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                placeholder="Minimal 8 karakter"
                                class="w-full bg-white text-slate-800 text-sm pl-10 pr-10 py-3 rounded-xl border border-slate-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 outline-none transition shadow-sm"
                            >
                            <button 
                                type="button" 
                                onclick="togglePasswordVisibility('password', 'password-toggle-icon-1')" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                            >
                                <i id="password-toggle-icon-1" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Password Confirmation Input -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Konfirmasi Kata Sandi</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-lock-check text-sm"></i>
                            </div>
                            <input 
                                id="password_confirmation" 
                                type="password" 
                                name="password_confirmation" 
                                required 
                                placeholder="Ulangi kata sandi"
                                class="w-full bg-white text-slate-800 text-sm pl-10 pr-10 py-3 rounded-xl border border-slate-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 outline-none transition shadow-sm"
                            >
                            <button 
                                type="button" 
                                onclick="togglePasswordVisibility('password_confirmation', 'password-toggle-icon-2')" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                            >
                                <i id="password-toggle-icon-2" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full py-3.5 bg-brand-green hover:bg-brand-green-dark text-white font-bold text-sm rounded-xl transition shadow-lg shadow-brand-green/20 hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2 mt-2"
                    >
                        <i class="fas fa-user-plus"></i> Daftar Akun Baru
                    </button>

                    <!-- Or Divider -->
                    <div class="relative my-6 text-center">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <span class="relative bg-brand-cream px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            atau daftar dengan
                        </span>
                    </div>

                    <!-- Google SSO Button -->
                    <a 
                        href="{{ route('auth.google') }}" 
                        class="w-full py-3 px-4 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 font-bold text-sm rounded-xl transition shadow-sm flex items-center justify-center gap-3 hover:-translate-y-0.5"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24">
                            <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                            <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                            <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                            <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                        </svg>
                        <span>Daftar dengan Google</span>
                    </a>
                </form>

                <!-- Switch to Login -->
                <div class="text-center mt-8 pt-6 border-t border-slate-200/80">
                    <p class="text-xs text-slate-500">
                        Sudah memiliki akun? 
                        <a href="{{ route('login') }}" class="font-bold text-brand-green hover:underline">
                            Masuk Sekarang
                        </a>
                    </p>
                </div>

            </div>

            <!-- Footer Small -->
            <div class="text-center text-[11px] text-slate-400">
                &copy; {{ date('Y') }} Juragan Pelem Indramayu. Seluruh hak cipta dilindungi.
            </div>

        </div>

    </div>

    <!-- Scripts -->
    <script>
        function selectRole(role) {
            document.getElementById('roleInput').value = role;
            const cardPembeli = document.getElementById('roleCardPembeli');
            const cardPenjual = document.getElementById('roleCardPenjual');

            if (role === 'pembeli') {
                cardPembeli.classList.add('active');
                cardPenjual.classList.remove('active');
            } else {
                cardPenjual.classList.add('active');
                cardPembeli.classList.remove('active');
            }
        }

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>
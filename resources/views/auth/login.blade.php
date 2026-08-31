<!DOCTYPE html>
<html lang="id" class="h-full bg-brand-cream">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk — Juragan Pelem | Marketplace Mangga & UMKM Indramayu</title>
    
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
                    <i class="fas fa-shield-check text-brand-amber"></i> Portal Terverifikasi
                </div>

                <h1 class="text-4xl sm:text-5xl font-extrabold font-display leading-tight tracking-tight text-white">
                    Akses Langsung ke <br>
                    <span class="text-amber-300">Ekosistem Mangga</span> Terbaik.
                </h1>

                <p class="text-emerald-100/90 text-sm leading-relaxed">
                    Masuk ke akun Anda untuk memantau status pesanan panen raya, mengelola keranjang belanja UMKM, atau mengatur inventaris toko kebun Anda.
                </p>

                <!-- Value Props -->
                <div class="grid grid-cols-2 gap-4 pt-4">
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-300 flex items-center justify-center mb-2">
                            <i class="fas fa-truck-fast text-xs"></i>
                        </div>
                        <h4 class="font-bold text-xs text-white">Logistik 24 Jam</h4>
                        <p class="text-[11px] text-emerald-200/70 mt-0.5">Pengiriman bergaransi segar</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white/5 border border-white/10 backdrop-blur">
                        <div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-300 flex items-center justify-center mb-2">
                            <i class="fas fa-shield-halved text-xs"></i>
                        </div>
                        <h4 class="font-bold text-xs text-white">Pembayaran Aman</h4>
                        <p class="text-[11px] text-emerald-200/70 mt-0.5">Terenkripsi via Midtrans</p>
                    </div>
                </div>
            </div>

            <!-- Testimonial Quote -->
            <div class="p-5 rounded-2xl bg-white/10 border border-white/15 backdrop-blur relative z-10">
                <div class="flex items-center gap-1 text-amber-400 text-xs mb-2">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p class="text-xs italic text-emerald-100 leading-relaxed">
                    "Platform ini memberikan kemudahan luar biasa untuk mendapatkan mangga asli Indramayu langsung saat baru petik matang pohon."
                </p>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-white/10 text-[11px] text-emerald-200">
                    <span class="font-bold text-white">Mitra Pembeli Terverifikasi</span>
                    <span>Jakarta Selatan</span>
                </div>
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
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight font-display">
                        Selamat Datang Kembali
                    </h2>
                    <p class="text-sm text-slate-500 mt-1.5">
                        Masukkan email dan password akun Anda untuk melanjutkan.
                    </p>
                </div>

                <!-- Session & Validation Alerts -->
                @if(session('error'))
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-start gap-3 text-xs font-medium shadow-sm">
                        <i class="fas fa-circle-exclamation text-base text-red-500 mt-0.5 shrink-0"></i>
                        <div>{{ session('error') }}</div>
                    </div>
                @endif

                @if(session('status'))
                    <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-brand-green rounded-2xl flex items-start gap-3 text-xs font-medium shadow-sm">
                        <i class="fas fa-circle-check text-base text-emerald-500 mt-0.5 shrink-0"></i>
                        <div>{{ session('status') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs font-medium shadow-sm">
                        <div class="flex items-center gap-2 font-bold mb-1">
                            <i class="fas fa-triangle-exclamation text-red-500"></i> Terjadi Kesalahan:
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 pl-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-4">
                    @csrf

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
                                autofocus 
                                placeholder="nama@email.com"
                                class="w-full bg-white text-slate-800 text-sm pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 outline-none transition shadow-sm"
                            >
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label for="password" class="block text-xs font-bold text-slate-700">Kata Sandi</label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-xs font-semibold text-brand-green hover:underline">
                                    Lupa sandi?
                                </a>
                            @endif
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                                <i class="fas fa-lock text-sm"></i>
                            </div>
                            <input 
                                id="password" 
                                type="password" 
                                name="password" 
                                required 
                                placeholder="••••••••"
                                class="w-full bg-white text-slate-800 text-sm pl-10 pr-10 py-3 rounded-xl border border-slate-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 outline-none transition shadow-sm"
                            >
                            <button 
                                type="button" 
                                onclick="togglePasswordVisibility('password', 'password-toggle-icon')" 
                                class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-slate-400 hover:text-slate-600 transition"
                            >
                                <i id="password-toggle-icon" class="fas fa-eye text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="flex items-center justify-between pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded border-slate-300 text-brand-green focus:ring-brand-green/30">
                            <span class="text-xs font-medium text-slate-600">Ingat sesi saya</span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="w-full py-3.5 bg-brand-green hover:bg-brand-green-dark text-white font-bold text-sm rounded-xl transition shadow-lg shadow-brand-green/20 hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2"
                    >
                        <i class="fas fa-arrow-right-to-bracket"></i> Masuk ke Akun
                    </button>

                    <!-- Or Divider -->
                    <div class="relative my-6 text-center">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-slate-200"></div>
                        </div>
                        <span class="relative bg-brand-cream px-3 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            atau masuk dengan
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
                        <span>Masuk dengan Google</span>
                    </a>
                </form>

                <!-- Switch to Register -->
                <div class="text-center mt-8 pt-6 border-t border-slate-200/80">
                    <p class="text-xs text-slate-500">
                        Belum memiliki akun Juragan Pelem? 
                        <a href="{{ route('register') }}" class="font-bold text-brand-green hover:underline">
                            Daftar Sekarang
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

    <!-- Password visibility toggle script -->
    <script>
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
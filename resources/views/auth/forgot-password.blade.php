<!DOCTYPE html>
<html lang="id" class="h-full bg-brand-cream">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi — Juragan Pelem | Marketplace Mangga Indramayu</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    @vite(['resources/css/app.css'])
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #FAFAF7;
        }
        .font-display {
            font-family: 'Outfit', sans-serif;
        }
        .bg-brand-green { background-color: #1B4D3E !important; }
        .bg-brand-green:hover, .hover\:bg-brand-green-dark:hover { background-color: #143D31 !important; }
        .text-brand-green { color: #1B4D3E !important; }
        .text-brand-amber { color: #F59E0B !important; }
        .bg-brand-amber { background-color: #F59E0B !important; }
        .bg-brand-cream { background-color: #FAFAF7 !important; }
        .text-brand-slate { color: #0F172A !important; }
    </style>
</head>

<body class="h-full flex min-h-[100dvh]">
    <div class="flex-1 flex flex-col lg:flex-row min-h-full">
        
        <!-- Left Side: Brand Showcase Panel -->
        <div class="hidden lg:flex lg:w-1/2 p-12 text-white flex-col justify-between relative overflow-hidden" style="background: linear-gradient(135deg, #0d281e 0%, #1b4d3e 45%, #064e3b 100%) !important; color: #ffffff !important;">
            <div class="absolute top-0 right-0 w-96 h-96 rounded-full blur-3xl pointer-events-none" style="background: rgba(52, 211, 153, 0.15);"></div>
            <div class="absolute bottom-0 left-0 w-96 h-96 rounded-full blur-3xl pointer-events-none" style="background: rgba(245, 158, 11, 0.15);"></div>

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

            <!-- Narrative -->
            <div class="space-y-6 relative z-10 max-w-lg">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/10 backdrop-blur border border-white/20 text-emerald-200 text-xs font-bold uppercase tracking-wider">
                    <i class="fas fa-shield-halved text-brand-amber"></i> Keamanan Akun Terjamin
                </div>

                <h1 class="text-4xl sm:text-5xl font-extrabold font-display leading-tight tracking-tight text-white">
                    Pemulihan Akses <br>
                    <span class="text-amber-300">Akun Terverifikasi</span>.
                </h1>

                <p class="text-emerald-100/90 text-sm leading-relaxed">
                    Kami menjaga privasi dan keamanan setiap data akun pelanggan serta mitra petani. Masukkan email Anda untuk menerima tautan reset kata sandi resmi.
                </p>
            </div>

            <!-- Bottom Info -->
            <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-xs text-emerald-200/80">
                <i class="fas fa-info-circle text-brand-amber mr-2"></i> Tautan reset kata sandi berlaku selama 60 menit demi keamanan akun Anda.
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="flex-1 flex flex-col justify-between p-6 sm:p-12 lg:p-16 bg-brand-cream overflow-y-auto">
            
            <div class="flex items-center justify-between">
                <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-brand-green transition">
                    <i class="fas fa-arrow-left"></i> Kembali ke Halaman Masuk
                </a>
                
                <a href="{{ route('landing') }}" class="flex lg:hidden items-center gap-2">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-8 w-auto">
                    <span class="font-display font-bold text-brand-green text-lg">Juragan<span class="text-brand-amber">Pelem</span></span>
                </a>
            </div>

            <div class="max-w-md w-full mx-auto my-auto py-6">
                
                <div class="mb-8">
                    <h2 class="text-3xl font-extrabold text-brand-slate tracking-tight font-display">
                        Lupa Kata Sandi?
                    </h2>
                    <p class="text-sm text-slate-500 mt-1.5">
                        Masukkan alamat email akun Anda. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi.
                    </p>
                </div>

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

                <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Alamat Email Terdaftar</label>
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

                    <button 
                        type="submit" 
                        class="w-full py-3.5 bg-brand-green hover:bg-brand-green-dark text-white font-bold text-sm rounded-xl transition shadow-lg shadow-brand-green/20 hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2 cursor-pointer mt-2"
                    >
                        <i class="fas fa-paper-plane"></i> Kirim Tautan Reset Kata Sandi
                    </button>
                </form>

                <div class="text-center mt-8 pt-6 border-t border-slate-200/80">
                    <p class="text-xs text-slate-500">
                        Ingat kata sandi Anda? 
                        <a href="{{ route('login') }}" class="font-bold text-brand-green hover:underline">
                            Masuk Sekarang
                        </a>
                    </p>
                </div>

            </div>

            <div class="text-center text-[11px] text-slate-400 mt-4">
                &copy; {{ date('Y') }} Juragan Pelem Indramayu. Seluruh hak cipta dilindungi.
            </div>

        </div>

    </div>
</body>

</html>
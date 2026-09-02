<!DOCTYPE html>
<html lang="id" class="h-full bg-brand-cream">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Kata Sandi — Juragan Pelem</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css'])
</head>

<body class="h-full flex items-center justify-center p-4 sm:p-6 bg-brand-cream font-sans">
    <div class="max-w-md w-full bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-xl relative">
        
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2.5 mb-4 group">
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-100 p-1.5 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                </div>
                <span class="text-xl font-bold font-display text-brand-green">Juragan<span class="text-brand-amber">Pelem</span></span>
            </a>
            
            <h1 class="text-2xl font-extrabold text-brand-slate font-display tracking-tight">Atur Ulang Kata Sandi</h1>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang kata sandi Anda.
            </p>
        </div>

        <!-- Session Status Alert -->
        @if (session('status'))
            <div class="mb-5 p-4 bg-emerald-50 border border-emerald-200 text-brand-green rounded-2xl flex items-center gap-2.5 text-xs font-semibold">
                <i class="fas fa-circle-check text-sm text-emerald-500"></i>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs font-semibold">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-4">
            @csrf

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
                        class="w-full bg-slate-50 hover:bg-white focus:bg-white text-slate-800 text-sm pl-10 pr-4 py-3 rounded-xl border border-slate-200 focus:border-brand-green focus:ring-2 focus:ring-brand-green/20 outline-none transition"
                    >
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full py-3.5 bg-brand-green hover:bg-brand-green-dark text-white font-bold text-sm rounded-xl transition shadow-lg shadow-brand-green/20 hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2"
            >
                <i class="fas fa-paper-plane"></i> Kirim Tautan Reset
            </button>
        </form>

        <div class="text-center mt-8 pt-6 border-t border-slate-100">
            <a href="{{ route('login') }}" class="inline-flex items-center gap-2 text-xs font-bold text-slate-500 hover:text-brand-green transition">
                <i class="fas fa-arrow-left"></i> Kembali ke Halaman Masuk
            </a>
        </div>

    </div>
</body>

</html>
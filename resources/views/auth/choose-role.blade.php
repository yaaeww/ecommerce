<!DOCTYPE html>
<html lang="id" class="h-full bg-brand-cream">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pilih Peran Akun — Juragan Pelem</title>
    
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
        .role-card.active {
            border-color: #1B4D3E;
            background-color: rgba(27, 77, 62, 0.04);
            box-shadow: 0 4px 14px rgba(27, 77, 62, 0.1);
        }
        .role-card.active .role-check {
            display: flex;
        }
    </style>
</head>

<body class="h-full flex items-center justify-center p-4 sm:p-6 bg-brand-cream font-sans">
    <div class="max-w-lg w-full bg-white p-8 sm:p-10 rounded-3xl border border-slate-200/80 shadow-xl relative">
        
        <!-- Brand Header -->
        <div class="text-center mb-8">
            <a href="{{ route('landing') }}" class="inline-flex items-center gap-2.5 mb-4 group">
                <div class="w-10 h-10 rounded-xl bg-white shadow-sm border border-slate-100 p-1.5 flex items-center justify-center group-hover:scale-105 transition-transform">
                    <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Juragan Pelem" class="h-full w-auto object-contain">
                </div>
                <span class="text-xl font-bold font-display text-brand-green">Juragan<span class="text-brand-amber">Pelem</span></span>
            </a>
            
            <h1 class="text-2xl font-extrabold text-brand-slate font-display tracking-tight">Pilih Peran Akun Anda</h1>
            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                Akun Google Anda berhasil terhubung. Silakan tentukan bagaimana Anda ingin menggunakan platform ini.
            </p>
        </div>

        @if ($errors->any())
            <div class="mb-5 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl text-xs font-semibold">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('auth.google.store-role') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="role" id="roleInput" value="pembeli">

            <div class="space-y-4">
                <!-- Option 1: Pembeli -->
                <div 
                    id="roleCardPembeli" 
                    onclick="selectRole('pembeli')" 
                    class="role-card active cursor-pointer p-5 rounded-2xl border-2 border-slate-200 bg-white transition relative flex items-center justify-between gap-4"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-brand-green flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-bag-shopping"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-brand-slate">Sebagai Pembeli</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Belanja mangga segar dan aneka produk olahan UMKM.</p>
                        </div>
                    </div>
                    <div class="role-check w-6 h-6 rounded-full bg-brand-green text-white flex items-center justify-center text-xs shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                </div>

                <!-- Option 2: Penjual -->
                <div 
                    id="roleCardPenjual" 
                    onclick="selectRole('penjual')" 
                    class="role-card cursor-pointer p-5 rounded-2xl border-2 border-slate-200 bg-white transition relative flex items-center justify-between gap-4"
                >
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-brand-amber flex items-center justify-center text-xl shrink-0">
                            <i class="fas fa-store"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-sm text-brand-slate">Sebagai Penjual / Mitra UMKM</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Buka toko digital, jual hasil panen, dan kelola produk.</p>
                        </div>
                    </div>
                    <div class="role-check hidden w-6 h-6 rounded-full bg-brand-green text-white items-center justify-center text-xs shrink-0">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full py-3.5 bg-brand-green hover:bg-brand-green-dark text-white font-bold text-sm rounded-xl transition shadow-lg shadow-brand-green/20 hover:shadow-xl hover:-translate-y-0.5 flex items-center justify-center gap-2"
            >
                Lanjutkan Masuk <i class="fas fa-arrow-right text-xs"></i>
            </button>
        </form>

    </div>

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
    </script>
</body>

</html>
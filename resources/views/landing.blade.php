<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UMKM Indramayu - Digitalisasi UMKM Lokal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
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

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, var(--dark-blue) 0%, var(--medium-blue) 70%, var(--light-blue) 100%);
    color: #e0e0e0;
    scroll-behavior: smooth;
    min-height: 100vh;
}

/* NAVBAR - Gradien yang menyatu dengan background */
.navbar-custom {
    background: linear-gradient(135deg, rgba(10, 22, 40, 0.98) 0%, rgba(26, 58, 95, 0.95) 100%);
    backdrop-filter: blur(15px);
    padding: 1rem 0;
    border-bottom: 2px solid var(--gold);
    box-shadow: 0 4px 30px rgba(255, 215, 0, 0.15);
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

.navbar-custom .nav-link {
    color: #e0e0e0;
    font-weight: 500;
    margin: 0 10px;
    padding: 8px 16px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.navbar-custom .nav-link:hover,
.navbar-custom .nav-link.active {
    color: var(--gold);
    background: rgba(255, 215, 0, 0.1);
    transform: translateY(-2px);
}

.btn-login {
    background: transparent;
    border: 2px solid var(--gold);
    color: var(--gold);
    font-weight: 600;
    padding: 8px 24px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-login:hover {
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    color: var(--dark-blue);
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(255, 215, 0, 0.4);
}

.btn-signup {
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    color: var(--dark-blue);
    font-weight: 700;
    padding: 8px 24px;
    border-radius: 8px;
    border: none;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
}

.btn-signup:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 25px rgba(255, 215, 0, 0.5);
}

/* HERO SECTION - Gradien utama yang sama dengan body */
.hero {
    position: relative;
    background: linear-gradient(135deg, var(--dark-blue) 0%, var(--medium-blue) 70%, var(--light-blue) 100%);
    min-height: 90vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    padding-top: 80px;
}

.hero::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: 
        radial-gradient(circle at 20% 50%, rgba(255, 215, 0, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 215, 0, 0.08) 0%, transparent 50%);
    animation: pulse 6s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.3; }
    50% { opacity: 0.6; }
}

.hero-content {
    position: relative;
    z-index: 2;
}

.hero h1 {
    font-size: 4rem;
    font-weight: 800;
    line-height: 1.2;
    margin-bottom: 1.5rem;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    text-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
    animation: fadeInUp 1s ease-out;
}

.hero p {
    font-size: 1.3rem;
    color: #c0c0c0;
    margin-bottom: 2rem;
    line-height: 1.8;
    animation: fadeInUp 1.2s ease-out;
}

.btn-explore {
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    color: var(--dark-blue);
    padding: 14px 36px;
    border: none;
    font-size: 1.1rem;
    font-weight: 700;
    border-radius: 50px;
    transition: all 0.3s ease;
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
    animation: fadeInUp 1.4s ease-out;
}

.btn-explore:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 30px rgba(255, 215, 0, 0.6);
}

.btn-video {
    color: var(--gold);
    text-decoration: none;
    font-size: 1rem;
    font-weight: 600;
    margin-left: 20px;
    transition: all 0.3s ease;
    animation: fadeInUp 1.4s ease-out;
}

.btn-video:hover {
    color: var(--gold-light);
    transform: translateX(5px);
}

/* SECTION KATEGORI & PRODUK - Gradien konsisten dengan variasi transparansi */
#kategori, #produk {
    background: linear-gradient(135deg, rgba(10, 22, 40, 0.7) 0%, rgba(26, 58, 95, 0.8) 100%);
    padding: 40px 0;
    margin: 40px auto;
    border-radius: 20px;
    border: 1px solid rgba(255, 215, 0, 0.2);
    backdrop-filter: blur(10px);
}

/* CARTOON ILLUSTRATION */
.seller-cartoon {
    position: relative;
    animation: float 3s ease-in-out infinite, fadeInUp 1s ease-out;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-20px); }
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* SVG Cartoon Illustration */
.cartoon-container {
    width: 100%;
    max-width: 500px;
    margin: 0 auto;
}

/* SECTION TITLE - Konsisten di semua section */
.section-title {
    font-size: 2.8rem;
    font-weight: 700;
    margin-bottom: 50px;
    text-align: center;
    position: relative;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.section-title::after {
    content: "";
    position: absolute;
    bottom: -20px;
    left: 50%;
    transform: translateX(-50%);
    width: 100px;
    height: 4px;
    background: linear-gradient(90deg, var(--gold), var(--gold-light));
    border-radius: 2px;
    box-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
}

/* ACCORDION - Gradien konsisten dengan section */
.accordion-item {
    background: linear-gradient(135deg, rgba(26, 58, 95, 0.6) 0%, rgba(42, 74, 127, 0.7) 100%);
    border: 1px solid rgba(255, 215, 0, 0.2);
    margin-bottom: 1rem;
    border-radius: 12px;
    overflow: hidden;
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.accordion-item:hover {
    border-color: rgba(255, 215, 0, 0.4);
    transform: translateY(-2px);
}

.accordion-button {
    font-weight: 600;
    color: var(--gold);
    background: linear-gradient(135deg, rgba(26, 58, 95, 0.8) 0%, rgba(42, 74, 127, 0.9) 100%);
    border: none;
    padding: 1.2rem 1.5rem;
    
}


.accordion-button:not(.collapsed) {
    background: linear-gradient(135deg, rgba(255, 215, 0, 0.15) 0%, rgba(255, 237, 78, 0.1) 100%);
    color: var(--gold-light);
    box-shadow: 0 0 20px rgba(255, 215, 0, 0.2);
}

.accordion-button::after {
    filter: invert(76%) sepia(92%) saturate(1600%) hue-rotate(1deg) brightness(105%) contrast(105%);
}


.accordion-body {
    background: linear-gradient(135deg, rgba(10, 22, 40, 0.8) 0%, rgba(26, 58, 95, 0.6) 100%);
    color: #c0c0c0;
    padding: 1.5rem;
}

/* CARDS - Gradien konsisten dengan variasi yang halus */
.card {
    background: linear-gradient(135deg, rgba(26, 58, 95, 0.7) 0%, rgba(42, 74, 127, 0.8) 100%);
    border: 1px solid rgba(255, 215, 0, 0.2);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.4s ease;
    height: 100%;
    backdrop-filter: blur(10px);
}

.card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 15px 40px rgba(255, 215, 0, 0.3);
    border-color: var(--gold);
    background: linear-gradient(135deg, rgba(26, 58, 95, 0.9) 0%, rgba(42, 74, 127, 0.9) 100%);
}

.card-img-top {
    height: 220px;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.card:hover .card-img-top {
    transform: scale(1.1);
}

.card-body {
    padding: 1.5rem;
}

.card-title {
    color: var(--gold);
    font-weight: 600;
    font-size: 1.1rem;
    margin-bottom: 0.8rem;
}

.card-text {
    color: var(--gold-light);
    font-weight: 700;
    font-size: 1.3rem;
}

.btn-warning {
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
    border: none;
    color: var(--dark-blue);
    font-weight: 700;
    padding: 10px 20px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-warning:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(255, 215, 0, 0.5);
}

/* TENTANG SECTION - Gradien yang sama dengan variasi */
#tentang {
    background: linear-gradient(135deg, rgba(10, 22, 40, 0.7) 0%, rgba(26, 58, 95, 0.8) 100%);
    padding: 60px 0;
    border-radius: 20px;
    margin-top: 40px;
    border: 1px solid rgba(255, 215, 0, 0.2);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    flex-direction: column;
}

#tentang p {
    color: #c0c0c0;
    font-size: 1.2rem;
    line-height: 1.8;
}

.map-responsive {
    overflow: hidden;
    padding-bottom: 56.25%;
    position: relative;
    height: 0;
    width: 80%;
    border-radius: 15px;
    margin-top: 30px;
    border: 2px solid var(--gold);
    box-shadow: 0 0 30px rgba(255, 215, 0, 0.2);
}

.map-responsive iframe {
    left: 0;
    top: 0;
    height:100%;
    width: 100%;
    position: absolute;
    justify-content: center;
}

/* FOOTER - Gradien yang menyempurnakan keseluruhan */
footer {
    background: linear-gradient(135deg, var(--dark-blue) 0%, var(--medium-blue) 100%);
    padding: 3rem 0;
    margin-top: 80px;
    border-top: 2px solid var(--gold);
    box-shadow: 0 -4px 30px rgba(255, 215, 0, 0.15);
}

footer p {
    color: var(--gold);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

footer small {
    color: #c0c0c0;
}

/* SCROLLBAR */
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

/* RESPONSIVE */
@media (max-width: 768px) {
    .hero h1 {
        font-size: 2.5rem;
    }

    .hero p {
        font-size: 1.1rem;
    }

    .section-title {
        font-size: 2rem;
    }

    .navbar-brand {
        font-size: 1.2rem;
    }

    .cartoon-container {
        max-width: 300px;
        margin-top: 30px;
    }
    
    #kategori, #produk, #tentang {
        margin: 20px 10px;
        padding: 30px 15px;
    }
}

/* Sparkle effect yang lebih halus */
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

.sparkle::before {
    top: 20%;
    left: 30%;
    animation-delay: 0s;
}

.sparkle::after {
    top: 60%;
    left: 70%;
    animation-delay: 1.5s;
}

@keyframes sparkleFloat {
    0%, 100% {
        transform: translateY(0) scale(1);
        opacity: 0;
    }
    50% {
        transform: translateY(-30px) scale(1.5);
        opacity: 1;
    }
}
    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Logo UMKM Indramayu" class="navbar-logo">
                UMKM Indramayu
            </a>
            <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" style="background: rgba(255,215,0,0.2);">
                <span class="navbar-toggler-icon" style="filter: brightness(0) invert(1);"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-between" id="navbarNav">
                <ul class="navbar-nav ms-auto me-4">
                    <li class="nav-item"><a class="nav-link active" href="#"><i class="fas fa-home me-2"></i>Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#kategori"><i class="fas fa-th-large me-2"></i>Kategori</a></li>
                    <li class="nav-item"><a class="nav-link" href="#produk"><i class="fas fa-box me-2"></i>Produk</a></li>
                    <li class="nav-item"><a class="nav-link" href="#tentang"><i class="fas fa-info-circle me-2"></i>Tentang</a></li>
                </ul>
                <div class="d-flex gap-2">
                    <a href="{{ route('login') }}" class="btn btn-login">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-signup">Sign Up</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="sparkle"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1>Digitalisasi UMKM<br>Indramayu</h1>
                        <p>Platform modern untuk memajukan produk lokal UMKM Indramayu melalui katalog online yang mudah, efisien, dan terpercaya.</p>
                        <div class="mt-4">
                            <a href="#produk" class="btn btn-explore"><i class="fas fa-rocket me-2"></i>Jelajahi Produk</a>
                            <a href="#tentang" class="btn-video">
                                <i class="fas fa-play-circle me-2"></i>Pelajari Lebih Lanjut
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="cartoon-container seller-cartoon">
                        <svg viewBox="0 0 400 400" xmlns="http://www.w3.org/2000/svg">
                            <!-- Background circle -->
                            <circle cx="200" cy="200" r="180" fill="rgba(255, 215, 0, 0.1)" />
                            
                            <!-- Stand/Table -->
                            <rect x="100" y="280" width="200" height="15" rx="5" fill="#d4af37" />
                            <rect x="110" y="295" width="180" height="8" rx="4" fill="#b8922b" />
                            
                            <!-- Products on stand -->
                            <rect x="120" y="250" width="40" height="30" rx="3" fill="#ff6b6b" />
                            <rect x="170" y="250" width="40" height="30" rx="3" fill="#4ecdc4" />
                            <rect x="220" y="250" width="40" height="30" rx="3" fill="#ffe66d" />
                            <rect x="270" y="250" width="25" height="30" rx="3" fill="#a8e6cf" />
                            
                            <!-- Body -->
                            <ellipse cx="200" cy="220" rx="50" ry="60" fill="#ffd700" />
                            
                            <!-- Arms -->
                            <ellipse cx="160" cy="220" rx="15" ry="45" fill="#ffed4e" transform="rotate(-20 160 220)" />
                            <ellipse cx="240" cy="220" rx="15" ry="45" fill="#ffed4e" transform="rotate(20 240 220)" />
                            
                            <!-- Hand pointing -->
                            <circle cx="145" cy="250" r="12" fill="#ffc966" />
                            <circle cx="255" cy="250" r="12" fill="#ffc966" />
                            
                            <!-- Head -->
                            <circle cx="200" cy="140" r="45" fill="#ffc966" />
                            
                            <!-- Hair -->
                            <path d="M 160 120 Q 170 100 200 100 Q 230 100 240 120" fill="#2c3e50" />
                            
                            <!-- Eyes -->
                            <circle cx="185" cy="140" r="8" fill="#2c3e50" />
                            <circle cx="215" cy="140" r="8" fill="#2c3e50" />
                            <circle cx="187" cy="138" r="3" fill="#fff" />
                            <circle cx="217" cy="138" r="3" fill="#fff" />
                            
                            <!-- Smile -->
                            <path d="M 180 155 Q 200 165 220 155" stroke="#2c3e50" stroke-width="3" fill="none" stroke-linecap="round" />
                            
                            <!-- Speech bubble -->
                            <ellipse cx="300" cy="100" rx="60" ry="35" fill="#fff" opacity="0.9" />
                            <polygon points="270,110 260,120 275,115" fill="#fff" opacity="0.9" />
                            <text x="300" y="95" text-anchor="middle" font-size="14" font-weight="bold" fill="#0a1628">Produk</text>
                            <text x="300" y="110" text-anchor="middle" font-size="14" font-weight="bold" fill="#0a1628">Berkualitas!</text>
                            
                            <!-- Stars decoration -->
                            <path d="M 80 80 L 85 90 L 95 92 L 87 100 L 90 110 L 80 105 L 70 110 L 73 100 L 65 92 L 75 90 Z" fill="#ffd700" opacity="0.6" />
                            <path d="M 320 180 L 323 186 L 330 187 L 325 192 L 327 199 L 320 195 L 313 199 L 315 192 L 310 187 L 317 186 Z" fill="#ffd700" opacity="0.6" />
                            
                            <!-- Money signs -->
                            <text x="100" y="200" font-size="24" fill="#ffd700" opacity="0.7">$</text>
                            <text x="310" y="240" font-size="24" fill="#ffd700" opacity="0.7">$</text>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kategori Produk -->
    <section id="kategori" class="container">
        <h2 class="section-title"><i class="fas fa-layer-group me-3"></i>Kategori Produk</h2>
        <div class="accordion" id="kategoriAccordion">
            @forelse($kategoris as $index => $kategori)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $index }}" aria-expanded="false" aria-controls="collapse{{ $index }}">
                            <i class="fas fa-folder-open me-3" style="color: var(--gold);"></i>
                            {{ $kategori->nama }}
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}" class="accordion-collapse collapse"
                        aria-labelledby="heading{{ $index }}" data-bs-parent="#kategoriAccordion">
                        <div class="accordion-body">
                            <!-- Gambar Kategori -->
                            <div class="mb-4">
                                <img src="{{ asset('storage/kategori/' . $kategori->gambar) }}"
                                    alt="{{ $kategori->nama }}" class="img-fluid rounded" style="max-height: 250px; width: 100%; object-fit: cover;">
                            </div>

                            <!-- Subkategori -->
                            @if($kategori->subkategoris->count())
                                <h5 class="text-white mb-3"><i class="fas fa-list me-2"></i>Subkategori:</h5>
                                <ul class="list-unstyled ms-3">
                                    @foreach($kategori->subkategoris as $sub)
                                        <li class="mb-2"><i class="fas fa-angle-right me-2" style="color: var(--gold);"></i>{{ $sub->nama }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            <!-- Produk -->
                            @if($kategori->produks->count())
                                <h5 class="mt-4 text-white mb-3"><i class="fas fa-box me-2"></i>Produk Terkait:</h5>
                                <div class="row g-3">
                                    @foreach($kategori->produks->take(3) as $produk)
                                        <div class="col-md-4">
                                            <div class="card h-100">
                                                <img src="{{ asset('storage/' . $produk->gambar) }}"
                                                    class="card-img-top" alt="{{ $produk->nama }}">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $produk->nama }}</h6>
                                                    <p class="card-text">Rp{{ number_format($produk->harga, 0, ',', '.') }}</p>
                                                    <a href="{{ route('pembeli.produk.show', $produk->id) }}"
                                                        class="btn btn-warning w-100">
                                                        <i class="fas fa-eye me-2"></i>Lihat Detail
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-secondary">Tidak ada produk pada kategori ini.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-secondary">Tidak ada kategori ditemukan.</p>
            @endforelse
        </div>
    </section>

    <!-- Produk Terbaru -->
    <section id="produk" class="container mt-5">
        <h2 class="section-title"><i class="fas fa-star me-3"></i>Produk Terbaru</h2>
        <div class="row g-4">
            @forelse($produks as $produk)
                <div class="col-md-4 col-lg-3">
                    <div class="card">
                        <img src="{{ asset('storage/' . $produk->gambar) }}" class="card-img-top" alt="{{ $produk->nama }}">
                        <div class="card-body">
                            <h5 class="card-title">{{ $produk->nama }}</h5>
                            <p class="card-text">Rp{{ number_format($produk->harga, 0, ',', '.') }}</p>
                            <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="btn btn-warning w-100">
                                <i class="fas fa-shopping-cart me-2"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-4x mb-3" style="color: #c0c0c0; opacity: 0.5;"></i>
                        <p class="text-secondary">Tidak ada produk tersedia saat ini.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Tentang Website -->
    <section id="tentang" class="container">
        <h2 class="section-title"><i class="fas fa-info-circle me-3"></i>Tentang Platform</h2>
        <div class="text-center mb-4 px-md-5">
            <p><i class="fas fa-check-circle me-2" style="color: var(--gold);"></i>Platform ini dibuat untuk memajukan UMKM di Indramayu melalui digitalisasi penjualan produk lokal.</p>
            <p><i class="fas fa-check-circle me-2" style="color: var(--gold);"></i>Dengan fitur katalog online, pembeli dan penjual dapat terhubung dengan mudah, efisien, dan aman.</p>
            <p><i class="fas fa-check-circle me-2" style="color: var(--gold);"></i>Kami berkomitmen untuk mengembangkan ekonomi lokal dan memberdayakan UMKM Indramayu.</p>
        </div>
        <div class="map-responsive">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3551.780347188987!2d108.28970287445466!3d-6.422555593568405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zNsKwMjUnMjEuMiJTIDEwOMKwMTcnMzIuMiJF!5e1!3m2!1sid!2sid!4v1747576331287!5m2!1sid!2sid" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <div class="mb-3">
                <i class="fas fa-store" style="font-size: 2.5rem; color: var(--gold);"></i>
            </div>
            <p class="mb-2">
                <i class="fas fa-copyright me-2"></i>{{ date('Y') }} UMKM Indramayu - Kelompok 7
            </p>
            <p class="mb-3">
                <i class="fas fa-map-marker-alt me-2"></i>Indramayu, Jawa Barat, Indonesia
            </p>
            <div class="mb-3">
                <a href="#" class="text-decoration-none mx-2" style="color: var(--gold); font-size: 1.5rem;">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="#" class="text-decoration-none mx-2" style="color: var(--gold); font-size: 1.5rem;">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="text-decoration-none mx-2" style="color: var(--gold); font-size: 1.5rem;">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="#" class="text-decoration-none mx-2" style="color: var(--gold); font-size: 1.5rem;">
                    <i class="fab fa-twitter"></i>
                </a>
            </div>
            <small>
                <i class="fas fa-code me-2"></i>Powered by Laravel & Bootstrap 5
            </small>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Smooth scroll
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Active nav on scroll
        window.addEventListener('scroll', () => {
            let current = '';
            const sections = document.querySelectorAll('section[id]');
            
            sections.forEach(section => {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.clientHeight;
                if (pageYOffset >= sectionTop - 100) {
                    current = section.getAttribute('id');
                }
            });

            document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                link.classList.remove('active');
                if (link.getAttribute('href') === `#${current}`) {
                    link.classList.add('active');
                }
            });
        });

        // Add sparkle animation on page load
        window.addEventListener('load', () => {
            const hero = document.querySelector('.hero');
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
                hero.appendChild(sparkle);
                
                setTimeout(() => sparkle.remove(), 2000);
            }, 500);
        });
    </script>
</body>
</html>
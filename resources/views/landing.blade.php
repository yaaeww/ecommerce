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
            overflow-x: hidden;
        }

        /* === NAVBAR RESPONSIVE === */
        .navbar-custom {
            background: linear-gradient(135deg, rgba(10, 22, 40, 0.98) 0%, rgba(26, 58, 95, 0.95) 100%);
            backdrop-filter: blur(15px);
            padding: 0.8rem 0;
            border-bottom: 2px solid var(--gold);
            box-shadow: 0 4px 30px rgba(255, 215, 0, 0.15);
            transition: all 0.3s ease;
        }

        .navbar-logo {
            height: 40px;
            width: auto;
            filter: brightness(1.2);
        }

        .navbar-brand {
            color: var(--gold) !important;
            font-weight: 700;
            font-size: 1.2rem;
            text-shadow: 0 0 10px rgba(255, 215, 0, 0.5);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .navbar-toggler {
            border: 2px solid rgba(255, 215, 0, 0.3) !important;
            padding: 0.25rem 0.5rem;
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255, 215, 0, 0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        .navbar-nav {
            text-align: center;
        }

        .nav-link {
            color: #e0e0e0 !important;
            font-weight: 500;
            padding: 8px 16px !important;
            margin: 5px 0;
            border-radius: 6px;
            transition: all 0.3s ease;
            display: inline-block;
            width: fit-content;
        }

        .nav-link:hover,
        .nav-link.active {
            color: var(--gold) !important;
            background: rgba(255, 215, 0, 0.1);
            transform: translateY(-2px);
        }

        .btn-login,
        .btn-signup {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            width: 100%;
            max-width: 150px;
            margin: 5px auto;
        }

        .btn-login {
            background: transparent;
            border: 2px solid var(--gold);
            color: var(--gold);
        }

        .btn-signup {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            color: var(--dark-blue);
            border: none;
            box-shadow: 0 4px 15px rgba(255, 215, 0, 0.3);
        }

        /* === HERO SECTION RESPONSIVE === */
        .hero {
            position: relative;
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--medium-blue) 70%, var(--light-blue) 100%);
            min-height: 85vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            padding: 80px 0 40px;
        }

        .hero h1 {
            font-size: clamp(2rem, 6vw, 4rem);
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1rem;
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            text-shadow: 0 0 30px rgba(255, 215, 0, 0.3);
        }

        .hero p {
            font-size: clamp(1rem, 3vw, 1.3rem);
            color: #c0c0c0;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .hero-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            align-items: center;
        }

        .btn-explore {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            color: var(--dark-blue);
            padding: 12px 30px;
            border: none;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 50px;
            transition: all 0.3s ease;
            box-shadow: 0 6px 20px rgba(255, 215, 0, 0.4);
            flex: 1;
            min-width: 200px;
        }

        .cartoon-container {
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
            transform: scale(0.9);
            transform-origin: center;
        }

        /* === KATEGORI SECTION === */
        #kategori,
        #produk {
            background: linear-gradient(135deg, rgba(10, 22, 40, 0.7) 0%, rgba(26, 58, 95, 0.8) 100%);
            padding: 30px 15px;
            margin: 20px auto;
            border-radius: 15px;
            border: 1px solid rgba(255, 215, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        .section-title {
            font-size: clamp(1.8rem, 5vw, 2.8rem);
            font-weight: 700;
            margin-bottom: 30px;
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
            bottom: -15px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 4px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
            border-radius: 2px;
        }

        /* === ACCORDION RESPONSIVE === */
        .accordion-item {
            background: linear-gradient(135deg, rgba(26, 58, 95, 0.6) 0%, rgba(42, 74, 127, 0.7) 100%);
            border: 1px solid rgba(255, 215, 0, 0.2);
            margin-bottom: 0.8rem;
            border-radius: 10px;
            overflow: hidden;
        }

        .accordion-button {
            font-weight: 600;
            color: var(--gold);
            background: linear-gradient(135deg, rgba(26, 58, 95, 0.8) 0%, rgba(42, 74, 127, 0.9) 100%);
            border: none;
            padding: 1rem 1.2rem;
            font-size: 0.95rem;
        }

        .accordion-button:not(.collapsed) {
            background: linear-gradient(135deg, rgba(255, 215, 0, 0.15) 0%, rgba(255, 237, 78, 0.1) 100%);
        }

        .accordion-body {
            background: linear-gradient(135deg, rgba(10, 22, 40, 0.8) 0%, rgba(26, 58, 95, 0.6) 100%);
            color: #c0c0c0;
            padding: 1.2rem;
        }

        /* === CARDS RESPONSIVE === */
        .card {
            background: linear-gradient(135deg, rgba(26, 58, 95, 0.7) 0%, rgba(42, 74, 127, 0.8) 100%);
            border: 1px solid rgba(255, 215, 0, 0.2);
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            height: 100%;
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 40px rgba(255, 215, 0, 0.3);
            border-color: var(--gold);
        }

        .card-img-top {
            height: 180px;
            object-fit: cover;
            width: 100%;
        }

        .card-body {
            padding: 1.2rem;
        }

        .card-title {
            color: var(--gold);
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 0.5rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            min-height: 3rem;
        }

        .card-text {
            color: var(--gold-light);
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 1rem;
        }

        .btn-warning {
            background: linear-gradient(135deg, var(--gold) 0%, var(--gold-light) 100%);
            border: none;
            color: var(--dark-blue);
            font-weight: 600;
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 0.9rem;
            width: 100%;
        }

        /* === PRODUK TERBARU GRID === */
        .row.g-4 {
            --bs-gutter-x: 1rem;
            --bs-gutter-y: 1rem;
        }

        .col-md-4.col-lg-3 {
            padding-left: calc(var(--bs-gutter-x) * .5);
            padding-right: calc(var(--bs-gutter-x) * .5);
        }

        /* === TENTANG SECTION === */
        #tentang {
            background: linear-gradient(135deg, rgba(10, 22, 40, 0.7) 0%, rgba(26, 58, 95, 0.8) 100%);
            padding: 40px 15px;
            border-radius: 15px;
            margin: 30px auto;
            border: 1px solid rgba(255, 215, 0, 0.2);
            backdrop-filter: blur(10px);
        }

        #tentang p {
            color: #c0c0c0;
            font-size: 1rem;
            line-height: 1.6;
            margin-bottom: 1rem;
        }

        .map-responsive {
            overflow: hidden;
            padding-bottom: 56.25%;
            position: relative;
            height: 0;
            width: 100%;
            max-width: 800px;
            border-radius: 10px;
            margin: 30px auto 0;
            border: 2px solid var(--gold);
            box-shadow: 0 0 30px rgba(255, 215, 0, 0.2);
        }

        .map-responsive iframe {
            left: 0;
            top: 0;
            height: 100%;
            width: 100%;
            position: absolute;
        }

        /* === FOOTER RESPONSIVE === */
        footer {
            background: linear-gradient(135deg, var(--dark-blue) 0%, var(--medium-blue) 100%);
            padding: 2rem 0;
            margin-top: 60px;
            border-top: 2px solid var(--gold);
        }

        footer p {
            color: var(--gold);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        footer small {
            color: #c0c0c0;
            font-size: 0.8rem;
        }

        /* === ANIMATIONS === */
        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(0.9);
            }

            50% {
                transform: translateY(-15px) scale(0.95);
            }
        }

        @keyframes sparkleFloat {

            0%,
            100% {
                transform: translateY(0) scale(1);
                opacity: 0;
            }

            50% {
                transform: translateY(-20px) scale(1.5);
                opacity: 1;
            }
        }

        .seller-cartoon {
            animation: float 4s ease-in-out infinite;
        }

        /* === RESPONSIVE BREAKPOINTS === */

        /* Extra small devices (phones, less than 576px) */
        @media (max-width: 575.98px) {
            .navbar-brand {
                font-size: 1rem;
            }

            .navbar-logo {
                height: 35px;
            }

            .hero {
                min-height: 75vh;
                padding: 70px 0 30px;
            }

            .hero h1 {
                text-align: center;
            }

            .hero p {
                text-align: center;
            }

            .hero-buttons {
                justify-content: center;
            }

            .cartoon-container {
                max-width: 300px;
                margin-top: 30px;
            }

            .accordion-button {
                font-size: 0.85rem;
                padding: 0.8rem 1rem;
            }

            .card-img-top {
                height: 150px;
            }

            .card-title {
                font-size: 0.9rem;
                min-height: 2.8rem;
            }

            #kategori,
            #produk,
            #tentang {
                margin: 15px;
                padding: 20px 10px;
                border-radius: 10px;
            }
        }

        /* Small devices (landscape phones, 576px and up) */
        @media (min-width: 576px) and (max-width: 767.98px) {
            .hero {
                min-height: 80vh;
            }

            .cartoon-container {
                max-width: 350px;
            }

            .btn-explore {
                min-width: 180px;
            }

            .card-img-top {
                height: 160px;
            }
        }

        /* Medium devices (tablets, 768px and up) */
        @media (min-width: 768px) and (max-width: 991.98px) {
            .navbar-brand {
                font-size: 1.1rem;
            }

            .hero {
                min-height: 85vh;
            }

            .cartoon-container {
                max-width: 400px;
            }

            .col-md-4.col-lg-3 {
                flex: 0 0 auto;
                width: 50%;
            }
        }

        /* Large devices (desktops, 992px and up) */
        @media (min-width: 992px) and (max-width: 1199.98px) {
            .cartoon-container {
                max-width: 450px;
            }

            .col-md-4.col-lg-3 {
                flex: 0 0 auto;
                width: 33.33333333%;
            }
        }

        /* Extra large devices (large desktops, 1200px and up) */
        @media (min-width: 1200px) {
            .cartoon-container {
                max-width: 500px;
            }

            .col-md-4.col-lg-3 {
                flex: 0 0 auto;
                width: 25%;
            }
        }

        /* === TOUCH DEVICE OPTIMIZATION === */
        @media (hover: none) and (pointer: coarse) {
            .card:hover {
                transform: none;
            }

            .nav-link:hover {
                transform: none;
            }

            .btn:hover {
                transform: none;
            }

            .card {
                cursor: pointer;
            }
        }

        /* === HIGH DPI SCREENS === */
        @media (-webkit-min-device-pixel-ratio: 2),
        (min-resolution: 192dpi) {
            .card-img-top {
                image-rendering: -webkit-optimize-contrast;
                image-rendering: crisp-edges;
            }
        }

        /* === PRINT STYLES === */
        @media print {

            .hero,
            .navbar,
            footer,
            .btn,
            .accordion-button::after {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
            }

            .card {
                break-inside: avoid;
                border: 1px solid #ddd !important;
                background: white !important;
                color: black !important;
            }
        }
    </style>
</head>

<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom fixed-top">
        <div class="container-fluid container-lg">
            <a class="navbar-brand" href="#">
                <img src="{{ asset('aset/finalisasi logo.png') }}" alt="Logo UMKM Indramayu" class="navbar-logo">
                <span class="d-none d-sm-inline">UMKM Indramayu</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center">
                    <li class="nav-item">
                        <a class="nav-link active" href="#"><i class="fas fa-home me-2"></i>Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#kategori"><i class="fas fa-th-large me-2"></i>Kategori</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#produk"><i class="fas fa-box me-2"></i>Produk</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#tentang"><i class="fas fa-info-circle me-2"></i>Tentang</a>
                    </li>
                </ul>
                <div class="d-flex flex-column flex-sm-row gap-2 gap-sm-3 justify-content-center mt-3 mt-lg-0">
                    <a href="{{ route('login') }}" class="btn btn-login">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-signup">
                        <i class="fas fa-user-plus me-2"></i>Sign Up
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6 order-2 order-lg-1">
                    <div class="hero-content text-center text-lg-start">
                        <h1>Digitalisasi UMKM<br>Indramayu</h1>
                        <p>Platform modern untuk memajukan produk lokal UMKM Indramayu melalui katalog online yang
                            mudah, efisien, dan terpercaya.</p>
                        <div class="hero-buttons">
                            <a href="#produk" class="btn btn-explore">
                                <i class="fas fa-rocket me-2"></i>Jelajahi Produk
                            </a>
                            <a href="#tentang" class="btn btn-login mt-2 mt-sm-0">
                                <i class="fas fa-play-circle me-2"></i>Pelajari Lebih
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6 order-1 order-lg-2">
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
                            <path d="M 180 155 Q 200 165 220 155" stroke="#2c3e50" stroke-width="3" fill="none"
                                stroke-linecap="round" />

                            <!-- Speech bubble -->
                            <ellipse cx="300" cy="100" rx="60" ry="35" fill="#fff" opacity="0.9" />
                            <polygon points="270,110 260,120 275,115" fill="#fff" opacity="0.9" />
                            <text x="300" y="95" text-anchor="middle" font-size="14" font-weight="bold"
                                fill="#0a1628">Produk</text>
                            <text x="300" y="110" text-anchor="middle" font-size="14" font-weight="bold"
                                fill="#0a1628">Berkualitas!</text>

                            <!-- Stars decoration -->
                            <path
                                d="M 80 80 L 85 90 L 95 92 L 87 100 L 90 110 L 80 105 L 70 110 L 73 100 L 65 92 L 75 90 Z"
                                fill="#ffd700" opacity="0.6" />
                            <path
                                d="M 320 180 L 323 186 L 330 187 L 325 192 L 327 199 L 320 195 L 313 199 L 315 192 L 310 187 L 317 186 Z"
                                fill="#ffd700" opacity="0.6" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Kategori Produk -->
    <section id="kategori" class="container-fluid container-lg">
        <h2 class="section-title"><i class="fas fa-layer-group me-2"></i>Kategori Produk</h2>
        <div class="accordion" id="kategoriAccordion">
            @forelse($kategoris as $index => $kategori)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $index }}">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $index }}" aria-expanded="false"
                            aria-controls="collapse{{ $index }}">
                            <i class="fas fa-folder-open me-2" style="color: var(--gold);"></i>
                            <span class="text-truncate">{{ $kategori->nama }}</span>
                        </button>
                    </h2>
                    <div id="collapse{{ $index }}" class="accordion-collapse collapse" aria-labelledby="heading{{ $index }}"
                        data-bs-parent="#kategoriAccordion">
                        <div class="accordion-body">
                            <!-- Gambar Kategori -->
                            <div class="mb-4">
                                <img src="{{ asset('storage/kategori/' . $kategori->gambar) }}" alt="{{ $kategori->nama }}"
                                    class="img-fluid rounded w-100" style="max-height: 250px; object-fit: cover;">
                            </div>

                            <!-- Subkategori -->
                            @if($kategori->subkategoris->count())
                                <h5 class="text-white mb-3"><i class="fas fa-list me-2"></i>Subkategori:</h5>
                                <div class="row">
                                    @foreach($kategori->subkategoris as $sub)
                                        <div class="col-6 col-md-4 mb-2">
                                            <div class="d-flex align-items-center">
                                                <i class="fas fa-angle-right me-2" style="color: var(--gold);"></i>
                                                <span>{{ $sub->nama }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            <!-- Produk -->
                            @if($kategori->produks->count())
                                <h5 class="mt-4 text-white mb-3"><i class="fas fa-box me-2"></i>Produk Terkait:</h5>
                                <div class="row g-3">
                                    @foreach($kategori->produks->take(3) as $produk)
                                        <div class="col-12 col-md-4">
                                            <div class="card h-100">
                                                <img src="{{ asset('storage/' . $produk->gambar) }}" class="card-img-top"
                                                    alt="{{ $produk->nama }}">
                                                <div class="card-body">
                                                    <h6 class="card-title">{{ $produk->nama }}</h6>
                                                    <p class="card-text">Rp{{ number_format($produk->harga, 0, ',', '.') }}</p>
                                                    <a href="{{ route('pembeli.produk.show', $produk->id) }}"
                                                        class="btn btn-warning">
                                                        <i class="fas fa-eye me-2"></i>Lihat Detail
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-center text-secondary py-3">Tidak ada produk pada kategori ini.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-center text-secondary py-5">Tidak ada kategori ditemukan.</p>
            @endforelse
        </div>
    </section>

    <!-- Produk Terbaru -->
    <section id="produk" class="container-fluid container-lg mt-4">
        <h2 class="section-title"><i class="fas fa-star me-2"></i>Produk Terbaru</h2>
        <div class="row g-4 justify-content-center">
            @forelse($produks as $produk)
                <div class="col-6 col-sm-6 col-md-4 col-lg-3">
                    <div class="card">
                        <img src="{{ asset('storage/' . $produk->gambar) }}" class="card-img-top" alt="{{ $produk->nama }}"
                            loading="lazy">
                        <div class="card-body">
                            <h5 class="card-title">{{ Str::limit($produk->nama, 40) }}</h5>
                            <p class="card-text">Rp{{ number_format($produk->harga, 0, ',', '.') }}</p>
                            <a href="{{ route('pembeli.produk.show', $produk->id) }}" class="btn btn-warning">
                                <i class="fas fa-shopping-cart me-2"></i>Lihat Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-box-open fa-3x mb-3" style="color: #c0c0c0; opacity: 0.5;"></i>
                        <p class="text-secondary">Tidak ada produk tersedia saat ini.</p>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Tentang Website -->
    <section id="tentang" class="container-fluid container-lg">
        <h2 class="section-title"><i class="fas fa-info-circle me-2"></i>Tentang Platform</h2>
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <div class="text-center mb-4">
                    <p class="mb-3">
                        <i class="fas fa-check-circle me-2" style="color: var(--gold);"></i>
                        Platform ini dibuat untuk memajukan UMKM di Indramayu melalui digitalisasi penjualan produk
                        lokal.
                    </p>
                    <p class="mb-3">
                        <i class="fas fa-check-circle me-2" style="color: var(--gold);"></i>
                        Dengan fitur katalog online, pembeli dan penjual dapat terhubung dengan mudah, efisien, dan
                        aman.
                    </p>
                    <p class="mb-4">
                        <i class="fas fa-check-circle me-2" style="color: var(--gold);"></i>
                        Kami berkomitmen untuk mengembangkan ekonomi lokal dan memberdayakan UMKM Indramayu.
                    </p>
                </div>
                <div class="map-responsive">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3551.780347188987!2d108.28970287445466!3d-6.422555593568405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zNsKwMjUnMjEuMiJTIDEwOMKwMTcnMzIuMiJF!5e1!3m2!1sid!2sid!4v1747576331287!5m2!1sid!2sid"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="container text-center">
            <div class="mb-3">
                <i class="fas fa-store" style="font-size: 2rem; color: var(--gold);"></i>
            </div>
            <p class="mb-2">
                <i class="fas fa-copyright me-2"></i>{{ date('Y') }} UMKM Indramayu - Kelompok 7
            </p>
            <p class="mb-3">
                <i class="fas fa-map-marker-alt me-2"></i>Indramayu, Jawa Barat, Indonesia
            </p>
            <div class="mb-3 d-flex justify-content-center gap-4">
                <a href="#" class="text-decoration-none" style="color: var(--gold); font-size: 1.3rem;">
                    <i class="fab fa-facebook"></i>
                </a>
                <a href="#" class="text-decoration-none" style="color: var(--gold); font-size: 1.3rem;">
                    <i class="fab fa-instagram"></i>
                </a>
                <a href="#" class="text-decoration-none" style="color: var(--gold); font-size: 1.3rem;">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="#" class="text-decoration-none" style="color: var(--gold); font-size: 1.3rem;">
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
        // Smooth scroll untuk semua anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                const href = this.getAttribute('href');
                if (href !== '#') {
                    e.preventDefault();
                    const target = document.querySelector(href);
                    if (target) {
                        window.scrollTo({
                            top: target.offsetTop - 80,
                            behavior: 'smooth'
                        });

                        // Update URL tanpa reload
                        history.pushState(null, null, href);
                    }
                }
            });
        });

        // Active nav on scroll dengan throttle
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimeout);
            scrollTimeout = setTimeout(() => {
                let current = '';
                const sections = document.querySelectorAll('section[id]');
                const scrollPos = window.pageYOffset + 100;

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    if (scrollPos >= sectionTop && scrollPos < sectionTop + sectionHeight) {
                        current = section.getAttribute('id');
                    }
                });

                document.querySelectorAll('.navbar-nav .nav-link').forEach(link => {
                    link.classList.remove('active');
                    if (link.getAttribute('href') === `#${current}`) {
                        link.classList.add('active');
                    }
                });
            }, 100);
        });

        // Touch device detection
        const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        if (isTouchDevice) {
            document.body.classList.add('touch-device');
        }

        // Lazy loading untuk images
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                img.src = img.dataset.src;
            });
        }

        // Prevent accordion from closing when clicking inside
        document.querySelectorAll('.accordion-body').forEach(body => {
            body.addEventListener('click', (e) => {
                e.stopPropagation();
            });
        });

        // Initialize on load
        document.addEventListener('DOMContentLoaded', () => {
            // Trigger scroll to set initial active nav
            window.dispatchEvent(new Event('scroll'));

            // Add animation class for cartoon
            const cartoon = document.querySelector('.seller-cartoon');
            if (cartoon) {
                cartoon.classList.add('animated');
            }
        });

        // Handle orientation change
        window.addEventListener('orientationchange', () => {
            setTimeout(() => {
                window.dispatchEvent(new Event('resize'));
            }, 100);
        });
    </script>
</body>

</html>
@extends('layouts.public')

@section('title', 'Tentang Kami — Juragan Pelem | Kisah, Misi & Dedikasi untuk Petani Indramayu')
@section('meta_description', 'Pelajari bagaimana Juragan Pelem mentransformasi ekosistem pertanian mangga dan memberdayakan puluhan UMKM lokal di Kabupaten Indramayu.')

@section('content')
<!-- Hero Section (Asymmetric Storytelling) -->
<section class="relative pt-16 pb-20 lg:pt-20 lg:pb-28 bg-gradient-to-b from-white via-brand-cream/40 to-brand-cream border-b border-slate-100 overflow-hidden">
    <!-- Ambient Glow -->
    <div class="absolute top-0 right-0 w-96 h-96 bg-brand-green-light/10 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute bottom-0 left-10 w-80 h-80 bg-brand-amber/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative">
        <div class="max-w-3xl space-y-6">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-brand-slate leading-[1.12]">
                Menghubungkan Manisnya <br>
                <span class="text-brand-green">Mangga Indramayu</span> ke Seantero Nusantara.
            </h1>

            <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                Juragan Pelem lahir dari sebuah mimpi sederhana: memastikan keringat petani mangga di Indramayu dihargai dengan layak, sambil mengantarkan buah kualitas terbaik langsung ke meja makan Anda tanpa perantara.
            </p>
        </div>

        <!-- Key Impact Metrics (4-Column Bento Stat) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mt-14">
            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-sm text-center sm:text-left">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-brand-green flex items-center justify-center text-lg mb-3 mx-auto sm:mx-0">
                    <i class="fas fa-tree"></i>
                </div>
                <p class="text-3xl font-extrabold text-brand-slate font-display tracking-tight">120+ Ha</p>
                <p class="text-xs text-slate-500 font-semibold mt-1">Kebun Binaan Terintegrasi</p>
            </div>

            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-sm text-center sm:text-left">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-brand-amber flex items-center justify-center text-lg mb-3 mx-auto sm:mx-0">
                    <i class="fas fa-store"></i>
                </div>
                <p class="text-3xl font-extrabold text-brand-slate font-display tracking-tight">50+ Mitra</p>
                <p class="text-xs text-slate-500 font-semibold mt-1">UMKM Pangan & Olahan Lokal</p>
            </div>

            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-sm text-center sm:text-left">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg mb-3 mx-auto sm:mx-0">
                    <i class="fas fa-box-check"></i>
                </div>
                <p class="text-3xl font-extrabold text-brand-slate font-display tracking-tight">15.000+</p>
                <p class="text-xs text-slate-500 font-semibold mt-1">Kotak Buah Terkirim Segar</p>
            </div>

            <div class="bento-card p-6 bg-white border border-slate-200/80 shadow-sm text-center sm:text-left">
                <div class="w-10 h-10 rounded-xl bg-teal-50 text-teal-600 flex items-center justify-center text-lg mb-3 mx-auto sm:mx-0">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
                <p class="text-3xl font-extrabold text-brand-slate font-display tracking-tight">85%</p>
                <p class="text-xs text-slate-500 font-semibold mt-1">Nilai Transaksi ke Petani</p>
            </div>
        </div>
    </div>
</section>

<!-- The Problem & Solution (Mengapa Juragan Pelem Hadir) -->
<section class="py-20 bg-white border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-12 gap-12 items-center">
            
            <div class="lg:col-span-6 space-y-6">
                <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">
                    Tantangan & Solusi
                </p>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-slate tracking-tight">
                    Memutus Rantai Tengkulak, Mengembalikan Nilai pada Petani.
                </h2>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    Indramayu dikenal sebagai <em>Kota Mangga</em> yang menyumbang persentase besar produksi mangga nasional. Namun selama beberapa dekade, tata niaga tradisional kerap merugikan petani lokal karena harga anjlok saat panen raya dan buah dipetik terlalu dini lalu diperam zat kimia.
                </p>
                <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                    <strong>Juragan Pelem</strong> hadir dengan pendekatan teknologi agro-commerce: menjamin kepastian pasar, mengedepankan petik matang pohon alami, dan memfasilitasi UMKM mengolah mangga menjadi produk bernilai tinggi seperti sirup, dodol, dan keripik premium.
                </p>

                <div class="pt-2 flex flex-col sm:flex-row gap-4">
                    <div class="flex items-center gap-2 text-xs font-bold text-brand-slate">
                        <i class="fas fa-check-circle text-brand-green text-sm"></i> Petik Matang Alami
                    </div>
                    <div class="flex items-center gap-2 text-xs font-bold text-brand-slate">
                        <i class="fas fa-check-circle text-brand-green text-sm"></i> Sertifikasi Mutu P-IRT
                    </div>
                    <div class="flex items-center gap-2 text-xs font-bold text-brand-slate">
                        <i class="fas fa-check-circle text-brand-green text-sm"></i> Hilirisasi Olahan UMKM
                    </div>
                </div>
            </div>

            <!-- Visual Split Card -->
            <div class="lg:col-span-6 space-y-4">
                <div class="bento-card p-6 bg-slate-50 border border-slate-200">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-red-100 text-red-600 flex items-center justify-center shrink-0 text-base font-bold">
                            <i class="fas fa-xmark"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Tata Niaga Tradisional Lama</h3>
                            <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                                Petani tidak memiliki akses harga pasar riil, buah diperam dengan karbit agar cepat kuning, dan risiko pembusukan tinggi saat panen melimpah tanpa pengolahan hilir.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bento-card p-6 bg-emerald-50/70 border border-emerald-200 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl bg-brand-green text-white flex items-center justify-center shrink-0 text-base font-bold">
                            <i class="fas fa-check"></i>
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-brand-slate">Ekosistem Digital Juragan Pelem</h3>
                            <p class="text-xs text-slate-600 mt-1 leading-relaxed">
                                Pemesanan instan sebelum panen, seleksi standar kemanisan 16-18° Brix, kemasan busa berperedam, dan penyerapan hasil kebun oleh sentra UMKM binaan.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- 4 Nilai Utama & Standar Kualitas -->
<section class="py-20 bg-brand-cream border-b border-slate-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">
                Prinsip Integritas
            </p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-slate tracking-tight">
                4 Pilar Kualitas Juragan Pelem
            </h2>
            <p class="text-slate-600 text-sm mt-2">Fondasi yang kami jaga ketat di setiap kotak kiriman Anda.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Nilai 1 -->
            <div class="bento-card p-7 space-y-4 bg-white">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-brand-green flex items-center justify-center text-xl">
                    <i class="fas fa-leaf"></i>
                </div>
                <h3 class="text-lg font-bold text-brand-slate">100% Bebas Karbit</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Kami hanya mengirimkan buah yang matang alami di pohon. Tidak ada bahan pematang buatan demi menjaga kesehatan dan keaslian rasa.
                </p>
            </div>

            <!-- Nilai 2 -->
            <div class="bento-card p-7 space-y-4 bg-white">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-brand-amber flex items-center justify-center text-xl">
                    <i class="fas fa-award"></i>
                </div>
                <h3 class="text-lg font-bold text-brand-slate">Grading Selektif</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Setiap buah melewati proses sortir manual: kulit mulus, bobot ideal, dan uji sampel kadar manis dengan alat refractometer optik.
                </p>
            </div>

            <!-- Nilai 3 -->
            <div class="bento-card p-7 space-y-4 bg-white">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-600 flex items-center justify-center text-xl">
                    <i class="fas fa-box-open"></i>
                </div>
                <h3 class="text-lg font-bold text-brand-slate">Packing Proteksi Tinggi</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Kemasan individual foam net tebal dan partisi kardus ventilasi dirancang khusus untuk melindungi buah selama perjalanan ekspedisi.
                </p>
            </div>

            <!-- Nilai 4 -->
            <div class="bento-card p-7 space-y-4 bg-white">
                <div class="w-12 h-12 rounded-2xl bg-teal-100 text-teal-600 flex items-center justify-center text-xl">
                    <i class="fas fa-rotate-left"></i>
                </div>
                <h3 class="text-lg font-bold text-brand-slate">Garansi Retur 100%</h3>
                <p class="text-slate-600 text-xs leading-relaxed">
                    Jika buah tiba dalam kondisi rusak atau busuk, kami ganti baru atau kembalikan dana secara penuh tanpa prosedur yang rumit.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Sentra Kebun & Lokasi Indramayu (Peta Interaktif) -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <p class="text-xs uppercase tracking-wider font-bold text-brand-green mb-1">
                Geografi & Sentra Kebun
            </p>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-slate tracking-tight">
                Pusat Budidaya & Gudang Sortir Kami
            </h2>
            <p class="text-slate-600 text-sm mt-2">Lokasi strategis di jantung lumbung pertanian mangga Kabupaten Indramayu.</p>
        </div>

        <div class="grid lg:grid-cols-12 gap-8 items-center">
            
            <!-- Sentra List (5 cols) -->
            <div class="lg:col-span-5 space-y-4">
                <div class="bento-card p-5 bg-brand-cream/40 border border-slate-200 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-brand-green text-white flex items-center justify-center shrink-0 font-bold">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Krasak & Jatibarang</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Sentra utama Mangga Gedong Gincu Super & Harum Manis Matang Pohon.</p>
                    </div>
                </div>

                <div class="bento-card p-5 bg-brand-cream/40 border border-slate-200 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-brand-amber text-white flex items-center justify-center shrink-0 font-bold">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Cikedung & Terisi</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Kawasan perkebunan luas dataran subur dengan bibit varietas unggulan.</p>
                    </div>
                </div>

                <div class="bento-card p-5 bg-brand-cream/40 border border-slate-200 flex items-start gap-4">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 font-bold">
                        <i class="fas fa-location-dot"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-sm">Lelea & Lohbener</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Klaster pengolahan UMKM hilirisasi sirup mangga, manisan, dan keripik.</p>
                    </div>
                </div>

                <div class="p-4 bg-emerald-50 rounded-2xl border border-emerald-200 text-xs text-brand-green flex items-center gap-3">
                    <i class="fas fa-warehouse text-xl"></i>
                    <div>
                        <p class="font-bold">Gudang Sortir & Ekspedisi Cepat</p>
                        <p class="text-[11px] text-slate-600">Jl. Raya Krasak No. 45, Jatibarang, Indramayu</p>
                    </div>
                </div>
            </div>

            <!-- Embedded Map (7 cols) -->
            <div class="lg:col-span-7">
                <div class="rounded-3xl overflow-hidden shadow-xl border border-slate-200 aspect-[16/10] bg-slate-100 relative">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3551.780347188987!2d108.28970287445466!3d-6.422555593568405!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zNsKwMjUnMjEuMiJTIDEwOMKwMTcnMzIuMiJF!5e1!3m2!1sid!2sid!4v1747576331287!5m2!1sid!2sid" 
                        width="100%" 
                        height="100%" 
                        style="border:0;" 
                        allowfullscreen="" 
                        loading="lazy" 
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Kemitraan & Ajakan Kolaborasi (Bottom CTA) -->
<section class="py-16 bg-gradient-to-br from-brand-green-dark via-brand-green to-emerald-800 text-white relative overflow-hidden">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-6 relative z-10">
        <p class="text-xs uppercase tracking-wider font-bold text-amber-300">
            Mari Bertumbuh Bersama
        </p>
        <h2 class="text-3xl sm:text-5xl font-extrabold tracking-tight text-white leading-tight">
            Ingin Bermitra Sebagai Petani <br>atau Pelaku UMKM Indramayu?
        </h2>
        <p class="text-emerald-100 text-base max-w-xl mx-auto leading-relaxed">
            Daftarkan kebun atau toko UMKM Anda sekarang dan jangkau ribuan pelanggan di seluruh Indonesia dengan platform digital resmi Juragan Pelem.
        </p>
        <div class="flex flex-wrap justify-center items-center gap-4 pt-4">
            <a href="{{ route('register') }}" class="px-8 py-3.5 bg-brand-amber hover:bg-amber-500 text-slate-900 font-bold rounded-xl text-base transition shadow-xl hover:scale-105">
                <i class="fas fa-handshake mr-2"></i> Gabung Jadi Mitra Penjual
            </a>
            <a href="{{ route('kategori') }}" class="px-8 py-3.5 bg-white/10 hover:bg-white/20 text-white font-bold rounded-xl text-base border border-white/30 backdrop-blur transition">
                Jelajahi Produk Kami
            </a>
        </div>
    </div>
</section>
@endsection

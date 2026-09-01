<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Label Pengiriman - #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Libre+Barcode+128&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @page {
            size: 100mm 150mm;
            margin: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px 0;
            color: #0f172a;
        }

        .barcode-text {
            font-family: 'Libre Barcode 128', cursive;
            font-size: 48px;
            line-height: 1;
            letter-spacing: 2px;
        }

        .mono-text {
            font-family: 'Space Mono', monospace;
        }

        .thermal-label {
            width: 100mm;
            min-height: 145mm;
            background: #ffffff;
            margin: 0 auto;
            border: 2px solid #0f172a;
            box-sizing: border-box;
            position: relative;
        }

        @media print {
            body {
                background: none;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .thermal-label {
                border: 2px solid #000000;
                margin: 0;
                width: 100%;
                height: 100%;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>

    <!-- Floating Action Bar (Non-Printable) -->
    <div class="no-print max-w-[100mm] mx-auto mb-4 flex items-center justify-between gap-3 px-2">
        <a href="{{ route('penjual.pesanan.create', $order->id) }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-white border border-slate-300 text-slate-700 font-bold text-xs hover:bg-slate-50 transition shadow-sm">
            <i class="fas fa-arrow-left"></i> Kembali ke Pesanan
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs transition shadow-md hover:shadow-lg">
            <i class="fas fa-print"></i> Cetak Label Thermal (A6)
        </button>
    </div>

    <!-- Thermal Label Container -->
    <div class="thermal-label shadow-xl print:shadow-none p-3.5 flex flex-col justify-between text-xs">
        
        <!-- Top Section: Header & Courier Info -->
        <div>
            <!-- Header Bar -->
            <div class="flex items-center justify-between border-b-2 border-black pb-2.5 mb-2">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-black text-white flex items-center justify-center font-black text-sm">
                        JP
                    </div>
                    <div>
                        <span class="block font-black text-sm tracking-tight leading-none">JuraganPelem</span>
                        <span class="block text-[8px] font-bold uppercase tracking-widest text-slate-600 mt-0.5">Agro Fresh Chain</span>
                    </div>
                </div>
                <div class="text-right">
                    <span class="inline-block px-2.5 py-1 bg-black text-white font-black text-xs uppercase tracking-wider rounded">
                        {{ $order->kurir_ekspedisi ?: ($order->kurir ?: 'J&T CARGO') }}
                    </span>
                    <span class="block text-[8px] font-extrabold text-slate-700 mt-0.5">STANDARD DELIVERY</span>
                </div>
            </div>

            <!-- Barcode & Tracking Number Block -->
            <div class="border-2 border-black rounded-lg p-2.5 text-center mb-2 bg-slate-50/50">
                <p class="barcode-text text-black select-none">
                    *ORD{{ str_pad($order->id, 7, '0', STR_PAD_LEFT) }}*
                </p>
                <div class="flex items-center justify-between border-t border-black/20 pt-1 mt-1">
                    <span class="text-[9px] font-bold text-slate-600 uppercase">NO. RESI:</span>
                    <span class="mono-text font-extrabold text-sm tracking-wider text-black">
                        {{ $order->no_resi ?: ($order->resi_pengiriman ?: 'JP-' . str_pad($order->id, 8, '0', STR_PAD_LEFT)) }}
                    </span>
                </div>
                <div class="flex items-center justify-between text-[9px] text-slate-600 mt-0.5">
                    <span>ORDER ID: #ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</span>
                    <span>TGL: {{ $order->created_at ? $order->created_at->format('d/m/Y H:i') : date('d/m/Y') }}</span>
                </div>
            </div>

            <!-- Recipient & Sender Address Section -->
            <div class="grid grid-cols-1 gap-2 border-b-2 border-black pb-2 mb-2">
                
                <!-- Recipient (Penerima) -->
                <div class="bg-black/5 p-2.5 rounded-lg border border-black/30">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-[9px] font-black uppercase tracking-wider bg-black text-white px-1.5 py-0.5 rounded">
                            PENERIMA (BUYER)
                        </span>
                        <span class="font-bold text-[10px] text-black">
                            <i class="fab fa-whatsapp"></i> {{ $order->phone }}
                        </span>
                    </div>
                    <p class="font-black text-sm text-black leading-tight">{{ $order->name }}</p>
                    <p class="text-[11px] font-medium text-slate-900 leading-snug mt-1 whitespace-pre-wrap">{{ $order->alamat }}</p>
                </div>

                <!-- Sender (Pengirim) -->
                <div class="p-2 rounded-lg border border-black/20 bg-white">
                    <div class="flex items-center justify-between mb-0.5">
                        <span class="text-[8px] font-black uppercase tracking-wider text-slate-600">
                            PENGIRIM (SELLER / KEBUN)
                        </span>
                        <span class="text-[9px] font-bold text-slate-800">
                            {{ $order->produk->umkm->no_telp ?? ($order->produk->umkm->user->phone ?? '0812-xxxx-xxxx') }}
                        </span>
                    </div>
                    <p class="font-black text-xs text-black">{{ $order->produk->umkm->nama_toko ?? 'Kebun Mangga Mitra' }}</p>
                    <p class="text-[9px] text-slate-600 truncate">{{ $order->produk->umkm->alamat ?? 'Sentra Mangga Gedong Gincu, Indramayu, Jawa Barat' }}</p>
                </div>
            </div>

            <!-- Commodity & Package Breakdown -->
            <div class="border border-black rounded-lg p-2 mb-2 bg-white">
                <div class="flex items-center justify-between border-b border-black/20 pb-1 mb-1 font-bold text-[10px]">
                    <span>Rincian Produk Mangga</span>
                    <span>Jumlah / Qty</span>
                </div>
                <div class="flex items-start justify-between text-[11px] py-0.5">
                    <div class="font-bold text-black pr-2 leading-tight">
                        {{ $order->produk->nama ?? 'Mangga Segar Indramayu' }}
                        <span class="block text-[9px] font-normal text-slate-600">Grade Super Indramayu Fresh Harvest</span>
                    </div>
                    <div class="mono-text font-black text-xs shrink-0">
                        {{ $order->jumlah }}x ({{ $order->jumlah * (($order->produk->berat_gram ?? 1000) / 1000) }} Kg)
                    </div>
                </div>
            </div>

        </div>

        <!-- Bottom Warning & Seal -->
        <div>
            <!-- Fragile Warning Banner -->
            <div class="border-2 border-dashed border-black rounded-lg p-2 bg-black text-white text-center mb-2">
                <div class="flex items-center justify-center gap-2 font-black text-[11px] tracking-wider uppercase">
                    <i class="fas fa-wine-glass-crack"></i>
                    <span>FRAGILE - MAKANAN / BUAH SEGAR</span>
                    <i class="fas fa-apple-whole"></i>
                </div>
                <p class="text-[8px] font-bold text-slate-200 mt-0.5">
                    JANGAN DIBANTING / JANGAN DITINDIH BEBAN BERAT / HINDARI PANAS LANGSUNG
                </p>
            </div>

            <!-- Footer Small Barcode & Security Text -->
            <div class="flex items-center justify-between text-[8px] font-bold text-slate-500 border-t border-black/20 pt-1">
                <span>E-Commerce Juragan Pelem Indramayu</span>
                <span>LUNAS (MIDTRANS PAID)</span>
                <span>SOP LOGISTIK v2.0</span>
            </div>
        </div>

    </div>

</body>
</html>

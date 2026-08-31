@extends('layouts.public')
@section('title', 'Pesanan Dikirim & Diterima')

@section('content')
    @php use App\Models\Ulasan; @endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-10 text-center sm:text-left flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-gray-200 pb-6">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center justify-center sm:justify-start gap-3">
                <i class="fas fa-truck text-indigo-600"></i>
                Pesanan Dikirim & Diterima
            </h1>
            <p class="mt-2 text-sm text-gray-600">Lacak pengiriman dan riwayat pesanan yang sudah Anda terima.</p>
        </div>
        <a href="{{ route('pembeli.profile.show') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i>
            Kembali ke Profil
        </a>
    </div>

    @php
        $dikirimOrders = $orders->where('status_pesanan', 'dikirim');
        $diterimaOrders = $orders->where('status_pesanan', 'diterima');
    @endphp

    <!-- Pesanan Sedang Dikirim -->
    <div class="mb-12">
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-shipping-fast text-blue-500"></i>
            Pesanan Sedang Dikirim
        </h2>

        <div class="space-y-6">
            @forelse($dikirimOrders as $order)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
                    
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6 border-b border-gray-100 pb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-receipt text-indigo-500"></i>
                                    {{ $order->invoice ?? 'INV-' . $order->id }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-calendar-alt mr-1.5"></i>
                                    {{ $order->created_at->format('d M Y') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 self-start">
                                <i class="fas fa-truck mr-1.5"></i> {{ ucfirst($order->status_pesanan) }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center mb-6">
                            <span class="text-sm font-medium text-gray-500">Total Belanja</span>
                            <span class="text-lg font-bold text-indigo-700">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                        </div>

                        <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h4 class="text-sm font-semibold text-gray-900 flex items-center gap-2 mb-1">
                                    <i class="fas fa-box-open text-indigo-500"></i>
                                    Paket telah sampai?
                                </h4>
                                <p class="text-xs text-gray-500">Konfirmasi jika Anda sudah menerima pesanan ini dengan baik.</p>
                            </div>
                            <form action="{{ route('pembeli.pesanan.updateStatus', $order->id) }}" method="POST"
                                onsubmit="return confirm('Yakin ingin mengonfirmasi pesanan ini sudah diterima?')">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors">
                                    <i class="fas fa-check-circle mr-2"></i>Konfirmasi Diterima
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-truck-loading text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-1">Tidak ada pesanan dikirim</h3>
                    <p class="text-sm text-gray-500">Belum ada pesanan Anda yang sedang dalam proses pengiriman saat ini.</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Divider -->
    <div class="relative py-8">
        <div class="absolute inset-0 flex items-center" aria-hidden="true">
            <div class="w-full border-t border-gray-200"></div>
        </div>
        <div class="relative flex justify-center">
            <span class="bg-white px-3 text-gray-400">
                <i class="fas fa-star text-indigo-300 text-lg"></i>
            </span>
        </div>
    </div>

    <!-- Pesanan Diterima -->
    <div>
        <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
            <i class="fas fa-check-circle text-green-500"></i>
            Pesanan Diterima
        </h2>

        <div class="space-y-8">
            @forelse($diterimaOrders as $order)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden relative">
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-green-500"></div>
                    
                    <div class="p-6 sm:p-8 border-b border-gray-100">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-receipt text-indigo-500"></i>
                                    {{ $order->invoice ?? 'INV-' . $order->id }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-calendar-check mr-1.5 text-green-500"></i>
                                    Diterima pada: {{ $order->updated_at->format('d M Y H:i') }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200 self-start">
                                <i class="fas fa-check mr-1.5"></i> Selesai
                            </span>
                        </div>
                        
                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                            <span class="text-sm font-medium text-gray-500">Total Belanja</span>
                            <span class="text-base font-bold text-gray-900">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Products in Order -->
                    <div class="p-6 sm:p-8 bg-gray-50/50">
                        <h4 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-box text-indigo-400"></i>
                            Produk dalam pesanan
                        </h4>
                        
                        <div class="space-y-6">
                            @if($order->produks && $order->produks->count())
                                @foreach($order->produks as $produk)
                                    @php
                                        $sudahDinilai = Ulasan::where('users_id', auth()->id())
                                            ->where('orders_id', $order->id)
                                            ->where('produks_id', $produk->id)
                                            ->exists();
                                    @endphp

                                    <div class="bg-white border border-gray-200 rounded-xl p-4 sm:p-6 shadow-sm">
                                        <div class="flex items-start gap-4 mb-4">
                                            <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-box-open text-indigo-500 text-xl"></i>
                                            </div>
                                            <div>
                                                <h5 class="text-base font-semibold text-gray-900">{{ $produk->nama }}</h5>
                                                <p class="text-sm text-gray-500 mt-1">Berikan ulasan untuk membantu pembeli lain.</p>
                                            </div>
                                        </div>

                                        @if(!$sudahDinilai)
                                            <div class="mt-4 pt-4 border-t border-gray-100">
                                                <form action="{{ route('pembeli.rating.store') }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="orders_id" value="{{ $order->id }}">
                                                    <input type="hidden" name="produks_id" value="{{ $produk->id }}">

                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Rating Produk</label>
                                                        <div class="flex flex-row-reverse justify-end items-center gap-1">
                                                            @for($i = 5; $i >= 1; $i--)
                                                                <input type="radio" id="star{{ $i }}-{{ $produk->id }}" name="bintang" value="{{ $i }}" class="peer hidden" required>
                                                                <label for="star{{ $i }}-{{ $produk->id }}" class="text-gray-300 peer-hover:text-yellow-400 peer-checked:text-yellow-400 hover:text-yellow-400 cursor-pointer transition-colors text-2xl">
                                                                    ★
                                                                </label>
                                                            @endfor
                                                        </div>
                                                    </div>

                                                    <div class="mb-4">
                                                        <label class="block text-sm font-medium text-gray-700 mb-2">Ulasan Lengkap</label>
                                                        <textarea name="ulasan" rows="3" required class="block w-full border border-gray-300 rounded-lg shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm resize-none" placeholder="Bagikan pengalaman Anda menggunakan produk ini..."></textarea>
                                                    </div>

                                                    <button type="submit" class="inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                                        <i class="fas fa-paper-plane mr-2"></i>Kirim Ulasan
                                                    </button>
                                                </form>
                                            </div>
                                        @else
                                            <div class="mt-4 bg-green-50 rounded-lg p-3 flex items-start gap-3 border border-green-100">
                                                <i class="fas fa-check-circle text-green-500 mt-0.5"></i>
                                                <p class="text-sm text-green-800 font-medium">Terima kasih, Anda sudah memberikan ulasan untuk produk ini.</p>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="bg-yellow-50 rounded-lg p-4 flex items-start gap-3 border border-yellow-200">
                                    <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                                    <p class="text-sm text-yellow-800">Tidak ada data produk yang terkait dengan pesanan ini.</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-clipboard-check text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 mb-1">Tidak ada pesanan selesai</h3>
                    <p class="text-sm text-gray-500">Belum ada riwayat pesanan yang telah Anda terima.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
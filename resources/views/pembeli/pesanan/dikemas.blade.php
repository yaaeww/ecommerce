@extends('layouts.public')
@section('title', 'Pesanan Dikemas')
@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8 text-center sm:text-left flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center justify-center sm:justify-start gap-3">
                <i class="fas fa-box text-indigo-600"></i>
                Pesanan Dikemas
            </h1>
            <p class="mt-2 text-sm text-gray-600">Pesanan Anda sedang dipersiapkan oleh penjual.</p>
        </div>
        <a href="{{ route('pembeli.pesanan.index') }}" class="inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
            <i class="fas fa-arrow-left mr-2 text-gray-400"></i>
            Kembali
        </a>
    </div>

    <!-- Orders List -->
    <div class="space-y-6">
        @if($orders->count() > 0)
            @foreach($orders as $order)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-300 relative">
                    <!-- Decorator Line -->
                    <div class="absolute left-0 top-0 bottom-0 w-1 bg-indigo-500"></div>
                    
                    <div class="p-6 sm:p-8">
                        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-start gap-4 mb-6 border-b border-gray-100 pb-4">
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                                    <i class="fas fa-receipt text-indigo-500"></i>
                                    Invoice: {{ $order->invoice ?? 'INV-' . $order->id }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    <i class="fas fa-calendar-alt mr-1.5"></i>
                                    {{ $order->created_at ? $order->created_at->format('d-m-Y H:i') : '-' }}
                                </p>
                            </div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 border border-blue-200 self-start">
                                <i class="fas fa-box mr-1.5"></i> {{ ucfirst(str_replace('_', ' ', $order->status_pesanan ?? '-')) }}
                            </span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-6">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                    <i class="fas fa-cube text-gray-400"></i> Produk
                                </dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $order->produk->nama ?? 'Tidak tersedia' }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                    <i class="fas fa-shopping-cart text-gray-400"></i> Jumlah
                                </dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $order->jumlah ?? 0 }} item</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wider mb-1 flex items-center gap-1.5">
                                    <i class="fas fa-money-bill-wave text-gray-400"></i> Total Harga
                                </dt>
                                <dd class="text-lg font-extrabold text-indigo-700">Rp {{ number_format($order->total_harga ?? 0, 0, ',', '.') }}</dd>
                            </div>
                        </div>
                        
                        <!-- Progress Indicator -->
                        <div class="mt-6 bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-xs font-semibold text-gray-700 uppercase tracking-wider flex items-center gap-2">
                                    <i class="fas fa-tasks text-indigo-500"></i>
                                    Status Pengemasan
                                </span>
                                <span class="text-xs font-medium text-indigo-600">Proses...</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden">
                                <div class="bg-indigo-600 h-2.5 rounded-full relative" style="width: 50%">
                                    <div class="absolute top-0 right-0 bottom-0 left-0 overflow-hidden">
                                        <div class="w-full h-full animate-pulse bg-white opacity-20"></div>
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                Pesanan Anda sedang dikemas dengan rapi dan aman.
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-12 text-center">
                <div class="w-20 h-20 bg-indigo-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-box-open text-3xl text-indigo-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Belum ada pesanan yang sedang dikemas</h3>
                <p class="text-gray-500 text-sm max-w-sm mx-auto mb-6">
                    Semua pesanan Anda telah diproses atau belum ada pesanan yang masuk tahap pengemasan saat ini.
                </p>
                <a href="{{ route('pembeli.produk.index') }}" class="inline-flex justify-center items-center px-6 py-3 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <i class="fas fa-shopping-bag mr-2"></i>
                    Belanja Sekarang
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
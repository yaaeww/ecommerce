@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <i class="fas fa-star text-yellow-500"></i>
                Rating dan Ulasan Saya
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Kelola penilaian produk yang telah Anda beli
            </p>
        </div>
        <a href="{{ route('pembeli.profile.show') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali ke Profil
        </a>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 rounded-lg bg-green-50 border border-green-200 flex items-center gap-3 text-green-700">
            <i class="fas fa-check-circle text-xl"></i>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
    @endif
    
    @if(session('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-50 border border-red-200 flex items-center gap-3 text-red-700">
            <i class="fas fa-exclamation-circle text-xl"></i>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
    @endif

    <div class="space-y-12">
        <!-- Belum Dinilai Section -->
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-yellow-500 rounded-full"></div>
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-clock text-gray-400"></i>
                    Belum Dinilai
                </h3>
                @if(!empty($produkBelumDinilai) && count($produkBelumDinilai) > 0)
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ count($produkBelumDinilai) }}
                    </span>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                @if(empty($produkBelumDinilai) || count($produkBelumDinilai) === 0)
                    <div class="text-center py-16 px-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                            <i class="fas fa-check-circle text-3xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Tidak ada pesanan yang belum dinilai</h3>
                        <p class="text-gray-500 text-sm">Semua pesanan dengan status diterima telah Anda beri rating.</p>
                    </div>
                @else
                    <!-- Desktop View (Table) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/4">Nomor Pesanan</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/3">Produk</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/4">Status Pesanan</th>
                                    <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/6">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($produkBelumDinilai as $item)
                                    @php
                                        $order = $item->order;
                                        $produk = $item->produk;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-semibold text-gray-900">{{ $order->invoice ?? 'INV-' . $order->id }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                    @if($produk && $produk->gambar)
                                                        <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover rounded">
                                                    @else
                                                        <i class="fas fa-box text-gray-400"></i>
                                                    @endif
                                                </div>
                                                <span class="text-sm font-medium text-gray-900">{{ $produk->nama ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                <i class="fas fa-clock mr-1.5"></i> {{ ucfirst(str_replace('_', ' ', $order->status_pesanan ?? 'Unknown')) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right">
                                            <a href="{{ route('pembeli.rating.create', ['order' => $order->id, 'product' => $produk->id]) }}" class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                                <i class="fas fa-edit mr-2"></i> Beri Ulasan
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile View (Cards) -->
                    <div class="md:hidden divide-y divide-gray-200">
                        @foreach($produkBelumDinilai as $item)
                            @php
                                $order = $item->order;
                                $produk = $item->produk;
                            @endphp
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-3">
                                    <span class="text-xs font-semibold text-gray-500">{{ $order->invoice ?? 'INV-' . $order->id }}</span>
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1"></i> {{ ucfirst(str_replace('_', ' ', $order->status_pesanan ?? 'Unknown')) }}
                                    </span>
                                </div>
                                
                                <div class="flex items-center gap-3 mb-4">
                                    <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center flex-shrink-0">
                                        @if($produk && $produk->gambar)
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover rounded">
                                        @else
                                            <i class="fas fa-box text-gray-400"></i>
                                        @endif
                                    </div>
                                    <div class="text-sm font-medium text-gray-900 line-clamp-2">{{ $produk->nama ?? '-' }}</div>
                                </div>
                                
                                <a href="{{ route('pembeli.rating.create', ['order' => $order->id, 'product' => $produk->id]) }}" class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <i class="fas fa-edit mr-2"></i> Beri Ulasan
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- Sudah Dinilai Section -->
        <div>
            <div class="flex items-center gap-3 mb-6">
                <div class="h-8 w-1 bg-green-500 rounded-full"></div>
                <h3 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i class="fas fa-check-circle text-green-500"></i>
                    Sudah Dinilai
                </h3>
                @if(!empty($produkSudahDinilai) && count($produkSudahDinilai) > 0)
                    <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                        {{ count($produkSudahDinilai) }}
                    </span>
                @endif
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
                @if(empty($produkSudahDinilai) || count($produkSudahDinilai) === 0)
                    <div class="text-center py-16 px-4">
                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                            <i class="fas fa-star text-3xl text-gray-300"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-1">Belum ada ulasan yang diberikan</h3>
                        <p class="text-gray-500 text-sm">Rating dan ulasan yang Anda berikan akan muncul di sini.</p>
                    </div>
                @else
                    <!-- Desktop View (Table) -->
                    <div class="hidden md:block overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/5">Nomor Pesanan</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/4">Produk</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/6">Rating</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider w-1/4">Ulasan</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($produkSudahDinilai as $ulasan)
                                    @php
                                        $produk = $ulasan->produk;
                                        $order = $ulasan->order;
                                    @endphp
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4">
                                            <span class="text-sm font-semibold text-gray-900">{{ $order->invoice ?? 'INV-' . $order->id }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-3">
                                                <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center flex-shrink-0">
                                                    @if($produk && $produk->gambar)
                                                        <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover rounded">
                                                    @else
                                                        <i class="fas fa-box text-gray-400 text-xs"></i>
                                                    @endif
                                                </div>
                                                <span class="text-sm font-medium text-gray-900 line-clamp-1" title="{{ $produk->nama ?? '-' }}">{{ $produk->nama ?? '-' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center text-yellow-400 text-sm">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= $ulasan->bintang)
                                                        <i class="fas fa-star"></i>
                                                    @else
                                                        <i class="far fa-star text-gray-300"></i>
                                                    @endif
                                                @endfor
                                                <span class="ml-2 text-xs font-medium text-gray-600">{{ $ulasan->bintang }}/5</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <p class="text-sm text-gray-600 line-clamp-2" title="{{ $ulasan->ulasan }}">{{ $ulasan->ulasan ?: '-' }}</p>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <i class="fas fa-calendar-alt mr-1"></i> {{ $ulasan->created_at->format('d M Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Mobile View (Cards) -->
                    <div class="md:hidden divide-y divide-gray-200">
                        @foreach($produkSudahDinilai as $ulasan)
                            @php
                                $produk = $ulasan->produk;
                                $order = $ulasan->order;
                            @endphp
                            <div class="p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-xs font-semibold text-gray-500">{{ $order->invoice ?? 'INV-' . $order->id }}</span>
                                    <span class="text-[10px] text-gray-400"><i class="fas fa-calendar-alt mr-1"></i> {{ $ulasan->created_at->format('d M Y') }}</span>
                                </div>
                                
                                <div class="flex items-start gap-3 mb-3">
                                    <div class="w-10 h-10 rounded bg-gray-100 flex items-center justify-center flex-shrink-0 mt-1">
                                        @if($produk && $produk->gambar)
                                            <img src="{{ asset('storage/' . $produk->gambar) }}" alt="{{ $produk->nama }}" class="w-full h-full object-cover rounded">
                                        @else
                                            <i class="fas fa-box text-gray-400 text-xs"></i>
                                        @endif
                                    </div>
                                    <div class="flex-1">
                                        <div class="text-sm font-medium text-gray-900 line-clamp-2 mb-1">{{ $produk->nama ?? '-' }}</div>
                                        <div class="flex items-center text-yellow-400 text-xs">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $ulasan->bintang)
                                                    <i class="fas fa-star"></i>
                                                @else
                                                    <i class="far fa-star text-gray-300"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                
                                @if($ulasan->ulasan)
                                    <div class="text-sm text-gray-600 bg-gray-50 p-3 rounded-lg border border-gray-100 italic">
                                        "{{ $ulasan->ulasan }}"
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
            <i class="fas fa-user-circle text-indigo-600"></i>
            Profil Saya
        </h1>
        <p class="mt-2 text-sm text-gray-600">
            Kelola informasi profil dan pantau status pesanan Anda
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Profile Card -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden h-full flex flex-col">
                <div class="p-8 text-center border-b border-gray-100 flex-grow flex flex-col items-center">
                    <!-- Avatar -->
                    <div class="relative inline-block mb-6">
                        @if(auth()->user()->avatar && Storage::disk('public')->exists(auth()->user()->avatar))
                            <img src="{{ asset('storage/' . auth()->user()->avatar) }}" 
                                 class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-lg ring-2 ring-indigo-100" 
                                 alt="Avatar">
                        @else
                            <div class="w-32 h-32 rounded-full bg-indigo-50 border-4 border-white shadow-lg ring-2 ring-indigo-100 flex items-center justify-center">
                                <i class="fas fa-user text-5xl text-indigo-300"></i>
                            </div>
                        @endif
                        <div class="absolute bottom-1 right-1 bg-green-500 w-5 h-5 rounded-full border-2 border-white shadow-sm" title="Online"></div>
                    </div>

                    <h2 class="text-2xl font-bold text-gray-900 mb-1">{{ auth()->user()->name }}</h2>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 mb-6">
                        <i class="fas fa-shield-alt mr-1.5"></i> Pembeli
                    </span>

                    <div class="w-full text-left space-y-4">
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-envelope text-gray-400"></i> Email
                            </span>
                            <span class="text-base text-gray-900 bg-gray-50 p-2.5 rounded-lg border border-gray-100 break-all">{{ auth()->user()->email }}</span>
                        </div>
                        
                        @php
                            $lastOrder = auth()->user()->orders->last();
                        @endphp

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-phone text-gray-400"></i> Telepon
                            </span>
                            <span class="text-base {{ $lastOrder && $lastOrder->phone ? 'text-gray-900 bg-gray-50 p-2.5 border border-gray-100' : 'text-gray-400 italic' }} rounded-lg">
                                {{ $lastOrder->phone ?? 'Belum ada data telepon' }}
                            </span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-semibold text-gray-500 uppercase tracking-wider flex items-center gap-2">
                                <i class="fas fa-map-marker-alt text-gray-400"></i> Alamat
                            </span>
                            <span class="text-base {{ $lastOrder && $lastOrder->alamat ? 'text-gray-900 bg-gray-50 p-2.5 border border-gray-100' : 'text-gray-400 italic' }} rounded-lg line-clamp-3">
                                {{ $lastOrder->alamat ?? 'Belum ada data alamat' }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 bg-gray-50">
                    <a href="{{ route('pembeli.profile.edit') }}" class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <i class="fas fa-user-edit mr-2"></i>
                        Edit Profil
                    </a>
                </div>
            </div>
        </div>

        <!-- Orders Activity -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden h-full flex flex-col">
                <div class="p-6 sm:p-8 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="fas fa-shopping-bag text-indigo-500"></i>
                        Aktivitas Pesanan
                    </h2>
                    <a href="{{ route('pembeli.pesanan.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                        Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <div class="p-6 sm:p-8 flex-grow">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Belum Bayar -->
                        <a href="{{ route('pembeli.status.belum-bayar') }}" class="group block p-6 bg-white border border-gray-200 rounded-xl hover:border-indigo-300 hover:shadow-md transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-yellow-50 text-yellow-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-credit-card text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors">Belum Bayar</h3>
                                    <p class="text-sm text-gray-500">Menunggu pembayaran dari Anda</p>
                                </div>
                            </div>
                        </a>

                        <!-- Dikemas -->
                        <a href="{{ route('pembeli.status.dikemas') }}" class="group block p-6 bg-white border border-gray-200 rounded-xl hover:border-indigo-300 hover:shadow-md transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-box text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors">Dikemas</h3>
                                    <p class="text-sm text-gray-500">Pesanan sedang disiapkan penjual</p>
                                </div>
                            </div>
                        </a>

                        <!-- Dikirim -->
                        <a href="{{ route('pembeli.status.dikirim') }}" class="group block p-6 bg-white border border-gray-200 rounded-xl hover:border-indigo-300 hover:shadow-md transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-truck text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors">Dikirim</h3>
                                    <p class="text-sm text-gray-500">Pesanan dalam perjalanan</p>
                                </div>
                            </div>
                        </a>

                        <!-- Beri Penilaian -->
                        <a href="{{ route('pembeli.rating.index') }}" class="group block p-6 bg-white border border-gray-200 rounded-xl hover:border-indigo-300 hover:shadow-md transition-all duration-300">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 rounded-full bg-green-50 text-green-500 flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas fa-star text-xl"></i>
                                </div>
                                <div>
                                    <h3 class="text-base font-bold text-gray-900 mb-1 group-hover:text-indigo-600 transition-colors">Beri Penilaian</h3>
                                    <p class="text-sm text-gray-500">Berikan ulasan produk yang diterima</p>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
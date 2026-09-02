@extends('layouts.public')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-900 flex items-center gap-3">
                <i class="fas fa-clock text-yellow-500"></i>
                Pesanan Belum Bayar
            </h1>
            <p class="mt-2 text-sm text-gray-600">
                Daftar pesanan Anda yang masih menunggu pembayaran
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

    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        @if($orders->count() > 0)
            <div class="p-4 sm:p-6 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-yellow-100 text-yellow-800 text-sm font-semibold">
                    <i class="fas fa-shopping-bag"></i>
                    Total: {{ $orders->count() }} pesanan
                </div>
            </div>

            <!-- Desktop View (Table) -->
            <div class="hidden md:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Detail Pelanggan</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Pesanan</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Total Harga</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <span class="text-sm font-semibold text-gray-900">{{ $order->name }}</span>
                                        <span class="text-sm text-gray-500 flex items-center gap-1.5"><i class="fas fa-phone text-xs"></i> {{ $order->phone }}</span>
                                        <span class="text-xs text-gray-500 max-w-xs truncate" title="{{ $order->alamat }}"><i class="fas fa-map-marker-alt text-xs mr-1"></i> {{ $order->alamat }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm text-gray-900 font-medium">{{ $order->jumlah }} Item</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-base font-bold text-indigo-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                        <i class="fas fa-clock mr-1.5"></i> {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right">
                                    <div class="flex flex-col sm:flex-row justify-end gap-2">
                                        <a href="{{ route('pembeli.pending', ['order_id_midtrans' => $order->order_id_midtrans]) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            <i class="fas fa-credit-card mr-1.5"></i> Bayar
                                        </a>
                                        <button type="button" onclick="cekStatusPembayaran('{{ $order->order_id_midtrans }}', this)" class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                            <i class="fas fa-rotate mr-1.5"></i> Cek Status
                                        </button>
                                        <form action="{{ route('pembeli.order.cancelExpired', $order->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                            @csrf
                                            <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                                <i class="fas fa-times mr-1.5"></i> Batalkan
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Mobile View (Cards) -->
            <div class="md:hidden divide-y divide-gray-200">
                @foreach ($orders as $order)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex justify-between items-start mb-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                <i class="fas fa-clock mr-1.5"></i> {{ ucfirst($order->status) }}
                            </span>
                            <span class="text-sm font-medium text-gray-900">{{ $order->jumlah }} Item</span>
                        </div>
                        
                        <div class="mb-4 space-y-1">
                            <div class="text-sm font-bold text-gray-900">{{ $order->name }}</div>
                            <div class="text-sm text-gray-500 flex items-center gap-1.5"><i class="fas fa-phone text-xs text-gray-400"></i> {{ $order->phone }}</div>
                            <div class="text-sm text-gray-500 flex items-start gap-1.5"><i class="fas fa-map-marker-alt text-xs text-gray-400 mt-1"></i> <span>{{ $order->alamat }}</span></div>
                        </div>
                        
                        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-100">
                            <div class="text-sm">
                                <div class="text-gray-500 text-xs uppercase tracking-wider font-semibold">Total Harga</div>
                                <div class="text-base font-bold text-indigo-600">Rp {{ number_format($order->total_harga, 0, ',', '.') }}</div>
                            </div>
                            
                            <div class="flex flex-col gap-2">
                                <a href="{{ route('pembeli.pending', ['order_id_midtrans' => $order->order_id_midtrans]) }}" class="inline-flex items-center justify-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <i class="fas fa-credit-card mr-1.5"></i> Bayar
                                </a>
                                <button type="button" onclick="cekStatusPembayaran('{{ $order->order_id_midtrans }}', this)" class="inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-indigo-700 bg-white hover:bg-indigo-50 hover:border-indigo-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                    <i class="fas fa-rotate mr-1.5"></i> Cek Status
                                </button>
                                <form action="{{ route('pembeli.order.cancelExpired', $order->id) }}" method="POST" onsubmit="return confirm('Yakin ingin membatalkan pesanan ini?')">
                                    @csrf
                                    <button type="submit" class="w-full inline-flex items-center justify-center px-3 py-1.5 border border-gray-300 text-xs font-medium rounded-md text-red-700 bg-white hover:bg-red-50 hover:border-red-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors">
                                        <i class="fas fa-times mr-1.5"></i> Batalkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-16 px-4">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-gray-50 mb-6">
                    <i class="fas fa-check-circle text-4xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ada pesanan belum bayar</h3>
                <p class="text-gray-500 max-w-md mx-auto mb-8">Semua pesanan Anda telah dibayar atau belum ada pesanan yang menunggu pembayaran.</p>
                <a href="{{ route('pembeli.produk.index') }}" class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 shadow-sm transition-all hover:shadow">
                    Mulai Belanja
                </a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    function cekStatusPembayaran(orderId, btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1.5"></i> Mengecek...';

        fetch('/pembeli/order/cek-status/' + encodeURIComponent(orderId), {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            if (data.success) {
                alert('Status pembayaran: ' + data.status.toUpperCase());
                window.location.reload();
            } else {
                alert(data.message || 'Gagal memeriksa status pembayaran.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-rotate mr-1.5"></i> Cek Status';
            }
        })
        .catch(function () {
            alert('Gagal memeriksa status pembayaran. Coba lagi.');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-rotate mr-1.5"></i> Cek Status';
        });
    }
</script>
@endpush
@endsection
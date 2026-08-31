@extends('layouts.public')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12 pt-24">
    <!-- Page Header -->
    <div class="text-center mb-12">
        <h1 class="text-3xl md:text-4xl font-extrabold text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600 mb-4 flex items-center justify-center gap-3">
            <i class="fas fa-receipt text-indigo-500"></i>
            Menunggu Pembayaran
        </h1>
    </div>

    <!-- Order Card -->
    <div class="bg-white rounded-3xl border border-gray-200 shadow-xl overflow-hidden">
        <div class="p-6 md:p-8 border-b border-gray-100 bg-gray-50/50 text-center">
            <h3 class="text-xl font-bold text-indigo-600 flex items-center justify-center gap-2">
                <i class="fas fa-info-circle"></i> Ringkasan Pesanan
            </h3>
        </div>

        <div class="p-6 md:p-8">
            <div class="space-y-6">
                <!-- User Info -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pb-4 border-b border-gray-100">
                    <div class="text-gray-500 font-medium">Nama Pemesan</div>
                    <div class="md:col-span-2 text-gray-900 font-semibold">{{ $order->name }}</div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pb-4 border-b border-gray-100">
                    <div class="text-gray-500 font-medium">Nomor HP</div>
                    <div class="md:col-span-2 text-gray-900 font-semibold">{{ $order->phone }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pb-4 border-b border-gray-100">
                    <div class="text-gray-500 font-medium">Alamat Pengiriman</div>
                    <div class="md:col-span-2 text-gray-900 font-semibold">{{ $order->alamat }}</div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pb-4 border-b border-gray-100">
                    <div class="text-gray-500 font-medium">ID Transaksi</div>
                    <div class="md:col-span-2 text-gray-900 font-mono text-sm bg-gray-100 px-2 py-1 rounded w-fit">{{ $order->order_id_midtrans }}</div>
                </div>

                <!-- Order Details (Multiple) -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pb-4 border-b border-gray-100">
                    <div class="text-gray-500 font-medium pt-2">Daftar Produk</div>
                    <div class="md:col-span-2 space-y-3">
                        @foreach($items as $item)
                        <div class="flex items-center justify-between bg-gray-50 p-3 rounded-lg border border-gray-100">
                            <div>
                                <div class="font-semibold text-gray-900">{{ $item->produk->nama }}</div>
                                <div class="text-sm text-gray-500">{{ $item->jumlah }} x Rp {{ number_format($item->harga_satuan, 0, ',', '.') }}</div>
                            </div>
                            <div class="font-bold text-indigo-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Total -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-2 pt-4 items-center">
                    <div class="text-gray-600 font-bold text-lg">Total Tagihan</div>
                    <div class="md:col-span-2 text-3xl font-black text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-purple-600">
                        Rp {{ number_format($total_harga, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <!-- Payment Section -->
            <div class="mt-10 pt-8 border-t border-gray-100 text-center">
                <div class="bg-indigo-50 border border-indigo-100 rounded-2xl p-6 mb-8 max-w-lg mx-auto">
                    <p class="text-gray-700 mb-2">
                        <span class="text-indigo-600 font-bold">Langkah selanjutnya:</span> 
                        Klik tombol di bawah untuk melanjutkan pembayaran
                    </p>
                    <p class="text-gray-500 text-sm flex items-center justify-center gap-2">
                        <i class="fas fa-shield-alt text-green-500"></i> 
                        Pembayaran diproses dengan aman oleh Midtrans
                    </p>
                </div>

                <button id="pay-button" class="w-full md:w-auto min-w-[250px] bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-4 px-8 rounded-xl font-bold text-lg shadow-lg shadow-indigo-200 hover:shadow-indigo-300 transform hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-3 mx-auto">
                    <i class="fas fa-credit-card"></i> Bayar Sekarang
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Midtrans Snap Script -->
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<script type="text/javascript">
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        // Show loading state
        payButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Memproses...';
        payButton.disabled = true;
        payButton.classList.add('opacity-75', 'cursor-not-allowed');
        payButton.classList.remove('hover:-translate-y-1', 'hover:shadow-indigo-300');

        window.snap.pay('{{$snapToken}}', {
            onSuccess: function (result) {
                console.log('Payment success result:', result);
                // Redirect to pesanan page if multiple orders, or invoice if you handle it
                window.location.href = '/pembeli/pesanan';
            },
            onPending: function (result) {
                alert("Menunggu pembayaran Anda.");
                resetButton();
            },
            onError: function (result) {
                alert("Pembayaran gagal. Coba lagi.");
                resetButton();
            },
            onClose: function () {
                alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                resetButton();
            }
        });
        
        function resetButton() {
            payButton.innerHTML = '<i class="fas fa-credit-card"></i> Bayar Sekarang';
            payButton.disabled = false;
            payButton.classList.remove('opacity-75', 'cursor-not-allowed');
            payButton.classList.add('hover:-translate-y-1', 'hover:shadow-indigo-300');
        }
    });
</script>
@endsection
@extends('layouts.public')

@section('content')
<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Page Header -->
    <div class="mb-8 text-center">
        <h1 class="text-3xl font-extrabold text-gray-900 flex items-center justify-center gap-3">
            <i class="fas fa-credit-card text-indigo-600"></i>
            Status Pembayaran
        </h1>
    </div>

    <!-- Payment Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-6 sm:p-10 border-b border-gray-100 bg-gray-50/50 flex flex-col items-center">
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-clock text-2xl text-yellow-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 mb-2 text-center">
                Menunggu Pembayaran
            </h3>
            <p class="text-sm text-gray-600 text-center max-w-md">
                Silakan lanjutkan pembayaran Anda melalui Midtrans untuk menyelesaikan pesanan.
            </p>
        </div>

        <div class="p-6 sm:p-10">
            <dl class="space-y-4 text-sm text-gray-600">
                <div class="flex flex-col sm:flex-row sm:justify-between py-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <dt class="font-medium text-gray-500 mb-1 sm:mb-0">Nama Lengkap</dt>
                    <dd class="font-semibold text-gray-900">{{ $order->name }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between py-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <dt class="font-medium text-gray-500 mb-1 sm:mb-0">Nomor HP/WhatsApp</dt>
                    <dd class="font-semibold text-gray-900">{{ $order->phone }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between py-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <dt class="font-medium text-gray-500 mb-1 sm:mb-0">Alamat Pengiriman</dt>
                    <dd class="font-semibold text-gray-900 sm:text-right max-w-xs">{{ $order->alamat }}</dd>
                </div>
                <div class="flex flex-col py-3 border-b border-gray-100">
                    <dt class="font-medium text-gray-500 mb-2">Daftar Produk</dt>
                    <dd class="w-full space-y-2">
                        @foreach($orders as $o)
                        <div class="flex justify-between bg-gray-50 p-2 rounded">
                            <span>{{ $o->produk->nama }} ({{ $o->jumlah }}x)</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($o->total_harga, 0, ',', '.') }}</span>
                        </div>
                        @endforeach
                    </dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between py-3 border-b border-gray-100 last:border-0 last:pb-0">
                    <dt class="font-medium text-gray-500 mb-1 sm:mb-0">Total Harga</dt>
                    <dd class="text-lg font-extrabold text-indigo-700">Rp {{ number_format($total_harga, 0, ',', '.') }}</dd>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between py-3">
                    <dt class="font-medium text-gray-500 mb-1 sm:mb-0">Status</dt>
                    <dd>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 border border-yellow-200">
                            <i class="fas fa-clock mr-1.5"></i> Pending
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        <!-- Payment Section -->
        <div class="p-6 sm:p-10 bg-indigo-50/50 border-t border-gray-100 flex flex-col items-center">
            <div class="text-center mb-6 max-w-md">
                <p class="text-sm text-gray-700 mb-2">
                    <span class="font-semibold text-indigo-700">Langkah selanjutnya:</span> Klik tombol di bawah untuk melanjutkan pembayaran
                </p>
                <p class="text-xs text-gray-500 flex items-center justify-center gap-1.5">
                    <i class="fas fa-shield-alt text-green-500"></i>
                    Pembayaran diproses dengan aman oleh Midtrans
                </p>
            </div>

            <button id="pay-button" class="w-full sm:w-auto inline-flex justify-center items-center px-8 py-3.5 border border-transparent shadow-sm text-base font-bold rounded-xl text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all transform active:scale-95 disabled:opacity-75 disabled:cursor-wait">
                <i class="fas fa-credit-card mr-2"></i> 
                Lanjutkan Pembayaran
            </button>
            
            <p class="mt-4 text-xs text-gray-400 flex items-center gap-1.5">
                <i class="fas fa-lock"></i>
                Transaksi Anda aman dan terenkripsi
            </p>
        </div>
    </div>
</div>

<!-- Midtrans Snap Script -->
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ config('midtrans.client_key') }}"></script>

<script type="text/javascript">
    var payButton = document.getElementById('pay-button');
    payButton.addEventListener('click', function () {
        // Show loading state
        payButton.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-2"></i> Memproses...';
        payButton.disabled = true;

        window.snap.pay('{{$snapToken}}', {
            onSuccess: function (result) {
                console.log('Payment success result:', result);
                window.location.href = '/pembeli/pesanan';
            },
            onPending: function (result) {
                alert("Menunggu pembayaran Anda.");
                // Reset button
                payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i> Lanjutkan Pembayaran';
                payButton.disabled = false;
            },
            onError: function (result) {
                alert("Pembayaran gagal. Silakan coba lagi.");
                // Reset button
                payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i> Lanjutkan Pembayaran';
                payButton.disabled = false;
            },
            onClose: function () {
                alert('Anda menutup popup sebelum menyelesaikan pembayaran.');
                // Reset button
                payButton.innerHTML = '<i class="fas fa-credit-card mr-2"></i> Lanjutkan Pembayaran';
                payButton.disabled = false;
            }
        });
    });
</script>
@endsection
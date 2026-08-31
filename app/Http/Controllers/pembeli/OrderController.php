<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Produk;
use App\Models\Order;
use App\Models\Keranjang;
use App\Models\User;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Konfigurasi Midtrans di constructor
    public function __construct()
    {
        // Inisialisasi konfigurasi Midtrans
        $this->initMidtrans();
    }

    // Fungsi inisialisasi Midtrans
    protected function initMidtrans()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    // ========================
    // METHODS UNTUK WEB
    // ========================

    // Tampilkan form pemesanan produk
    protected function hitungHargaSetelahDiskon(Produk $produk)
    {
        if (
            $produk->diskon &&
            $produk->diskon->persen_diskon > 0 &&
            now()->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir)
        ) {
            return $produk->harga - ($produk->harga * $produk->diskon->persen_diskon / 100);
        }

        return $produk->harga;
    }

    public function showForm($produkId = null, $quantity = null)
    {
        $userId = Auth::id();

        if ($produkId) {
            // Checkout single product (Beli Langsung)
            $produk = Produk::with('diskon')->findOrFail($produkId);
            $quantity = $quantity ?? 1;
            
            $keranjang = Keranjang::where('produk_id', $produkId)
                ->where('user_id', $userId)
                ->first();
            
            if ($keranjang) {
                $quantity = $keranjang->jumlah;
            }

            if ($quantity > $produk->stok) {
                return redirect()->back()->with('error', 'Stok produk "' . $produk->nama . '" tidak mencukupi (Tersisa: ' . $produk->stok . ').');
            }

            $harga_diskon = $this->hitungHargaSetelahDiskon($produk);
            $total_harga = $harga_diskon * $quantity;
            $items = [(object)[
                'produk' => $produk,
                'jumlah' => $quantity,
                'harga_satuan' => $harga_diskon,
                'subtotal' => $total_harga,
                'keranjang_id' => $keranjang ? $keranjang->id : null
            ]];
            $is_cart = false;
        } else {
            // Checkout entire cart
            $keranjangs = Keranjang::with('produk.diskon')->where('user_id', $userId)->get();
            if ($keranjangs->isEmpty()) {
                return redirect()->route('pembeli.keranjang.index')->with('error', 'Keranjang kosong.');
            }
            
            $items = [];
            $total_harga = 0;
            foreach($keranjangs as $k) {
                if ($k->jumlah > $k->produk->stok) {
                    return redirect()->route('pembeli.keranjang.index')->with('error', 'Stok produk "' . $k->produk->nama . '" tidak mencukupi (Tersisa: ' . $k->produk->stok . ').');
                }

                $harga_diskon = $this->hitungHargaSetelahDiskon($k->produk);
                $sub = $harga_diskon * $k->jumlah;
                $total_harga += $sub;
                $items[] = (object)[
                    'produk' => $k->produk,
                    'jumlah' => $k->jumlah,
                    'harga_satuan' => $harga_diskon,
                    'subtotal' => $sub,
                    'keranjang_id' => $k->id
                ];
            }
            $is_cart = true;
        }

        return view('pembeli.order', compact('items', 'total_harga', 'is_cart', 'produkId', 'quantity'));
    }

    public function konfirmasiPembelian($produk_id, $quantity)
    {
        $produk = Produk::with('diskon')->findOrFail($produk_id);

        if ($quantity > $produk->stok) {
            return redirect()->back()->with('error', 'Stok produk "' . $produk->nama . '" tidak mencukupi (Tersisa: ' . $produk->stok . ').');
        }

        $harga_diskon = $this->hitungHargaSetelahDiskon($produk);
        $total_harga = $harga_diskon * $quantity;

        return view('pembeli.order', compact('produk', 'quantity', 'harga_diskon', 'total_harga'));
    }

    // Proses checkout (untuk web - satu produk)
    public function checkout(Request $request)
    {
        try {
            $request->validate([
                'is_cart' => 'required|boolean',
                'produk_id' => 'nullable|exists:produks,id',
                'jumlah' => 'nullable|integer|min:1',
                'name' => 'required|string',
                'phone' => 'required|string',
                'alamat' => 'required|string',
            ]);

            $user = Auth::user();
            $is_cart = $request->is_cart;

            $items = [];
            $total_harga = 0;
            
            if ($is_cart) {
                // Ambil dari keranjang
                $keranjangs = Keranjang::with('produk')->where('user_id', $user->id)->get();
                if ($keranjangs->isEmpty()) {
                    return back()->with('error', 'Keranjang kosong.');
                }
                foreach($keranjangs as $k) {
                    if ($k->jumlah > $k->produk->stok) {
                        return back()->with('error', 'Stok produk "' . $k->produk->nama . '" tidak mencukupi (Tersisa: ' . $k->produk->stok . ').');
                    }

                    $harga = $this->hitungHargaSetelahDiskon($k->produk);
                    $sub = $harga * $k->jumlah;
                    $total_harga += $sub;
                    $items[] = (object)[
                        'produk' => $k->produk,
                        'jumlah' => $k->jumlah,
                        'harga_satuan' => $harga,
                        'subtotal' => $sub,
                        'keranjang_id' => $k->id
                    ];
                }
            } else {
                // Beli langsung
                if (!$request->produk_id || !$request->jumlah) {
                    return back()->with('error', 'Data produk tidak valid.');
                }
                $produk = Produk::findOrFail($request->produk_id);

                if ($request->jumlah > $produk->stok) {
                    return back()->with('error', 'Stok produk "' . $produk->nama . '" tidak mencukupi (Tersisa: ' . $produk->stok . ').');
                }

                $harga = $this->hitungHargaSetelahDiskon($produk);
                $total_harga = $harga * $request->jumlah;
                $items[] = (object)[
                    'produk' => $produk,
                    'jumlah' => $request->jumlah,
                    'harga_satuan' => $harga,
                    'subtotal' => $total_harga,
                    'keranjang_id' => null
                ];
            }

            // Generate order_id yang UNIK
            $orderIdMidtrans = 'WEB-' . date('YmdHis') . '-' . $user->id . '-' . rand(1000, 9999);

            // Cek apakah sudah ada order dengan order_id_midtrans yang sama
            while (Order::where('order_id_midtrans', $orderIdMidtrans)->exists()) {
                $orderIdMidtrans = 'WEB-' . date('YmdHis') . '-' . $user->id . '-' . rand(10000, 99999);
            }

            $orders = [];
            $itemDetails = [];

            foreach ($items as $item) {
                // Buat order untuk setiap produk
                $order = Order::create([
                    'user_id' => $user->id,
                    'produk_id' => $item->produk->id,
                    'jumlah' => $item->jumlah,
                    'total_harga' => $item->subtotal,
                    'status' => 'pending',
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'alamat' => $request->alamat,
                    'order_id_midtrans' => $orderIdMidtrans,
                ]);

                $orders[] = $order;

                $itemDetails[] = [
                    'id' => $item->produk->id,
                    'price' => round($item->harga_satuan),
                    'quantity' => $item->jumlah,
                    'name' => substr($item->produk->nama, 0, 50),
                ];
            }

            Log::info('Web Checkout - Orders Created:', [
                'order_id' => $orderIdMidtrans,
                'total_amount' => $total_harga,
                'items_count' => count($items)
            ]);

            // Siapkan parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderIdMidtrans,
                    'gross_amount' => round($total_harga),
                ],
                'customer_details' => [
                    'first_name' => $request->name,
                    'email' => $user->email,
                    'phone' => $request->phone,
                ],
                'item_details' => $itemDetails,
            ];

            // Dapatkan Snap Token
            $snapToken = Snap::getSnapToken($params);

            // Update semua orders dengan snap token
            foreach ($orders as $o) {
                $o->update(['snap_token' => $snapToken]);
            }

            // Kosongkan keranjang jika dari keranjang
            if ($is_cart) {
                Keranjang::where('user_id', $user->id)->delete();
            } else {
                // Jika dari "Beli Langsung", hapus produk ini dari keranjang jika ada
                if ($items[0]->keranjang_id) {
                    Keranjang::where('id', $items[0]->keranjang_id)->delete();
                } else {
                    Keranjang::where('user_id', $user->id)->where('produk_id', $items[0]->produk->id)->delete();
                }
            }

            // Kita pakai order pertama untuk view pembeli.checkout karena view saat ini mengharapkan $order (walaupun kita akan update view-nya juga)
            $order = $orders[0];

            return view('pembeli.checkout', compact('snapToken', 'order', 'items', 'total_harga', 'orders'));
        } catch (\Exception $e) {
            Log::error('Web Checkout Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // ========================
    // METHODS UNTUK API
    // ========================

    // API: Get all orders
    public function indexApi()
    {
        try {
            $user = Auth::user();

            $orders = Order::with('produk')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data order berhasil diambil',
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            Log::error('API Index Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API: Create new order dari keranjang (bisa multi produk)
    public function storeApi(Request $request)
    {
        try {
            $request->validate([
                'alamat_pengiriman' => 'required|string',
                'catatan' => 'nullable|string',
            ]);

            $user = Auth::user();

            // 1. Ambil item dari keranjang
            $keranjangItems = Keranjang::with('produk')
                ->where('user_id', $user->id)
                ->get();

            if ($keranjangItems->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Keranjang kosong'
                ], 400);
            }

            // Validasi ketersediaan stok
            foreach ($keranjangItems as $item) {
                if ($item->jumlah > $item->produk->stok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok produk "' . $item->produk->nama . '" tidak mencukupi (Tersisa: ' . $item->produk->stok . ').'
                    ], 400);
                }
            }

            // 2. Generate order_id yang UNIK
            $orderIdMidtrans = 'API-' . date('YmdHis') . '-' . $user->id . '-' . rand(10000, 99999);

            // Cek duplikasi
            while (Order::where('order_id_midtrans', $orderIdMidtrans)->exists()) {
                $orderIdMidtrans = 'API-' . date('YmdHis') . '-' . $user->id . '-' . rand(10000, 99999);
            }

            Log::info('API Store - Order ID Generated:', ['order_id' => $orderIdMidtrans]);

            // 3. Buat order untuk SETIAP produk di keranjang
            $orders = [];
            $itemDetails = [];
            $totalAmount = 0;

            foreach ($keranjangItems as $item) {
                $subtotal = $item->jumlah * $item->produk->harga;
                $totalAmount += $subtotal;

                // Buat order untuk setiap produk
                $order = Order::create([
                    'user_id' => $user->id,
                    'produk_id' => $item->produk_id,
                    'jumlah' => $item->jumlah,
                    'total_harga' => $subtotal,
                    'status' => 'pending',
                    'alamat' => $request->alamat_pengiriman,
                    'name' => $user->name,
                    'phone' => $user->phone ?? $user->no_telepon ?? '',
                    'order_id_midtrans' => $orderIdMidtrans, // SAMA untuk semua order dalam satu checkout
                    'catatan' => $request->catatan,
                ]);

                $orders[] = $order;

                // Tambahkan ke item details untuk Midtrans
                $itemDetails[] = [
                    'id' => $item->produk_id,
                    'price' => (int) $item->produk->harga,
                    'quantity' => (int) $item->jumlah,
                    'name' => $item->produk->nama,
                ];
            }

            // 4. Siapkan parameter untuk Midtrans
            $params = [
                'transaction_details' => [
                    'order_id' => $orderIdMidtrans, // INI YANG DIPASTIKAN TIDAK KOSONG
                    'gross_amount' => $totalAmount,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? $user->no_telepon ?? '',
                ],
                'item_details' => $itemDetails,
            ];

            Log::info('API Store - Midtrans Params:', $params);

            // 5. Dapatkan Snap Token dari Midtrans
            $snapToken = Snap::getSnapToken($params);

            // 6. Update semua orders dengan snap token yang sama
            Order::where('order_id_midtrans', $orderIdMidtrans)
                ->update(['snap_token' => $snapToken]);

            // 7. Kosongkan keranjang
            Keranjang::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Order berhasil dibuat',
                'data' => [
                    'order_id' => $orderIdMidtrans,
                    'snap_token' => $snapToken,
                    'redirect_url' => (config('midtrans.is_production')
                        ? 'https://app.midtrans.com/snap/v2/vtweb/'
                        : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/') . $snapToken,
                    'total_amount' => $totalAmount,
                    'orders_count' => count($orders),
                    'orders' => $orders
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('API Store Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // API: Get specific order
    public function showApi($id)
    {
        try {
            $user = Auth::user();

            $order = Order::with('produk')
                ->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Data order berhasil diambil',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            Log::error('API Show Error:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data order',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // ========================
    // CALLBACK & SHARED METHODS
    // ========================

    // Callback dari Midtrans
    public function callback(Request $request)
    {
        try {
            Log::info('Midtrans Callback Received:', $request->all());

            // Validasi gross_amount harus positif
            if ((float) $request->gross_amount <= 0) {
                Log::warning('Midtrans Callback: Invalid gross_amount', ['gross_amount' => $request->gross_amount]);
                return response()->json(['message' => 'Invalid gross amount'], 400);
            }

            $serverKey = config('midtrans.server_key');

            // Verifikasi signature
            $hashed = hash(
                "sha512",
                $request->order_id . $request->status_code . $request->gross_amount . $serverKey
            );

            if ($hashed !== $request->signature_key) {
                Log::warning('Invalid Midtrans Signature');
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $statusBaru = $this->mapTransactionStatus($request->transaction_status);

            // Gunakan transaksi database dan lockForUpdate untuk mencegah race condition & replay callback
            $processed = DB::transaction(function () use ($request, $statusBaru) {
                $orders = Order::where('order_id_midtrans', $request->order_id)
                    ->lockForUpdate()
                    ->get();

                if ($orders->isEmpty()) {
                    return false;
                }

                foreach ($orders as $order) {
                    $statusLama = $order->status;

                    if ($statusLama !== $statusBaru) {
                        $order->status = $statusBaru;
                        $order->save();

                        // Kurangi stok HANYA saat status bertransisi dari non-complete menjadi complete
                        if ($statusBaru === 'complete' && $statusLama !== 'complete') {
                            $produk = Produk::where('id', $order->produk_id)->lockForUpdate()->first();
                            if ($produk) {
                                $produk->stok = max(0, $produk->stok - $order->jumlah);
                                $produk->save();
                            }
                        }
                    }
                }

                return true;
            });

            if (!$processed) {
                Log::warning('Orders not found for Midtrans callback:', ['order_id' => $request->order_id]);
                return response()->json(['message' => 'Orders not found'], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Callback processed successfully'
            ]);
        } catch (\Exception $e) {
            Log::error('Midtrans Callback Error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Callback processing failed'
            ], 500);
        }
    }

    // Helper untuk mapping status Midtrans ke status aplikasi
    protected function mapTransactionStatus($midtransStatus)
    {
        $statusMap = [
            'capture' => 'complete',
            'settlement' => 'complete',
            'pending' => 'pending',
            'deny' => 'failed',
            'expire' => 'expired',
            'cancel' => 'canceled',
        ];

        return $statusMap[$midtransStatus] ?? 'pending';
    }

    // Tampilkan invoice
    public function invoice($id)
    {
        $order = Order::with('produk')
            ->where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pembeli.invoice', compact('order'));
    }

    // Status belum dibayar
    public function statusBelumBayar()
    {
        $userId = Auth::id();
        $orders = Order::with('produk')
            ->where('user_id', $userId)
            ->where('status', 'pending')
            ->whereNotNull('order_id_midtrans')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pembeli.status_belum_bayar', compact('orders'));
    }

    // Bayar ulang untuk pending order
    public function pending($order_id_midtrans)
    {
        $orders = Order::with('produk')
            ->where('order_id_midtrans', $order_id_midtrans)
            ->get();

        if ($orders->isEmpty()) {
            abort(404);
        }

        $order = $orders->first();

        if ($order->user_id !== auth()->id()) {
            abort(403);
        }
        
        $total_harga = $orders->sum('total_harga');

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id_midtrans,
                'gross_amount' => round($total_harga),
            ],
            'customer_details' => [
                'first_name' => $order->name,
                'phone' => $order->phone,
            ],
        ];

        $snapToken = Snap::getSnapToken($params);
        return view('pembeli.pending', compact('order', 'snapToken', 'orders', 'total_harga'));
    }

    // Batalkan pesanan manual
    public function batal($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status === 'pending') {
            $order->status = 'canceled';
            $order->save();
            return redirect()->back()->with('success', 'Pesanan berhasil dibatalkan.');
        }

        return redirect()->back()->with('error', 'Pesanan tidak dapat dibatalkan.');
    }
}

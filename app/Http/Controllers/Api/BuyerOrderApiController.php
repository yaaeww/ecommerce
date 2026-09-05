<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Alamat;
use App\Models\Keranjang;
use App\Models\Order;
use App\Models\Produk;
use App\Traits\SyncsMidtransStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Transaction;

class BuyerOrderApiController extends Controller
{
    use SyncsMidtransStatus;

    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

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

    /**
     * Get buyer orders with optional status filter
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            
            // Auto-sync pending orders with Midtrans
            $this->syncPendingOrdersMidtrans();

            $status = $request->query('status'); // pending, dikemas, dikirim, diterima, cancel

            $query = Order::with(['produk.umkm', 'produk.diskon', 'komplain', 'ulasan', 'ulasan.user'])
                ->where('user_id', $user->id);

            if ($status === 'pending') {
                $query->where('status', 'pending');
            } elseif ($status === 'dikemas') {
                $query->where('status', 'complete')
                    ->where(function($q) {
                        $q->where('status_pesanan', 'dikemas')
                          ->orWhereNull('status_pesanan');
                    });
            } elseif ($status === 'dikirim') {
                $query->where('status', 'complete')
                    ->where('status_pesanan', 'dikirim');
            } elseif ($status === 'diterima') {
                $query->where('status', 'complete')
                    ->where('status_pesanan', 'diterima');
            } elseif ($status === 'cancel') {
                $query->whereIn('status', ['cancel', 'expire', 'deny']);
            }

            $orders = $query->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Data pesanan berhasil diambil',
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get single order detail
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $order = Order::with(['produk.umkm', 'produk.diskon', 'komplain', 'ulasan', 'ulasan.user'])
                ->where('user_id', $user->id)
                ->find($id);

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail pesanan berhasil diambil',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Checkout from Cart or Direct Buy with Snap Token
     */
    public function checkout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'is_cart' => 'required|boolean',
            'produk_id' => 'nullable|required_if:is_cart,false|exists:produks,id',
            'jumlah' => 'nullable|required_if:is_cart,false|integer|min:1',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:30',
            'alamat' => 'required|string',
            'catatan' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $isCart = $request->boolean('is_cart');
            $items = [];
            $totalHarga = 0;

            if ($isCart) {
                $keranjangs = Keranjang::with('produk.diskon')->where('user_id', $user->id)->get();
                if ($keranjangs->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Keranjang belanja kosong'
                    ], 400);
                }

                foreach ($keranjangs as $k) {
                    if (!$k->produk || !$k->produk->is_active) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Produk "' . ($k->produk->nama ?? 'Unknown') . '" tidak lagi tersedia'
                        ], 400);
                    }

                    if ($k->jumlah > $k->produk->stok) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Stok produk "' . $k->produk->nama . '" tidak mencukupi (Tersisa: ' . $k->produk->stok . ')'
                        ], 400);
                    }

                    $harga = $this->hitungHargaSetelahDiskon($k->produk);
                    $sub = $harga * $k->jumlah;
                    $totalHarga += $sub;
                    $items[] = (object)[
                        'produk' => $k->produk,
                        'jumlah' => $k->jumlah,
                        'harga_satuan' => $harga,
                        'subtotal' => $sub,
                        'keranjang_id' => $k->id,
                    ];
                }
            } else {
                $produk = Produk::with('diskon')->findOrFail($request->produk_id);

                if (!$produk->is_active) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Produk "' . $produk->nama . '" tidak lagi tersedia'
                    ], 400);
                }

                if ($request->jumlah > $produk->stok) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok produk "' . $produk->nama . '" tidak mencukupi (Tersisa: ' . $produk->stok . ')'
                    ], 400);
                }

                $harga = $this->hitungHargaSetelahDiskon($produk);
                $totalHarga = $harga * $request->jumlah;
                $items[] = (object)[
                    'produk' => $produk,
                    'jumlah' => $request->jumlah,
                    'harga_satuan' => $harga,
                    'subtotal' => $totalHarga,
                    'keranjang_id' => null,
                ];
            }

            // Generate unique Order ID Midtrans
            $orderIdMidtrans = 'MOB-' . date('YmdHis') . '-' . $user->id . '-' . rand(1000, 9999);
            while (Order::where('order_id_midtrans', $orderIdMidtrans)->exists()) {
                $orderIdMidtrans = 'MOB-' . date('YmdHis') . '-' . $user->id . '-' . rand(10000, 99999);
            }

            $orders = [];
            $itemDetails = [];

            foreach ($items as $item) {
                $order = Order::create([
                    'user_id' => $user->id,
                    'produk_id' => $item->produk->id,
                    'jumlah' => $item->jumlah,
                    'total_harga' => $item->subtotal,
                    'status' => 'pending',
                    'name' => $request->name,
                    'phone' => $request->phone,
                    'alamat' => $request->alamat,
                    'catatan' => $request->catatan,
                    'order_id_midtrans' => $orderIdMidtrans,
                ]);

                $orders[] = $order;

                $itemDetails[] = [
                    'id' => (string)$item->produk->id,
                    'price' => (int)round($item->harga_satuan),
                    'quantity' => (int)$item->jumlah,
                    'name' => substr($item->produk->nama, 0, 50),
                ];
            }

            // Midtrans Snap Token
            $params = [
                'transaction_details' => [
                    'order_id' => $orderIdMidtrans,
                    'gross_amount' => (int)round($totalHarga),
                ],
                'customer_details' => [
                    'first_name' => $request->name,
                    'email' => $user->email,
                    'phone' => $request->phone,
                ],
                'item_details' => $itemDetails,
            ];

            $snapToken = Snap::getSnapToken($params);

            // Update snap token on created orders
            Order::where('order_id_midtrans', $orderIdMidtrans)->update(['snap_token' => $snapToken]);

            // Clear Cart if needed
            if ($isCart) {
                Keranjang::where('user_id', $user->id)->delete();
            } else {
                Keranjang::where('user_id', $user->id)->where('produk_id', $items[0]->produk->id)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat',
                'data' => [
                    'order_id_midtrans' => $orderIdMidtrans,
                    'snap_token' => $snapToken,
                    'redirect_url' => (config('midtrans.is_production')
                        ? 'https://app.midtrans.com/snap/v2/vtweb/'
                        : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/') . $snapToken,
                    'total_harga' => $totalHarga,
                    'orders' => $orders
                ]
            ], 201);

        } catch (\Exception $e) {
            Log::error('API Mobile Checkout Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memproses checkout: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buyer cancel order (if still pending or dikemas before shipped)
     */
    public function cancel(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'batal_alasan' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Mohon sertakan alasan pembatalan pesanan.',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $order = Order::where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            if ($order->status_pesanan === 'dikirim' || $order->status_pesanan === 'diterima') {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan yang sudah dalam proses pengiriman atau diterima tidak dapat dibatalkan.'
                ], 400);
            }

            // Return stock if order was already paid (complete) before cancellation
            $wasPaid = $order->status === 'complete';

            $order->status = 'cancel';
            $order->batal_alasan = $request->batal_alasan;
            $order->batal_at = now();
            $order->save();

            if ($wasPaid) {
                $produk = Produk::where('id', $order->produk_id)->lockForUpdate()->first();
                if ($produk) {
                    $produk->stok += $order->jumlah;
                    $produk->save();
                }
            }

            ActivityLog::record(
                'BUYER_CANCELLED',
                "Pembeli {$order->name} membatalkan pesanan #{$order->id}. Alasan: {$request->batal_alasan}",
                $order
            );

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibatalkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membatalkan pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Buyer confirm order received (Release escrow to seller)
     */
    public function confirmReceived(Request $request, $id)
    {
        try {
            $user = $request->user();
            $order = Order::with('produk.umkm')->where('id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Pesanan tidak ditemukan'
                ], 404);
            }

            if ($order->status === 'complete' && $order->status_pesanan === 'dikirim' && !empty($order->resi_pengiriman) && !$order->is_escrow_released) {
                $order->status_pesanan = 'diterima';
                $order->diterima_at = now();
                $order->is_escrow_released = true;
                $order->save();

                ActivityLog::record(
                    'ORDER_CONFIRMED',
                    "Pembeli {$order->name} telah mengonfirmasi penerimaan pesanan #{$order->id} ({$order->produk->nama})",
                    $order
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Pesanan telah dikonfirmasi diterima. Terima kasih!'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Pesanan belum memenuhi syarat untuk dikonfirmasi diterima.'
            ], 400);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengonfirmasi pesanan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sync and check payment status direct with Midtrans
     */
    public function checkStatus(Request $request, $order_id_midtrans)
    {
        try {
            $user = $request->user();
            $orders = Order::where('order_id_midtrans', $order_id_midtrans)->get();

            if ($orders->isEmpty() || $orders->first()->user_id !== $user->id) {
                return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
            }

            $result = (array)Transaction::status($order_id_midtrans);
            $transactionStatus = $result['transaction_status'] ?? null;

            if (!$transactionStatus) {
                return response()->json(['success' => false, 'message' => 'Status transaksi tidak diketahui'], 422);
            }

            $statusMap = [
                'capture' => 'complete',
                'settlement' => 'complete',
                'pending' => 'pending',
                'deny' => 'cancel',
                'expire' => 'cancel',
                'cancel' => 'cancel',
            ];

            $statusBaru = $statusMap[$transactionStatus] ?? 'pending';

            DB::transaction(function () use ($orders, $statusBaru) {
                foreach ($orders as $order) {
                    $statusLama = $order->status;
                    if ($statusLama !== $statusBaru) {
                        $order->status = $statusBaru;
                        if ($statusBaru === 'complete') {
                            $order->status_pesanan = 'dikemas';
                            $order->dikemas_at = now();
                            if ($statusLama !== 'complete') {
                                $produk = Produk::where('id', $order->produk_id)->lockForUpdate()->first();
                                if ($produk) {
                                    $produk->stok = max(0, $produk->stok - $order->jumlah);
                                    $produk->save();
                                }
                            }
                        } elseif (in_array($statusBaru, ['cancel', 'expire', 'deny'])) {
                            if ($statusLama === 'complete') {
                                $produk = Produk::where('id', $order->produk_id)->lockForUpdate()->first();
                                if ($produk) {
                                    $produk->stok += $order->jumlah;
                                    $produk->save();
                                }
                            }
                            $order->batal_at = now();
                        }
                        $order->save();
                    }
                }
            });

            return response()->json([
                'success' => true,
                'transaction_status' => $transactionStatus,
                'status' => $statusBaru,
                'message' => 'Status pembayaran berhasil disinkronkan'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memeriksa status pembayaran',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

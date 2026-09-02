<?php

namespace App\Traits;

use App\Models\Order;
use App\Models\Produk;
use Midtrans\Config;
use Midtrans\Transaction;

trait SyncsMidtransStatus
{
    /**
     * Sinkronkan status semua order pending milik pembeli yang sedang login
     * langsung ke Midtrans. Dipakai untuk menutup celah ketika webhook
     * tidak sampai (misal: development di localhost).
     */
    protected function syncPendingOrdersMidtrans()
    {
        $userId = auth()->id();

        if (! $userId) {
            return;
        }

        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);

        $orders = Order::where('user_id', $userId)
            ->where('status', 'pending')
            ->whereNotNull('order_id_midtrans')
            ->orderBy('created_at', 'desc')
            ->get();

        $ordersGrouped = $orders->groupBy('order_id_midtrans');

        foreach ($ordersGrouped as $midtransOrderId => $orderGroup) {
            try {
                $result = Transaction::status($midtransOrderId);
            } catch (\Exception $e) {
                // Transaksi belum ada di Midtrans / jaringan error -> lewati
                continue;
            }

            // json_decode -> stdClass, konversi aman ke array
            $result = (array) $result;
            $midtransStatus = $result['transaction_status'] ?? null;

            if (! $midtransStatus) {
                continue;
            }

            if (in_array($midtransStatus, ['capture', 'settlement'])) {
                $statusBaru = 'complete';
            } elseif (in_array($midtransStatus, ['deny', 'expire', 'cancel'])) {
                $statusBaru = 'cancel';
            } else {
                // pending / lainnya -> belum berubah
                continue;
            }

            foreach ($orderGroup as $order) {
                $statusLama = $order->status;

                if ($statusLama === $statusBaru) {
                    continue;
                }

                $order->status = $statusBaru;

                if ($statusBaru === 'complete') {
                    $order->status_pesanan = 'dikemas';
                    $order->dikemas_at = now();

                    if ($statusLama !== 'complete') {
                        $produk = Produk::where('id', $order->produk_id)->first();
                        if ($produk) {
                            $produk->stok = max(0, $produk->stok - $order->jumlah);
                            $produk->save();
                        }
                    }
                } else {
                    if ($statusLama === 'complete') {
                        $produk = Produk::where('id', $order->produk_id)->first();
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
    }
}

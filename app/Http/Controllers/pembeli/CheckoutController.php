<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Order;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;
use Midtrans\Notification;

class CheckoutController extends Controller
{
    // Tampilkan halaman checkout dari keranjang
    public function index()
    {
        $items = Keranjang::where('user_id', Auth::id())
            ->with('produk.diskon')
            ->get()
            ->filter(fn($item) => $item->produk !== null);

        if ($items->isEmpty()) {
            return redirect()->route('pembeli.keranjang.index')->with('error', 'Keranjang kosong atau produk tidak tersedia.');
        }

        $totalHarga = $items->sum(function ($item) {
            $hargaSetelahDiskon = $this->hitungHargaSetelahDiskon($item->produk);
            $item->subtotal = $hargaSetelahDiskon * $item->jumlah;
            return $item->subtotal;
        });

        return view('pembeli.checkout', compact('items', 'totalHarga'));
    }

    // Checkout langsung tanpa keranjang
    public function checkoutProduk($produk_id, $quantity)
    {
        $produk = Produk::with('diskon')->findOrFail($produk_id);

        if ($produk->stok < $quantity) {
            return redirect()->back()->with('error', 'Stok produk tidak mencukupi.');
        }

        $hargaDiskon = $this->hitungHargaSetelahDiskon($produk);
        $subtotal = $hargaDiskon * $quantity;

        $items = collect([
            (object)[
                'produk' => $produk,
                'jumlah' => $quantity,
                'subtotal' => $subtotal,
            ]
        ]);

        return view('pembeli.checkout', [
            'items' => $items,
            'totalHarga' => $subtotal
        ]);
    }

    // Proses transaksi dan buat Snap Token
    public function checkout(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'alamat' => 'required|string',
        ]);

        $produk = Produk::with('diskon')->findOrFail($request->produk_id);

        if ($produk->stok < $request->jumlah) {
            return redirect()->back()->with('error', 'Stok produk tidak mencukupi.');
        }

        // Hitung harga setelah diskon
        $hargaDiskon = $this->hitungHargaSetelahDiskon($produk);
        $total_harga = round($hargaDiskon * $request->jumlah);

        // Cek apakah ada order yang belum punya order_id_midtrans
        $order = Order::where('user_id', Auth::id())
            ->where('produk_id', $produk->id)
            ->whereNull('order_id_midtrans')
            ->first();

        if (!$order) {
            $order = Order::create([
                'user_id' => Auth::id(),
                'produk_id' => $produk->id,
                'name' => $request->name,
                'alamat' => $request->alamat,
                'phone' => $request->phone,
                'jumlah' => $request->jumlah,
                'total_harga' => $total_harga,
                'status' => 'pending',
                'order_id_midtrans' => 'ORDER-' . uniqid(),
            ]);
        }

        // Konfigurasi Midtrans
        $this->setMidtransConfig();

        $params = [
            'transaction_details' => [
                'order_id' => $order->order_id_midtrans,
                'gross_amount' => $total_harga,
            ],
            'customer_details' => [
                'first_name' => $request->name,
                'phone' => $request->phone,
            ],
            'item_details' => [[
                'id' => $produk->id,
                'price' => round($hargaDiskon),
                'quantity' => $request->jumlah,
                'name' => substr($produk->nama, 0, 50),
            ]],
        ];

        $snapToken = Snap::getSnapToken($params);

        $order->update(['snap_token' => $snapToken]);

        // Siapkan data untuk view
        $items = collect([
            (object)[
                'produk' => $produk,
                'jumlah' => $request->jumlah,
                'subtotal' => $total_harga,
            ]
        ]);

        return view('pembeli.checkout', compact('snapToken', 'order', 'items', 'total_harga'));
    }


    // Callback Midtrans
    public function midtransCallback(Request $request)
    {
        $notif = new Notification();
        $transaction = $notif->transaction_status;
        $order_id = $notif->order_id;

        $orders = Order::where('order_id_midtrans', $order_id)->get();

        if ($orders->isEmpty()) {
            return response()->json(['message' => 'Order tidak ditemukan'], 404);
        }

        foreach ($orders as $order) {
            if (in_array($transaction, ['capture', 'settlement']) && $order->status !== 'complete') {
                $produk = Produk::find($order->produk_id);
    
                if ($produk && $produk->stok >= $order->jumlah) {
                    $produk->decrement('stok', $order->jumlah);
                    $order->update([
                        'status' => 'complete',
                        'stok_dikurangi' => 1,
                    ]);
                } else {
                    // Jika stok tidak mencukupi, set status cancel / failed
                    $order->update(['status' => 'cancel']);
                }
            } elseif (in_array($transaction, ['cancel', 'expire', 'deny'])) {
                $order->update(['status' => 'cancel']);
            }
        }

        return response()->json(['message' => 'Notifikasi diproses']);
    }

    // Hitung harga setelah diskon (jika berlaku)
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

    // Konfigurasi Midtrans
    protected function setMidtransConfig()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}

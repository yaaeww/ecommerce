<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ulasan;
use App\Models\Order;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        // Ambil semua ulasan pengguna beserta produk dan order
        $produkSudahDinilai = Ulasan::where('users_id', $userId)
            ->with('produk', 'order')
            ->latest()
            ->get();

        // Ambil order pengguna yang sudah diterima, include produk
        $orders = Order::where('user_id', $userId)
            ->where('status_pesanan', 'diterima')
            ->with('produk')
            ->get();

        $produkBelumDinilai = [];

        foreach ($orders as $order) {
            $produk = $order->produk;

            if ($produk) {
                $sudahDiulas = Ulasan::where('users_id', $userId)
                    ->where('orders_id', $order->id)
                    ->where('produks_id', $produk->id)
                    ->exists();

                if (!$sudahDiulas) {
                    $produkBelumDinilai[] = (object)[
                        'order' => $order,
                        'produk' => $produk,
                    ];
                }
            }
        }

        return view('pembeli.rating.index', [
            'produkBelumDinilai' => $produkBelumDinilai,
            'produkSudahDinilai' => $produkSudahDinilai,
        ]);
    }

    public function create(Request $request)
    {
        $orderId = $request->query('order');
        $productId = $request->query('product');
        $userId = Auth::id();

        // Validasi order yang dimiliki user dan status diterima
        $order = Order::where('id', $orderId)
            ->where('user_id', $userId)
            ->where('status_pesanan', 'diterima')
            ->firstOrFail();

        // Ambil data produk
        $produk = Produk::findOrFail($productId);

        // Cek apakah sudah diulas
        $exists = Ulasan::where('users_id', $userId)
            ->where('orders_id', $orderId)
            ->where('produks_id', $productId)
            ->exists();

        if ($exists) {
            return redirect()->route('pembeli.rating.index')
                ->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        return view('pembeli.rating.create', compact('order', 'produk'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'orders_id' => 'required|exists:orders,id',
            'produks_id' => 'required|exists:produks,id',
            'bintang' => 'required|integer|min:1|max:5',
            'ulasan' => 'required|string|max:500',
        ]);

        $userId = Auth::id();

        // Validasi order milik user dan status diterima & lunas
        $order = Order::where('id', $request->orders_id)
            ->where('user_id', $userId)
            ->where('status', 'complete')
            ->where('status_pesanan', 'diterima')
            ->firstOrFail();

        // Validasi produk
        $produk = Produk::findOrFail($request->produks_id);

        // Cek duplikat ulasan
        $exists = Ulasan::where('users_id', $userId)
            ->where('orders_id', $order->id)
            ->where('produks_id', $produk->id)
            ->exists();

        if ($exists) {
            return redirect()->route('pembeli.rating.index')
                ->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        // Simpan ulasan baru
        Ulasan::create([
            'users_id' => $userId,
            'orders_id' => $order->id,
            'produks_id' => $produk->id,
            'bintang' => $request->bintang,
            'ulasan' => $request->ulasan,
            'status_moderasi' => 'published',
        ]);

        // Sinkronisasi rata-rata rating pada tabel produks
        $avgRating = Ulasan::where('produks_id', $produk->id)
            ->where(function ($q) {
                $q->whereNull('status_moderasi')
                  ->orWhere('status_moderasi', '!=', 'hidden');
            })
            ->avg('bintang');

        $produk->update(['rating' => round($avgRating, 1)]);

        return redirect()->route('pembeli.rating.index')->with('success', 'Terima kasih atas ulasan Anda!');
    }
}

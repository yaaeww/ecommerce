<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Umkm;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardPenjualController extends Controller
{
    /**
     * Menampilkan halaman dashboard penjual.
     */
    public function index()
    {
        $user = Auth::user();

        // Ambil UMKM milik user penjual
        $umkm = Umkm::where('user_id', $user->id)->first();

        // Inisialisasi variabel default
        $produks = collect();
        $totalProduk = 0;
        $totalKategori = 0;
        $totalPembeliUnik = 0;
        $pendapatan = 0;
        $produkTerlaris = collect();

        if ($umkm) {
            // Ambil produk-produk dari UMKM terkait
            $produks = Produk::where('umkm_id', $umkm->id)
                ->latest()
                ->paginate(10);

            // Ambil semua ID produk dari UMKM
            $produkIds = $produks->pluck('id');

            // Hitung total produk
            $totalProduk = $produks->total();

            // Hitung jumlah kategori unik dari produk
            $totalKategori = Produk::where('umkm_id', $umkm->id)
                ->distinct('kategori_produk_id')
                ->count('kategori_produk_id');

            // Hitung jumlah pembeli unik dari pesanan produk yang statusnya "complete"
            $totalPembeliUnik = Order::whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->distinct('user_id')
                ->count('user_id');

            // Hitung total pendapatan dari pesanan yang selesai
            $pendapatan = Order::whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->sum('total_harga');

            // Hitung jumlah_terjual per produk
            $orderTerjual = Order::select('produk_id', DB::raw('SUM(jumlah) as jumlah_terjual'))
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->groupBy('produk_id')
                ->pluck('jumlah_terjual', 'produk_id'); // hasil: [produk_id => jumlah_terjual]

            // Masukkan ke masing-masing produk
            foreach ($produks as $produk) {
                $produk->jumlah_terjual = $orderTerjual[$produk->id] ?? 0;
            }

            // Ambil produk terlaris berdasarkan total_harga dari pesanan yang complete
            $produkTerlaris = DB::table('orders')
                ->join('produks', 'orders.produk_id', '=', 'produks.id')
                ->join('users', 'produks.user_id', '=', 'users.id')
                ->select(
                    'produks.id',
                    'produks.nama',
                    'produks.harga',
                    'produks.gambar',
                    'users.name as penjual_name',
                    DB::raw('SUM(orders.jumlah) as total_unit'), // UNIT terjual
                    DB::raw('SUM(orders.total_harga) as total_penjualan') // Total UANG
                )
                ->where('produks.umkm_id', $umkm->id)
                ->where('orders.status', 'complete')
                ->groupBy('produks.id', 'produks.nama', 'produks.harga', 'produks.gambar', 'users.name')
                ->orderByDesc('total_unit')
                ->limit(5)
                ->get();
        }

        return view('penjual.dashboard', compact(
            'umkm',
            'produks',
            'totalProduk',
            'totalKategori',
            'totalPembeliUnik',
            'pendapatan',
            'produkTerlaris'
        ));
    }
}

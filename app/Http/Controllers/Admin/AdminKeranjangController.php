<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Keranjang;
use App\Models\Produk;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminKeranjangController extends Controller
{
    /**
     * Tampilkan analisis keranjang belanja pembeli, produk paling diminati, dan potensi omzet tertahan.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        // 1. Data seluruh item di keranjang dengan relasi
        $cartQuery = Keranjang::with(['user', 'produk.umkm']);

        if ($search) {
            $cartQuery->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('produk', function ($p) use ($search) {
                    $p->where('nama', 'like', "%{$search}%")
                      ->orWhereHas('umkm', function ($um) use ($search) {
                          $um->where('nama_toko', 'like', "%{$search}%");
                      });
                });
            });
        }

        $activeCarts = $cartQuery->latest()->paginate(15)->withQueryString();

        // 2. Statistik Global Keranjang
        $totalItemsInCart = Keranjang::sum('quantity');
        $totalActiveUsersWithCart = Keranjang::distinct('user_id')->count('user_id');

        // Hitung total potensi GMV yang ada di keranjang
        $potentialGMV = DB::table('keranjangs')
            ->join('produks', 'keranjangs.produk_id', '=', 'produks.id')
            ->select(DB::raw('SUM(keranjangs.quantity * produks.harga) as total_potensi'))
            ->value('total_potensi') ?: 0;

        // 3. Top 5 Produk Paling Banyak Disimpan di Keranjang (High Demand / Wishlist Trend)
        $topDemandedProducts = DB::table('keranjangs')
            ->join('produks', 'keranjangs.produk_id', '=', 'produks.id')
            ->join('umkms', 'produks.umkm_id', '=', 'umkms.id')
            ->select(
                'produks.id',
                'produks.nama',
                'produks.harga',
                'produks.gambar',
                'umkms.nama_toko',
                DB::raw('SUM(keranjangs.quantity) as total_kuantitas'),
                DB::raw('COUNT(DISTINCT keranjangs.user_id) as total_pembeli_peminat'),
                DB::raw('SUM(keranjangs.quantity * produks.harga) as potensi_omzet_produk')
            )
            ->groupBy('produks.id', 'produks.nama', 'produks.harga', 'produks.gambar', 'umkms.nama_toko')
            ->orderByDesc('total_kuantitas')
            ->take(6)
            ->get();

        return view('admin.keranjang.index', compact(
            'activeCarts',
            'totalItemsInCart',
            'totalActiveUsersWithCart',
            'potentialGMV',
            'topDemandedProducts',
            'search'
        ));
    }
}

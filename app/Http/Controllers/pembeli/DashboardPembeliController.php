<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardPembeliController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategoriId = $request->input('kategori');

        $kategoriAktif = null;
        $subkategoris = collect();
        $kategoris = KategoriProduk::whereNull('parent_id')->orderBy('nama')->get();
        $produksQuery = Produk::query();

        // Jika ada filter kategori
        if ($kategoriId && is_numeric($kategoriId)) {
            $kategoriAktif = KategoriProduk::with('children')->find($kategoriId);

            if ($kategoriAktif) {
                $subkategoris = $kategoriAktif->children;

                $kategoriIds = $this->getAllKategoriIds($kategoriAktif);

                $produksQuery->whereIn('kategori_produk_id', $kategoriIds);

                if ($search) {
                    $produksQuery->where('nama', 'like', '%' . $search . '%');
                }

                $produks = $produksQuery->with('kategori')->latest()->paginate(12);
            } else {
                $produks = Produk::whereRaw('0 = 1')->paginate(12);
            }
        } else {
            if ($search) {
                $produksQuery->where('nama', 'like', '%' . $search . '%');
            }

            $produks = $produksQuery->with('kategori')->latest()->paginate(12);
        }

        // ✅✅✅ FIXED: Produk Terlaris dengan SUBQUERY ✅✅✅
        $produkTerlaris = Produk::select('produks.*')
            ->addSelect([
                'total_jumlah_pesanan' => Order::selectRaw('COALESCE(SUM(jumlah), 0)')
                    ->whereColumn('produk_id', 'produks.id')
                    ->where('status', 'complete')
            ])
            ->havingRaw('total_jumlah_pesanan >= ?', [10])
            ->orderByDesc('total_jumlah_pesanan')
            ->limit(8)
            ->get();

        $notifikasiDikirim = collect();
        if (Auth::check()) {
            $notifikasiDikirim = Order::where('user_id', Auth::id())
                ->where('status_pesanan', 'dikirim')
                ->latest()
                ->get();
        }

        return view('pembeli.dashboard', compact(
            'produks',
            'kategoris',
            'kategoriAktif',
            'subkategoris',
            'search',
            'produkTerlaris',
            'notifikasiDikirim'
        ));
    }

    private function getAllKategoriIds(KategoriProduk $kategori)
    {
        $ids = [$kategori->id];
        foreach ($kategori->children as $child) {
            $ids = array_merge($ids, $this->getAllKategoriIds($child));
        }
        return $ids;
    }
}

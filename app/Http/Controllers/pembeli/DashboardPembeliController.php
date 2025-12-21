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
        $produksQuery = Produk::query(); // base query

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
                // Kategori tidak ditemukan, tampilkan produk kosong dengan pagination
                $produks = Produk::whereRaw('0 = 1')->paginate(12);
            }
        } else {
            // Jika tidak ada kategori dipilih
            if ($search) {
                $produksQuery->where('nama', 'like', '%' . $search . '%');
            }

            $produks = $produksQuery->with('kategori')->latest()->paginate(12);
        }

        // Produk Terlaris (minimal 10 pesanan dengan status 'complete')
        $produkTerlaris = Produk::select([
            'produks.id',
            'produks.nama',
            'produks.harga',
            'produks.diskon',
            'produks.foto',
            'produks.kategori_produk_id',
            'produks.created_at',
            'produks.updated_at',
            DB::raw('COALESCE(SUM(CASE WHEN orders.status = "complete" THEN orders.jumlah ELSE 0 END), 0) as total_jumlah_pesanan')
        ])
            ->leftJoin('orders', 'orders.produk_id', '=', 'produks.id')
            ->groupBy(
                'produks.id',
                'produks.nama',
                'produks.harga',
                'produks.diskon',
                'produks.foto',
                'produks.kategori_produk_id',
                'produks.created_at',
                'produks.updated_at'
            )
            ->having('total_jumlah_pesanan', '>=', 10)
            ->orderByDesc('total_jumlah_pesanan')
            ->limit(8)
            ->get();

        // Alternatif yang lebih optimal jika ada banyak kolom:
        // $produkTerlaris = Produk::select('produks.*')
        //     ->selectRaw('COALESCE(SUM(CASE WHEN orders.status = "complete" THEN orders.jumlah ELSE 0 END), 0) as total_jumlah_pesanan')
        //     ->leftJoin('orders', 'orders.produk_id', '=', 'produks.id')
        //     ->groupBy('produks.id') // Hanya group by primary key jika sql_mode mengizinkan
        //     ->having('total_jumlah_pesanan', '>=', 10)
        //     ->orderByDesc('total_jumlah_pesanan')
        //     ->limit(8)
        //     ->get();

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

    /**
     * Ambil semua ID kategori anak termasuk induknya (rekursif)
     */
    private function getAllKategoriIds(KategoriProduk $kategori)
    {
        $ids = [$kategori->id];

        foreach ($kategori->children as $child) {
            $ids = array_merge($ids, $this->getAllKategoriIds($child));
        }

        return $ids;
    }
}

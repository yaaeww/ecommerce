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

        // SOLUSI 1: Menggunakan withSum() - Paling direkomendasikan
        $produkTerlaris = Produk::withSum([
            'order as total_jumlah_pesanan' => function ($query) {
                $query->where('status', 'complete');
            }
        ], 'jumlah')
            ->having('total_jumlah_pesanan', '>=', 10)
            ->orderByDesc('total_jumlah_pesanan')
            ->limit(8)
            ->get();

        // SOLUSI 2: Subquery alternatif
        // $produkTerlaris = Produk::select('produks.*')
        //     ->addSelect([
        //         'total_jumlah_pesanan' => Order::selectRaw('COALESCE(SUM(jumlah), 0)')
        //             ->whereColumn('produk_id', 'produks.id')
        //             ->where('status', 'complete')
        //     ])
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

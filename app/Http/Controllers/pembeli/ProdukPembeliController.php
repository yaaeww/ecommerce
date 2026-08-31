<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Ulasan;
use Illuminate\Support\Facades\DB;

class ProdukPembeliController extends Controller
{
    // Menampilkan semua produk untuk pembeli, dengan harga setelah diskon
    public function index()
    {
        $today = now();

        // Ambil produk beserta relasi diskon
        $produks = Produk::with('diskon')->latest()->paginate(12);

        // Hitung harga setelah diskon untuk setiap produk
        $produks->transform(function ($produk) use ($today) {
            $diskon = $produk->diskon;
            if ($diskon && $today->between($diskon->tanggal_mulai, $diskon->tanggal_berakhir)) {
                $produk->harga_setelah_diskon = round($produk->harga * (1 - ($diskon->persen_diskon / 100)), 2);
            } else {
                $produk->harga_setelah_diskon = $produk->harga;
            }
            return $produk;
        });

        return view('pembeli.produk.index', compact('produks'));
    }



    // Menampilkan detail produk beserta ulasan dan produk terkait, dengan harga diskon
    public function show($id)
    {
        $produk = Produk::with(['user', 'umkm', 'kategori', 'diskon'])->findOrFail($id);

        $today = now();
        $diskon = $produk->diskon;

        // Hitung harga setelah diskon jika berlaku
        if ($diskon && $today->between($diskon->tanggal_mulai, $diskon->tanggal_berakhir)) {
            $produk->harga_setelah_diskon = round($produk->harga * (1 - ($diskon->persen_diskon / 100)), 2);
        } else {
            $produk->harga_setelah_diskon = $produk->harga;
        }

        // Ambil ulasan
        $ulasan = Ulasan::with('user')
            ->where('produks_id', $id)
            ->latest()
            ->get();

        // Hitung rata-rata rating per user lalu global
        $avgBintang = DB::table(function ($query) use ($id) {
            $query->from('ulasan')
                ->select('users_id', DB::raw('AVG(bintang) as user_avg'))
                ->where('produks_id', $id)
                ->groupBy('users_id');
        }, 'subquery')
            ->select(DB::raw('AVG(user_avg) as rata_rata'))
            ->value('rata_rata');

        $produk->rating = round($avgBintang ?? 0, 2);

        // Produk terkait dari penjual atau kategori
        $produkTerkait = Produk::with(['umkm', 'diskon'])
            ->where('user_id', $produk->user_id)
            ->where('id', '!=', $produk->id)
            ->latest()
            ->take(4)
            ->get();

        // Hitung harga diskon untuk produk terkait juga
        $produkTerkait->transform(function ($p) use ($today) {
            $diskon = $p->diskon;
            if ($diskon && $today->between($diskon->tanggal_mulai, $diskon->tanggal_berakhir)) {
                $p->harga_setelah_diskon = round($p->harga * (1 - ($diskon->persen_diskon / 100)), 2);
            } else {
                $p->harga_setelah_diskon = $p->harga;
            }
            return $p;
        });

        return view('pembeli.produk.show', compact('produk', 'ulasan', 'produkTerkait'));
    }
}

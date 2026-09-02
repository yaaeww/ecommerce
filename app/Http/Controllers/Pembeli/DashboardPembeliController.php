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
        // 1. Ambil kategori beserta subkategori dan hitungan produk
        $kategoris = KategoriProduk::with(['subkategoris.produks', 'produks'])
            ->whereNull('parent_id')
            ->orderBy('nama')
            ->get();

        // 2. Query Produk
        $query = Produk::with(['diskon', 'umkm', 'kategori']);

        // Filter Kategori (Mendukung array checkbox atau single ID)
        if ($request->filled('kategori')) {
            $kategoriInputs = is_array($request->kategori) ? $request->kategori : [$request->kategori];
            
            // Ambil juga ID subkategori jika parent dipilih
            $allSelectedIds = [];
            foreach ($kategoriInputs as $kId) {
                if (is_numeric($kId)) {
                    $allSelectedIds[] = (int) $kId;
                    $kat = KategoriProduk::with('children')->find($kId);
                    if ($kat && $kat->children->isNotEmpty()) {
                        $allSelectedIds = array_merge($allSelectedIds, $this->getAllKategoriIds($kat));
                    }
                }
            }
            $allSelectedIds = array_unique($allSelectedIds);

            if (!empty($allSelectedIds)) {
                $query->whereIn('kategori_produk_id', $allSelectedIds);
            }
        }

        // Filter Pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $search . '%');
            });
        }

        // Filter Rentang Harga
        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        // Pengurutan (Sort)
        if ($request->filled('sort')) {
            if ($request->sort == 'termurah') {
                $query->orderBy('harga', 'asc');
            } elseif ($request->sort == 'termahal') {
                $query->orderBy('harga', 'desc');
            } elseif ($request->sort == 'terbaru') {
                $query->latest();
            }
        } else {
            $query->latest();
        }

        $produks = $query->paginate(12)->withQueryString();

        // Jika Request AJAX (untuk Live Filter / Search)
        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.product_grid', compact('produks'))->render(),
                'total' => $produks->total(),
                'from' => $produks->firstItem() ?? 0,
                'to' => $produks->lastItem() ?? 0,
            ]);
        }

        // 3. Penawaran Spesial Panen (Diskon Unggulan)
        $diskonProduks = Produk::with(['diskon', 'umkm'])
            ->whereHas('diskon', function ($q) {
                $q->where('persen_diskon', '>', 0)
                  ->where('tanggal_mulai', '<=', now())
                  ->where('tanggal_berakhir', '>=', now());
            })
            ->take(4)
            ->get();

        // 4. Ringkasan Status Pesanan Pembeli (Quick Order Hub)
        $orderStats = [
            'dikemas' => 0,
            'dikirim' => 0,
            'diterima' => 0,
        ];
        $notifikasiDikirim = collect();

        if (Auth::check()) {
            $userId = Auth::id();
            $orderStats['dikemas'] = Order::where('user_id', $userId)
                ->where('status', 'complete')
                ->where(function($q) {
                    $q->where('status_pesanan', 'dikemas')->orWhereNull('status_pesanan');
                })
                ->count();

            $orderStats['dikirim'] = Order::where('user_id', $userId)
                ->where('status', 'complete')
                ->where('status_pesanan', 'dikirim')
                ->count();

            $orderStats['diterima'] = Order::where('user_id', $userId)
                ->where('status', 'complete')
                ->where('status_pesanan', 'diterima')
                ->count();

            $notifikasiDikirim = Order::where('user_id', $userId)
                ->where('status', 'complete')
                ->where('status_pesanan', 'dikirim')
                ->latest()
                ->get();
        }

        return view('pembeli.dashboard', compact(
            'produks',
            'kategoris',
            'diskonProduks',
            'orderStats',
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

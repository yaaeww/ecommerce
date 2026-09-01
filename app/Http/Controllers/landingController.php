<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\Umkm;
use App\Models\Ulasan;
use App\Models\Order;

class LandingController extends Controller
{
    public function index(Request $request)
    {
        // Ambil kategori induk beserta subkategoris dan produks
        $kategoris = KategoriProduk::with(['subkategoris.produks', 'produks'])
            ->whereNull('parent_id')
            ->get();

        // Pencarian produk jika ada query search
        $search = $request->query('search');
        $produks = Produk::with(['diskon', 'umkm'])
            ->when($search, function ($query, $search) {
                return $query->where('nama', 'like', '%' . $search . '%')
                            ->orWhere('deskripsi', 'like', '%' . $search . '%');
            })
            ->latest()
            ->take(8)
            ->get();

        // Ambil produk diskon / flash sale
        $diskonProduks = Produk::with(['diskon', 'umkm'])
            ->whereHas('diskon', function ($q) {
                $q->where('persen_diskon', '>', 0)
                  ->where('tanggal_mulai', '<=', now())
                  ->where('tanggal_berakhir', '>=', now());
            })
            ->take(4)
            ->get();

        // Mitra UMKM
        $umkms = Umkm::with('user')->latest()->take(6)->get();

        // Ulasan / Testimoni (Kecualikan yang disembunyikan / hidden oleh Superadmin)
        $ulasans = Ulasan::with(['user', 'produk'])
            ->where(function($q) {
                $q->whereNull('status_moderasi')
                  ->orWhere('status_moderasi', '!=', 'hidden');
            })
            ->latest()
            ->take(4)
            ->get();

        // Statistik ringkas
        $stats = [
            'total_produk' => Produk::count(),
            'total_umkm' => Umkm::count(),
            'total_transaksi' => Order::where('status', 'complete')->count() + 150,
            'rating_rata' => '4.9',
        ];

        return view('landing', compact('kategoris', 'produks', 'diskonProduks', 'umkms', 'ulasans', 'stats'));
    }

    public function kategori(Request $request)
    {
        $kategoris = KategoriProduk::with(['subkategoris.produks', 'produks'])
            ->whereNull('parent_id')
            ->get();
            
        $query = Produk::with(['diskon', 'umkm']);
        
        // Filter by category
        if ($request->filled('kategori')) {
            $query->whereIn('kategori_produk_id', $request->kategori);
        }
        
        // Filter by search term
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('deskripsi', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by price
        if ($request->filled('min_harga')) {
            $query->where('harga', '>=', $request->min_harga);
        }
        if ($request->filled('max_harga')) {
            $query->where('harga', '<=', $request->max_harga);
        }

        // Sort
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

        if ($request->ajax()) {
            return response()->json([
                'html' => view('partials.product_grid', compact('produks'))->render(),
                'total' => $produks->total(),
                'from' => $produks->firstItem() ?? 0,
                'to' => $produks->lastItem() ?? 0,
            ]);
        }

        return view('kategori', compact('kategoris', 'produks'));
    }

    public function liveSearch(Request $request)
    {
        $q = trim($request->get('q', ''));

        if (strlen($q) < 1) {
            return response()->json([
                'products' => [],
                'categories' => [],
                'total' => 0
            ]);
        }

        $today = now();
        $produks = Produk::with(['diskon', 'umkm', 'kategori'])
            ->where(function($query) use ($q) {
                $query->where('nama', 'like', "%{$q}%")
                      ->orWhere('deskripsi', 'like', "%{$q}%");
            })
            ->take(6)
            ->get()
            ->map(function ($p) use ($today) {
                $hasDiskon = $p->diskon && $today->between($p->diskon->tanggal_mulai, $p->diskon->tanggal_berakhir);
                $hargaFinal = $p->harga;
                if ($hasDiskon) {
                    $hargaFinal = round($p->harga * (1 - ($p->diskon->persen_diskon / 100)), 2);
                }

                return [
                    'id' => $p->id,
                    'nama' => $p->nama,
                    'harga' => 'Rp ' . number_format($p->harga, 0, ',', '.'),
                    'harga_final' => 'Rp ' . number_format($hargaFinal, 0, ',', '.'),
                    'has_diskon' => $hasDiskon,
                    'persen_diskon' => $hasDiskon ? $p->diskon->persen_diskon : null,
                    'gambar_url' => $p->gambar ? asset('storage/' . $p->gambar) : null,
                    'toko' => $p->umkm->nama_toko ?? 'Mitra UMKM',
                    'kategori' => $p->kategori->nama ?? null,
                    'url' => route('pembeli.produk.show', $p->id),
                    'stok' => $p->stok
                ];
            });

        $categories = KategoriProduk::where('nama', 'like', "%{$q}%")
            ->take(3)
            ->get()
            ->map(function ($k) {
                return [
                    'id' => $k->id,
                    'nama' => $k->nama,
                    'url' => route('kategori', ['kategori' => [$k->id]])
                ];
            });

        $totalCount = Produk::where('nama', 'like', "%{$q}%")
            ->orWhere('deskripsi', 'like', "%{$q}%")
            ->count();

        return response()->json([
            'products' => $produks,
            'categories' => $categories,
            'total' => $totalCount,
            'all_results_url' => route('kategori', ['search' => $q])
        ]);
    }

    public function tentang()
    {
        return view('tentang');
    }
}

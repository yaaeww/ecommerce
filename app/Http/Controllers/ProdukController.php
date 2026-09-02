<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\Request;

class ProdukController extends Controller
{
    public function indexApi(Request $request)
    {
        try {
            $query = Produk::with(['diskon', 'kategori', 'umkm'])
                ->where('is_active', true)
                ->where('stok', '>', 0);

            if ($request->has('search') && $request->search !== '') {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('nama', 'like', "%{$search}%")
                      ->orWhere('deskripsi', 'like', "%{$search}%");
                });
            }

            if ($request->has('kategori_id') && $request->kategori_id !== '') {
                $query->where('kategori_produk_id', $request->kategori_id);
            }

            $produks = $query->orderBy('created_at', 'desc')->get();

            return response()->json([
                'success' => true,
                'data' => $produks
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function produkTerbaru()
    {
        try {
            $produks = Produk::with(['diskon', 'kategori', 'umkm'])
                ->where('is_active', true)
                ->where('stok', '>', 0)
                ->orderBy('created_at', 'desc')
                ->take(20)
                ->get();

            return response()->json([
                'success' => true,
                'data' => $produks
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk terbaru',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function showApi($id)
    {
        try {
            $produk = Produk::with([
                'diskon', 
                'kategori', 
                'umkm',
                'ulasan' => function($query) {
                    $query->where(function($q) {
                        $q->whereNull('status_moderasi')
                          ->orWhere('status_moderasi', '!=', 'hidden');
                    })
                    ->with('user')
                    ->orderBy('created_at', 'desc');
                }
            ])->find($id);

            if (!$produk || !$produk->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            $averageRating = $produk->ulasan->avg('bintang');
            $produk->average_rating = $averageRating ? round($averageRating, 1) : 0;

            return response()->json([
                'success' => true,
                'data' => $produk
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data produk',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function ulasanByProduk($id)
    {
        try {
            $produk = Produk::find($id);

            if (!$produk) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak ditemukan'
                ], 404);
            }

            $ulasans = $produk->ulasan()
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $ulasans
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data ulasan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
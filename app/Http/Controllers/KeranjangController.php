<?php

namespace App\Http\Controllers;

use App\Models\Keranjang;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class KeranjangController extends Controller
{
    public function indexApi(Request $request)
    {
        try {
            $user = $request->user();
            $keranjang = Keranjang::with(['produk' => function($query) {
                $query->with('diskon');
            }])
            ->where('user_id', $user->id)
            ->get();

            return response()->json([
                'success' => true,
                'data' => $keranjang
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data keranjang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function storeApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produk_id' => 'required|exists:produks,id',
            'jumlah' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $produk = Produk::find($request->produk_id);

            if (!$produk || !$produk->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'Produk tidak tersedia atau sudah tidak aktif'
                ], 400);
            }

            // Check stock
            if ($produk->stok < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $produk->stok
                ], 400);
            }

            // Check if item already in cart
            $existingItem = Keranjang::where('user_id', $user->id)
                ->where('produk_id', $request->produk_id)
                ->first();

            if ($existingItem) {
                $existingItem->update([
                    'jumlah' => $existingItem->jumlah + $request->jumlah
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Produk berhasil ditambahkan ke keranjang',
                    'data' => $existingItem
                ]);
            }

            $keranjang = Keranjang::create([
                'user_id' => $user->id,
                'produk_id' => $request->produk_id,
                'jumlah' => $request->jumlah,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang',
                'data' => $keranjang->load('produk.diskon')
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ke keranjang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateApi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $keranjang = Keranjang::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$keranjang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item keranjang tidak ditemukan'
                ], 404);
            }

            // Check stock
            if ($keranjang->produk->stok < $request->jumlah) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $keranjang->produk->stok
                ], 400);
            }

            $keranjang->update([
                'jumlah' => $request->jumlah
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diupdate',
                'data' => $keranjang->load('produk.diskon')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate keranjang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyApi(Request $request, $id)
    {
        try {
            $user = $request->user();
            $keranjang = Keranjang::where('user_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$keranjang) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item keranjang tidak ditemukan'
                ], 404);
            }

            $keranjang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus item dari keranjang',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function clearApi(Request $request)
    {
        try {
            $user = $request->user();
            Keranjang::where('user_id', $user->id)->delete();

            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengosongkan keranjang',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
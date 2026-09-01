<?php

namespace App\Http\Controllers;

use App\Models\Ulasan;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UlasanController extends Controller
{
    public function storeApi(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'produk_id' => 'required|exists:produks,id',
            'orders_id' => 'required|exists:orders,id',
            'bintang' => 'required|integer|min:1|max:5',
            'ulasan' => 'required|string|min:10',
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

            // Validasi kepemilikan dan status pesanan harus 'diterima' & lunas
            $order = \App\Models\Order::where('id', $request->orders_id)
                ->where('user_id', $user->id)
                ->where('status_pesanan', 'diterima')
                ->where('status', 'complete')
                ->first();

            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ulasan hanya dapat diberikan jika pesanan telah lunas dan sampai/diterima oleh pembeli.'
                ], 403);
            }

            // Check if user already reviewed this product
            $existingReview = Ulasan::where('users_id', $user->id)
                ->where('produks_id', $request->produk_id)
                ->where('orders_id', $request->orders_id)
                ->first();

            if ($existingReview) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah memberikan ulasan untuk produk ini'
                ], 400);
            }

            $ulasan = Ulasan::create([
                'users_id' => $user->id,
                'produks_id' => $request->produk_id,
                'orders_id' => $request->orders_id,
                'bintang' => $request->bintang,
                'ulasan' => $request->ulasan,
            ]);

            // Update product rating
            $produk = Produk::find($request->produk_id);
            $averageRating = Ulasan::where('produks_id', $request->produk_id)->avg('bintang');
            $produk->update([
                'rating' => round($averageRating, 1)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil ditambahkan',
                'data' => $ulasan->load('user')
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan ulasan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateApi(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'bintang' => 'sometimes|integer|min:1|max:5',
            'ulasan' => 'sometimes|string|min:10',
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
            $ulasan = Ulasan::where('users_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$ulasan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ulasan tidak ditemukan'
                ], 404);
            }

            $ulasan->update($request->only(['bintang', 'ulasan']));

            // Update product rating
            $produk = Produk::find($ulasan->produks_id);
            $averageRating = Ulasan::where('produks_id', $ulasan->produks_id)->avg('bintang');
            $produk->update([
                'rating' => round($averageRating, 1)
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil diupdate',
                'data' => $ulasan->load('user')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate ulasan',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function destroyApi(Request $request, $id)
    {
        try {
            $user = $request->user();
            $ulasan = Ulasan::where('users_id', $user->id)
                ->where('id', $id)
                ->first();

            if (!$ulasan) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ulasan tidak ditemukan'
                ], 404);
            }

            $produkId = $ulasan->produks_id;
            $ulasan->delete();

            // Update product rating
            $produk = Produk::find($produkId);
            $averageRating = Ulasan::where('produks_id', $produkId)->avg('bintang');
            $produk->update([
                'rating' => $averageRating ? round($averageRating, 1) : null
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ulasan berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus ulasan',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

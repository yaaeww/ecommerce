<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Komplain;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class KomplainApiController extends Controller
{
    /**
     * Get list of disputes / complaints by authenticated buyer
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $komplains = Komplain::with(['order.produk.umkm'])
                ->where('user_id', $user->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data komplain berhasil diambil',
                'data' => $komplains
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data komplain',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Submit new dispute / complaint
     */
    public function store(Request $request, $orderId)
    {
        $user = $request->user();
        $order = Order::where('id', $orderId)->where('user_id', $user->id)->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Pesanan tidak ditemukan'
            ], 404);
        }

        if ($order->komplain) {
            return response()->json([
                'success' => false,
                'message' => 'Anda sudah mengajukan komplain untuk pesanan ini',
                'data' => $order->komplain
            ], 400);
        }

        $validator = Validator::make($request->all(), [
            'tipe_komplain' => 'required|in:buah_busuk,kardus_rusak,berat_kurang,tidak_sesuai,lainnya',
            'deskripsi' => 'required|string|min:10|max:1000',
            'solusi_diminta' => 'required|in:refund,ganti_buah',
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'video_unboxing' => 'nullable|mimes:mp4,mov,avi,webm|max:20480',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $fotoPath = $request->file('foto_bukti')->store('bukti_komplain', 'public');
            $videoPath = null;
            if ($request->hasFile('video_unboxing')) {
                $videoPath = $request->file('video_unboxing')->store('video_komplain', 'public');
            }

            $komplain = Komplain::create([
                'order_id' => $order->id,
                'user_id' => $user->id,
                'tipe_komplain' => $request->tipe_komplain,
                'deskripsi' => $request->deskripsi,
                'solusi_diminta' => $request->solusi_diminta,
                'foto_bukti' => $fotoPath,
                'video_unboxing' => $videoPath,
                'status' => 'diajukan',
            ]);

            ActivityLog::record(
                'SUBMIT_DISPUTE',
                "Pembeli {$order->name} mengajukan komplain Garansi Segar untuk pesanan #{$order->id}",
                $komplain
            );

            return response()->json([
                'success' => true,
                'message' => 'Komplain Garansi Segar berhasil diajukan',
                'data' => $komplain
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengajukan komplain: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show complaint details
     */
    public function show(Request $request, $id)
    {
        try {
            $user = $request->user();
            $komplain = Komplain::with(['order.produk.umkm'])->where('user_id', $user->id)->find($id);

            if (!$komplain) {
                return response()->json([
                    'success' => false,
                    'message' => 'Komplain tidak ditemukan'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Detail komplain berhasil diambil',
                'data' => $komplain
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil detail komplain',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

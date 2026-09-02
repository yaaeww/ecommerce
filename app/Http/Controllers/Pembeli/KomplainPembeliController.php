<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Komplain;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomplainPembeliController extends Controller
{
    /**
     * Tampilkan daftar komplain garansi buah segar milik pembeli.
     */
    public function index()
    {
        $komplains = Komplain::with(['order.produk.umkm', 'order.user'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return view('pembeli.komplain.index', compact('komplains'));
    }

    /**
     * Form pengajuan komplain garansi kesegaran / barang rusak.
     */
    public function create(Order $order)
    {
        // Validasi kepemilikan pesanan
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Cek apakah sudah pernah komplain sebelumnya
        if ($order->komplain) {
            return redirect()->route('pembeli.komplain.show', $order->komplain->id)
                ->with('info', 'Anda sudah mengajukan komplain untuk pesanan ini.');
        }

        return view('pembeli.komplain.create', compact('order'));
    }

    /**
     * Simpan formulir komplain dan berkas bukti unboxing.
     */
    public function store(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Akses tidak sah.');
        }

        $request->validate([
            'tipe_komplain' => 'required|in:buah_busuk,kardus_rusak,berat_kurang,tidak_sesuai,lainnya',
            'deskripsi' => 'required|string|min:10|max:1000',
            'solusi_diminta' => 'required|in:refund,ganti_buah',
            'foto_bukti' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120',
            'video_unboxing' => 'nullable|mimes:mp4,mov,avi,webm|max:20480',
        ], [
            'foto_bukti.required' => 'Foto bukti kondisi buah segar / kardus wajib diunggah.',
            'deskripsi.required' => 'Mohon jelaskan secara rinci kendala buah yang Anda terima.',
            'deskripsi.min' => 'Deskripsi minimal 10 karakter.',
        ]);

        $fotoPath = $request->file('foto_bukti')->store('bukti_komplain', 'public');
        $videoPath = null;
        if ($request->hasFile('video_unboxing')) {
            $videoPath = $request->file('video_unboxing')->store('video_komplain', 'public');
        }

        $komplain = Komplain::create([
            'order_id' => $order->id,
            'user_id' => Auth::id(),
            'tipe_komplain' => $request->tipe_komplain,
            'deskripsi' => $request->deskripsi,
            'solusi_diminta' => $request->solusi_diminta,
            'foto_bukti' => $fotoPath,
            'video_unboxing' => $videoPath,
            'status' => 'diajukan',
        ]);

        ActivityLog::record(
            'SUBMIT_DISPUTE',
            "Pembeli {$order->name} mengajukan komplain Garansi Segar untuk pesanan #{$order->id} (Tipe: {$komplain->label_tipe}, Solusi: {$komplain->solusi_diminta})",
            $komplain
        );

        return redirect()->route('pembeli.komplain.show', $komplain->id)
            ->with('success', 'Komplain Garansi Segar berhasil diajukan! Tim Customer Care Juragan Pelem akan memverifikasi bukti dalam 1x24 jam.');
    }

    /**
     * Tampilkan detail status mediasi komplain.
     */
    public function show($id)
    {
        $komplain = Komplain::with(['order.produk.umkm', 'user'])->findOrFail($id);

        if ($komplain->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        return view('pembeli.komplain.show', compact('komplain'));
    }
}

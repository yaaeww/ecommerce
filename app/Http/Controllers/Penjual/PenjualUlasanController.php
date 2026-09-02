<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use App\Models\UMKM;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PenjualUlasanController extends Controller
{
    /**
     * 💬 Balas Ulasan Pelanggan oleh Toko Penjual
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'balasan_penjual' => 'required|string|max:1000',
        ]);

        $ulasan = Ulasan::with('produk.umkm')->findOrFail($id);

        // Validasi kepemilikan produk/toko
        $sellerId = Auth::id();
        if ($ulasan->produk->umkm->user_id !== $sellerId) {
            abort(403, 'Anda tidak memiliki hak akses untuk membalas ulasan produk toko lain.');
        }

        $ulasan->update([
            'balasan_penjual' => $request->balasan_penjual,
            'balasan_penjual_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Balasan ulasan resmi toko Anda berhasil dipublikasikan.');
    }
}

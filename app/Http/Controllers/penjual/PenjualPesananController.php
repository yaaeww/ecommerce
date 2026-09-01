<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\Order;
use App\Models\Produk;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PenjualPesananController extends Controller
{
    // Menampilkan daftar pesanan
    public function index()
    {
        $user = Auth::user();
        $umkm = Umkm::where('user_id', $user->id)->first();

        $pesananComplete = collect();
        $pesananCancel = collect();

        if ($umkm) {
            $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');

            // Ambil pesanan yang statusnya complete
            $pesananComplete = Order::with(['produk', 'user'])
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->latest()
                ->get();

            // Ambil pesanan yang statusnya cancel
            $pesananCancel = Order::with(['produk', 'user'])
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'cancel')
                ->latest()
                ->get();
        }

        return view('penjual.pesanan.index', compact('pesananComplete', 'pesananCancel'));
    }

    // Menampilkan detail pesanan
    public function create(Order $order)
    {
        // Pastikan produk yang dipesan milik penjual
        $penjualId = Auth::id();
        $isAuthorized = $order->produk->umkm->user_id === $penjualId;

        if (!$isAuthorized) {
            abort(403, 'Anda tidak memiliki akses ke pesanan ini.');
        }

        return view('penjual.pesanan.create', compact('order'));
    }

    // Update status pesanan
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status_pesanan' => 'required|in:dikemas,dikirim,diterima,belum_diterima',
            'no_resi' => 'nullable|string|max:100',
            'kurir_ekspedisi' => 'nullable|string|max:100',
            'foto_bukti_pengiriman' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $penjualId = Auth::id();
        if ($order->produk->umkm->user_id !== $penjualId) {
            abort(403, 'Anda tidak memiliki akses untuk mengubah status pesanan ini.');
        }

        $updateData = [
            'status_pesanan' => $request->status_pesanan,
        ];

        if ($request->filled('no_resi')) {
            $updateData['resi_pengiriman'] = $request->no_resi;
        }
        if ($request->filled('kurir_ekspedisi')) {
            $updateData['kurir'] = $request->kurir_ekspedisi;
        }
        if ($request->hasFile('foto_bukti_pengiriman')) {
            $fotoPath = $request->file('foto_bukti_pengiriman')->store('bukti_pengiriman', 'public');
            $updateData['foto_bukti_pengiriman'] = $fotoPath;
        }

        $order->update($updateData);

        return redirect()->route('penjual.pesanan.index')->with('success', 'Status pesanan, resi, dan bukti pengiriman berhasil diperbarui.');
    }

}

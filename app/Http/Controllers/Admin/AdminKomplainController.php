<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Komplain;
use App\Models\Order;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config as MidtransConfig;
use Midtrans\CoreApi;
use Midtrans\Securer;
use Carbon\Carbon;

class AdminKomplainController extends Controller
{
    /**
     * Tampilkan seluruh sengketa/komplain komoditas dari pembeli.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'semua');
        $search = $request->get('search');

        $query = Komplain::with(['order.produk.umkm', 'user']);

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('order.produk', function ($p) use ($search) {
                      $p->where('nama', 'like', "%{$search}%");
                  });
            });
        }

        $komplains = $query->latest()->paginate(12)->withQueryString();

        // Statistik Cepat
        $totalKomplain = Komplain::count();
        $totalDiajukan = Komplain::where('status', 'diajukan')->count();
        $totalDisetujui = Komplain::where('status', 'disetujui')->count();
        $totalDitolak = Komplain::where('status', 'ditolak')->count();

        return view('admin.komplain.index', compact(
            'komplains',
            'status',
            'search',
            'totalKomplain',
            'totalDiajukan',
            'totalDisetujui',
            'totalDitolak'
        ));
    }

    /**
     * Tampilkan investigasi & rincian komplain unboxing.
     */
    public function show($id)
    {
        $komplain = Komplain::with(['order.produk.umkm', 'user'])->findOrFail($id);
        return view('admin.komplain.show', compact('komplain'));
    }

    /**
     * Putuskan mediasi sengketa komplain: Disetujui (Refund/Ganti Buah) atau Ditolak.
     */
    public function process(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diproses,disetujui,ditolak,selesai',
            'catatan_admin' => 'required|string|max:1000',
            'nominal_refund' => 'nullable|numeric|min:0',
        ], [
            'catatan_admin.required' => 'Catatan keputusan mediasi wajib diisi.',
        ]);

        $komplain = Komplain::with(['order.produk.umkm', 'user'])->findOrFail($id);

        $updateData = [
            'status' => $request->status,
            'catatan_admin' => $request->catatan_admin,
            'diproses_at' => Carbon::now(),
        ];

        if ($request->status === 'disetujui') {
            $nominal = $request->filled('nominal_refund') ? $request->nominal_refund : $komplain->order->total_harga;
            $updateData['nominal_refund'] = $nominal;
            $updateData['selesai_at'] = Carbon::now();

            // Kunci penampungan dana (escrow) agar tidak diteruskan ke saldo toko penjual
            if ($komplain->order) {
                $komplain->order->update([
                    'is_escrow_released' => false,
                ]);
            }
        } elseif ($request->status === 'selesai' || $request->status === 'ditolak') {
            $updateData['selesai_at'] = Carbon::now();
        }

        $komplain->update($updateData);

        ActivityLog::record(
            'RESOLVE_DISPUTE',
            "Superadmin memutuskan komplain #{$komplain->id} (Order #{$komplain->order_id}) dengan status [{$request->status}]. Catatan: {$request->catatan_admin}",
            $komplain
        );

        return redirect()->back()->with('success', "Keputusan mediasi komplain #{$komplain->id} berhasil diperbarui.");
    }
}

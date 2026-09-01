<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Umkm;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminPengirimanController extends Controller
{
    /**
     * Tampilkan monitoring siklus pengiriman & SLA fulfillment pesanan toko mitra.
     */
    public function index(Request $request)
    {
        $statusFulfillment = $request->get('sla_status', 'semua');
        $umkmId = $request->get('umkm_id');
        $search = $request->get('search');

        $query = Order::with(['user', 'produk.umkm.user', 'produks.umkm'])
            ->where('status', 'complete'); // Hanya pesanan yang sudah lunas dibayar

        // Filter Toko
        if ($umkmId) {
            $query->whereHas('produk', function ($q) use ($umkmId) {
                $q->where('umkm_id', $umkmId);
            });
        }

        // Filter Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhere('resi_pengiriman', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhereHas('produk', function ($p) use ($search) {
                        $p->where('nama', 'like', "%{$search}%")
                            ->orWhereHas('umkm', function ($u) use ($search) {
                                $u->where('nama_toko', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $now = Carbon::now();

        // Hitung statistik fulfillment
        $allCompleteOrders = (clone $query)->get();

        $totalLunas = $allCompleteOrders->count();
        $totalOverdue = 0;
        $totalDiproses = 0;
        $totalDikirim = 0;
        $totalDiterima = 0;

        foreach ($allCompleteOrders as $ord) {
            $hoursSincePaid = $ord->created_at->diffInHours($now);
            $isOverdue = in_array($ord->status_pesanan, ['dikemas', null, '']) && $hoursSincePaid >= 24;

            if ($isOverdue) {
                $totalOverdue++;
            }

            if (in_array($ord->status_pesanan, ['dikemas', null, ''])) {
                $totalDiproses++;
            } elseif ($ord->status_pesanan === 'dikirim') {
                $totalDikirim++;
            } elseif ($ord->status_pesanan === 'diterima') {
                $totalDiterima++;
            }
        }

        // Apply SLA status filter
        if ($statusFulfillment === 'overdue') {
            $query->where(function ($q) {
                $q->whereIn('status_pesanan', ['dikemas', null])
                    ->where('created_at', '<=', Carbon::now()->subHours(24));
            });
        } elseif ($statusFulfillment === 'dikemas') {
            $query->where(function ($q) {
                $q->whereIn('status_pesanan', ['dikemas', null])
                    ->where('created_at', '>', Carbon::now()->subHours(24));
            });
        } elseif ($statusFulfillment === 'dikirim') {
            $query->where('status_pesanan', 'dikirim');
        } elseif ($statusFulfillment === 'diterima') {
            $query->where('status_pesanan', 'diterima');
        }

        $orders = $query->latest()->paginate(12)->withQueryString();
        $umkms = Umkm::orderBy('nama_toko')->get();

        return view('admin.pengiriman.index', compact(
            'orders',
            'statusFulfillment',
            'umkmId',
            'search',
            'umkms',
            'totalLunas',
            'totalOverdue',
            'totalDiproses',
            'totalDikirim',
            'totalDiterima'
        ));
    }

    /**
     * Update Resi & Kurir Pengiriman oleh Superadmin.
     */
    public function updateResi(Request $request, $id)
    {
        $request->validate([
            'resi_pengiriman' => 'required|string|max:100',
            'kurir' => 'required|string|max:100',
            'status_pesanan' => 'required|in:dikemas,dikirim,diterima',
            'catatan_pengiriman' => 'nullable|string|max:500',
        ]);

        $order = Order::findOrFail($id);
        $order->resi_pengiriman = $request->resi_pengiriman;
        $order->kurir = $request->kurir;
        $order->status_pesanan = $request->status_pesanan;
        $order->catatan_pengiriman = $request->catatan_pengiriman;

        if ($request->status_pesanan === 'dikirim' && !$order->tanggal_dikirim) {
            $order->tanggal_dikirim = Carbon::now();
        }

        $order->save();

        ActivityLog::record(
            'UPDATE_PENGIRIMAN',
            "Superadmin memperbarui resi #ORD-{$order->id} ({$request->kurir} - {$request->resi_pengiriman}) status: {$request->status_pesanan}",
            $order
        );

        return redirect()->back()->with('success', "Data pengiriman pesanan #ORD-{$order->id} berhasil diperbarui!");
    }
}

<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\Order;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PenjualPesananController extends Controller
{
    // Menampilkan daftar pesanan dengan filter tanggal kalender
    public function index(Request $request)
    {
        $user = Auth::user();
        $umkm = Umkm::where('user_id', $user->id)->first();

        // Date filter parameters
        $period = $request->get('period', 'all');
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');
        
        $startDate = null;
        $endDate = null;
        $activePeriodLabel = 'Semua Waktu';

        if ($period === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
            $activePeriodLabel = 'Hari Ini (' . $startDate->translatedFormat('d F Y') . ')';
        } elseif ($period === '7days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $activePeriodLabel = '7 Hari Terakhir';
        } elseif ($period === '30days') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $activePeriodLabel = '30 Hari Terakhir';
        } elseif ($period === 'this_month') {
            $startDate = Carbon::now()->startOfMonth()->startOfDay();
            $endDate = Carbon::now()->endOfMonth()->endOfDay();
            $activePeriodLabel = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
        } elseif ($period === 'this_year') {
            $startDate = Carbon::now()->startOfYear()->startOfDay();
            $endDate = Carbon::now()->endOfYear()->endOfDay();
            $activePeriodLabel = 'Tahun Ini (' . $startDate->translatedFormat('Y') . ')';
        } elseif ($startDateInput) {
            $period = 'custom';
            $startDate = Carbon::parse($startDateInput)->startOfDay();
            $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : Carbon::parse($startDateInput)->endOfDay();
            if ($startDate->isSameDay($endDate)) {
                $activePeriodLabel = $startDate->translatedFormat('d F Y');
            } else {
                $activePeriodLabel = $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
            }
        }

        $pesananComplete = collect();
        $pesananCancel = collect();

        if ($umkm) {
            $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');

            // Query pesanan complete
            $completeQuery = Order::with(['produk', 'user'])
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete');

            if ($startDate && $endDate) {
                $completeQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $pesananComplete = $completeQuery->latest()->get();

            // Query pesanan cancel
            $cancelQuery = Order::with(['produk', 'user'])
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'cancel');

            if ($startDate && $endDate) {
                $cancelQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $pesananCancel = $cancelQuery->latest()->get();
        }

        return view('penjual.pesanan.index', compact(
            'pesananComplete',
            'pesananCancel',
            'period',
            'startDateInput',
            'endDateInput',
            'activePeriodLabel'
        ));
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

        if ($request->status_pesanan === 'dikemas' && !$order->dikemas_at) {
            $updateData['dikemas_at'] = now();
        } elseif ($request->status_pesanan === 'dikirim') {
            $updateData['dikirim_at'] = now();
            $updateData['tanggal_dikirim'] = now();
        } elseif ($request->status_pesanan === 'diterima') {
            $updateData['diterima_at'] = now();
            $updateData['is_escrow_released'] = true;
        }

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

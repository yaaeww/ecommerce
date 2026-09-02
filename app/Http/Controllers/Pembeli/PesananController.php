<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Order;
use App\Models\Produk;
use App\Traits\SyncsMidtransStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PesananController extends Controller
{
    use SyncsMidtransStatus;

    public function index()
    {
        // Sinkronkan dulu status pembayaran langsung ke Midtrans
        $this->syncPendingOrdersMidtrans();

        $orders = Order::with(['produk.umkm', 'produk.kategori', 'komplain', 'ulasan'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return view('pembeli.pesanan.index', ['orders' => $orders]);
    }

    public function statusDikemas()
    {
        $orders = Order::with(['produk.umkm', 'komplain'])
            ->where('user_id', Auth::id())
            ->where(function ($q) {
                $q->where('status_pesanan', 'dikemas')
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'complete')->whereNull('status_pesanan');
                    });
            })
            ->latest()
            ->get();

        return view('pembeli.pesanan.dikemas', ['orders' => $orders]);
    }

    public function dikirim()
    {
        $orders = Order::with(['produk.umkm', 'komplain'])
            ->where('user_id', Auth::id())
            ->whereIn('status_pesanan', ['dikirim', 'diterima'])
            ->latest()
            ->get();

        return view('pembeli.pesanan.dikirim', ['orders' => $orders]);
    }

    public function updateStatus(Request $request, $id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Validasi ketat: order harus complete, berstatus dikirim, memiliki nomor resi, dan belum pernah direlease
        if ($order->status === 'complete' && $order->status_pesanan === 'dikirim' && ! empty($order->resi_pengiriman) && ! $order->is_escrow_released) {
            $order->status_pesanan = 'diterima';
            $order->diterima_at = now();
            $order->is_escrow_released = true;
            $order->save();

            ActivityLog::record(
                'ORDER_CONFIRMED',
                "Pembeli {$order->name} telah mengonfirmasi penerimaan pesanan #{$order->id} ({$order->produk->nama})",
                $order
            );

            return redirect()->route('pembeli.pesanan.index')
                ->with('success', 'Pesanan telah dikonfirmasi diterima. Saldo bagi hasil telah diteruskan ke toko.');
        }

        return back()->with('error', 'Status pesanan tidak dapat diubah atau belum memenuhi syarat pengiriman.');
    }

    /**
     * ⏳ Pembatalan Pesanan oleh Pembeli (Sebelum Dikirim)
     */
    public function cancelOrder(Request $request, $id)
    {
        $request->validate([
            'batal_alasan' => 'required|string|max:500',
        ], [
            'batal_alasan.required' => 'Mohon sertakan alasan pembatalan pesanan.',
        ]);

        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Hanya boleh dibatalkan jika belum dikirim (belum ada resi & status dikemas/pending)
        if ($order->status_pesanan === 'dikirim' || $order->status_pesanan === 'diterima') {
            return back()->with('error', 'Pesanan yang sudah dalam proses pengiriman atau diterima tidak dapat dibatalkan.');
        }

        if ($order->status === 'cancel') {
            return back()->with('warning', 'Pesanan ini sudah dibatalkan sebelumnya.');
        }

        $statusAwal = $order->status;

        // Kembalikan kuota stok produk HANYA jika status sebelumnya complete (karena saat pending stok belum terpotong)
        if ($statusAwal === 'complete') {
            $produk = Produk::find($order->produk_id);
            if ($produk) {
                $produk->increment('stok', $order->jumlah);
            }
        }

        $order->update([
            'status' => 'cancel',
            'status_pesanan' => null,
            'is_escrow_released' => false,
            'batal_alasan' => $request->batal_alasan,
            'batal_at' => now(),
        ]);

        ActivityLog::record(
            'BUYER_CANCEL_ORDER',
            "Pembeli {$order->name} membatalkan pesanan #{$order->id}. Alasan: {$request->batal_alasan}",
            $order
        );

        return redirect()->route('pembeli.pesanan.index')
            ->with('success', 'Pesanan berhasil dibatalkan.');
    }

    public function destroy($id)
    {
        $order = Order::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status === 'cancel') {
            $order->delete();

            return back()->with('success', 'Pesanan berhasil dihapus.');
        }

        return back()->with('error', 'Hanya pesanan dengan status cancel yang bisa dihapus.');
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('order_ids', []);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada pesanan yang dipilih untuk dihapus.');
        }

        $deletedCount = Order::whereIn('id', $ids)
            ->where('user_id', Auth::id())
            ->where('status', 'cancel')
            ->delete();

        if ($deletedCount > 0) {
            return back()->with('success', "$deletedCount pesanan berhasil dihapus.");
        }

        return back()->with('error', 'Tidak ada pesanan dengan status cancel yang dipilih.');
    }
}

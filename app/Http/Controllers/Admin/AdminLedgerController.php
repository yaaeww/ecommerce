<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Umkm;
use App\Models\PenarikanSaldo;
use App\Models\Komplain;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminLedgerController extends Controller
{
    /**
     * Tampilkan Buku Besar Akuntansi Platform & Monitoring Saldo Escrow.
     */
    public function index(Request $request)
    {
        $komisiPersen = (float) Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        // 1. Total Transaksi Masuk (Gross Midtrans Inflow)
        $totalGrossInflow = Order::where('status', 'complete')->sum('total_harga');

        // 2. Total Dana Tertahan di Rekening Escrow Platform (Belum Diterima / Dalam Pengiriman)
        $totalEscrowHolding = Order::where('status', 'complete')
            ->where(function($q) {
                $q->whereIn('status_pesanan', ['dikemas', 'dikirim', 'belum_diterima'])
                  ->orWhereNull('status_pesanan');
            })
            ->where('is_escrow_released', false)
            ->sum('total_harga');

        // 3. Total Transaksi Selesai / Settled (Pesanan Diterima)
        $totalSettledGross = Order::where('status', 'complete')
            ->where(function($q) {
                $q->where('status_pesanan', 'diterima')
                  ->orWhere('is_escrow_released', true);
            })
            ->sum('total_harga');

        // 4. Total Hak Bersih Petani / Toko (Settled)
        $totalHakBersihTokoSettled = $totalSettledGross * ($tokoPersen / 100);

        // 5. Total Payout Petani yang Sudah Dicairkan (Approved)
        $totalPayoutApproved = PenarikanSaldo::where('status', 'approved')->sum('jumlah');

        // 6. Total Payout Petani yang Sedang Antre Verifikasi (Pending)
        $totalPayoutPending = PenarikanSaldo::where('status', 'pending')->sum('jumlah');

        // 7. Sisa Kewajiban Hutang Platform ke Petani (Unclaimed Settled Balance)
        $sisaKewajibanToko = max(0, $totalHakBersihTokoSettled - $totalPayoutApproved - $totalPayoutPending);

        // 8. Total Keuntungan Bersih Platform (20% Komisi dari seluruh pesanan selesai)
        $totalPlatformRevenue = $totalSettledGross * ($komisiPersen / 100);
        $potensiKomisiEscrow = $totalEscrowHolding * ($komisiPersen / 100);

        // 9. Total Refund Dikembalikan ke Pembeli
        $totalRefundApproved = Komplain::where('status', 'disetujui')->sum('nominal_refund');

        // 10. Rekapitulasi per Toko UMKM Mitra
        $umkms = Umkm::with('user')->get()->map(function($umkm) use ($komisiPersen, $tokoPersen) {
            $grossSettled = DB::table('orders')
                ->join('produks', 'orders.produk_id', '=', 'produks.id')
                ->where('produks.umkm_id', $umkm->id)
                ->where('orders.status', 'complete')
                ->where(function($q) {
                    $q->where('orders.status_pesanan', 'diterima')
                      ->orWhere('orders.is_escrow_released', true);
                })
                ->sum('orders.total_harga');

            $grossEscrow = DB::table('orders')
                ->join('produks', 'orders.produk_id', '=', 'produks.id')
                ->where('produks.umkm_id', $umkm->id)
                ->where('orders.status', 'complete')
                ->where(function($q) {
                    $q->whereIn('orders.status_pesanan', ['dikemas', 'dikirim', 'belum_diterima'])
                      ->orWhereNull('orders.status_pesanan');
                })
                ->where('orders.is_escrow_released', false)
                ->sum('orders.total_harga');

            $hakBersih = $grossSettled * ($tokoPersen / 100);
            $payoutApproved = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'approved')->sum('jumlah');
            $payoutPending = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'pending')->sum('jumlah');
            $saldoSiapTarik = max(0, $hakBersih - $payoutApproved - $payoutPending);

            return (object)[
                'umkm' => $umkm,
                'gross_total' => $grossSettled + $grossEscrow,
                'gross_settled' => $grossSettled,
                'gross_escrow' => $grossEscrow,
                'hak_bersih' => $hakBersih,
                'payout_approved' => $payoutApproved,
                'payout_pending' => $payoutPending,
                'saldo_siap_tarik' => $saldoSiapTarik,
            ];
        });

        // 11. Log Transaksi Masuk & Keluar Terkini (General Ledger Feed)
        $recentOrders = Order::with(['produk.umkm', 'user'])
            ->where('status', 'complete')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.ledger.index', compact(
            'komisiPersen',
            'tokoPersen',
            'totalGrossInflow',
            'totalEscrowHolding',
            'totalSettledGross',
            'totalHakBersihTokoSettled',
            'totalPayoutApproved',
            'totalPayoutPending',
            'sisaKewajibanToko',
            'totalPlatformRevenue',
            'potensiKomisiEscrow',
            'totalRefundApproved',
            'umkms',
            'recentOrders'
        ));
    }
}

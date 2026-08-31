<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendapatanController extends Controller
{
    public function index(Request $request)
    {
        // Default ke bulan dan tahun saat ini
        $bulan = $request->get('bulan', date('m'));
        $tahun = $request->get('tahun', date('Y'));
        $filterMinggu = $request->get('minggu', false);

        $query = DB::table('orders')
            ->join('produks', 'orders.produk_id', '=', 'produks.id')
            ->join('umkms', 'produks.umkm_id', '=', 'umkms.id')
            ->where('orders.status', 'complete');

        // Filter berdasarkan bulan dan tahun
        if ($bulan && $tahun) {
            $query->whereYear('orders.created_at', $tahun)
                ->whereMonth('orders.created_at', $bulan);
        }

        // Filter minggu ini jika dipilih
        if ($filterMinggu) {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $query->whereBetween('orders.created_at', [$startOfWeek, $endOfWeek]);
        }

        // Total pendapatan
        $totalPendapatan = $query->sum('orders.total_harga');
        $pendapatanAdmin = $totalPendapatan * 0.2;

        // Query untuk rekap per toko (gunakan query yang sama dengan filter)
        $rekapQuery = DB::table('orders')
            ->join('produks', 'orders.produk_id', '=', 'produks.id')
            ->join('umkms', 'produks.umkm_id', '=', 'umkms.id')
            ->where('orders.status', 'complete');

        // Apply same filters to rekap query
        if ($bulan && $tahun) {
            $rekapQuery->whereYear('orders.created_at', $tahun)
                ->whereMonth('orders.created_at', $bulan);
        }

        if ($filterMinggu) {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $rekapQuery->whereBetween('orders.created_at', [$startOfWeek, $endOfWeek]);
        }

        $rekapPerToko = $rekapQuery->select('umkms.nama_toko', 'umkms.user_id', DB::raw('SUM(orders.total_harga) as total_penjualan'))
            ->groupBy('umkms.nama_toko', 'umkms.user_id')
            ->orderByDesc('total_penjualan')
            ->get();

        // Data untuk dropdown filter
        $tahunList = range(date('Y') - 2, date('Y'));
        $bulanList = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember'
        ];

        // Info periode yang ditampilkan
        $periodeInfo = $this->getPeriodeInfo($bulan, $tahun, $filterMinggu);

        return view('admin.pendapatan.index', compact(
            'totalPendapatan',
            'pendapatanAdmin',
            'rekapPerToko',
            'bulan',
            'tahun',
            'filterMinggu',
            'tahunList',
            'bulanList',
            'periodeInfo'
        ));
    }

    private function getPeriodeInfo($bulan, $tahun, $filterMinggu)
    {
        if ($filterMinggu) {
            $startOfWeek = Carbon::now()->startOfWeek()->format('d M Y');
            $endOfWeek = Carbon::now()->endOfWeek()->format('d M Y');
            return "Minggu Ini ($startOfWeek - $endOfWeek)";
        }

        if ($bulan && $tahun) {
            $bulanList = [
                1 => 'Januari',
                2 => 'Februari',
                3 => 'Maret',
                4 => 'April',
                5 => 'Mei',
                6 => 'Juni',
                7 => 'Juli',
                8 => 'Agustus',
                9 => 'September',
                10 => 'Oktober',
                11 => 'November',
                12 => 'Desember'
            ];
            return ($bulanList[(int)$bulan] ?? 'Bulan ' . $bulan) . ' ' . $tahun;
        }

        return 'Semua Waktu';
    }
}

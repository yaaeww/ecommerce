<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PendapatanController extends Controller
{
    public function index(Request $request)
    {
        // Ambil persentase komisi dinamis dari database (default: 20%)
        $komisiPersen = (float) Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        // Date filter parameters
        $period = $request->get('period');
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');
        
        $startDate = null;
        $endDate = null;
        $periodeInfo = '';

        if ($period === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
            $periodeInfo = 'Hari Ini (' . $startDate->translatedFormat('d F Y') . ')';
        } elseif ($period === '7days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $periodeInfo = '7 Hari Terakhir';
        } elseif ($period === '30days') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $periodeInfo = '30 Hari Terakhir';
        } elseif ($period === 'this_month') {
            $startDate = Carbon::now()->startOfMonth()->startOfDay();
            $endDate = Carbon::now()->endOfMonth()->endOfDay();
            $periodeInfo = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
        } elseif ($period === 'this_year') {
            $startDate = Carbon::now()->startOfYear()->startOfDay();
            $endDate = Carbon::now()->endOfYear()->endOfDay();
            $periodeInfo = 'Tahun Ini (' . $startDate->translatedFormat('Y') . ')';
        } elseif ($startDateInput) {
            $period = 'custom';
            $startDate = Carbon::parse($startDateInput)->startOfDay();
            $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : Carbon::parse($startDateInput)->endOfDay();
            if ($startDate->isSameDay($endDate)) {
                $periodeInfo = $startDate->translatedFormat('d F Y');
            } else {
                $periodeInfo = $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
            }
        }

        // Fallback to legacy month/year if period not given
        $bulan = $request->get('bulan', $startDate ? null : date('m'));
        $tahun = $request->get('tahun', $startDate ? null : date('Y'));
        $filterMinggu = $request->get('minggu', false);

        $query = DB::table('orders')
            ->join('produks', 'orders.produk_id', '=', 'produks.id')
            ->join('umkms', 'produks.umkm_id', '=', 'umkms.id')
            ->where('orders.status', 'complete');

        // Apply filters
        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [$startDate, $endDate]);
        } elseif ($filterMinggu) {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $query->whereBetween('orders.created_at', [$startOfWeek, $endOfWeek]);
            $periodeInfo = "Minggu Ini (" . $startOfWeek->translatedFormat('d M') . " - " . $endOfWeek->translatedFormat('d M Y') . ")";
        } elseif ($bulan && $tahun) {
            $query->whereYear('orders.created_at', $tahun)
                ->whereMonth('orders.created_at', $bulan);
            $periodeInfo = $this->getPeriodeInfo($bulan, $tahun, false);
        } elseif ($tahun) {
            $query->whereYear('orders.created_at', $tahun);
            $periodeInfo = "Tahun " . $tahun;
        } else {
            $periodeInfo = 'Semua Waktu';
        }

        // Total pendapatan kotor & komisi terhitung
        $totalPendapatan = (float) $query->sum('orders.total_harga');
        $pendapatanAdmin = $totalPendapatan * ($komisiPersen / 100);
        $pendapatanMitra = $totalPendapatan * ($tokoPersen / 100);

        // Query untuk rekap per toko (gunakan query yang sama dengan filter)
        $rekapQuery = DB::table('orders')
            ->join('produks', 'orders.produk_id', '=', 'produks.id')
            ->join('umkms', 'produks.umkm_id', '=', 'umkms.id')
            ->where('orders.status', 'complete');

        if ($startDate && $endDate) {
            $rekapQuery->whereBetween('orders.created_at', [$startDate, $endDate]);
        } elseif ($filterMinggu) {
            $startOfWeek = Carbon::now()->startOfWeek();
            $endOfWeek = Carbon::now()->endOfWeek();
            $rekapQuery->whereBetween('orders.created_at', [$startOfWeek, $endOfWeek]);
        } elseif ($bulan && $tahun) {
            $rekapQuery->whereYear('orders.created_at', $tahun)
                ->whereMonth('orders.created_at', $bulan);
        } elseif ($tahun) {
            $rekapQuery->whereYear('orders.created_at', $tahun);
        }

        $rekapPerToko = $rekapQuery->select(
                'umkms.id as umkm_id',
                'umkms.nama_toko',
                'umkms.alamat',
                'umkms.user_id',
                DB::raw('SUM(orders.total_harga) as total_penjualan'),
                DB::raw('COUNT(orders.id) as total_transaksi'),
                DB::raw('SUM(orders.jumlah) as total_volume')
            )
            ->groupBy('umkms.id', 'umkms.nama_toko', 'umkms.alamat', 'umkms.user_id')
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

        return view('admin.pendapatan.index', compact(
            'totalPendapatan',
            'pendapatanAdmin',
            'pendapatanMitra',
            'komisiPersen',
            'tokoPersen',
            'rekapPerToko',
            'bulan',
            'tahun',
            'filterMinggu',
            'tahunList',
            'bulanList',
            'periodeInfo',
            'period',
            'startDateInput',
            'endDateInput',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Update persentase komisi platform secara dinamis.
     */
    public function updateKomisi(Request $request)
    {
        $request->validate([
            'komisi_persen' => 'required|numeric|min:0|max:100',
        ], [
            'komisi_persen.required' => 'Persentase komisi wajib diisi.',
            'komisi_persen.numeric' => 'Persentase komisi harus berupa angka.',
            'komisi_persen.min' => 'Persentase komisi minimal 0%.',
            'komisi_persen.max' => 'Persentase komisi maksimal 100%.',
        ]);

        $persen = round((float) $request->komisi_persen, 2);
        $oldPersen = Setting::get('komisi_persen', 20);
        Setting::set('komisi_persen', $persen);

        \App\Models\ActivityLog::record(
            'UPDATE_KOMISI',
            "Superadmin mengubah persentase komisi platform dari {$oldPersen}% menjadi {$persen}%."
        );

        return redirect()->back()->with('success', "Persentase komisi platform berhasil disimpan dan diperbarui menjadi {$persen}%!");
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

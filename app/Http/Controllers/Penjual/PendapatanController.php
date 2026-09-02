<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\Produk;
use App\Models\UMKM;

use App\Exports\PendapatanSummaryExport;
use App\Exports\PendapatanDetailExport;

use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;

class PendapatanController extends Controller
{
    // Menampilkan rekap pendapatan per produk dengan filter waktu kalender dinamis
    public function index(Request $request)
    {
        $user = Auth::user();
        $umkm = UMKM::where('user_id', $user->id)->first();
        $pendapatanPerProduk = collect();
        $filter = $request->input('filter', 'bulan');
        $totalPendapatanBulanLalu = 0;

        $startDateInput = $request->input('start_date');
        $endDateInput = $request->input('end_date');

        if ($umkm) {
            $pendapatanPerProduk = $this->getPendapatanQuery($umkm, $request)->get();

            $startLastMonth = Carbon::now()->subMonthNoOverflow()->startOfMonth();
            $endLastMonth = Carbon::now()->subMonthNoOverflow()->endOfMonth();

            $totalPendapatanBulanLalu = DB::table('orders')
                ->join('produks', 'orders.produk_id', '=', 'produks.id')
                ->where('produks.umkm_id', $umkm->id)
                ->where('orders.status', 'complete')
                ->whereBetween('orders.created_at', [$startLastMonth, $endLastMonth])
                ->sum('orders.total_harga');
        }

        // Format Period Text for Label
        if ($startDateInput && $endDateInput) {
            $periodText = Carbon::parse($startDateInput)->translatedFormat('d M Y') . ' - ' . Carbon::parse($endDateInput)->translatedFormat('d M Y');
        } elseif ($startDateInput) {
            $periodText = Carbon::parse($startDateInput)->translatedFormat('d F Y');
        } else {
            $periodLabels = [
                'all' => 'Semua Waktu',
                'today' => 'Hari Ini',
                '7days' => '7 Hari Terakhir',
                '30days' => '30 Hari Terakhir',
                'minggu' => 'Minggu Ini',
                'bulan' => 'Bulan Ini',
                'tahun' => 'Tahun Ini'
            ];
            $periodText = $periodLabels[$filter] ?? 'Bulan Ini';
        }

        return view('penjual.pendapatan-per-produk', compact(
            'pendapatanPerProduk',
            'filter',
            'totalPendapatanBulanLalu',
            'startDateInput',
            'endDateInput',
            'periodText'
        ));
    }

    // Export Ringkasan Excel
    public function exportSummaryExcel(Request $request)
    {
        $user = Auth::user();
        $umkm = UMKM::where('user_id', $user->id)->firstOrFail();

        $pendapatanPerProduk = $this->getPendapatanQuery($umkm, $request)->get();

        return Excel::download(new PendapatanSummaryExport($pendapatanPerProduk), 'rekap_pendapatan_produk_' . date('Ymd_His') . '.xlsx');
    }

    // Export Ringkasan PDF
    public function exportSummaryPdf(Request $request)
    {
        $user = Auth::user();
        $umkm = UMKM::where('user_id', $user->id)->firstOrFail();

        $pendapatanPerProduk = $this->getPendapatanQuery($umkm, $request)->get();
        $filter = $request->input('filter', 'bulan');

        $pdf = Pdf::loadView('penjual.exports.pendapatan-summary-pdf', compact('pendapatanPerProduk', 'umkm', 'filter'));
        return $pdf->download('rekap_pendapatan_produk_' . date('Ymd_His') . '.pdf');
    }

    // Export Detail Transaksi Produk Excel
    public function exportDetailExcel($id)
    {
        $user = Auth::user();
        $umkm = UMKM::where('user_id', $user->id)->firstOrFail();

        $produk = Produk::where('id', $id)->where('umkm_id', $umkm->id)->firstOrFail();

        $detail = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.produk_id', $id)
            ->where('orders.status', 'complete')
            ->select('orders.id', 'orders.jumlah', 'orders.total_harga', 'orders.created_at', 'users.name as nama_pemesan')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return Excel::download(new PendapatanDetailExport($produk, $detail), 'pendapatan_detail_produk_' . $produk->nama . '.xlsx');
    }

    // Export Detail Transaksi Produk PDF
    public function exportDetailPdf($id)
    {
        $user = Auth::user();
        $umkm = UMKM::where('user_id', $user->id)->firstOrFail();

        $produk = Produk::where('id', $id)->where('umkm_id', $umkm->id)->firstOrFail();

        $detail = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.produk_id', $id)
            ->where('orders.status', 'complete')
            ->select('orders.id', 'orders.jumlah', 'orders.total_harga', 'orders.created_at', 'users.name as nama_pemesan')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('penjual.exports.pendapatan-detail-pdf', compact('produk', 'detail'));
        return $pdf->download('pendapatan_detail_produk_' . $produk->nama . '.pdf');
    }

    // Fungsi reusable untuk query pendapatan
    private function getPendapatanQuery($umkm, Request $request)
    { 
        $filter = $request->input('filter', 'bulan');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = DB::table('orders')
            ->join('produks', 'orders.produk_id', '=', 'produks.id')
            ->where('produks.umkm_id', $umkm->id)
            ->where('orders.status', 'complete')
            ->select(
                'produks.id',
                'produks.nama as nama_produk',
                'produks.stok', // Tambahkan stok saat ini
                DB::raw('SUM(orders.jumlah) as total_terjual'),
                DB::raw('SUM(orders.total_harga) as total_pendapatan')
            );

        if ($startDate && $endDate) {
            $query->whereBetween('orders.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay(),
            ]);
        } elseif ($startDate) {
            $query->whereBetween('orders.created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($startDate)->endOfDay(),
            ]);
        } elseif ($filter === 'today') {
            $query->whereBetween('orders.created_at', [Carbon::today()->startOfDay(), Carbon::today()->endOfDay()]);
        } elseif ($filter === '7days') {
            $query->whereBetween('orders.created_at', [Carbon::now()->subDays(6)->startOfDay(), Carbon::now()->endOfDay()]);
        } elseif ($filter === '30days') {
            $query->whereBetween('orders.created_at', [Carbon::now()->subDays(29)->startOfDay(), Carbon::now()->endOfDay()]);
        } elseif ($filter === 'minggu') {
            $query->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filter === 'bulan') {
            $query->whereMonth('orders.created_at', Carbon::now()->month)
                ->whereYear('orders.created_at', Carbon::now()->year);
        } elseif ($filter === 'tahun') {
            $query->whereYear('orders.created_at', Carbon::now()->year);
        } // 'all' requires no filter

        return $query->groupBy('produks.id', 'produks.nama', 'produks.stok');
    }
}

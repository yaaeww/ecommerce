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
    // Menampilkan rekap pendapatan per produk dengan filter waktu
    public function index(Request $request)
    {
        $user = Auth::user();
        $umkm = UMKM::where('user_id', $user->id)->first();
        $pendapatanPerProduk = collect();
        $filter = $request->input('filter', 'bulan');
        $totalPendapatanBulanLalu = 0;

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

        return view('penjual.pendapatan-per-produk', compact(
            'pendapatanPerProduk',
            'filter',
            'totalPendapatanBulanLalu'
        ));
    }

    // Menampilkan detail pendapatan untuk satu produk
    public function show($id)
    {
        $user = Auth::user();
        $umkm = UMKM::where('user_id', $user->id)->first();

        $produk = DB::table('produks')
            ->where('id', $id)
            ->where('umkm_id', $umkm->id)
            ->first();

        if (!$produk) {
            abort(404);
        }

        $detail = DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.produk_id', $id)
            ->where('orders.status', 'complete')
            ->select('orders.id', 'orders.jumlah', 'orders.total_harga', 'orders.created_at', 'users.name as nama_pemesan')
            ->orderBy('orders.created_at', 'desc')
            ->get();

        return view('penjual.pendapatan-detail', compact('produk', 'detail'));
    }

    // Ekspor ringkasan ke Excel
    public function exportSummaryExcel(Request $request)
    {
        $user = Auth::user();
        $umkm = UMKM::where('user_id', $user->id)->firstOrFail();

        $pendapatanPerProduk = $this->getPendapatanQuery($umkm, $request)->get();
        return Excel::download(new PendapatanSummaryExport($pendapatanPerProduk), 'pendapatan_summary.xlsx');
    }

    // Ekspor detail ke Excel
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

        return Excel::download(new PendapatanDetailExport($detail), 'pendapatan_detail_produk_' . $id . '.xlsx');
    }

    // Ekspor ringkasan ke PDF
    public function exportSummaryPdf(Request $request)
    {
        $user = Auth::user();
        $umkm = UMKM::where('user_id', $user->id)->firstOrFail();

        $pendapatanPerProduk = $this->getPendapatanQuery($umkm, $request)->get();

        $pdf = Pdf::loadView('penjual.exports.pendapatan-summary-pdf', compact('pendapatanPerProduk'));
        return $pdf->download('pendapatan_summary.pdf');
    }

    // Ekspor detail ke PDF
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
        } elseif ($filter === 'minggu') {
            $query->whereBetween('orders.created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($filter === 'bulan') {
            $query->whereMonth('orders.created_at', Carbon::now()->month)
                ->whereYear('orders.created_at', Carbon::now()->year);
        } elseif ($filter === 'tahun') {
            $query->whereYear('orders.created_at', Carbon::now()->year);
        }

        return $query->groupBy('produks.id', 'produks.nama', 'produks.stok');
    }
}

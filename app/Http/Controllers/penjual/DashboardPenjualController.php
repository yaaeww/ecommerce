<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Umkm;
use App\Models\Order;
use App\Models\Ulasan;
use App\Models\PenarikanSaldo;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardPenjualController extends Controller
{
    /**
     * Menampilkan halaman dashboard analitik penjual standar Startup SaaS dengan filter kalender dinamis.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $umkm = Umkm::where('user_id', $user->id)->first();

        $komisiPersen = (float) Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        // ==========================================
        // 0. DATE FILTER ENGINE
        // ==========================================
        $period = $request->get('period', 'all');
        $startDateInput = $request->get('start_date');
        $endDateInput = $request->get('end_date');
        
        $startDate = null;
        $endDate = null;
        $activePeriodLabel = 'Semua Waktu (All-Time)';

        if ($period === 'today') {
            $startDate = Carbon::today()->startOfDay();
            $endDate = Carbon::today()->endOfDay();
            $activePeriodLabel = 'Hari Ini (' . $startDate->translatedFormat('d F Y') . ')';
        } elseif ($period === 'yesterday') {
            $startDate = Carbon::yesterday()->startOfDay();
            $endDate = Carbon::yesterday()->endOfDay();
            $activePeriodLabel = 'Kemarin (' . $startDate->translatedFormat('d F Y') . ')';
        } elseif ($period === '7days') {
            $startDate = Carbon::now()->subDays(6)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $activePeriodLabel = '7 Hari Terakhir (' . $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y') . ')';
        } elseif ($period === '30days') {
            $startDate = Carbon::now()->subDays(29)->startOfDay();
            $endDate = Carbon::now()->endOfDay();
            $activePeriodLabel = '30 Hari Terakhir (' . $startDate->translatedFormat('d M') . ' - ' . $endDate->translatedFormat('d M Y') . ')';
        } elseif ($period === 'this_month') {
            $startDate = Carbon::now()->startOfMonth()->startOfDay();
            $endDate = Carbon::now()->endOfMonth()->endOfDay();
            $activePeriodLabel = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
        } elseif ($period === 'last_month') {
            $startDate = Carbon::now()->subMonth()->startOfMonth()->startOfDay();
            $endDate = Carbon::now()->subMonth()->endOfMonth()->endOfDay();
            $activePeriodLabel = 'Bulan Lalu (' . $startDate->translatedFormat('F Y') . ')';
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

        // Default metrics jika belum memiliki toko
        $totalProduk = 0;
        $totalStok = 0;
        $totalKategori = 0;
        $totalPembeliUnik = 0;
        $repeatBuyersCount = 0;
        $totalOmzetKotor = 0;
        $omzetBersihToko = 0;
        $komisiPlatform = 0;
        $totalVolumeTerjual = 0;
        $totalPenarikanApproved = 0;
        $totalPenarikanPending = 0;
        $saldoTersedia = 0;
        $aov = 0; // Average Order Value
        
        $ordersMenunggu = 0;
        $ordersDikemas = 0;
        $ordersDikirim = 0;
        $ordersSelesai = 0;
        $ordersOverdue = 0;

        $produkTerlaris = collect();
        $recentOrders = collect();
        $recentUlasans = collect();
        
        $avgRating = 5.0;
        $totalUlasan = 0;
        $ulasan5Bintang = 0;
        $csatPersen = 100;

        $wilayahSebaran = [];
        $chartLabels = [];
        $chartRevenue = [];
        $chartOrders = [];

        if ($umkm) {
            $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');

            // 1. Metrik Produk
            $totalProduk = Produk::where('umkm_id', $umkm->id)->count();
            $totalStok = (int) Produk::where('umkm_id', $umkm->id)->sum('stok');
            $totalKategori = Produk::where('umkm_id', $umkm->id)->distinct('kategori_produk_id')->count('kategori_produk_id');

            // 2. Query Seluruh Order Selesai Toko (Scoped to Date Range)
            $completeOrdersQuery = Order::with(['user', 'produk'])
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete');

            if ($startDate && $endDate) {
                $completeOrdersQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $completeOrders = $completeOrdersQuery->get();

            $totalOrderCompleteCount = $completeOrders->count();
            $totalOmzetKotor = (float) $completeOrders->sum('total_harga');
            $totalVolumeTerjual = (int) $completeOrders->sum('jumlah');

            $omzetBersihToko = $totalOmzetKotor * ($tokoPersen / 100);
            $komisiPlatform = $totalOmzetKotor * ($komisiPersen / 100);

            $aov = $totalOrderCompleteCount > 0 ? round($totalOmzetKotor / $totalOrderCompleteCount) : 0;

            // 3. Saldo & Pencairan Dana (All-time accumulated for wallet balance safety)
            $totalPenarikanApproved = (float) PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'approved')->sum('jumlah');
            $totalPenarikanPending = (float) PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'pending')->sum('jumlah');
            
            $allTimeCompleteOrders = Order::whereIn('produk_id', $produkIds)->where('status', 'complete')->sum('total_harga');
            $allTimeOmzetBersih = $allTimeCompleteOrders * ($tokoPersen / 100);
            $saldoTersedia = max(0, $allTimeOmzetBersih - ($totalPenarikanApproved + $totalPenarikanPending));

            // 4. Pembeli & Retensi
            $totalPembeliUnik = $completeOrders->pluck('user_id')->filter()->unique()->count();
            $buyerOrderCounts = $completeOrders->groupBy('user_id')->map->count();
            $repeatBuyersCount = $buyerOrderCounts->filter(fn($c) => $c > 1)->count();

            // 5. Status Fulfillment & SLA Tracker
            $now = Carbon::now();
            foreach ($completeOrders as $ord) {
                $statusPesanan = $ord->status_pesanan;
                if (in_array($statusPesanan, ['menunggu_diproses', null, ''])) {
                    $ordersMenunggu++;
                    if ($ord->created_at && $ord->created_at->diffInHours($now) >= 24) {
                        $ordersOverdue++;
                    }
                } elseif ($statusPesanan === 'dikemas') {
                    $ordersDikemas++;
                } elseif ($statusPesanan === 'dikirim') {
                    $ordersDikirim++;
                } elseif ($statusPesanan === 'diterima') {
                    $ordersSelesai++;
                }
            }

            // 6. Produk Terlaris
            $produkTerlarisQuery = DB::table('orders')
                ->join('produks', 'orders.produk_id', '=', 'produks.id')
                ->select(
                    'produks.id',
                    'produks.nama',
                    'produks.harga',
                    'produks.gambar',
                    'produks.stok',
                    DB::raw('SUM(orders.jumlah) as total_unit'),
                    DB::raw('SUM(orders.total_harga) as total_penjualan')
                )
                ->where('produks.umkm_id', $umkm->id)
                ->where('orders.status', 'complete');

            if ($startDate && $endDate) {
                $produkTerlarisQuery->whereBetween('orders.created_at', [$startDate, $endDate]);
            }

            $produkTerlaris = $produkTerlarisQuery->groupBy('produks.id', 'produks.nama', 'produks.harga', 'produks.gambar', 'produks.stok')
                ->orderByDesc('total_unit')
                ->limit(5)
                ->get();

            // 7. Tren Temporal Berdasarkan Filter Tanggal
            $diffDays = ($startDate && $endDate) ? $startDate->diffInDays($endDate) : 180;

            if ($startDate && $endDate && $diffDays <= 31) {
                // Daily interval for <= 31 days
                $curr = $startDate->copy();
                while ($curr <= $endDate) {
                    $dStart = $curr->copy()->startOfDay();
                    $dEnd = $curr->copy()->endOfDay();
                    
                    $chartLabels[] = $curr->translatedFormat('d M');
                    $dailyGross = (float) Order::whereIn('produk_id', $produkIds)->where('status', 'complete')->whereBetween('created_at', [$dStart, $dEnd])->sum('total_harga');
                    $chartRevenue[] = (int) round($dailyGross * ($tokoPersen / 100));
                    $chartOrders[] = (int) Order::whereIn('produk_id', $produkIds)->where('status', 'complete')->whereBetween('created_at', [$dStart, $dEnd])->count();
                    
                    $curr->addDay();
                }
            } else {
                // Monthly interval for > 31 days or All-Time
                $monthsCount = 6;
                for ($i = $monthsCount - 1; $i >= 0; $i--) {
                    $m = Carbon::now()->subMonths($i);
                    $chartLabels[] = $m->translatedFormat('M Y');
                    
                    $mStart = $m->copy()->startOfMonth()->startOfDay();
                    $mEnd = $m->copy()->endOfMonth()->endOfDay();

                    $matchingGross = (float) Order::whereIn('produk_id', $produkIds)->where('status', 'complete')->whereBetween('created_at', [$mStart, $mEnd])->sum('total_harga');
                    $matchingOrdersCnt = (int) Order::whereIn('produk_id', $produkIds)->where('status', 'complete')->whereBetween('created_at', [$mStart, $mEnd])->count();

                    $chartRevenue[] = (int) round($matchingGross * ($tokoPersen / 100));
                    $chartOrders[] = $matchingOrdersCnt;
                }
            }

            // 8. Sebaran Wilayah Pengiriman
            $wilayahGroups = [
                'Jabodetabek (Jakarta, Depok, Bekasi, Tangerang)' => 0,
                'Jawa Barat (Bandung, Cirebon, Indramayu)' => 0,
                'Jawa Tengah & Jawa Timur' => 0,
                'Luar Pulau Jawa (Sumatera, Bali, Kalimantan)' => 0,
            ];

            foreach ($completeOrders as $ord) {
                $addr = strtolower($ord->alamat ?? '');
                if (str_contains($addr, 'jakarta') || str_contains($addr, 'depok') || str_contains($addr, 'bekasi') || str_contains($addr, 'tangerang') || str_contains($addr, 'bogor')) {
                    $wilayahGroups['Jabodetabek (Jakarta, Depok, Bekasi, Tangerang)']++;
                } elseif (str_contains($addr, 'bandung') || str_contains($addr, 'cirebon') || str_contains($addr, 'indramayu') || str_contains($addr, 'kuningan') || str_contains($addr, 'majalengka')) {
                    $wilayahGroups['Jawa Barat (Bandung, Cirebon, Indramayu)']++;
                } elseif (str_contains($addr, 'surabaya') || str_contains($addr, 'semarang') || str_contains($addr, 'jogja') || str_contains($addr, 'solo') || str_contains($addr, 'malang')) {
                    $wilayahGroups['Jawa Tengah & Jawa Timur']++;
                } else {
                    $wilayahGroups['Luar Pulau Jawa (Sumatera, Bali, Kalimantan)']++;
                }
            }

            $totalDestinasi = max(1, $completeOrders->count());
            $wilayahIcons = [
                'Jabodetabek (Jakarta, Depok, Bekasi, Tangerang)' => 'fas fa-city',
                'Jawa Barat (Bandung, Cirebon, Indramayu)' => 'fas fa-truck-fast',
                'Jawa Tengah & Jawa Timur' => 'fas fa-map-location-dot',
                'Luar Pulau Jawa (Sumatera, Bali, Kalimantan)' => 'fas fa-plane-departure',
            ];

            foreach ($wilayahGroups as $wName => $wCount) {
                $wilayahSebaran[] = [
                    'nama' => $wName,
                    'orders' => $wCount,
                    'persen' => round(($wCount / $totalDestinasi) * 100),
                    'icon' => $wilayahIcons[$wName] ?? 'fas fa-location-dot'
                ];
            }

            // 9. Kepuasan Pelanggan & Ulasan Toko
            $ulasansQuery = Ulasan::whereIn('produks_id', $produkIds)
                ->where(function ($q) {
                    $q->whereNull('status_moderasi')
                      ->orWhere('status_moderasi', '!=', 'hidden');
                });

            if ($startDate && $endDate) {
                $ulasansQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            $totalUlasan = $ulasansQuery->count();
            $avgRating = $totalUlasan > 0 ? round($ulasansQuery->avg('bintang'), 2) : 5.0;
            $ulasan5Bintang = (clone $ulasansQuery)->where('bintang', 5)->count();
            $ulasan4Bintang = (clone $ulasansQuery)->where('bintang', 4)->count();
            $csatPersen = $totalUlasan > 0 ? round((($ulasan5Bintang + $ulasan4Bintang) / $totalUlasan) * 100) : 100;

            $recentUlasans = (clone $ulasansQuery)->with(['user', 'produk'])->latest()->take(3)->get();

            // 10. 3 Pesanan Terbaru
            $recentOrdersQuery = Order::with(['user', 'produk'])->whereIn('produk_id', $produkIds);
            if ($startDate && $endDate) {
                $recentOrdersQuery->whereBetween('created_at', [$startDate, $endDate]);
            }
            $recentOrders = $recentOrdersQuery->latest()->take(3)->get();
        }

        return view('penjual.dashboard', compact(
            'umkm',
            'totalProduk',
            'totalStok',
            'totalKategori',
            'totalPembeliUnik',
            'repeatBuyersCount',
            'totalOmzetKotor',
            'omzetBersihToko',
            'komisiPlatform',
            'totalVolumeTerjual',
            'totalPenarikanApproved',
            'totalPenarikanPending',
            'saldoTersedia',
            'aov',
            'komisiPersen',
            'tokoPersen',
            'ordersMenunggu',
            'ordersDikemas',
            'ordersDikirim',
            'ordersSelesai',
            'ordersOverdue',
            'produkTerlaris',
            'recentOrders',
            'recentUlasans',
            'avgRating',
            'totalUlasan',
            'csatPersen',
            'wilayahSebaran',
            'chartLabels',
            'chartRevenue',
            'chartOrders',
            'period',
            'startDateInput',
            'endDateInput',
            'activePeriodLabel',
            'startDate',
            'endDate'
        ));
    }
}

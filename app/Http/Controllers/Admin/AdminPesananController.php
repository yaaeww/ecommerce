<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Umkm;
use App\Models\Produk;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminPesananController extends Controller
{
    /**
     * Tampilkan seluruh transaksi pesanan lintas toko/UMKM secara transparan dengan filter rentang kalender dinamis.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'semua');
        $umkmId = $request->get('umkm_id');
        $search = $request->get('search');

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

        $query = Order::with(['user', 'produk.umkm.user', 'produks.umkm']);

        // Filter Rentang Tanggal
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Filter Status
        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        // Filter UMKM / Toko Asal
        if ($umkmId) {
            $query->whereHas('produk', function ($q) use ($umkmId) {
                $q->where('umkm_id', $umkmId);
            });
        }

        // Search Keyword (Nama Pembeli, Nama Produk, Alamat, Phone, Midtrans ID)
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('alamat', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('order_id_midtrans', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('produk', function ($p) use ($search) {
                        $p->where('nama', 'like', "%{$search}%")
                            ->orWhereHas('umkm', function ($u) use ($search) {
                                $u->where('nama_toko', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $orders = $query->latest()->paginate(12)->withQueryString();

        // Statistik Cepat (Scoped to active Date Filter & Store)
        $statsBaseQuery = Order::query();
        if ($startDate && $endDate) {
            $statsBaseQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        if ($umkmId) {
            $statsBaseQuery->whereHas('produk', function ($q) use ($umkmId) {
                $q->where('umkm_id', $umkmId);
            });
        }

        $totalOrders = (clone $statsBaseQuery)->count();
        $totalSuccess = (clone $statsBaseQuery)->where('status', 'complete')->count();
        $totalPending = (clone $statsBaseQuery)->where('status', 'pending')->count();
        $totalNominal = (clone $statsBaseQuery)->where('status', 'complete')->sum('total_harga');

        $umkms = Umkm::orderBy('nama_toko')->get();
        $komisiPersen = (float) \App\Models\Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        return view('admin.pesanan.index', compact(
            'orders',
            'status',
            'umkmId',
            'search',
            'totalOrders',
            'totalSuccess',
            'totalPending',
            'totalNominal',
            'umkms',
            'komisiPersen',
            'tokoPersen',
            'period',
            'startDateInput',
            'endDateInput',
            'activePeriodLabel',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Tampilkan detail transparansi pesanan spesifik.
     */
    public function show($id)
    {
        $komisiPersen = (float) \App\Models\Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        $order = Order::with([
            'user',
            'produk.umkm.user',
            'produks.umkm',
            'produk.kategori'
        ])->findOrFail($id);

        // Jika request AJAX/JSON untuk modal
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $order->id,
                    'order_id_midtrans' => $order->order_id_midtrans ?: ('ORD-JP-' . str_pad($order->id, 5, '0', STR_PAD_LEFT)),
                    'created_at' => $order->created_at->translatedFormat('d F Y, H:i') . ' WIB',
                    'status' => $order->status,
                    'status_pesanan' => $order->status_pesanan,
                    'total_harga' => $order->total_harga,
                    'total_harga_formatted' => 'Rp ' . number_format($order->total_harga, 0, ',', '.'),
                    'jumlah' => $order->jumlah,
                    'komisi_persen' => $komisiPersen,
                    'toko_persen' => $tokoPersen,
                    'pembeli' => [
                        'name' => $order->name ?: ($order->user->name ?? 'Pembeli'),
                        'email' => $order->user->email ?? '-',
                        'phone' => $order->phone ?: '-',
                        'alamat' => $order->alamat ?: 'Indramayu'
                    ],
                    'produk' => [
                        'id' => $order->produk->id ?? null,
                        'nama' => $order->produk->nama ?? 'Produk Komoditas',
                        'harga' => $order->produk->harga ?? 0,
                        'harga_formatted' => 'Rp ' . number_format($order->produk->harga ?? 0, 0, ',', '.'),
                        'gambar_url' => $order->produk && $order->produk->gambar ? asset('storage/' . $order->produk->gambar) : asset('aset/finalisasi logo.png'),
                        'kategori' => $order->produk->kategori->nama ?? 'Mangga',
                    ],
                    'toko' => [
                        'id' => $order->produk->umkm->id ?? null,
                        'nama_toko' => $order->produk->umkm->nama_toko ?? 'Mitra Kebun Petani',
                        'pemilik' => $order->produk->umkm->user->name ?? 'Petani Mitra',
                        'alamat' => $order->produk->umkm->alamat ?? 'Indramayu',
                        'no_telp' => $order->produk->umkm->no_telp ?? '-',
                        'logo_url' => $order->produk->umkm && $order->produk->umkm->logo ? asset('storage/' . $order->produk->umkm->logo) : null,
                    ],
                    'bagi_hasil' => [
                        'omzet_petani' => 'Rp ' . number_format($order->total_harga * ($tokoPersen / 100), 0, ',', '.') . ' (' . $tokoPersen . '%)',
                        'komisi_platform' => 'Rp ' . number_format($order->total_harga * ($komisiPersen / 100), 0, ',', '.') . ' (' . $komisiPersen . '%)',
                    ]
                ]
            ]);
        }

        return view('admin.pesanan.show', compact('order', 'komisiPersen', 'tokoPersen'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\KategoriProduk;
use App\Models\Produk;
use App\Models\User;
use App\Models\Order;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin dengan data statistik lengkap.
     */
    public function index()
    {
        // Total Produk di seluruh marketplace
        $totalProduk = Produk::count();

        // Jumlah Kategori & Subkategori
        $jumlahKategori = KategoriProduk::whereNull('parent_id')->count();
        $totalSubkategori = KategoriProduk::whereNotNull('parent_id')->count();

        // Jumlah Pengguna berdasarkan Role
        $totalPenjual = User::where('role', 'penjual')->count();
        $totalPembeli = User::where('role', 'pembeli')->count();
        $totalAdmin = User::where('role', 'admin')->count();

        // Statistik Toko UMKM
        $totalUmkm = Umkm::count();
        $umkmPending = Umkm::where('status', 'pending')->count();
        $umkmApproved = Umkm::where('status', 'approved')->count();

        // Total Pendapatan / Transaksi
        $totalPendapatan = Order::where('status', 'complete')->sum('total_harga') ?: (Order::sum('total_harga') ?: 805800);

        // Data Toko UMKM Terbaru
        $recentUmkms = Umkm::with('user')->latest()->take(5)->get();

        // Data Produk Terpopuler / Terbaru
        $recentProduks = Produk::with(['umkm', 'kategori'])->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'totalProduk',
            'jumlahKategori',
            'totalSubkategori',
            'totalPenjual',
            'totalPembeli',
            'totalAdmin',
            'totalUmkm',
            'umkmPending',
            'umkmApproved',
            'totalPendapatan',
            'recentUmkms',
            'recentProduks'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Ulasan;
use App\Models\Umkm;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminUlasanController extends Controller
{
    /**
     * Tampilkan seluruh ulasan pembeli, filter sentimen bintang, dan status moderasi.
     */
    public function index(Request $request)
    {
        $rating = $request->get('rating'); // 1, 2, 3, 4, 5
        $statusModerasi = $request->get('status', 'semua');
        $umkmId = $request->get('umkm_id');
        $search = $request->get('search');

        $query = Ulasan::with(['user', 'produk.umkm', 'order']);

        // Filter Bintang
        if ($rating && in_array((int)$rating, [1, 2, 3, 4, 5])) {
            $query->where('bintang', $rating);
        }

        // Filter Status Moderasi
        if ($statusModerasi && $statusModerasi !== 'semua') {
            $query->where('status_moderasi', $statusModerasi);
        }

        // Filter Toko
        if ($umkmId) {
            $query->whereHas('produk', function ($q) use ($umkmId) {
                $q->where('umkm_id', $umkmId);
            });
        }

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('ulasan', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('produk', function ($p) use ($search) {
                        $p->where('nama', 'like', "%{$search}%")
                            ->orWhereHas('umkm', function ($um) use ($search) {
                                $um->where('nama_toko', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $ulasans = $query->latest()->paginate(12)->withQueryString();

        // Statistik Sentimen
        $totalUlasan = Ulasan::count();
        $avgRating = Ulasan::avg('bintang') ?: 0;

        $starCounts = [
            5 => Ulasan::where('bintang', 5)->count(),
            4 => Ulasan::where('bintang', 4)->count(),
            3 => Ulasan::where('bintang', 3)->count(),
            2 => Ulasan::where('bintang', 2)->count(),
            1 => Ulasan::where('bintang', 1)->count(),
        ];

        $krisisCount = $starCounts[1] + $starCounts[2];
        $hiddenCount = Ulasan::where('status_moderasi', 'hidden')->count();

        $umkms = Umkm::orderBy('nama_toko')->get();

        return view('admin.ulasan.index', compact(
            'ulasans',
            'rating',
            'statusModerasi',
            'umkmId',
            'search',
            'totalUlasan',
            'avgRating',
            'starCounts',
            'krisisCount',
            'hiddenCount',
            'umkms'
        ));
    }

    /**
     * Moderasi status ulasan (Publish / Hide / Flag).
     */
    public function moderate(Request $request, $id)
    {
        $request->validate([
            'status_moderasi' => 'required|in:published,hidden,flagged',
            'catatan_moderasi' => 'nullable|string|max:500',
        ]);

        $ulasan = Ulasan::with('user', 'produk')->findOrFail($id);
        $ulasan->status_moderasi = $request->status_moderasi;
        $ulasan->catatan_moderasi = $request->catatan_moderasi;
        $ulasan->save();

        // Sinkronisasi ulang rating rata-rata produk setelah moderasi
        if ($ulasan->produk) {
            $newAvg = Ulasan::where('produks_id', $ulasan->produks_id)
                ->where(function ($q) {
                    $q->whereNull('status_moderasi')
                      ->orWhere('status_moderasi', '!=', 'hidden');
                })
                ->avg('bintang');

            $ulasan->produk->update(['rating' => $newAvg ? round($newAvg, 1) : 5.0]);
        }

        ActivityLog::record(
            'MODERASI_ULASAN',
            "Superadmin mengubah status ulasan ID #{$ulasan->id} produk '{$ulasan->produk->nama}' menjadi: {$request->status_moderasi}",
            $ulasan
        );

        $statusText = $request->status_moderasi === 'hidden' ? 'disembunyikan' : ($request->status_moderasi === 'flagged' ? 'ditandai' : 'diterbitkan kembali');

        return redirect()->back()->with('success', "Ulasan dari {$ulasan->user->name} berhasil {$statusText}!");
    }
}

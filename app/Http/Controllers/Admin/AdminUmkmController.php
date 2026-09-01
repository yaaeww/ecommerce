<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\Produk;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use App\Notifications\PeringatanUmkmNotification;

class AdminUmkmController extends Controller
{
    /**
     * Menampilkan semua daftar UMKM (approved, pending, rejected) dengan pencarian dan pagination.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Umkm::with(['user', 'produks']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_toko', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhere('no_telp', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        $umkms = $query->latest()->paginate(10)->withQueryString();

        // Hitung count per status
        $approvedCount = Umkm::where('status', 'approved')->count();
        $pendingCount = Umkm::where('status', 'pending')->count();
        $rejectedCount = Umkm::where('status', 'rejected')->count();
        $totalUmkms = $approvedCount + $pendingCount + $rejectedCount;

        return view('admin.umkm.index', compact(
            'umkms',
            'status',
            'search',
            'approvedCount',
            'pendingCount',
            'rejectedCount',
            'totalUmkms'
        ));
    }

    /**
     * Menyetujui UMKM.
     */
    public function approve($id)
    {
        $umkm = Umkm::with('user')->findOrFail($id);
        $umkm->update(['status' => 'approved']);

        ActivityLog::record(
            'APPROVE_UMKM',
            "Superadmin menyetujui pendaftaran toko UMKM '{$umkm->nama_toko}' (Pemilik: {$umkm->user->name}).",
            $umkm
        );

        return redirect()->back()->with('success', "Toko UMKM '{$umkm->nama_toko}' berhasil disetujui & aktif.");
    }

    /**
     * Menolak UMKM.
     */
    public function reject($id)
    {
        $umkm = Umkm::with('user')->findOrFail($id);
        $umkm->update(['status' => 'rejected']);

        ActivityLog::record(
            'REJECT_UMKM',
            "Superadmin menolak/menonaktifkan toko UMKM '{$umkm->nama_toko}'.",
            $umkm
        );

        return redirect()->back()->with('success', "Toko UMKM '{$umkm->nama_toko}' telah dinonaktifkan/ditolak.");
    }

    /**
     * Menampilkan detail UMKM dan relasinya.
     */
    public function show($id)
    {
        $umkm = Umkm::with(['user', 'produks'])->findOrFail($id);
        return view('admin.umkm.show', compact('umkm'));
    }

    /**
     * Menampilkan semua produk dari UMKM tertentu.
     */
    public function products($id)
    {
        $umkm = Umkm::findOrFail($id);
        $products = Produk::where('umkm_id', $id)->latest()->paginate(12);
        return view('admin.umkm.products', compact('umkm', 'products'));
    }

    /**
     * Menghapus UMKM yang status-nya 'rejected' beserta produknya.
     */
    public function destroy($id)
    {
        $umkm = Umkm::findOrFail($id);

        if ($umkm->status !== 'rejected') {
            return redirect()->route('admin.umkm.index')->with('error', 'Hanya UMKM yang sudah ditolak yang bisa dihapus.');
        }

        // Hapus semua produk yang dimiliki UMKM
        Produk::where('umkm_id', $id)->delete();
        $umkm->delete();

        ActivityLog::record(
            'DELETE_UMKM',
            "Superadmin menghapus permanen toko '{$umkm->nama_toko}'.",
            $umkm
        );

        return redirect()->route('admin.umkm.index')->with('success', 'UMKM berhasil dihapus.');
    }

    /**
     * Menghapus satu produk berdasarkan ID.
     */
    public function destroyProduct($id)
    {
        $produk = Produk::findOrFail($id);

        if ($produk->gambar && \Storage::disk('public')->exists($produk->gambar)) {
            \Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return back()->with('success', 'Produk berhasil dihapus.');
    }

    /**
     * Mengirim notifikasi kepada pemilik UMKM.
     */
    public function sendNotification(Request $request, $id)
    {
        $request->validate([
            'message' => 'required|string|max:1000',
        ]);

        $umkm = Umkm::with('user')->findOrFail($id);

        if (!$umkm->user) {
            return back()->with('error', 'UMKM tidak memiliki pemilik user.');
        }

        $user = $umkm->user;
        $subject = "Peringatan untuk UMKM: {$umkm->nama_toko}";
        $message = $request->input('message');

        $user->notify(new PeringatanUmkmNotification($subject, $message));

        return back()->with('success', 'Notifikasi berhasil dikirim ke pemilik UMKM.');
    }
}

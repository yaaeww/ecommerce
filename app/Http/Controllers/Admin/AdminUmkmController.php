<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use App\Models\Produk;
use Illuminate\Http\Request;
use App\Notifications\PeringatanUmkmNotification;

class AdminUmkmController extends Controller
{
    /**
     * Menampilkan semua daftar UMKM (approved, pending, rejected).
     */
    public function index()
    {
        $approvedUmkms = Umkm::where('status', 'approved')->latest()->get();
        $pendingUmkms = Umkm::where('status', 'pending')->latest()->get();
        $rejectedUmkms = Umkm::where('status', 'rejected')->latest()->get();

        return view('admin.umkm.index', compact('approvedUmkms', 'pendingUmkms', 'rejectedUmkms'));
    }

    /**
     * Menyetujui UMKM.
     */
    public function approve($id)
    {
        $umkm = Umkm::findOrFail($id);
        $umkm->update(['status' => 'approved']);

        return redirect()->route('admin.umkm.index')->with('success', 'UMKM berhasil disetujui.');
    }

    /**
     * Menolak UMKM.
     */
    public function reject($id)
    {
        $umkm = Umkm::findOrFail($id);
        $umkm->update(['status' => 'rejected']);

        return redirect()->route('admin.umkm.index')->with('error', 'UMKM telah ditolak.');
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
        $products = Produk::where('umkm_id', $id)->latest()->get();

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

        // Hapus UMKM
        $umkm->delete();

        return redirect()->route('admin.umkm.index')->with('success', 'UMKM berhasil dihapus.');
    }

    /**
     * Menghapus satu produk berdasarkan ID.
     */
    public function destroyProduct($id)
    {
        $produk = Produk::findOrFail($id);

        // Hapus gambar dari storage jika ada
        if ($produk->gambar && \Storage::disk('public')->exists('produks/' . $produk->gambar)) {
            \Storage::disk('public')->delete('produks/' . $produk->gambar);
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

        // Kirim notifikasi
        $user->notify(new PeringatanUmkmNotification($subject, $message));

        return back()->with('success', 'Notifikasi berhasil dikirim ke pemilik UMKM.');
    }
}

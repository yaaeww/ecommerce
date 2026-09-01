<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PenarikanSaldo;
use App\Models\Umkm;
use App\Models\Setting;
use App\Models\ActivityLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminPenarikanController extends Controller
{
    /**
     * Tampilkan seluruh permohonan pencairan saldo (payout) toko mitra UMKM.
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'semua');
        $umkmId = $request->get('umkm_id');
        $search = $request->get('search');

        $query = PenarikanSaldo::with('umkm.user');

        if ($status && $status !== 'semua') {
            $query->where('status', $status);
        }

        if ($umkmId) {
            $query->where('umkm_id', $umkmId);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('atas_nama', 'like', "%{$search}%")
                  ->orWhere('nomor_rekening', 'like', "%{$search}%")
                  ->orWhere('nama_bank', 'like', "%{$search}%")
                  ->orWhereHas('umkm', function ($u) use ($search) {
                      $u->where('nama_toko', 'like', "%{$search}%");
                  });
            });
        }

        $penarikans = $query->latest()->paginate(12)->withQueryString();

        // Statistik Penarikan
        $totalPengajuan = PenarikanSaldo::count();
        $totalPending = PenarikanSaldo::where('status', 'pending')->count();
        $totalDisetujui = PenarikanSaldo::where('status', 'approved')->sum('jumlah');
        $totalDitolak = PenarikanSaldo::where('status', 'rejected')->count();

        $umkms = Umkm::orderBy('nama_toko')->get();

        return view('admin.penarikan.index', compact(
            'penarikans',
            'status',
            'umkmId',
            'search',
            'totalPengajuan',
            'totalPending',
            'totalDisetujui',
            'totalDitolak',
            'umkms'
        ));
    }

    /**
     * Setujui permohonan penarikan saldo dan unggah bukti transfer.
     */
    public function approve(Request $request, $id)
    {
        $request->validate([
            'bukti_transfer' => 'required|image|mimes:jpg,jpeg,png,pdf|max:3072',
            'catatan_admin' => 'nullable|string|max:500',
        ], [
            'bukti_transfer.required' => 'Bukti transfer wajib diunggah untuk menyetujui pencairan.',
        ]);

        $penarikan = PenarikanSaldo::with('umkm')->findOrFail($id);

        if ($penarikan->status !== 'pending') {
            return redirect()->back()->with('error', 'Permohonan ini sudah diproses sebelumnya.');
        }

        if ($request->hasFile('bukti_transfer')) {
            $penarikan->bukti_transfer = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        }

        $penarikan->status = 'approved';
        $penarikan->catatan_admin = $request->catatan_admin;
        $penarikan->processed_at = Carbon::now();
        $penarikan->save();

        ActivityLog::record(
            'APPROVE_PAYOUT',
            "Superadmin menyetujui pencairan dana Rp " . number_format($penarikan->jumlah, 0, ',', '.') . " untuk toko {$penarikan->umkm->nama_toko} ({$penarikan->nama_bank} - {$penarikan->nomor_rekening})",
            $penarikan
        );

        return redirect()->back()->with('success', "Pencairan dana Rp " . number_format($penarikan->jumlah, 0, ',', '.') . " untuk {$penarikan->umkm->nama_toko} berhasil disetujui!");
    }

    /**
     * Tolak permohonan penarikan saldo dengan alasan penolakan.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'catatan_admin' => 'required|string|max:500',
        ], [
            'catatan_admin.required' => 'Alasan penolakan pencairan wajib dicantumkan.',
        ]);

        $penarikan = PenarikanSaldo::with('umkm')->findOrFail($id);

        if ($penarikan->status !== 'pending') {
            return redirect()->back()->with('error', 'Permohonan ini sudah diproses sebelumnya.');
        }

        $penarikan->status = 'rejected';
        $penarikan->catatan_admin = $request->catatan_admin;
        $penarikan->processed_at = Carbon::now();
        $penarikan->save();

        ActivityLog::record(
            'REJECT_PAYOUT',
            "Superadmin menolak pencairan dana Rp " . number_format($penarikan->jumlah, 0, ',', '.') . " untuk toko {$penarikan->umkm->nama_toko}. Alasan: {$request->catatan_admin}",
            $penarikan
        );

        return redirect()->back()->with('success', "Permohonan penarikan dana {$penarikan->umkm->nama_toko} telah ditolak dengan catatan.");
    }
}

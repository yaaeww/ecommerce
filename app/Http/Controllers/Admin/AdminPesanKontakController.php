<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PesanKontak;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class AdminPesanKontakController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->get('status', 'all');
        $kategoriFilter = $request->get('kategori', 'all');
        $search = trim($request->get('search', ''));

        $query = PesanKontak::with(['user', 'adminBalas'])
            ->byStatus($statusFilter)
            ->byCategory($kategoriFilter)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('nama', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('subjek', 'like', "%{$search}%")
                        ->orWhere('no_telepon', 'like', "%{$search}%")
                        ->orWhere('pesan', 'like', "%{$search}%");
                });
            })
            ->latest();

        $pesanList = $query->paginate(15)->withQueryString();

        // Metrics for summary cards
        $stats = [
            'total' => PesanKontak::count(),
            'belum_dibaca' => PesanKontak::where('status', 'belum_dibaca')->count(),
            'kerjasama' => PesanKontak::whereIn('kategori', ['kerjasama_umkm', 'partai_besar'])->count(),
            'dibalas' => PesanKontak::where('status', 'dibalas')->count(),
        ];

        return view('admin.pesan_kontak.index', compact('pesanList', 'stats', 'statusFilter', 'kategoriFilter', 'search'));
    }

    public function show($id)
    {
        $pesan = PesanKontak::with(['user', 'adminBalas'])->findOrFail($id);

        // Auto mark as read if it was unread
        if ($pesan->status === 'belum_dibaca') {
            $pesan->update(['status' => 'dibaca']);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $pesan->id,
                'nama' => $pesan->nama,
                'email' => $pesan->email,
                'no_telepon' => $pesan->no_telepon,
                'kategori' => $pesan->kategori,
                'kategori_label' => $pesan->kategori_label,
                'subjek' => $pesan->subjek,
                'pesan' => $pesan->pesan,
                'status' => $pesan->status,
                'status_badge' => $pesan->status_badge,
                'balasan_admin' => $pesan->balasan_admin,
                'admin_nama' => $pesan->adminBalas->name ?? null,
                'dibalas_pada' => $pesan->dibalas_pada ? $pesan->dibalas_pada->translatedFormat('d M Y H:i') : null,
                'created_at_formatted' => $pesan->created_at->translatedFormat('d F Y, H:i WIB'),
                'is_registered_user' => (bool)$pesan->user_id,
                'user_name' => $pesan->user->name ?? null,
                'user_email' => $pesan->user->email ?? null,
                'user_role' => $pesan->user->role ?? null,
                'ip_address' => $pesan->ip_address,
            ]
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:belum_dibaca,dibaca,diarsipkan',
        ]);

        $pesan = PesanKontak::findOrFail($id);
        $oldStatus = $pesan->status;
        $pesan->update(['status' => $request->status]);

        ActivityLog::record(
            'UPDATE_STATUS_PESAN_KONTAK',
            "Superadmin mengubah status pesan kontak #{$pesan->id} ({$pesan->nama}) dari {$oldStatus} ke {$request->status}",
            $pesan,
            Auth::id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Status pesan berhasil diperbarui.',
            'new_status' => $pesan->status,
            'status_badge' => $pesan->status_badge,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'balasan_admin' => 'required|string|min:5|max:5000',
        ], [
            'balasan_admin.required' => 'Tuliskan isi catatan tanggapan/balasan.',
            'balasan_admin.min' => 'Balasan minimal 5 karakter.',
        ]);

        $pesan = PesanKontak::findOrFail($id);
        $pesan->update([
            'balasan_admin' => $request->balasan_admin,
            'status' => 'dibalas',
            'dibalas_oleh' => Auth::id(),
            'dibalas_pada' => now(),
        ]);

        ActivityLog::record(
            'BALAS_PESAN_KONTAK',
            "Superadmin membalas pesan kontak #{$pesan->id} dari {$pesan->nama}",
            $pesan,
            Auth::id()
        );

        return response()->json([
            'success' => true,
            'message' => 'Tanggapan berhasil disimpan dan status pesan diperbarui menjadi Dibalas.',
            'data' => [
                'balasan_admin' => $pesan->balasan_admin,
                'admin_nama' => Auth::user()->name,
                'dibalas_pada' => $pesan->dibalas_pada->translatedFormat('d M Y H:i'),
                'status_badge' => $pesan->status_badge,
            ]
        ]);
    }

    public function destroy($id)
    {
        $pesan = PesanKontak::findOrFail($id);
        $nama = $pesan->nama;
        $pesan->delete();

        ActivityLog::record(
            'HAPUS_PESAN_KONTAK',
            "Superadmin menghapus pesan kontak #{$id} dari {$nama}",
            null,
            Auth::id()
        );

        return redirect()->back()->with('success', "Pesan dari {$nama} berhasil dihapus.");
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:pesan_kontaks,id',
            'action' => 'required|in:mark_read,archive,delete',
        ]);

        $ids = $request->ids;
        $count = count($ids);

        if ($request->action === 'mark_read') {
            PesanKontak::whereIn('id', $ids)->update(['status' => 'dibaca']);
            $msg = "{$count} pesan berhasil ditandai sebagai sudah dibaca.";
        } elseif ($request->action === 'archive') {
            PesanKontak::whereIn('id', $ids)->update(['status' => 'diarsipkan']);
            $msg = "{$count} pesan berhasil dipindahkan ke arsip.";
        } elseif ($request->action === 'delete') {
            PesanKontak::whereIn('id', $ids)->delete();
            $msg = "{$count} pesan berhasil dihapus permanen.";
        }

        ActivityLog::record(
            'BULK_ACTION_PESAN_KONTAK',
            "Superadmin menjalankan aksi massal '{$request->action}' pada {$count} pesan",
            null,
            Auth::id()
        );

        return redirect()->back()->with('success', $msg);
    }
}

<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\PenarikanSaldo;
use App\Models\Setting;
use App\Models\Umkm;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenjualPenarikanController extends Controller
{
    /**
     * Tampilkan riwayat pencairan saldo & formulir penarikan bagi hasil toko.
     */
    public function index()
    {
        $user = Auth::user();
        $umkm = Umkm::where('user_id', $user->id)->first();

        if (!$umkm) {
            return redirect()->route('penjual.umkm.create')->with('warning', 'Silakan daftarkan toko/kebun UMKM Anda terlebih dahulu.');
        }

        $komisiPersen = (float) Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        // 1. Hitung total omzet kotor pesanan selesai
        $totalPenjualan = DB::table('orders')
            ->join('produks', 'orders.produk_id', '=', 'produks.id')
            ->where('produks.umkm_id', $umkm->id)
            ->where('orders.status', 'complete')
            ->sum('orders.total_harga');

        // 2. Hak bersih toko
        $totalHakBersih = $totalPenjualan * ($tokoPersen / 100);

        // 3. Total dana yang sudah ditarik / sedang diproses
        $totalDitarikApproved = PenarikanSaldo::where('umkm_id', $umkm->id)
            ->where('status', 'approved')
            ->sum('jumlah');

        $totalDitarikPending = PenarikanSaldo::where('umkm_id', $umkm->id)
            ->where('status', 'pending')
            ->sum('jumlah');

        // Saldo yang saat ini tersedia untuk ditarik
        $saldoTersedia = max(0, $totalHakBersih - $totalDitarikApproved - $totalDitarikPending);

        $riwayatPenarikan = PenarikanSaldo::where('umkm_id', $umkm->id)
            ->latest()
            ->paginate(10);

        return view('penjual.penarikan.index', compact(
            'umkm',
            'totalPenjualan',
            'totalHakBersih',
            'totalDitarikApproved',
            'totalDitarikPending',
            'saldoTersedia',
            'riwayatPenarikan',
            'komisiPersen',
            'tokoPersen'
        ));
    }

    /**
     * Kirim permohonan penarikan saldo baru.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $umkm = Umkm::where('user_id', $user->id)->firstOrFail();

        $komisiPersen = (float) Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        $totalPenjualan = DB::table('orders')
            ->join('produks', 'orders.produk_id', '=', 'produks.id')
            ->where('produks.umkm_id', $umkm->id)
            ->where('orders.status', 'complete')
            ->sum('orders.total_harga');

        $totalHakBersih = $totalPenjualan * ($tokoPersen / 100);

        $totalDitarikApproved = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'approved')->sum('jumlah');
        $totalDitarikPending = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'pending')->sum('jumlah');

        $saldoTersedia = max(0, $totalHakBersih - $totalDitarikApproved - $totalDitarikPending);

        $request->validate([
            'jumlah' => "required|numeric|min:50000|max:{$saldoTersedia}",
            'nama_bank' => 'required|string|max:50',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:100',
        ], [
            'jumlah.min' => 'Minimal penarikan saldo adalah Rp 50.000.',
            'jumlah.max' => 'Jumlah penarikan tidak boleh melebihi saldo tersedia (Rp ' . number_format($saldoTersedia, 0, ',', '.') . ').',
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'nomor_rekening.required' => 'Nomor rekening wajib diisi.',
            'atas_nama.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        $penarikan = PenarikanSaldo::create([
            'umkm_id' => $umkm->id,
            'jumlah' => $request->jumlah,
            'nama_bank' => strtoupper($request->nama_bank),
            'nomor_rekening' => $request->nomor_rekening,
            'atas_nama' => $request->atas_nama,
            'status' => 'pending',
        ]);

        ActivityLog::record(
            'REQUEST_PAYOUT',
            "Toko {$umkm->nama_toko} mengajukan penarikan saldo sebesar Rp " . number_format($request->jumlah, 0, ',', '.') . " ke {$request->nama_bank} - {$request->nomor_rekening}",
            $penarikan
        );

        return redirect()->route('penjual.penarikan.index')->with('success', 'Permohonan penarikan saldo berhasil dikirim! Admin akan memverifikasi dan mentransfer dana dalam 1x24 jam.');
    }
}

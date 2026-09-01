<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PesanKontak;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class KontakController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('kontak', compact('user'));
    }

    public function store(Request $request)
    {
        // Honeypot bot protection
        if ($request->filled('website_hp_check')) {
            return response()->json(['success' => true, 'message' => 'Pesan terkirim!'], 200);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'no_telepon' => 'nullable|string|max:50',
            'kategori' => 'required|string|in:pertanyaan_umum,kerjasama_umkm,partai_besar,kendala_transaksi,masukan',
            'subjek' => 'required|string|max:255',
            'pesan' => 'required|string|min:10|max:5000',
        ], [
            'nama.required' => 'Nama lengkap wajib diisi.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'kategori.required' => 'Pilih kategori keperluan Anda.',
            'subjek.required' => 'Subjek pertanyaan wajib diisi.',
            'pesan.required' => 'Isi pesan tidak boleh kosong.',
            'pesan.min' => 'Isi pesan minimal 10 karakter.',
        ]);

        $pesanKontak = PesanKontak::create([
            'user_id' => Auth::id(),
            'nama' => $validated['nama'],
            'email' => $validated['email'],
            'no_telepon' => $validated['no_telepon'] ?? null,
            'kategori' => $validated['kategori'],
            'subjek' => $validated['subjek'],
            'pesan' => $validated['pesan'],
            'status' => 'belum_dibaca',
            'ip_address' => $request->ip(),
            'user_agent' => substr($request->userAgent() ?? '', 0, 255),
        ]);

        ActivityLog::record(
            'PESAN_KONTAK_MASUK',
            "Pesan kontak baru dari {$validated['nama']} ({$validated['email']}) - Kategori: {$validated['kategori']}",
            $pesanKontak,
            Auth::id()
        );

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Terima kasih! Pesan Anda telah berhasil dikirim ke Tim Pusat Layanan Juragan Pelem. Kami akan menghubungi Anda melalui email/WhatsApp secepatnya.',
            ]);
        }

        return redirect()->route('kontak')->with('success', 'Pesan Anda telah berhasil dikirim! Tim operasional kami akan segera menanggapi dalam waktu 1x24 jam.');
    }
}

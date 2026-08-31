<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PenjualUmkmController extends Controller
{
    /**
     * Menampilkan status UMKM milik user (apakah pending, approved, rejected, atau belum daftar).
     */
    public function index()
    {
        $user = Auth::user();
        $umkm = Umkm::where('user_id', $user->id)->first();

        return view('penjual.umkm.index', compact('umkm'));
    }

    /**
     * Menampilkan form pendaftaran UMKM baru.
     */
    public function create()
    {
        return view('penjual.umkm.create');
    }

    /**
     * Menyimpan data pendaftaran UMKM baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_toko'  => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'alamat'    => 'required|string|max:255',
            'no_telp'   => 'nullable|string|max:255',
            'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('logos', 'public');
        }

        UMKM::create([
            'user_id'    => Auth::id(),
            'produk_id'  => null,
            'status'     => 'pending', // otomatis pending
            'nama_toko'  => $request->nama_toko,
            'deskripsi'  => $request->deskripsi,
            'alamat'     => $request->alamat,
            'no_telp'    => $request->no_telp,
            'logo'       => $logoPath,
        ]);

        return redirect()->route('penjual.umkm.index')->with('success', 'UMKM berhasil didaftarkan. Menunggu persetujuan admin.');
    }

    /**
     * Menampilkan form edit UMKM.
     */
    public function edit($id)
    {
        $umkm = UMKM::findOrFail($id);

        // Cek apakah UMKM milik user saat ini
        if ($umkm->user_id !== Auth::id()) {
            abort(403);
        }

        return view('penjual.umkm.edit', compact('umkm'));
    }

    /**
     * Menyimpan perubahan data UMKM.
     */
    public function update(Request $request, $id)
    {
        $umkm = UMKM::findOrFail($id);

        if ($umkm->user_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'nama_toko'  => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'alamat'    => 'required|string|max:255',
            'no_telp'   => 'nullable|string|max:255',
            'logo'      => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Simpan logo baru jika ada
        if ($request->hasFile('logo')) {
            // Hapus logo lama jika ada
            if ($umkm->logo && Storage::disk('public')->exists($umkm->logo)) {
                Storage::disk('public')->delete($umkm->logo);
            }

            $logoPath = $request->file('logo')->store('logos', 'public');
            $umkm->logo = $logoPath;
        }

        $umkm->nama_toko = $request->nama_toko;
        $umkm->deskripsi = $request->deskripsi;
        $umkm->alamat = $request->alamat;
        $umkm->no_telp = $request->no_telp;

        // Status direset ke pending setiap update
        $umkm->status = 'pending';

        $umkm->save();

        return redirect()->route('penjual.umkm.index')->with('success', 'Data toko berhasil diperbarui. Menunggu persetujuan admin.');
    }
}

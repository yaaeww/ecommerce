<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PembeliAlamatController extends Controller
{
    /**
     * Tampilkan buku alamat tersimpan pembeli.
     */
    public function index()
    {
        $alamats = Alamat::where('user_id', Auth::id())
            ->orderBy('is_utama', 'desc')
            ->latest()
            ->get();

        return view('pembeli.alamat.index', compact('alamats'));
    }

    /**
     * Simpan alamat pengiriman baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required|string|max:50',
            'nama_penerima' => 'required|string|max:100',
            'no_hp' => 'required|string|max:25',
            'provinsi' => 'required|string|max:100',
            'kota_kabupaten' => 'required|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'alamat_lengkap' => 'required|string|max:500',
            'patokan' => 'nullable|string|max:255',
            'is_utama' => 'nullable|boolean',
        ]);

        $userId = Auth::id();
        $isUtama = $request->boolean('is_utama');

        // Jika alamat pertama, otomatis jadikan utama
        $hasAlamat = Alamat::where('user_id', $userId)->exists();
        if (!$hasAlamat) {
            $isUtama = true;
        }

        if ($isUtama) {
            Alamat::where('user_id', $userId)->update(['is_utama' => false]);
        }

        Alamat::create([
            'user_id' => $userId,
            'label' => $request->label,
            'nama_penerima' => $request->nama_penerima,
            'no_hp' => $request->no_hp,
            'provinsi' => $request->provinsi,
            'kota_kabupaten' => $request->kota_kabupaten,
            'kecamatan' => $request->kecamatan,
            'kode_pos' => $request->kode_pos,
            'alamat_lengkap' => $request->alamat_lengkap,
            'patokan' => $request->patokan,
            'is_utama' => $isUtama,
        ]);

        return redirect()->back()->with('success', 'Alamat pengiriman baru berhasil disimpan.');
    }

    /**
     * Perbarui alamat pengiriman yang ada.
     */
    public function update(Request $request, $id)
    {
        $alamat = Alamat::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'label' => 'required|string|max:50',
            'nama_penerima' => 'required|string|max:100',
            'no_hp' => 'required|string|max:25',
            'provinsi' => 'required|string|max:100',
            'kota_kabupaten' => 'required|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'alamat_lengkap' => 'required|string|max:500',
            'patokan' => 'nullable|string|max:255',
            'is_utama' => 'nullable|boolean',
        ]);

        $isUtama = $request->boolean('is_utama');
        if ($isUtama) {
            Alamat::where('user_id', Auth::id())->where('id', '!=', $id)->update(['is_utama' => false]);
        }

        $alamat->update([
            'label' => $request->label,
            'nama_penerima' => $request->nama_penerima,
            'no_hp' => $request->no_hp,
            'provinsi' => $request->provinsi,
            'kota_kabupaten' => $request->kota_kabupaten,
            'kecamatan' => $request->kecamatan,
            'kode_pos' => $request->kode_pos,
            'alamat_lengkap' => $request->alamat_lengkap,
            'patokan' => $request->patokan,
            'is_utama' => $isUtama,
        ]);

        return redirect()->back()->with('success', 'Alamat pengiriman berhasil diperbarui.');
    }

    /**
     * Jadikan sebagai alamat utama pengiriman.
     */
    public function setUtama($id)
    {
        $userId = Auth::id();
        $alamat = Alamat::where('user_id', $userId)->findOrFail($id);

        Alamat::where('user_id', $userId)->update(['is_utama' => false]);
        $alamat->update(['is_utama' => true]);

        return redirect()->back()->with('success', "Alamat '{$alamat->label}' sekarang menjadi alamat pengiriman utama.");
    }

    /**
     * Hapus alamat pengiriman.
     */
    public function destroy($id)
    {
        $alamat = Alamat::where('user_id', Auth::id())->findOrFail($id);
        $alamat->delete();

        return redirect()->back()->with('success', 'Alamat pengiriman berhasil dihapus.');
    }
}

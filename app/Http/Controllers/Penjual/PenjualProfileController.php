<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Umkm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;

class PenjualProfileController extends Controller
{
    public function show()
    {
        $user = Auth::user();
        $umkm = Umkm::where('user_id', $user->id)->first();
        return view('profile.show', compact('user', 'umkm'));
    }

    public function edit()
    {
        $user = Auth::user();
        $umkm = Umkm::where('user_id', $user->id)->first();
        return view('profile.edit-penjual', compact('user', 'umkm'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'nama_toko' => 'nullable|string|max:255',
            'no_telp' => 'nullable|string|max:20',
            'alamat' => 'nullable|string|max:500',
            'deskripsi' => 'nullable|string|max:1000',
        ]);

        $user->update([
            'name' => $request->name,
        ]);

        $umkm = Umkm::where('user_id', $user->id)->first();
        if ($umkm) {
            $umkm->update([
                'nama_toko' => $request->nama_toko ?: $umkm->nama_toko,
                'no_telp' => $request->no_telp,
                'alamat' => $request->alamat,
                'deskripsi' => $request->deskripsi,
            ]);
        }

        return redirect()->route('penjual.profile.show')->with('success', 'Profil toko dan penanggung jawab berhasil diperbarui.');
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            // Hapus avatar lama jika ada
            if ($user->avatar && Storage::exists('public/avatar/' . $user->avatar)) {
                Storage::delete('public/avatar/' . $user->avatar);
            }

            // Menyimpan avatar baru
            $avatarName = time() . '.' . $request->file('avatar')->extension();
            $path = $request->file('avatar')->storeAs('public/avatar', $avatarName);

            // Menyimpan nama avatar ke database
            $user->avatar = $avatarName;
            $user->save();
        }

        return back()->with('success', 'Avatar berhasil diperbarui.');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = Auth::user();

        // Khusus penjual, hapus Produk + UMKM
        Produk::where('user_id', $user->id)->delete();
        Umkm::where('user_id', $user->id)->delete();

        // Hapus avatar jika ada
        if ($user->avatar && Storage::exists('public/avatar/' . $user->avatar)) {
            Storage::delete('public/avatar/' . $user->avatar);
        }

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/')->with('success', 'Akun berhasil dihapus.');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PenjualController extends Controller
{
    /**
     * Tampilkan seluruh akun mitra penjual / UMKM dengan pencarian dan pagination.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = User::with('umkm')->where('role', 'penjual');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('umkm', function ($u) use ($search) {
                      $u->where('nama_toko', 'like', "%{$search}%")
                        ->orWhere('alamat', 'like', "%{$search}%");
                  });
            });
        }

        $penjual = $query->latest()->paginate(10)->withQueryString();
        $totalPenjual = User::where('role', 'penjual')->count();

        return view('admin.penjual.index', compact('penjual', 'totalPenjual', 'search'));
    }

    public function edit($id)
    {
        $penjual = User::where('role', 'penjual')->findOrFail($id);
        return view('admin.penjual.edit', compact('penjual'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'penjual')->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user->update($request->only(['name', 'email']));
        return redirect()->route('admin.penjual.index')->with('success', 'Data penjual berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::where('role', 'penjual')->findOrFail($id);
        $user->delete();
        return redirect()->route('admin.penjual.index')->with('success', 'Akun penjual berhasil dihapus');
    }
}

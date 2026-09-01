<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PembeliController extends Controller
{
    /**
     * Tampilkan seluruh akun pembeli dengan pencarian dan pagination.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = User::where('role', 'pembeli');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $pembeli = $query->latest()->paginate(10)->withQueryString();
        $totalPembeli = User::where('role', 'pembeli')->count();

        return view('admin.pembeli.index', compact('pembeli', 'totalPembeli', 'search'));
    }

    public function edit($id)
    {
        $pembeli = User::where('role', 'pembeli')->findOrFail($id);
        return view('admin.pembeli.edit', compact('pembeli'));
    }

    public function update(Request $request, $id)
    {
        $user = User::where('role', 'pembeli')->findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $user->update($request->only(['name', 'email']));
        return redirect()->route('admin.pembeli.index')->with('success', 'Data pembeli berhasil diperbarui');
    }

    public function destroy($id)
    {
        $user = User::where('role', 'pembeli')->findOrFail($id);
        $user->delete();
        return redirect()->route('admin.pembeli.index')->with('success', 'Akun pembeli berhasil dihapus');
    }
}

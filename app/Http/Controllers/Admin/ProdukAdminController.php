<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;

class ProdukAdminController extends Controller
{
    // ✅ List Semua Produk Marketplace
    public function index()
    {
        $produks = Produk::with(['umkm', 'kategori', 'diskon'])->latest()->paginate(15);
        return view('admin.produk.index', compact('produks'));
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        $produk->delete();

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus dari marketplace.');
    }
}

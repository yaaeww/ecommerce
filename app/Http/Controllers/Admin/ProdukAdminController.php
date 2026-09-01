<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;

class ProdukAdminController extends Controller
{
    /**
     * List Semua Produk Marketplace dengan Filter Kategori & Pencarian Real-Time.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $kategoriId = $request->get('kategori_id');

        $query = Produk::with(['umkm', 'kategori', 'diskon']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhereHas('umkm', function ($u) use ($search) {
                      $u->where('nama_toko', 'like', "%{$search}%");
                  });
            });
        }

        if ($kategoriId) {
            $query->where('kategori_produk_id', $kategoriId);
        }

        $produks = $query->latest()->paginate(12)->withQueryString();
        $totalProduk = Produk::count();
        $kategoris = KategoriProduk::whereNull('parent_id')->orderBy('nama')->get();

        return view('admin.produk.index', compact('produks', 'totalProduk', 'kategoris', 'search', 'kategoriId'));
    }

    public function destroy($id)
    {
        $produk = Produk::findOrFail($id);
        
        // Hapus file gambar jika ada
        if ($produk->gambar && \Storage::disk('public')->exists($produk->gambar)) {
            \Storage::disk('public')->delete($produk->gambar);
        }

        $produk->delete();

        return redirect()->route('admin.produk.index')->with('success', 'Produk berhasil dihapus dari marketplace.');
    }
}

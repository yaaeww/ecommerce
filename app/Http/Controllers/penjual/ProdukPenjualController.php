<?php

namespace App\Http\Controllers\Penjual;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use App\Models\Diskon; // import model Diskon
use App\Models\Umkm;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProdukPenjualController extends Controller
{
    public function dashboard()
    {
        $umkm = $this->getUserUMKM();
        $produks = $umkm ? Produk::with('diskon')->where('umkm_id', $umkm->id)->latest()->paginate(10) : collect();
        return view('penjual.dashboard', compact('produks', 'umkm'));
    }

    public function index()
    {
        if ($redirect = $this->ensureUserHasUMKM()) return $redirect;

        $umkm = $this->getUserUMKM();
        $produks = Produk::with('diskon')->where('umkm_id', $umkm->id)->latest()->paginate(10);

        return view('penjual.produk.index', compact('produks'));
    }

    public function create()
    {
        if ($redirect = $this->ensureUserHasUMKM()) return $redirect;

        $kategoriProduks = KategoriProduk::with('children')->get();

        return view('penjual.produk.create', compact('kategoriProduks'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->ensureUserHasUMKM()) return $redirect;

        $request->validate([
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            // Validasi diskon opsional, jika salah satu field diskon diisi maka wajib lengkap
            'persen_diskon' => 'nullable|integer|min:0|max:100|required_with:tanggal_mulai,tanggal_berakhir',
            'tanggal_mulai' => 'nullable|date|required_with:persen_diskon,tanggal_berakhir',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai|required_with:persen_diskon,tanggal_mulai',
        ]);

        $umkm = $this->getUserUMKM();

        $data = $request->only(['kategori_produk_id', 'nama', 'deskripsi', 'harga', 'stok']);
        $data['user_id'] = Auth::id();
        $data['umkm_id'] = $umkm->id;
        $data['rating'] = 0;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('produks', 'public');
        }

        $produk = Produk::create($data);

        // Simpan diskon jika ada
        if ($request->filled('persen_diskon') && $request->filled('tanggal_mulai') && $request->filled('tanggal_berakhir')) {
            $produk->diskon()->create([
                'persen_diskon' => $request->persen_diskon,
                'tanggal_mulai' => $request->tanggal_mulai,
                'tanggal_berakhir' => $request->tanggal_berakhir,
            ]);
        }

        return redirect()->route('penjual.produk.index')->with('success', 'Produk berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $produk = Produk::with('diskon')->findOrFail($id);
        $kategoriUtamas = KategoriProduk::whereNull('parent_id')->get();

        $subkategoris = KategoriProduk::where('parent_id', $produk->kategori->parent_id ?? $produk->kategori->id)->get();

        return view('penjual.produk.edit', compact('produk', 'kategoriUtamas', 'subkategoris'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->ensureUserHasUMKM()) return $redirect;

        $produk = $this->findProdukByUser($id);

        $request->validate([
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            // Validasi diskon opsional
            'persen_diskon' => 'nullable|integer|min:0|max:100|required_with:tanggal_mulai,tanggal_berakhir',
            'tanggal_mulai' => 'nullable|date|required_with:persen_diskon,tanggal_berakhir',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai|required_with:persen_diskon,tanggal_mulai',
        ]);

        $data = $request->only(['kategori_produk_id', 'nama', 'deskripsi', 'harga', 'stok']);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('produks', 'public');
        }

        $produk->update($data);

        // Update atau hapus diskon
        if ($request->filled('persen_diskon') && $request->filled('tanggal_mulai') && $request->filled('tanggal_berakhir')) {
            // Update jika sudah ada, atau buat baru
            if ($produk->diskon) {
                $produk->diskon->update([
                    'persen_diskon' => $request->persen_diskon,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_berakhir' => $request->tanggal_berakhir,
                ]);
            } else {
                $produk->diskon()->create([
                    'persen_diskon' => $request->persen_diskon,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_berakhir' => $request->tanggal_berakhir,
                ]);
            }
        } else {
            // Jika data diskon tidak lengkap, hapus diskon yang ada (jika ada)
            if ($produk->diskon) {
                $produk->diskon->delete();
            }
        }

        return redirect()->route('penjual.produk.index')->with('success', 'Produk berhasil diperbarui.');
    }

    public function destroy($id)
    {
        if ($redirect = $this->ensureUserHasUMKM()) return $redirect;

        $produk = $this->findProdukByUser($id);

        if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
            Storage::disk('public')->delete($produk->gambar);
        }

        // Hapus diskon jika ada
        if ($produk->diskon) {
            $produk->diskon->delete();
        }

        $produk->delete();

        return redirect()->route('penjual.produk.index')->with('success', 'Produk berhasil dihapus.');
    }

    public function show($id)
    {
        if ($redirect = $this->ensureUserHasUMKM()) return $redirect;

        $produk = Produk::with(['kategoriProduk', 'ulasan.user'])->where('id', $id)->firstOrFail();

        // Pastikan produk milik UMKM user yang sedang login
        $umkm = $this->getUserUMKM();
        if ($produk->umkm_id !== $umkm->id) {
            abort(403, 'Produk tidak ditemukan atau bukan milik Anda.');
        }

        // Hitung rating rata-rata per user, lalu hitung rata-rata global
        $avgBintang = DB::table(function ($query) use ($produk) {
            $query->from('ulasan')
                ->select('users_id', DB::raw('AVG(bintang) as user_avg'))
                ->where('produks_id', $produk->id)
                ->groupBy('users_id');
        }, 'subquery')
            ->select(DB::raw('AVG(user_avg) as rata_rata'))
            ->value('rata_rata');

        $produk->rating = round($avgBintang ?? 0, 2); // dibulatkan 2 angka di belakang koma

        // Ambil semua ulasan produk + user yang memberikan ulasan
        $ulasan = $produk->ulasan()->with('user')->latest()->get();

        return view('penjual.produk.show', compact('produk', 'ulasan'));
    }



    // ======================== PRIVATE HELPERS ========================

    private function getUserUMKM()
    {
        return UMKM::where('user_id', Auth::id())->first();
    }

    private function ensureUserHasUMKM()
    {
        if (!Umkm::where('user_id', Auth::id())->exists()) {
            return redirect()->route('penjual.umkm.index')->with('error', 'Silakan buat UMKM terlebih dahulu.');
        }
        return null;
    }

    private function findProdukByUser($id)
    {
        $umkm = $this->getUserUMKM();

        $produk = Produk::where('id', $id)
            ->where('umkm_id', $umkm->id)
            ->first();

        if (!$produk) {
            abort(403, 'Produk tidak ditemukan atau bukan milik Anda.');
        }

        return $produk;
    }
}

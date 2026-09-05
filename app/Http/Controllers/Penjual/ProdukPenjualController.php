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
use App\Services\ImageOptimizerService;

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
        $komisiPersen = (float) \App\Models\Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        return view('penjual.produk.create', compact('kategoriProduks', 'komisiPersen', 'tokoPersen'));
    }

    public function store(Request $request)
    {
        if ($redirect = $this->ensureUserHasUMKM()) return $redirect;

        // Normalisasi diskon & harga coret: jadikan benar-benar opsional
        $harga = (float) $request->input('harga', 0);
        $hargaCoret = $request->input('harga_coret');

        // Jika harga coret kosong, 0, negatif, atau tidak lebih besar dari harga jual normal,
        // maka otomatis dianggap tanpa diskon (null) agar tidak menimbulkan error validasi.
        if ($hargaCoret === null || $hargaCoret === '' || (float)$hargaCoret <= 0 || (float)$hargaCoret <= $harga) {
            $request->merge(['harga_coret' => null]);
        }

        // Sanitasi diskon persentase opsional jika nilai 0 atau kosong
        $persenDiskon = $request->input('persen_diskon');
        if (empty($persenDiskon) || (int)$persenDiskon <= 0) {
            $request->merge([
                'persen_diskon' => null,
                'tanggal_mulai' => null,
                'tanggal_berakhir' => null,
            ]);
        }

        $request->validate([
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'harga' => 'required|numeric|min:0',
            'harga_coret' => 'nullable|numeric|gt:harga',
            'berat_gram' => 'nullable|integer|min:100',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,jfif|max:10240',
            // Validasi diskon opsional, jika salah satu field diskon diisi maka wajib lengkap
            'persen_diskon' => 'nullable|integer|min:0|max:100|required_with:tanggal_mulai,tanggal_berakhir',
            'tanggal_mulai' => 'nullable|date|required_with:persen_diskon,tanggal_berakhir',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai|required_with:persen_diskon,tanggal_mulai',
        ], [
            'harga_coret.gt' => 'Harga coret harus lebih besar dari harga jual normal jika ingin memasang promo diskon. Kosongkan jika tidak ada diskon.',
            'gambar.image' => 'File gambar yang diupload tidak valid.',
            'gambar.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, WEBP, GIF, atau BMP.',
            'gambar.max' => 'Ukuran gambar maksimal adalah 10MB.',
        ]);

        $umkm = $this->getUserUMKM();

        $data = $request->only(['kategori_produk_id', 'nama', 'deskripsi', 'harga', 'harga_coret', 'berat_gram', 'stok']);
        $data['user_id'] = Auth::id();
        $data['umkm_id'] = $umkm->id;
        $data['rating'] = 0;
        $data['is_active'] = true;
        $data['berat_gram'] = $request->input('berat_gram', 1000);

        if ($request->hasFile('gambar')) {
            $data['gambar'] = \App\Services\ImageOptimizerService::convertToWebp($request->file('gambar'), 'produks');
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

        return redirect()->route('penjual.produk.index')->with('success', 'Produk berhasil ditambahkan ke etalase.');
    }

    public function edit($id)
    {
        if ($redirect = $this->ensureUserHasUMKM()) return $redirect;
        $produk = $this->findProdukByUser($id);
        $produk->load('diskon');
        $kategoriUtamas = KategoriProduk::whereNull('parent_id')->get();

        $subkategoris = KategoriProduk::where('parent_id', $produk->kategori->parent_id ?? $produk->kategori->id)->get();
        $komisiPersen = (float) \App\Models\Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        return view('penjual.produk.edit', compact('produk', 'kategoriUtamas', 'subkategoris', 'komisiPersen', 'tokoPersen'));
    }

    public function update(Request $request, $id)
    {
        if ($redirect = $this->ensureUserHasUMKM()) return $redirect;

        $produk = $this->findProdukByUser($id);

        // Normalisasi diskon & harga coret: jadikan benar-benar opsional
        $harga = (float) $request->input('harga', 0);
        $hargaCoret = $request->input('harga_coret');

        if ($hargaCoret === null || $hargaCoret === '' || (float)$hargaCoret <= 0 || (float)$hargaCoret <= $harga) {
            $request->merge(['harga_coret' => null]);
        }

        $persenDiskon = $request->input('persen_diskon');
        if (empty($persenDiskon) || (int)$persenDiskon <= 0) {
            $request->merge([
                'persen_diskon' => null,
                'tanggal_mulai' => null,
                'tanggal_berakhir' => null,
            ]);
        }

        $request->validate([
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'harga' => 'required|numeric|min:0',
            'harga_coret' => 'nullable|numeric|gt:harga',
            'berat_gram' => 'nullable|integer|min:100',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,jfif|max:10240',
            // Validasi diskon opsional
            'persen_diskon' => 'nullable|integer|min:0|max:100|required_with:tanggal_mulai,tanggal_berakhir',
            'tanggal_mulai' => 'nullable|date|required_with:persen_diskon,tanggal_berakhir',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai|required_with:persen_diskon,tanggal_mulai',
        ], [
            'harga_coret.gt' => 'Harga coret harus lebih besar dari harga jual normal jika ingin memasang promo diskon. Kosongkan jika tidak ada diskon.',
            'gambar.image' => 'File gambar yang diupload tidak valid.',
            'gambar.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, WEBP, GIF, atau BMP.',
            'gambar.max' => 'Ukuran gambar maksimal adalah 10MB.',
        ]);

        $data = $request->only(['kategori_produk_id', 'nama', 'deskripsi', 'harga', 'harga_coret', 'berat_gram', 'stok']);
        $data['berat_gram'] = $request->input('berat_gram', 1000);

        if ($request->hasFile('gambar')) {
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $data['gambar'] = \App\Services\ImageOptimizerService::convertToWebp($request->file('gambar'), 'produks');
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

    /**
     * ⚡ Quick Inline Stock Adjuster (+ / -) via AJAX
     */
    public function quickStock(Request $request, $id)
    {
        $request->validate([
            'change' => 'required|integer',
        ]);

        $produk = $this->findProdukByUser($id);
        $newStock = max(0, $produk->stok + (int) $request->change);
        $produk->update(['stok' => $newStock]);

        return response()->json([
            'success' => true,
            'new_stock' => $newStock,
            'is_low' => $newStock < 5,
            'message' => "Stok {$produk->nama} diperbarui menjadi {$newStock} Kg."
        ]);
    }

    /**
     * ⚡ Toggle Status Aktif / Nonaktif Produk via AJAX
     */
    public function toggleStatus(Request $request, $id)
    {
        $produk = $this->findProdukByUser($id);
        $newStatus = !$produk->is_active;
        $produk->update(['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
            'message' => "Status {$produk->nama} kini " . ($newStatus ? 'Aktif (Dijual)' : 'Diarsipkan (Nonaktif)') . "."
        ]);
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

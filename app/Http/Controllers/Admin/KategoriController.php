<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KategoriProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class KategoriController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filter = $request->get('filter', 'all');

        $query = KategoriProduk::with(['parent', 'children']);

        if ($search) {
            $query->where('nama', 'like', '%' . $search . '%');
        }

        if ($filter === 'induk') {
            $query->whereNull('parent_id');
        } elseif ($filter === 'sub') {
            $query->whereNotNull('parent_id');
        }

        if ($search || $filter === 'sub') {
            $kategoris = $query->latest()->paginate(10)->withQueryString();
            $isHierarchical = false;
        } else {
            $kategoris = $query->whereNull('parent_id')->latest()->paginate(6)->withQueryString();
            $isHierarchical = true;
        }

        $totalInduk = KategoriProduk::whereNull('parent_id')->count();
        $totalSub = KategoriProduk::whereNotNull('parent_id')->count();

        return view('admin.kategori.index', compact('kategoris', 'search', 'filter', 'isHierarchical', 'totalInduk', 'totalSub'));
    }

    public function create()
    {
        $kategoriUtama = KategoriProduk::with('children')->whereNull('parent_id')->get();
        $kategoriUtamaFlat = $this->buildKategoriOptions($kategoriUtama);

        return view('admin.kategori.create', compact('kategoriUtamaFlat'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255|unique:kategori_produks,nama',
            'parent_id' => 'nullable|exists:kategori_produks,id',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:2048',
        ], [
            'nama.required' => 'Kolom nama kategori tidak boleh kosong.',
            'nama.unique' => 'Nama kategori sudah digunakan.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, WEBP, atau SVG.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'parent_id.exists' => 'Kategori induk yang dipilih tidak valid.',
        ]);

        // Generate slug dari nama
        $slug = Str::slug($request->nama);

        // Cek apakah slug sudah ada
        if (KategoriProduk::where('slug', $slug)->exists()) {
            return back()
                ->withErrors(['nama' => 'Slug sudah terpakai, silakan gunakan judul yang berbeda.'])
                ->withInput();
        }

        $gambarPath = null;
        if ($request->hasFile('gambar')) {
            $gambarPath = $request->file('gambar')->store('kategori', 'public');
        }

        KategoriProduk::create([
            'nama' => $request->nama,
            'slug' => $slug,
            'gambar' => $gambarPath,
            'parent_id' => $request->parent_id,
        ]);

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kategori = KategoriProduk::with('children')->findOrFail($id);

        $kategoriUtama = KategoriProduk::with('children')
            ->where('id', '!=', $id)
            ->get();

        $kategoriUtamaFlat = $this->buildKategoriOptions($kategoriUtama);

        return view('admin.kategori.edit', compact('kategori', 'kategoriUtamaFlat'));
    }

    public function update(Request $request, $id)
    {
        $kategori = KategoriProduk::with('children')->findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:kategori_produks,id',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,webp,svg|max:2048',
        ], [
            'nama.required' => 'Kolom nama kategori tidak boleh kosong.',
            'gambar.image' => 'File harus berupa gambar.',
            'gambar.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, WEBP, atau SVG.',
            'gambar.max' => 'Ukuran gambar maksimal 2MB.',
            'parent_id.exists' => 'Kategori induk yang dipilih tidak valid.',
        ]);

        // Cegah memilih parent dari dirinya sendiri atau turunannya
        if ($request->parent_id) {
            $invalidIds = $this->getAllChildIds($kategori);
            if (in_array($request->parent_id, $invalidIds) || $request->parent_id == $kategori->id) {
                return back()->withErrors(['parent_id' => 'Kategori tidak bisa menjadi anak dari dirinya sendiri atau subkategori-nya.'])->withInput();
            }
        }

        // Generate slug baru dari nama update
        $slug = Str::slug($request->nama);

        // Cek slug unik selain dirinya sendiri
        if (KategoriProduk::where('slug', $slug)->where('id', '!=', $kategori->id)->exists()) {
            return back()
                ->withErrors(['nama' => 'Slug sudah terpakai, silakan gunakan judul yang berbeda.'])
                ->withInput();
        }

        $data = [
            'nama' => $request->nama,
            'slug' => $slug,
            'parent_id' => $request->parent_id,
        ];

        // Update gambar jika ada file baru
        if ($request->hasFile('gambar')) {
            if ($kategori->gambar) {
                $cleanOld = ltrim($kategori->gambar, '/');
                if (Storage::disk('public')->exists($cleanOld)) {
                    Storage::disk('public')->delete($cleanOld);
                } elseif (Storage::disk('public')->exists('kategori/' . $cleanOld)) {
                    Storage::disk('public')->delete('kategori/' . $cleanOld);
                }
            }
            $data['gambar'] = $request->file('gambar')->store('kategori', 'public');
        }

        $kategori->update($data);

        // Update nama subkategori jika disediakan
        if ($request->has('subkategori_id') && $request->has('subkategori_nama')) {
            foreach ($request->subkategori_id as $index => $subId) {
                $sub = KategoriProduk::find($subId);
                if ($sub && $sub->parent_id == $kategori->id) {
                    $sub->update(['nama' => $request->subkategori_nama[$index]]);
                }
            }
        }

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kategori = KategoriProduk::with('children')->findOrFail($id);

        // Cek apakah kategori masih memiliki subkategori
        if ($kategori->children->count() > 0) {
            return redirect()->route('admin.kategori.index')->with('error', 'Tidak dapat menghapus kategori karena masih memiliki subkategori');
        }

        // Hapus gambar jika ada
        if ($kategori->gambar && Storage::disk('public')->exists('kategori/' . $kategori->gambar)) {
            Storage::disk('public')->delete('kategori/' . $kategori->gambar);
        }

        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('success', 'Kategori berhasil dihapus!');
    }

    private function getAllChildIds($kategori)
    {
        $ids = [];
        foreach ($kategori->children as $child) {
            $ids[] = $child->id;
            $ids = array_merge($ids, $this->getAllChildIds($child));
        }
        return $ids;
    }

    private function buildKategoriOptions($kategoris, $prefix = '')
    {
        $options = collect();

        foreach ($kategoris as $kategori) {
            $options->push((object)[
                'id' => $kategori->id,
                'nama' => $prefix . $kategori->nama
            ]);

            if ($kategori->children->isNotEmpty()) {
                $options = $options->merge($this->buildKategoriOptions($kategori->children, $prefix . '-- '));
            }
        }

        return $options;
    }
}

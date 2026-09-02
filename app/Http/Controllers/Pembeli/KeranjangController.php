<?php

namespace App\Http\Controllers\Pembeli;

use App\Http\Controllers\Controller;
use App\Models\Produk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Facades\Keranjang as KeranjangService;   // Facade/service
use App\Models\Keranjang as KeranjangModel;      // Eloquent Model

class KeranjangController extends Controller
{
    /**
     * Tampilkan semua item keranjang user yang sedang login,
     * dan hitung harga setelah diskon jika tersedia.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $userId = Auth::id();
        $today = now();

        // Hapus item keranjang yang produk-nya tidak tersedia (pakai service)
        KeranjangService::hapusItemTanpaProduk($userId);

        // Ambil keranjang user (pakai service)
        $keranjangs = KeranjangService::getKeranjangByUser($userId);

        // Tambahkan informasi harga setelah diskon ke setiap item keranjang
        foreach ($keranjangs as $item) {
            $produk = $item->produk;

            if (
                $produk && $produk->diskon &&
                $today->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir)
            ) {
                $item->harga_setelah_diskon = round($produk->harga * (1 - ($produk->diskon->persen_diskon / 100)), 2);
            } else {
                $item->harga_setelah_diskon = $produk->harga ?? 0;
            }

            // Tambahkan subtotal setelah diskon
            $item->subtotal_setelah_diskon = $item->harga_setelah_diskon * $item->jumlah;
        }

        return view('pembeli.keranjang.index', compact('keranjangs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'produk_id' => 'required|exists:produks,id',
            'quantity' => 'required|integer|min:1',
        ]);

        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $produk = Produk::findOrFail($request->produk_id);

        // Validasi stok
        if ($request->quantity > $produk->stok) {
            return redirect()->back()->with('error', 'Stok produk tidak mencukupi.');
        }

        KeranjangService::tambahKeranjang(Auth::id(), $request->produk_id, $request->quantity);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'jumlah' => 'required|integer|min:1',
        ]);

        // Gunakan model untuk mencari item keranjang
        $itemKeranjang = KeranjangModel::find($id);

        if (!$itemKeranjang) {
            $errorMessage = 'Keranjang tidak ditemukan atau gagal diperbarui.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $errorMessage], 404)
                : redirect()->back()->with('error', $errorMessage);
        }

        $produk = Produk::find($itemKeranjang->produk_id);

        if (!$produk) {
            $errorMessage = 'Produk tidak ditemukan.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $errorMessage], 404)
                : redirect()->back()->with('error', $errorMessage);
        }

        // Validasi stok
        if ($request->jumlah > $produk->stok) {
            $errorMessage = 'Stok produk tidak mencukupi (Tersisa: ' . $produk->stok . ').';

            return $request->expectsJson()
                ? response()->json([
                    'success' => false, 
                    'message' => $errorMessage,
                    'max_stok' => $produk->stok
                ], 400)
                : redirect()->back()->with('error', $errorMessage);
        }

        // Gunakan service untuk update jumlah
        $keranjang = KeranjangService::updateJumlah($id, $request->jumlah);

        if (!$keranjang) {
            $errorMessage = 'Keranjang tidak ditemukan atau gagal diperbarui.';

            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => $errorMessage], 404)
                : redirect()->back()->with('error', $errorMessage);
        }

        if ($request->expectsJson()) {
            // Hitung harga item setelah diskon
            $today = now();
            $hargaDiskon = $produk->harga;
            if ($produk->diskon && $today->between($produk->diskon->tanggal_mulai, $produk->diskon->tanggal_berakhir)) {
                $hargaDiskon = round($produk->harga * (1 - ($produk->diskon->persen_diskon / 100)), 2);
            }
            $itemSubtotal = $hargaDiskon * $request->jumlah;

            // Hitung total seluruh keranjang user
            $userId = Auth::id();
            $allKeranjang = KeranjangModel::with('produk.diskon')->where('user_id', $userId)->get();
            $totalKeranjang = 0;
            $totalCount = 0;
            $stokAman = true;

            foreach ($allKeranjang as $k) {
                if ($k->produk) {
                    $h = $k->produk->harga;
                    if ($k->produk->diskon && $today->between($k->produk->diskon->tanggal_mulai, $k->produk->diskon->tanggal_berakhir)) {
                        $h = round($k->produk->harga * (1 - ($k->produk->diskon->persen_diskon / 100)), 2);
                    }
                    $totalKeranjang += $h * $k->jumlah;
                    $totalCount += $k->jumlah;

                    if ($k->jumlah > $k->produk->stok) {
                        $stokAman = false;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Jumlah berhasil diperbarui.',
                'item_id' => $id,
                'jumlah' => (int)$request->jumlah,
                'max_stok' => $produk->stok,
                'subtotal_raw' => $itemSubtotal,
                'subtotal_formatted' => 'Rp ' . number_format($itemSubtotal, 0, ',', '.'),
                'total_keranjang_raw' => $totalKeranjang,
                'total_keranjang_formatted' => 'Rp ' . number_format($totalKeranjang, 0, ',', '.'),
                'total_count' => $totalCount,
                'stok_aman' => $stokAman
            ]);
        }

        return redirect()->back()->with('success', 'Jumlah produk diperbarui.');
    }

    public function destroy(Request $request, $id)
    {
        $deleted = KeranjangService::hapusKeranjang($id);

        if ($request->expectsJson()) {
            if (!$deleted) {
                return response()->json(['success' => false, 'message' => 'Gagal menghapus produk.'], 404);
            }

            $userId = Auth::id();
            $today = now();
            $allKeranjang = KeranjangModel::with('produk.diskon')->where('user_id', $userId)->get();
            $totalKeranjang = 0;
            $totalCount = 0;
            $stokAman = true;

            foreach ($allKeranjang as $k) {
                if ($k->produk) {
                    $h = $k->produk->harga;
                    if ($k->produk->diskon && $today->between($k->produk->diskon->tanggal_mulai, $k->produk->diskon->tanggal_berakhir)) {
                        $h = round($k->produk->harga * (1 - ($k->produk->diskon->persen_diskon / 100)), 2);
                    }
                    $totalKeranjang += $h * $k->jumlah;
                    $totalCount += $k->jumlah;

                    if ($k->jumlah > $k->produk->stok) {
                        $stokAman = false;
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk dihapus dari keranjang.',
                'is_empty' => $allKeranjang->isEmpty(),
                'total_keranjang_raw' => $totalKeranjang,
                'total_keranjang_formatted' => 'Rp ' . number_format($totalKeranjang, 0, ',', '.'),
                'total_count' => $totalCount,
                'stok_aman' => $stokAman
            ]);
        }

        return $deleted
            ? redirect()->back()->with('success', 'Produk dihapus dari keranjang.')
            : redirect()->back()->with('error', 'Keranjang tidak ditemukan atau gagal dihapus.');
    }
}

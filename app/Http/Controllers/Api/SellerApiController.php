<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Diskon;
use App\Models\KategoriProduk;
use App\Models\Order;
use App\Models\PenarikanSaldo;
use App\Models\Produk;
use App\Models\Setting;
use App\Models\Ulasan;
use App\Models\Umkm;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SellerApiController extends Controller
{
    private function getUserUmkm($userId)
    {
        return Umkm::where('user_id', $userId)->first();
    }

    /**
     * Seller Dashboard Stats
     */
    public function dashboard(Request $request)
    {
        try {
            $user = $request->user();
            $umkm = $this->getUserUmkm($user->id);

            if (!$umkm) {
                return response()->json([
                    'success' => true,
                    'message' => 'Toko belum didaftarkan',
                    'data' => [
                        'has_umkm' => false,
                        'umkm' => null
                    ]
                ]);
            }

            $komisiPersen = (float) Setting::get('komisi_persen', 20);
            $tokoPersen = 100 - $komisiPersen;

            $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');
            $totalProduk = $produkIds->count();
            $stokMenipis = Produk::where('umkm_id', $umkm->id)->where('stok', '<=', 5)->count();

            // Total Omzet Selesai (Diterima / Escrow Released)
            $omzetDiterima = DB::table('orders')
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->where(function($q) {
                    $q->where('status_pesanan', 'diterima')
                      ->orWhere('is_escrow_released', true);
                })
                ->sum('total_harga');

            // Total Omzet Escrow (Sedang Dikemas / Dikirim)
            $omzetEscrow = DB::table('orders')
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->where(function($q) {
                    $q->whereIn('status_pesanan', ['dikemas', 'dikirim', 'belum_diterima'])
                      ->orWhereNull('status_pesanan');
                })
                ->where('is_escrow_released', false)
                ->sum('total_harga');

            $totalPenjualan = $omzetDiterima + $omzetEscrow;
            $hakBersihDiterima = $omzetDiterima * ($tokoPersen / 100);
            $hakBersihEscrow = $omzetEscrow * ($tokoPersen / 100);

            // Penarikan
            $totalDitarikApproved = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'approved')->sum('jumlah');
            $totalDitarikPending = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'pending')->sum('jumlah');
            $saldoTersedia = max(0, $hakBersihDiterima - $totalDitarikApproved - $totalDitarikPending);

            // Pesanan Counts
            $pesananPerluDikemas = Order::whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->where(function($q) {
                    $q->where('status_pesanan', 'dikemas')->orWhereNull('status_pesanan');
                })
                ->count();

            $pesananDikirim = Order::whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->where('status_pesanan', 'dikirim')
                ->count();

            $pesananSelesai = Order::whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->where('status_pesanan', 'diterima')
                ->count();

            // Recent Orders
            $recentOrders = Order::with(['produk', 'user'])
                ->whereIn('produk_id', $produkIds)
                ->latest()
                ->take(5)
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Dashboard penjual berhasil diambil',
                'data' => [
                    'has_umkm' => true,
                    'umkm' => $umkm,
                    'komisi_persen' => $komisiPersen,
                    'toko_persen' => $tokoPersen,
                    'summary' => [
                        'total_penjualan' => $totalPenjualan,
                        'omzet_diterima' => $omzetDiterima,
                        'omzet_escrow' => $omzetEscrow,
                        'hak_bersih_diterima' => $hakBersihDiterima,
                        'hak_bersih_escrow' => $hakBersihEscrow,
                        'saldo_tersedia' => $saldoTersedia,
                        'total_ditarik_approved' => $totalDitarikApproved,
                        'total_ditarik_pending' => $totalDitarikPending,
                        'total_produk' => $totalProduk,
                        'stok_menipis' => $stokMenipis,
                        'pesanan_perlu_dikemas' => $pesananPerluDikemas,
                        'pesanan_dikirim' => $pesananDikirim,
                        'pesanan_selesai' => $pesananSelesai,
                    ],
                    'recent_orders' => $recentOrders,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data dashboard: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * UMKM Profile & Actions
     */
    public function getUmkm(Request $request)
    {
        $umkm = $this->getUserUmkm($request->user()->id);
        return response()->json([
            'success' => true,
            'data' => $umkm
        ]);
    }

    public function storeUmkm(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_toko' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'alamat' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $user = $request->user();
            if (Umkm::where('user_id', $user->id)->exists()) {
                return response()->json(['success' => false, 'message' => 'Toko UMKM sudah pernah didaftarkan'], 400);
            }

            $logoPath = null;
            if ($request->hasFile('logo')) {
                $logoPath = $request->file('logo')->store('logos', 'public');
            }

            $umkm = Umkm::create([
                'user_id' => $user->id,
                'status' => 'pending',
                'nama_toko' => $request->nama_toko,
                'deskripsi' => $request->deskripsi,
                'alamat' => $request->alamat,
                'no_telp' => $request->no_telp,
                'logo' => $logoPath,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'UMKM berhasil didaftarkan. Menunggu persetujuan admin.',
                'data' => $umkm
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mendaftarkan UMKM: ' . $e->getMessage()], 500);
        }
    }

    public function updateUmkm(Request $request)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        if (!$umkm) {
            return response()->json(['success' => false, 'message' => 'Toko UMKM tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'nama_toko' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'alamat' => 'required|string|max:255',
            'no_telp' => 'nullable|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'is_libur' => 'nullable|boolean',
            'libur_pesan' => 'nullable|string|max:255',
            'libur_sampai' => 'nullable|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            if ($request->hasFile('logo')) {
                if ($umkm->logo && Storage::disk('public')->exists($umkm->logo)) {
                    Storage::disk('public')->delete($umkm->logo);
                }
                $umkm->logo = $request->file('logo')->store('logos', 'public');
            }

            $umkm->nama_toko = $request->nama_toko;
            $umkm->deskripsi = $request->deskripsi;
            $umkm->alamat = $request->alamat;
            $umkm->no_telp = $request->no_telp;
            if ($request->has('is_libur')) {
                $umkm->is_libur = $request->boolean('is_libur');
            }
            $umkm->libur_pesan = $request->libur_pesan;
            $umkm->libur_sampai = $request->libur_sampai;

            $umkm->save();

            return response()->json([
                'success' => true,
                'message' => 'Data toko berhasil diperbarui',
                'data' => $umkm
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui UMKM: ' . $e->getMessage()], 500);
        }
    }

    public function toggleLibur(Request $request)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        if (!$umkm) {
            return response()->json(['success' => false, 'message' => 'Toko UMKM tidak ditemukan'], 404);
        }

        $newLibur = !$umkm->is_libur;
        $umkm->is_libur = $newLibur;
        if ($newLibur && !$umkm->libur_pesan) {
            $umkm->libur_pesan = 'Kebun sedang masa pemulihan pasca-panen. Pemesanan ditutup sementara.';
        }
        $umkm->save();

        return response()->json([
            'success' => true,
            'is_libur' => $newLibur,
            'message' => 'Mode Libur Toko kini ' . ($newLibur ? 'AKTIF (Tutup Sementara)' : 'NONAKTIF (Buka Kembali)')
        ]);
    }

    /**
     * Seller Products Management
     */
    public function getProduks(Request $request)
    {
        try {
            $user = $request->user();
            $umkm = $this->getUserUmkm($user->id);

            if (!$umkm) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $produks = Produk::with(['diskon', 'kategori'])
                ->where('umkm_id', $umkm->id)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $produks
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data produk'], 500);
        }
    }

    public function showProduk(Request $request, $id)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        $produk = Produk::with(['diskon', 'kategori'])
            ->where('umkm_id', $umkm->id)
            ->find($id);

        if (!$produk) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $produk]);
    }

    public function storeProduk(Request $request)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        if (!$umkm) {
            return response()->json(['success' => false, 'message' => 'Silakan daftarkan toko UMKM Anda terlebih dahulu'], 400);
        }

        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'harga' => 'required|numeric|min:0',
            'harga_coret' => 'nullable|numeric|gt:harga',
            'berat_gram' => 'nullable|integer|min:100',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'persen_diskon' => 'nullable|integer|min:0|max:100|required_with:tanggal_mulai,tanggal_berakhir',
            'tanggal_mulai' => 'nullable|date|required_with:persen_diskon,tanggal_berakhir',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai|required_with:persen_diskon,tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only(['kategori_produk_id', 'nama', 'deskripsi', 'harga', 'harga_coret', 'stok']);
            $data['user_id'] = $user->id;
            $data['umkm_id'] = $umkm->id;
            $data['rating'] = 0;
            $data['is_active'] = true;
            $data['berat_gram'] = $request->input('berat_gram', 1000);

            if ($request->hasFile('gambar')) {
                $data['gambar'] = $request->file('gambar')->store('produks', 'public');
            }

            $produk = Produk::create($data);

            if ($request->filled('persen_diskon') && $request->filled('tanggal_mulai') && $request->filled('tanggal_berakhir')) {
                $produk->diskon()->create([
                    'persen_diskon' => $request->persen_diskon,
                    'tanggal_mulai' => $request->tanggal_mulai,
                    'tanggal_berakhir' => $request->tanggal_berakhir,
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan',
                'data' => $produk->load('diskon', 'kategori')
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan produk: ' . $e->getMessage()], 500);
        }
    }

    public function updateProduk(Request $request, $id)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        $produk = Produk::where('umkm_id', $umkm->id)->find($id);
        if (!$produk) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'harga' => 'required|numeric|min:0',
            'harga_coret' => 'nullable|numeric|gt:harga',
            'berat_gram' => 'nullable|integer|min:100',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
            'persen_diskon' => 'nullable|integer|min:0|max:100',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        try {
            $data = $request->only(['kategori_produk_id', 'nama', 'deskripsi', 'harga', 'harga_coret', 'stok']);
            if ($request->has('berat_gram')) {
                $data['berat_gram'] = $request->input('berat_gram');
            }

            if ($request->hasFile('gambar')) {
                if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                    Storage::disk('public')->delete($produk->gambar);
                }
                $data['gambar'] = $request->file('gambar')->store('produks', 'public');
            }

            $produk->update($data);

            if ($request->filled('persen_diskon') && $request->filled('tanggal_mulai') && $request->filled('tanggal_berakhir')) {
                $produk->diskon()->updateOrCreate(
                    ['produk_id' => $produk->id],
                    [
                        'persen_diskon' => $request->persen_diskon,
                        'tanggal_mulai' => $request->tanggal_mulai,
                        'tanggal_berakhir' => $request->tanggal_berakhir,
                    ]
                );
            } elseif ($request->has('persen_diskon') && empty($request->persen_diskon)) {
                $produk->diskon()->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil diperbarui',
                'data' => $produk->load('diskon', 'kategori')
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui produk: ' . $e->getMessage()], 500);
        }
    }

    public function deleteProduk(Request $request, $id)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        $produk = Produk::where('umkm_id', $umkm->id)->find($id);
        if (!$produk) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        try {
            if ($produk->gambar && Storage::disk('public')->exists($produk->gambar)) {
                Storage::disk('public')->delete($produk->gambar);
            }
            $produk->diskon()->delete();
            $produk->delete();

            return response()->json(['success' => true, 'message' => 'Produk berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal menghapus produk: ' . $e->getMessage()], 500);
        }
    }

    public function quickStock(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'stok' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        $produk = Produk::where('umkm_id', $umkm->id)->find($id);
        if (!$produk) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        $produk->update(['stok' => $request->stok]);

        return response()->json([
            'success' => true,
            'message' => 'Stok produk berhasil diperbarui',
            'stok' => $produk->stok
        ]);
    }

    public function toggleStatusProduk(Request $request, $id)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        $produk = Produk::where('umkm_id', $umkm->id)->find($id);
        if (!$produk) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        $newStatus = !$produk->is_active;
        $produk->update(['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'is_active' => $newStatus,
            'message' => 'Status produk berhasil diubah menjadi ' . ($newStatus ? 'Aktif' : 'Nonaktif')
        ]);
    }

    /**
     * Seller Orders Management
     */
    public function getPesanan(Request $request)
    {
        try {
            $user = $request->user();
            $umkm = $this->getUserUmkm($user->id);

            if (!$umkm) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');
            $status = $request->query('status'); // dikemas, dikirim, diterima, cancel

            $query = Order::with(['produk.diskon', 'user', 'komplain'])
                ->whereIn('produk_id', $produkIds);

            if ($status === 'dikemas') {
                $query->where('status', 'complete')
                    ->where(function($q) {
                        $q->where('status_pesanan', 'dikemas')->orWhereNull('status_pesanan');
                    });
            } elseif ($status === 'dikirim') {
                $query->where('status', 'complete')->where('status_pesanan', 'dikirim');
            } elseif ($status === 'diterima') {
                $query->where('status', 'complete')->where('status_pesanan', 'diterima');
            } elseif ($status === 'cancel') {
                $query->whereIn('status', ['cancel', 'expire', 'deny']);
            }

            $orders = $query->latest()->get();

            return response()->json([
                'success' => true,
                'data' => $orders
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data pesanan: ' . $e->getMessage()], 500);
        }
    }

    public function showPesanan(Request $request, $id)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');
        $order = Order::with(['produk.diskon', 'user', 'komplain', 'ulasan'])
            ->whereIn('produk_id', $produkIds)
            ->find($id);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        return response()->json(['success' => true, 'data' => $order]);
    }

    public function updatePesananStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status_pesanan' => 'required|in:dikemas,dikirim,diterima,belum_diterima',
            'no_resi' => 'nullable|string|max:100',
            'kurir_ekspedisi' => 'nullable|string|max:100',
            'foto_bukti_pengiriman' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        $order = Order::with('produk')->find($id);
        if (!$order || $order->produk->umkm_id !== $umkm->id) {
            return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan atau akses ditolak'], 403);
        }

        try {
            $updateData = ['status_pesanan' => $request->status_pesanan];

            if ($request->status_pesanan === 'dikemas' && !$order->dikemas_at) {
                $updateData['dikemas_at'] = now();
            } elseif ($request->status_pesanan === 'dikirim') {
                $updateData['dikirim_at'] = now();
                $updateData['tanggal_dikirim'] = now();
            } elseif ($request->status_pesanan === 'diterima') {
                $updateData['diterima_at'] = now();
                $updateData['is_escrow_released'] = true;
            }

            if ($request->filled('no_resi')) {
                $updateData['resi_pengiriman'] = $request->no_resi;
            }
            if ($request->filled('kurir_ekspedisi')) {
                $updateData['kurir'] = $request->kurir_ekspedisi;
            }
            if ($request->hasFile('foto_bukti_pengiriman')) {
                $updateData['foto_bukti_pengiriman'] = $request->file('foto_bukti_pengiriman')->store('bukti_pengiriman', 'public');
            }

            $order->update($updateData);

            return response()->json([
                'success' => true,
                'message' => 'Status pesanan dan nomor resi berhasil diperbarui',
                'data' => $order
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memperbarui pesanan: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Seller Finance & Payout
     */
    public function getPendapatan(Request $request)
    {
        try {
            $user = $request->user();
            $umkm = $this->getUserUmkm($user->id);

            if (!$umkm) {
                return response()->json(['success' => false, 'message' => 'Toko UMKM tidak ditemukan'], 404);
            }

            $komisiPersen = (float) Setting::get('komisi_persen', 20);
            $tokoPersen = 100 - $komisiPersen;

            $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');

            $omzetDiterima = DB::table('orders')
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->where(function($q) {
                    $q->where('status_pesanan', 'diterima')
                      ->orWhere('is_escrow_released', true);
                })
                ->sum('total_harga');

            $omzetEscrow = DB::table('orders')
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->where(function($q) {
                    $q->whereIn('status_pesanan', ['dikemas', 'dikirim', 'belum_diterima'])
                      ->orWhereNull('status_pesanan');
                })
                ->where('is_escrow_released', false)
                ->sum('total_harga');

            $totalPenjualan = $omzetDiterima + $omzetEscrow;
            $hakBersihDiterima = $omzetDiterima * ($tokoPersen / 100);
            $hakBersihEscrow = $omzetEscrow * ($tokoPersen / 100);

            $totalDitarikApproved = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'approved')->sum('jumlah');
            $totalDitarikPending = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'pending')->sum('jumlah');
            $saldoTersedia = max(0, $hakBersihDiterima - $totalDitarikApproved - $totalDitarikPending);

            return response()->json([
                'success' => true,
                'data' => [
                    'komisi_persen' => $komisiPersen,
                    'toko_persen' => $tokoPersen,
                    'total_penjualan' => $totalPenjualan,
                    'omzet_diterima' => $omzetDiterima,
                    'omzet_escrow' => $omzetEscrow,
                    'hak_bersih_diterima' => $hakBersihDiterima,
                    'hak_bersih_escrow' => $hakBersihEscrow,
                    'saldo_tersedia' => $saldoTersedia,
                    'total_ditarik_approved' => $totalDitarikApproved,
                    'total_ditarik_pending' => $totalDitarikPending,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil rincian pendapatan: ' . $e->getMessage()], 500);
        }
    }

    public function getPenarikan(Request $request)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        if (!$umkm) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $riwayat = PenarikanSaldo::where('umkm_id', $umkm->id)->latest()->get();

        return response()->json(['success' => true, 'data' => $riwayat]);
    }

    public function storePenarikan(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jumlah' => 'required|numeric|min:50000',
            'nama_bank' => 'required|string|max:50',
            'nomor_rekening' => 'required|string|max:50',
            'atas_nama' => 'required|string|max:100',
        ], [
            'jumlah.min' => 'Minimal penarikan saldo adalah Rp 50.000.',
            'nama_bank.required' => 'Nama bank wajib diisi.',
            'nomor_rekening.required' => 'Nomor rekening wajib diisi.',
            'atas_nama.required' => 'Nama pemilik rekening wajib diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        try {
            $record = DB::transaction(function () use ($request, $user) {
                $umkm = Umkm::where('user_id', $user->id)->lockForUpdate()->firstOrFail();

                $komisiPersen = (float) Setting::get('komisi_persen', 20);
                $tokoPersen = 100 - $komisiPersen;

                $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');
                $omzetDiterima = DB::table('orders')
                    ->whereIn('produk_id', $produkIds)
                    ->where('status', 'complete')
                    ->where(function($q) {
                        $q->where('status_pesanan', 'diterima')
                          ->orWhere('is_escrow_released', true);
                    })
                    ->sum('total_harga');

                $hakBersihDiterima = $omzetDiterima * ($tokoPersen / 100);

                $totalDitarikApproved = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'approved')->lockForUpdate()->sum('jumlah');
                $totalDitarikPending = PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'pending')->lockForUpdate()->sum('jumlah');

                $saldoTersedia = max(0, $hakBersihDiterima - $totalDitarikApproved - $totalDitarikPending);

                if ($request->jumlah > $saldoTersedia) {
                    throw new \Exception('Jumlah penarikan melebihi saldo siap tarik (Rp ' . number_format($saldoTersedia, 0, ',', '.') . ').');
                }

                $rec = PenarikanSaldo::create([
                    'umkm_id' => $umkm->id,
                    'jumlah' => $request->jumlah,
                    'nama_bank' => strtoupper($request->nama_bank),
                    'nomor_rekening' => $request->nomor_rekening,
                    'atas_nama' => $request->atas_nama,
                    'status' => 'pending',
                ]);

                ActivityLog::record(
                    'REQUEST_PAYOUT',
                    "Toko {$umkm->nama_toko} mengajukan penarikan saldo sebesar Rp " . number_format($request->jumlah, 0, ',', '.') . " ke {$request->nama_bank} - {$request->nomor_rekening}",
                    $rec
                );

                return $rec;
            });

            return response()->json([
                'success' => true,
                'message' => 'Permohonan penarikan saldo berhasil dikirim. Admin akan memverifikasi dalam 1x24 jam.',
                'data' => $record
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    public function replyUlasan(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'balasan' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['success' => false, 'message' => 'Validasi gagal', 'errors' => $validator->errors()], 422);
        }

        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        $ulasan = Ulasan::with('produk')->find($id);
        if (!$ulasan || $ulasan->produk->umkm_id !== $umkm->id) {
            return response()->json(['success' => false, 'message' => 'Ulasan tidak ditemukan atau bukan produk toko Anda'], 403);
        }

        $ulasan->update(['balasan' => $request->balasan]);

        return response()->json([
            'success' => true,
            'message' => 'Balasan ulasan berhasil disimpan',
            'data' => $ulasan
        ]);
    }
}

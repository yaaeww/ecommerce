<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Diskon;
use App\Models\KategoriProduk;
use App\Models\Komplain;
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
use App\Services\ImageOptimizerService;

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

            // Semua order berstatus complete (teralisasi & dalam proses) = omzet kotor
            $completeOrders = Order::with(['produk', 'user'])
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->get();

            $totalOmzetKotor = (float) $completeOrders->sum('total_harga');
            $omzetBersih = round($totalOmzetKotor * ($tokoPersen / 100), 2);

            // Omzet diterima (sudah konfirmasi diterima / escrow dilepas)
            $omzetDiterima = (float) $completeOrders
                ->filter(function ($o) {
                    return $o->status_pesanan === 'diterima' || $o->is_escrow_released === true;
                })
                ->sum('total_harga');

            // Omzet escrow (masih dalam proses pengiriman / belum diterima)
            $omzetEscrow = (float) $completeOrders
                ->filter(function ($o) {
                    return $o->is_escrow_released !== true
                        && in_array($o->status_pesanan, ['dikemas', 'dikirim', 'belum_diterima', null, '']);
                })
                ->sum('total_harga');

            $hakBersihDiterima = round($omzetDiterima * ($tokoPersen / 100), 2);
            $hakBersihEscrow = round($omzetEscrow * ($tokoPersen / 100), 2);

            // Penarikan
            $totalDitarikApproved = (float) PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'approved')->sum('jumlah');
            $totalDitarikPending = (float) PenarikanSaldo::where('umkm_id', $umkm->id)->where('status', 'pending')->sum('jumlah');
            $saldoTersedia = max(0, $omzetBersih - $totalDitarikApproved - $totalDitarikPending);

            // Pesanan yang perlu ditindaklanjuti (menunggu diproses / sedang dikemas / belum input)
            $pesananPerluDikirim = $completeOrders
                ->filter(function ($o) {
                    return in_array($o->status_pesanan, ['menunggu_diproses', 'dikemas', 'belum_diterima', null, '']);
                })
                ->count();

            $pesananDikirim = $completeOrders->where('status_pesanan', 'dikirim')->count();
            $pesananSelesai = $completeOrders->where('status_pesanan', 'diterima')->count();

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
                        'total_penjualan' => $totalOmzetKotor,
                        'omzet_bersih' => $omzetBersih,
                        'omzet_diterima' => $omzetDiterima,
                        'omzet_escrow' => $omzetEscrow,
                        'hak_bersih_diterima' => $hakBersihDiterima,
                        'hak_bersih_escrow' => $hakBersihEscrow,
                        'saldo_tersedia' => $saldoTersedia,
                        'total_ditarik_approved' => $totalDitarikApproved,
                        'total_ditarik_pending' => $totalDitarikPending,
                        'total_produk' => $totalProduk,
                        'stok_menipis' => $stokMenipis,
                        'pesanan_perlu_dikirim' => $pesananPerluDikirim,
                        'pesanan_dikirim' => $pesananDikirim,
                        'pesanan_selesai' => $pesananSelesai,
                    ],
                    'stats' => [
                        'saldo_tersedia' => $saldoTersedia,
                        'omzet_bersih' => $omzetBersih,
                        'total_produk' => $totalProduk,
                        'pesanan_perlu_dikirim' => $pesananPerluDikirim,
                        'total_penjualan' => $totalOmzetKotor,
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
                return $this->updateUmkm($request);
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
     * Seller Settings (komisi info for form)
     */
    public function getSettings(Request $request)
    {
        $komisiPersen = (float) Setting::get('komisi_persen', 20);
        $tokoPersen = 100 - $komisiPersen;

        return response()->json([
            'success' => true,
            'data' => [
                'komisi_persen' => $komisiPersen,
                'toko_persen' => $tokoPersen,
            ]
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

            $query = Produk::with(['diskon', 'kategori'])
                ->where('umkm_id', $umkm->id);

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where('nama', 'like', "%{$search}%");
            }

            $produks = $query->latest()->get();

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

        $produk = Produk::with(['diskon', 'kategori', 'ulasan.user'])
            ->where('umkm_id', $umkm->id)
            ->find($id);

        if (!$produk) {
            return response()->json(['success' => false, 'message' => 'Produk tidak ditemukan'], 404);
        }

        $avgBintang = DB::table(function ($query) use ($produk) {
            $query->from('ulasan')
                ->select('users_id', DB::raw('AVG(bintang) as user_avg'))
                ->where('produks_id', $produk->id)
                ->groupBy('users_id');
        }, 'subquery')
            ->select(DB::raw('AVG(user_avg) as rata_rata'))
            ->value('rata_rata');

        $produk->rating = round($avgBintang ?? 0, 2);

        return response()->json(['success' => true, 'data' => $produk]);
    }

    public function storeProduk(Request $request)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        if (!$umkm) {
            return response()->json(['success' => false, 'message' => 'Silakan daftarkan toko UMKM Anda terlebih dahulu'], 400);
        }

        // Normalisasi diskon & harga coret opsional
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

        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'harga' => 'required|numeric|min:0',
            'harga_coret' => 'nullable|numeric|gt:harga',
            'berat_gram' => 'nullable|integer|min:100',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,jfif|max:10240',
            'persen_diskon' => 'nullable|integer|min:0|max:100|required_with:tanggal_mulai,tanggal_berakhir',
            'tanggal_mulai' => 'nullable|date|required_with:persen_diskon,tanggal_berakhir',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai|required_with:persen_diskon,tanggal_mulai',
        ], [
            'harga_coret.gt' => 'Harga coret harus lebih besar dari harga jual normal jika ingin memasang promo diskon. Kosongkan jika tidak ada diskon.',
            'gambar.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, WEBP, GIF, atau BMP.',
            'gambar.max' => 'Ukuran gambar maksimal adalah 10MB.',
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
                $data['gambar'] = ImageOptimizerService::convertToWebp($request->file('gambar'), 'produks');
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

        // Normalisasi diskon & harga coret opsional
        $harga = (float) $request->input('harga', $produk->harga);
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

        $validator = Validator::make($request->all(), [
            'kategori_produk_id' => 'required|exists:kategori_produks,id',
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:5000',
            'harga' => 'required|numeric|min:0',
            'harga_coret' => 'nullable|numeric|gt:harga',
            'berat_gram' => 'nullable|integer|min:100',
            'stok' => 'required|integer|min:0',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp,bmp,jfif|max:10240',
            'persen_diskon' => 'nullable|integer|min:0|max:100',
            'tanggal_mulai' => 'nullable|date',
            'tanggal_berakhir' => 'nullable|date|after_or_equal:tanggal_mulai',
        ], [
            'harga_coret.gt' => 'Harga coret harus lebih besar dari harga jual normal jika ingin memasang promo diskon. Kosongkan jika tidak ada diskon.',
            'gambar.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, WEBP, GIF, atau BMP.',
            'gambar.max' => 'Ukuran gambar maksimal adalah 10MB.',
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
                $data['gambar'] = ImageOptimizerService::convertToWebp($request->file('gambar'), 'produks');
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
                return response()->json([
                    'success' => true,
                    'data' => [],
                    'counts' => ['all' => 0, 'dikemas' => 0, 'dikirim' => 0, 'diterima' => 0, 'cancel' => 0],
                    'period' => 'all',
                    'active_period_label' => 'Semua Waktu',
                ]);
            }

            $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');
            $status = $request->query('status'); // dikemas, dikirim, diterima, cancel, all
            $search = $request->query('search');

            // Date filter parameters (identik dengan filter website)
            $period = $request->query('period', 'all');
            $startDateInput = $request->query('start_date');
            $endDateInput = $request->query('end_date');

            $startDate = null;
            $endDate = null;
            $activePeriodLabel = 'Semua Waktu';

            if ($period === 'today') {
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $activePeriodLabel = 'Hari Ini (' . $startDate->translatedFormat('d F Y') . ')';
            } elseif ($period === '7days') {
                $startDate = Carbon::now()->subDays(6)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $activePeriodLabel = '7 Hari Terakhir';
            } elseif ($period === '30days') {
                $startDate = Carbon::now()->subDays(29)->startOfDay();
                $endDate = Carbon::now()->endOfDay();
                $activePeriodLabel = '30 Hari Terakhir';
            } elseif ($period === 'this_month') {
                $startDate = Carbon::now()->startOfMonth()->startOfDay();
                $endDate = Carbon::now()->endOfMonth()->endOfDay();
                $activePeriodLabel = 'Bulan Ini (' . $startDate->translatedFormat('F Y') . ')';
            } elseif ($period === 'this_year') {
                $startDate = Carbon::now()->startOfYear()->startOfDay();
                $endDate = Carbon::now()->endOfYear()->endOfDay();
                $activePeriodLabel = 'Tahun Ini (' . $startDate->translatedFormat('Y') . ')';
            } elseif ($startDateInput) {
                $period = 'custom';
                $startDate = Carbon::parse($startDateInput)->startOfDay();
                $endDate = $endDateInput ? Carbon::parse($endDateInput)->endOfDay() : Carbon::parse($startDateInput)->endOfDay();
                if ($startDate->isSameDay($endDate)) {
                    $activePeriodLabel = $startDate->translatedFormat('d F Y');
                } else {
                    $activePeriodLabel = $startDate->translatedFormat('d M Y') . ' s/d ' . $endDate->translatedFormat('d M Y');
                }
            }

            // Base query for seller orders
            $baseQuery = Order::with(['produk.diskon', 'produk.umkm.user', 'user', 'komplain'])
                ->whereIn('produk_id', $produkIds);

            if ($startDate && $endDate) {
                $baseQuery->whereBetween('created_at', [$startDate, $endDate]);
            }

            if (!empty($search)) {
                $baseQuery->where(function($q) use ($search) {
                    $q->where('order_id_midtrans', 'like', "%{$search}%")
                      ->orWhere('name', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%")
                      ->orWhereHas('produk', function($pq) use ($search) {
                          $pq->where('nama', 'like', "%{$search}%");
                      });
                });
            }

            // Count summaries for tabs
            $counts = [
                'all' => (clone $baseQuery)->count(),
                'dikemas' => (clone $baseQuery)->where('status', 'complete')->where(function($q) {
                    $q->where('status_pesanan', 'dikemas')->orWhereNull('status_pesanan');
                })->count(),
                'dikirim' => (clone $baseQuery)->where('status', 'complete')->where('status_pesanan', 'dikirim')->count(),
                'diterima' => (clone $baseQuery)->where('status', 'complete')->where('status_pesanan', 'diterima')->count(),
                'cancel' => (clone $baseQuery)->whereIn('status', ['cancel', 'expire', 'deny'])->count(),
            ];

            // Apply status filter
            $query = clone $baseQuery;
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
                'data' => $orders,
                'counts' => $counts,
                'period' => $period,
                'active_period_label' => $activePeriodLabel,
                'start_date' => $startDateInput,
                'end_date' => $endDateInput,
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
        $order = Order::with(['produk.diskon', 'produk.umkm.user', 'user', 'komplain', 'ulasan', 'ulasan.user'])
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
            'foto_bukti_pengiriman' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif,bmp|max:5120',
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
                $updateData['foto_bukti_pengiriman'] = ImageOptimizerService::convertToWebp(
                    $request->file('foto_bukti_pengiriman'),
                    'bukti_pengiriman'
                );
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

            $riwayatPenarikan = PenarikanSaldo::where('umkm_id', $umkm->id)
                ->latest()
                ->get()
                ->map(function ($p) {
                    return [
                        'id' => $p->id,
                        'umkm_id' => $p->umkm_id,
                        'jumlah' => (float) $p->jumlah,
                        'nama_bank' => $p->nama_bank,
                        'nomor_rekening' => $p->nomor_rekening,
                        'atas_nama' => $p->atas_nama,
                        'status' => $p->status,
                        'catatan_admin' => $p->catatan_admin,
                        'bukti_transfer' => $p->bukti_transfer,
                        'bukti_transfer_url' => $p->bukti_transfer
                            ? url('storage/' . $p->bukti_transfer)
                            : null,
                        'processed_at' => $p->processed_at,
                        'created_at' => $p->created_at ? $p->created_at->toDateTimeString() : null,
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'komisi_persen' => $komisiPersen,
                    'toko_persen' => $tokoPersen,
                    'total_penjualan' => $totalPenjualan,
                    'omzet_total' => $totalPenjualan,
                    'omzet_diterima' => $omzetDiterima,
                    'omzet_escrow' => $omzetEscrow,
                    'hak_bersih_diterima' => $hakBersihDiterima,
                    'hak_bersih_escrow' => $hakBersihEscrow,
                    'saldo_tersedia' => $saldoTersedia,
                    'saldo_tertahan' => $hakBersihEscrow,
                    'total_ditarik' => $totalDitarikApproved,
                    'total_ditarik_approved' => $totalDitarikApproved,
                    'total_ditarik_pending' => $totalDitarikPending,
                    'total_pending' => $totalDitarikPending,
                    'riwayat_penarikan' => $riwayatPenarikan,
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
            'balasan_penjual' => 'required|string|max:500',
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

        $ulasan->update([
            'balasan_penjual' => $request->balasan_penjual,
            'balasan_penjual_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Balasan ulasan berhasil disimpan',
            'data' => $ulasan
        ]);
    }

    /**
     * Seller Notifications (computed on-the-fly, memory-based via DB timestamp)
     */
    public function getNotifications(Request $request)
    {
        $user = $request->user();
        $lastRead = $user->last_read_notif_at;
        $notifications = collect();

        $umkm = $this->getUserUmkm($user->id);

        if (!$umkm) {
            $notifications->push([
                'id' => 'umkm_empty',
                'type' => 'umkm_empty',
                'category' => 'action_required',
                'title' => 'Daftarkan Toko Anda',
                'description' => 'Toko belum didaftarkan. Daftarkan toko untuk mulai berjualan.',
                'time' => now()->diffForHumans(),
                'timestamp' => time(),
                'is_unread' => true,
                'is_critical' => true,
            ]);
            return response()->json($this->notificationPayload($notifications));
        }

        $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');
        $now = Carbon::now();

        // 1. New orders / SLA overdue
        try {
            $pendingOrders = Order::with(['user', 'produk'])
                ->whereIn('produk_id', $produkIds)
                ->where('status', 'complete')
                ->where(function ($q) {
                    $q->whereNull('status_pesanan')
                      ->orWhere('status_pesanan', 'menunggu_diproses')
                      ->orWhere('status_pesanan', 'dikemas');
                })
                ->latest()
                ->take(5)
                ->get();

            foreach ($pendingOrders as $order) {
                $isOverdue = $order->created_at && $order->created_at->diffInHours($now) >= 24;
                $ts = $order->created_at ? $order->created_at->timestamp : time();
                $notifications->push([
                    'id' => 'order_' . $order->id,
                    'type' => $isOverdue ? 'sla_overdue' : 'new_order',
                    'category' => $isOverdue ? 'action_required' : 'order',
                    'title' => $isOverdue ? '⚠️ SLA Keterlambatan Pengiriman' : '🛒 Pesanan Baru Masuk',
                    'description' => "Pesanan #ORD-" . str_pad($order->id, 5, '0', STR_PAD_LEFT) . " dari " . ($order->name ?: 'Pembeli') . " (" . ($order->produk->nama ?? 'Produk') . " - {$order->jumlah}x)",
                    'time' => $order->created_at ? $order->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => !$lastRead || $ts > $lastRead->timestamp,
                    'is_critical' => $isOverdue,
                    'ref_id' => $order->id,
                    'ref_type' => 'order',
                ]);
            }
        } catch (\Throwable $e) {}

        // 2. Payout status
        try {
            $payouts = PenarikanSaldo::where('umkm_id', $umkm->id)
                ->latest()
                ->take(3)
                ->get();

            foreach ($payouts as $payout) {
                $ts = $payout->updated_at ? $payout->updated_at->timestamp : time();
                $isApproved = $payout->status === 'approved';
                $isPending = $payout->status === 'pending';

                $notifications->push([
                    'id' => 'payout_' . $payout->id,
                    'type' => 'payout',
                    'category' => 'finance',
                    'title' => $isApproved ? '💰 Pencairan Saldo Berhasil' : ($isPending ? '⏳ Pencairan Saldo Diproses' : '❌ Pencairan Saldo Ditolak'),
                    'description' => "Penarikan Rp " . number_format((float) ($payout->jumlah ?? 0), 0, ',', '.') . " (" . ucfirst($payout->status) . ")",
                    'time' => $payout->updated_at ? $payout->updated_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => !$lastRead || $ts > $lastRead->timestamp,
                    'is_critical' => false,
                    'ref_id' => $payout->id,
                    'ref_type' => 'payout',
                ]);
            }
        } catch (\Throwable $e) {}

        // 3. New reviews
        try {
            $recentUlasans = Ulasan::with(['user', 'produk'])
                ->whereIn('produks_id', $produkIds)
                ->where(function ($q) {
                    $q->whereNull('status_moderasi')
                      ->orWhere('status_moderasi', '!=', 'hidden');
                })
                ->latest()
                ->take(3)
                ->get();

            foreach ($recentUlasans as $review) {
                $ts = $review->created_at ? $review->created_at->timestamp : time();
                $isLowRating = $review->bintang <= 2;

                $notifications->push([
                    'id' => 'review_' . $review->id,
                    'type' => 'review',
                    'category' => $isLowRating ? 'action_required' : 'review',
                    'title' => "⭐ Ulasan Baru: " . $review->bintang . " Bintang",
                    'description' => "Pembeli " . ($review->user->name ?? 'Pelanggan') . " mengulas " . ($review->produk->nama ?? 'Produk') . ": \"" . substr($review->ulasan, 0, 45) . "\"",
                    'time' => $review->created_at ? $review->created_at->diffForHumans() : 'Baru saja',
                    'timestamp' => $ts,
                    'is_unread' => !$lastRead || $ts > $lastRead->timestamp,
                    'is_critical' => $isLowRating,
                    'ref_id' => $review->id,
                    'ref_type' => 'review',
                ]);
            }
        } catch (\Throwable $e) {}

        return response()->json($this->notificationPayload(
            $notifications->sortByDesc('timestamp')->values()
        ));
    }

    private function notificationPayload($notifications)
    {
        $actionRequiredCount = $notifications->where('is_critical', true)->where('is_unread', true)->count();
        $totalUnreadCount = $notifications->where('is_unread', true)->count();
        $totalCount = $notifications->count();

        return [
            'success' => true,
            'action_required_count' => $actionRequiredCount,
            'total_unread_count' => $totalUnreadCount,
            'total_count' => $totalCount,
            'data' => $notifications->values()->all(),
        ];
    }

    public function markNotificationsRead(Request $request)
    {
        $user = $request->user();
        $user->last_read_notif_at = now();
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Semua notifikasi telah ditandai sudah dibaca.',
        ]);
    }

    /**
     * Seller Complaints / Warranty (read-only view)
     */
    public function getKomplain(Request $request)
    {
        try {
            $user = $request->user();
            $umkm = $this->getUserUmkm($user->id);

            if (!$umkm) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');

            $komplains = Komplain::with(['order.produk', 'order.ulasan', 'user'])
                ->whereHas('order', function ($q) use ($produkIds) {
                    $q->whereIn('produk_id', $produkIds);
                })
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data komplain toko berhasil diambil',
                'data' => $komplains
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data komplain toko'], 500);
        }
    }

    public function getUlasanList(Request $request)
    {
        try {
            $user = $request->user();
            $umkm = $this->getUserUmkm($user->id);

            if (!$umkm) {
                return response()->json(['success' => true, 'data' => []]);
            }

            $produkIds = Produk::where('umkm_id', $umkm->id)->pluck('id');

            $ulasan = Ulasan::with(['produk', 'user'])
                ->whereIn('produks_id', $produkIds)
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'data' => $ulasan
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengambil data ulasan'], 500);
        }
    }

    public function showKomplain(Request $request, $id)
    {
        $user = $request->user();
        $umkm = $this->getUserUmkm($user->id);

        $komplain = Komplain::with(['order.produk', 'order.ulasan', 'order.ulasan.user', 'user'])
            ->whereHas('order', function ($q) use ($umkm) {
                $q->whereHas('produk', function ($p) use ($umkm) {
                    $p->where('umkm_id', $umkm->id);
                });
            })
            ->find($id);

        if (!$komplain) {
            return response()->json(['success' => false, 'message' => 'Komplain tidak ditemukan'], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Detail komplain berhasil diambil',
            'data' => $komplain
        ]);
    }
}

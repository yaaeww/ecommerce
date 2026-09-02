<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Alamat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AlamatApiController extends Controller
{
    /**
     * Get all addresses for authenticated buyer
     */
    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $alamats = Alamat::where('user_id', $user->id)
                ->orderBy('is_utama', 'desc')
                ->latest()
                ->get();

            return response()->json([
                'success' => true,
                'message' => 'Data alamat berhasil diambil',
                'data' => $alamats
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store new shipping address
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:50',
            'nama_penerima' => 'required|string|max:100',
            'no_hp' => 'required|string|max:25',
            'provinsi' => 'required|string|max:100',
            'kota_kabupaten' => 'required|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'alamat_lengkap' => 'required|string|max:500',
            'patokan' => 'nullable|string|max:255',
            'is_utama' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = $request->user();
            $isUtama = $request->boolean('is_utama');

            // If this is user's first address, make it default utama
            $hasAlamat = Alamat::where('user_id', $user->id)->exists();
            if (!$hasAlamat) {
                $isUtama = true;
            }

            if ($isUtama) {
                Alamat::where('user_id', $user->id)->update(['is_utama' => false]);
            }

            $alamat = Alamat::create([
                'user_id' => $user->id,
                'label' => $request->label,
                'nama_penerima' => $request->nama_penerima,
                'no_hp' => $request->no_hp,
                'provinsi' => $request->provinsi,
                'kota_kabupaten' => $request->kota_kabupaten,
                'kecamatan' => $request->kecamatan,
                'kode_pos' => $request->kode_pos,
                'alamat_lengkap' => $request->alamat_lengkap,
                'patokan' => $request->patokan,
                'is_utama' => $isUtama,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alamat baru berhasil disimpan',
                'data' => $alamat
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menyimpan alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update address
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();
        $alamat = Alamat::where('user_id', $user->id)->find($id);

        if (!$alamat) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'label' => 'required|string|max:50',
            'nama_penerima' => 'required|string|max:100',
            'no_hp' => 'required|string|max:25',
            'provinsi' => 'required|string|max:100',
            'kota_kabupaten' => 'required|string|max:100',
            'kecamatan' => 'nullable|string|max:100',
            'kode_pos' => 'nullable|string|max:10',
            'alamat_lengkap' => 'required|string|max:500',
            'patokan' => 'nullable|string|max:255',
            'is_utama' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $isUtama = $request->boolean('is_utama');
            if ($isUtama) {
                Alamat::where('user_id', $user->id)->where('id', '!=', $id)->update(['is_utama' => false]);
            }

            $alamat->update([
                'label' => $request->label,
                'nama_penerima' => $request->nama_penerima,
                'no_hp' => $request->no_hp,
                'provinsi' => $request->provinsi,
                'kota_kabupaten' => $request->kota_kabupaten,
                'kecamatan' => $request->kecamatan,
                'kode_pos' => $request->kode_pos,
                'alamat_lengkap' => $request->alamat_lengkap,
                'patokan' => $request->patokan,
                'is_utama' => $isUtama,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil diperbarui',
                'data' => $alamat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Set address as primary
     */
    public function setUtama(Request $request, $id)
    {
        $user = $request->user();
        $alamat = Alamat::where('user_id', $user->id)->find($id);

        if (!$alamat) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

        try {
            Alamat::where('user_id', $user->id)->update(['is_utama' => false]);
            $alamat->update(['is_utama' => true]);

            return response()->json([
                'success' => true,
                'message' => "Alamat '{$alamat->label}' sekarang menjadi alamat utama",
                'data' => $alamat
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah alamat utama',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete address
     */
    public function destroy(Request $request, $id)
    {
        $user = $request->user();
        $alamat = Alamat::where('user_id', $user->id)->find($id);

        if (!$alamat) {
            return response()->json([
                'success' => false,
                'message' => 'Alamat tidak ditemukan'
            ], 404);
        }

        try {
            $alamat->delete();
            return response()->json([
                'success' => true,
                'message' => 'Alamat berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus alamat',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

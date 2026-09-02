<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class GoogleAuthApiController extends Controller
{
    /**
     * Verifikasi ID Token Google dan login/buat akun.
     * Token diverifikasi ke Google Token Info endpoint secara stateless.
     */
    public function login(Request $request)
    {
        $request->validate([
            'id_token' => ['required', 'string'],
        ], [
            'id_token.required' => 'Token Google tidak ditemukan.',
        ]);

        try {
            // Verifikasi ID token ke Google
            $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $request->id_token,
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Token Google tidak valid atau sudah kedaluwarsa.',
                ], 401);
            }

            $info = $response->json();
            $googleEmail = $info['email'] ?? null;

            if (!$googleEmail) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak ditemukan pada akun Google.',
                ], 422);
            }

            // Cek apakah user sudah terdaftar
            $user = User::where('email', $googleEmail)->first();

            if (!$user) {
                // User baru belum memilih role — kembalikan kandidat agar aplikasi memilih role
                return response()->json([
                    'success' => true,
                    'requires_role_selection' => true,
                    'error' => false,
                    'message' => 'Akun Google baru terdeteksi. Silakan pilih jenis akun (Pembeli/Penjual).',
                    'data' => [
                        'name' => $info['name'] ?? $googleEmail,
                        'email' => $googleEmail,
                        'avatar' => $info['picture'] ?? null,
                    ],
                ]);
            }

            $token = $user->createToken('auth_token')->plainTextToken;

            if ($user->role === 'penjual') {
                $user->load('umkm');
            }

            return response()->json([
                'success' => true,
                'message' => 'Login dengan Google berhasil.',
                'data' => [
                    'user' => $user,
                    'token' => $token,
                    'token_type' => 'Bearer',
                ],
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'Login dengan Google gagal! ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Buat akun baru yang berasal dari Google setelah memilih role.
     */
    public function chooseRole(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', 'in:penjual,pembeli'],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.unique' => 'Email sudah terdaftar. Silakan login langsung.',
            'role.required' => 'Silakan pilih jenis akun.',
        ]);

        $requestData = $request->all();

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt(Str::random(32)),
            'role' => $request->role,
            'avatar' => $request->avatar ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dibuat dengan Google.',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }
}

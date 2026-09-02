<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Mail\ResetPasswordOtpMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class OtpPasswordResetApiController extends Controller
{
    /**
     * Langkah 1: Terima email dan kirim 6 digit kode OTP.
     */
    public function requestOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Silakan masukkan alamat email Anda.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Alamat email tidak terdaftar di sistem Juragan Pelem.',
        ]);

        $user = User::where('email', $request->email)->first();
        $otp = (string) mt_rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now(),
            ]
        );

        try {
            Mail::to($request->email)->send(new ResetPasswordOtpMail($otp, $user->name ?? 'Pelanggan'));
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim email OTP: " . $e->getMessage());
        }

        // Mask email: ih***@domain.com
        $parts = explode('@', $request->email);
        $name = $parts[0];
        $domain = $parts[1] ?? 'email.com';
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 3));
        $maskedEmail = $maskedName . '@' . $domain;

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP telah dikirimkan ke alamat email Anda. Silakan periksa kotak masuk (Inbox/Spam).',
            'data' => [
                'email' => $request->email,
                'masked_email' => $maskedEmail,
                'expires_in_minutes' => 10,
                // Hint hanya untuk pengembangan lokal (mail driver = log)
                'dev_otp_hint' => config('mail.default') === 'log' ? $otp : null,
            ],
        ]);
    }

    /**
     * Langkah 2: Verifikasi 6 digit kode OTP, kembalikan token reset sementara.
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'email.required' => 'Silakan masukkan alamat email Anda.',
            'email.exists' => 'Alamat email tidak terdaftar.',
            'otp.required' => 'Silakan masukkan 6 digit kode OTP.',
            'otp.size' => 'Kode OTP harus terdiri dari 6 angka.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan OTP tidak ditemukan. Silakan ajukan ulang.',
            ], 422);
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(10)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP telah kedaluwarsa (berlaku 10 menit). Silakan kirim ulang OTP.',
            ], 422);
        }

        if (!Hash::check($request->otp, $record->token)) {
            return response()->json([
                'success' => false,
                'message' => 'Kode OTP salah. Pastikan Anda memasukkan 6 angka yang benar.',
            ], 422);
        }

        // Buat token otorisasi ganti password (sementara, valid 10 menit)
        $resetToken = Str::random(40);

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP terverifikasi! Silakan buat kata sandi baru Anda.',
            'data' => [
                'reset_token' => $resetToken,
                'expires_in_minutes' => 10,
            ],
        ]);
    }

    /**
     * Kirim ulang kode OTP.
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Silakan masukkan alamat email Anda.',
            'email.exists' => 'Alamat email tidak terdaftar.',
        ]);

        $user = User::where('email', $request->email)->first();
        $otp = (string) mt_rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now(),
            ]
        );

        try {
            Mail::to($request->email)->send(new ResetPasswordOtpMail($otp, $user->name ?? 'Pelanggan'));
        } catch (\Throwable $e) {
            Log::error("Gagal kirim ulang OTP: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Kode OTP baru telah berhasil dikirimkan ke email Anda!',
            'data' => [
                'dev_otp_hint' => config('mail.default') === 'log' ? $otp : null,
            ],
        ]);
    }

    /**
     * Langkah 3: Simpan kata sandi baru (verifikasi via reset_token).
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'reset_token' => ['required', 'string', 'size:40'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.required' => 'Silakan masukkan alamat email Anda.',
            'reset_token.required' => 'Token verifikasi tidak ditemukan.',
            'password.required' => 'Silakan masukkan kata sandi baru.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        $record = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Permintaan reset tidak ditemukan. Silakan ulangi proses dari awal.',
            ], 422);
        }

        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(10)->isPast()) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi reset telah kedaluwarsa. Silakan ulangi proses dari awal.',
            ], 422);
        }

        // Update password user
        $user = User::where('email', $request->email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.',
        ]);
    }
}

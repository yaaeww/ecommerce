<?php

namespace App\Http\Controllers\Auth;

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

class OtpPasswordResetController extends Controller
{
    /**
     * Langkah 1: Menerima email dan mengirimkan 6 Digit Kode OTP
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

        // Simpan hash OTP ke tabel password_reset_tokens
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now(),
            ]
        );

        // Kirim email OTP
        try {
            Mail::to($request->email)->send(new ResetPasswordOtpMail($otp, $user->name ?? 'Pelanggan'));
        } catch (\Throwable $e) {
            Log::error("Gagal mengirim email OTP: " . $e->getMessage());
        }

        // Catat di session
        session([
            'otp_email' => $request->email,
            'otp_sent_at' => Carbon::now()->timestamp,
            // Simpan dev hint jika mailer lokal/log
            'dev_otp_hint' => config('mail.default') === 'log' ? $otp : null,
        ]);

        return redirect()->route('password.otp.view')
            ->with('status', 'Kode OTP telah dikirimkan ke alamat email Anda. Silakan periksa kotak masuk (Inbox/Spam).');
    }

    /**
     * Langkah 2: Menampilkan halaman input 6 digit OTP
     */
    public function showOtpForm()
    {
        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi telah berakhir. Silakan masukkan email Anda kembali.']);
        }

        // Mask email: ih***@domain.com
        $parts = explode('@', $email);
        $name = $parts[0];
        $domain = $parts[1] ?? 'email.com';
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 3));
        $maskedEmail = $maskedName . '@' . $domain;

        return view('auth.verify-otp', [
            'email' => $email,
            'maskedEmail' => $maskedEmail,
            'devOtpHint' => session('dev_otp_hint'),
        ]);
    }

    /**
     * Langkah 3: Verifikasi 6 Digit OTP
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'size:6'],
        ], [
            'otp.required' => 'Silakan masukkan 6 digit kode OTP.',
            'otp.size' => 'Kode OTP harus terdiri dari 6 angka.',
        ]);

        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Sesi telah berakhir. Silakan minta kode OTP baru.']);
        }

        $record = DB::table('password_reset_tokens')->where('email', $email)->first();

        if (!$record) {
            return back()->withErrors(['otp' => 'Permintaan OTP tidak ditemukan. Silakan ajukan ulang.']);
        }

        // Cek kedaluwarsa 10 menit
        $createdAt = Carbon::parse($record->created_at);
        if ($createdAt->addMinutes(10)->isPast()) {
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa (berlaku 10 menit). Silakan kirim ulang OTP.']);
        }

        // Cek kesesuaian OTP
        if (!Hash::check($request->otp, $record->token)) {
            return back()->withErrors(['otp' => 'Kode OTP salah. Pastikan Anda memasukkan 6 angka yang benar.']);
        }

        // Buat token otorisasi ganti password
        $authResetToken = Str::random(40);
        session([
            'reset_authorized_email' => $email,
            'reset_auth_token' => $authResetToken,
        ]);

        return redirect()->route('password.otp.new-password')
            ->with('status', 'Kode OTP terverifikasi! Silakan buat kata sandi baru Anda.');
    }

    /**
     * Kirim Ulang OTP
     */
    public function resendOtp()
    {
        $email = session('otp_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $user = User::where('email', $email)->first();
        $otp = (string) mt_rand(100000, 999999);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $email],
            [
                'token' => Hash::make($otp),
                'created_at' => Carbon::now(),
            ]
        );

        try {
            Mail::to($email)->send(new ResetPasswordOtpMail($otp, $user->name ?? 'Pelanggan'));
        } catch (\Throwable $e) {
            Log::error("Gagal kirim ulang OTP: " . $e->getMessage());
        }

        session([
            'otp_sent_at' => Carbon::now()->timestamp,
            'dev_otp_hint' => config('mail.default') === 'log' ? $otp : null,
        ]);

        return back()->with('status', 'Kode OTP baru telah berhasil dikirimkan ke email Anda!');
    }

    /**
     * Langkah 4: Tampilan Form Buat Password Baru
     */
    public function showNewPasswordForm()
    {
        $email = session('reset_authorized_email');
        $authToken = session('reset_auth_token');

        if (!$email || !$authToken) {
            return redirect()->route('password.request')
                ->withErrors(['email' => 'Silakan lakukan verifikasi kode OTP terlebih dahulu.']);
        }

        return view('auth.reset-password-otp', [
            'email' => $email,
        ]);
    }

    /**
     * Langkah 5: Simpan Password Baru ke Database
     */
    public function updatePassword(Request $request)
    {
        $email = session('reset_authorized_email');
        if (!$email) {
            return redirect()->route('password.request');
        }

        $request->validate([
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.required' => 'Silakan masukkan kata sandi baru.',
            'password.min' => 'Kata sandi minimal terdiri dari 8 karakter.',
            'password.confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ]);

        // Update password user
        $user = User::where('email', $email)->first();
        if ($user) {
            $user->password = Hash::make($request->password);
            $user->save();
        }

        // Hapus token reset dari database
        DB::table('password_reset_tokens')->where('email', $email)->delete();

        // Bersihkan session reset
        session()->forget(['otp_email', 'otp_sent_at', 'dev_otp_hint', 'reset_authorized_email', 'reset_auth_token']);

        return redirect()->route('login')
            ->with('status', 'Kata sandi berhasil diperbarui! Silakan masuk dengan kata sandi baru Anda.');
    }
}

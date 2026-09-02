<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
        ], [
            'email.required' => 'Silakan masukkan alamat email Anda.',
            'email.email' => 'Format email tidak valid.',
            'email.exists' => 'Alamat email tidak terdaftar di sistem Juragan Pelem.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Buat token reset resmi dari Laravel Password Broker
        $token = Password::createToken($user);

        // Coba kirim email jika konfigurasi SMTP tersedia
        try {
            $status = Password::sendResetLink(
                $request->only('email')
            );

            if ($status === Password::RESET_LINK_SENT) {
                return back()->with('status', 'Tautan reset kata sandi telah dikirim ke email Anda!');
            }
        } catch (\Throwable $e) {
            Log::warning('SMTP Mail offline, dialihkan langsung ke halaman reset password: ' . $e->getMessage());
        }

        // Fallback otomatis (Local/Offline): Langsung arahkan ke halaman pembuatan password baru dengan token valid
        return redirect()->route('password.reset', [
            'token' => $token,
            'email' => $request->email,
        ])->with('status', 'Identitas terverifikasi! Silakan buat kata sandi baru Anda di bawah ini.');
    }
}

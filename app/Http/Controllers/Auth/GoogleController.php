<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Exception;

class GoogleController extends Controller
{
    /**
     * Redirect ke halaman login Google.
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google setelah login.
     */
    public function callback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Cek apakah user sudah terdaftar
            $user = User::where('email', $googleUser->getEmail())->first();

            if (!$user) {
                // Simpan data sementara ke session untuk memilih role
                session(['google_user' => [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                ]]);

                // Arahkan ke halaman pemilihan role
                return redirect()->route('auth.google.role');
            }

            // Login langsung jika user sudah ada
            Auth::login($user);
            request()->session()->regenerate();

            return $this->redirectByRole($user);
        } catch (Exception $e) {
            return redirect()->route('login')->with('error', 'Login dengan Google gagal! ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman untuk memilih role setelah login Google pertama kali.
     */
    public function chooseRole()
    {
        if (!session('google_user')) {
            return redirect()->route('login')->with('error', 'Sesi Google sudah berakhir.');
        }
        return view('auth.choose-role');
    }

    /**
     * Menyimpan role dan buat akun baru.
     */
    public function saveRole(Request $request)
    {
        $request->validate(['role' => 'required|in:penjual,pembeli']);

        $googleData = session('google_user');

        if (!$googleData) {
            return redirect()->route('login')->with('error', 'Sesi Google sudah berakhir.');
        }

        // Simpan user baru
        $user = User::create([
            'name' => $googleData['name'],
            'email' => $googleData['email'],
            'password' => bcrypt(Str::random(16)),
            'role' => $request->role,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget('google_user');

        return $this->redirectByRole($user);
    }

    /**
     * Arahkan user berdasarkan role.
     */
    private function redirectByRole(User $user)
    {
        switch ($user->role) {
            case 'admin':
                return redirect()->route('admin.dashboard');
            case 'penjual':
                return redirect()->route('penjual.dashboard');
            case 'pembeli':
                return redirect()->route('pembeli.dashboard');
            default:
                Auth::logout();
                return redirect()->route('login')->with('error', 'Role pengguna tidak dikenali!');
        }
    }
}

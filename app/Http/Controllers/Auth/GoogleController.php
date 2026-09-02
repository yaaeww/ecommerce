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
     * Redirect ke halaman login Google (untuk website).
     */
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Redirect ke Google khusus Flutter mobile/web.
     */
    public function mobileRedirect()
    {
        session(['oauth_source' => 'flutter_web']);

        // Simpan URL tujuan kembali ke Flutter app (dikirim dari sisi aplikasi)
        $returnTo = request()->query('return_to');
        $mode = request()->query('mode', 'popup');

        if ($mode === 'redirect' || $returnTo) {
            session(['oauth_mode' => 'redirect']);
            if ($returnTo) {
                session(['oauth_return_to' => $returnTo]);
            }
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Callback dari Google setelah login (menangani Website dan Flutter Web).
     */
    public function callback(Request $request)
    {
        $isMobile = session('oauth_source') === 'flutter_web' || $request->query('state') === 'mobile_flutter';
        $isRedirectMode = session('oauth_mode') === 'redirect';

        try {
            $googleUser = Socialite::driver('google')->user();
            $user = User::where('email', $googleUser->getEmail())->first();

            if ($isMobile) {
                session()->forget('oauth_source');
                session()->forget('oauth_mode');

                if (!$user) {
                    $payload = [
                        'success'                 => true,
                        'requires_role_selection' => true,
                        'name'                    => $googleUser->getName(),
                        'email'                   => $googleUser->getEmail(),
                        'avatar'                  => $googleUser->getAvatar(),
                    ];
                    return $isRedirectMode
                        ? $this->redirectResponse($payload)
                        : $this->postMessageResponse($payload);
                }

                $token = $user->createToken('flutter_mobile')->plainTextToken;

                if ($user->role === 'penjual') {
                    $user->load('umkm');
                }

                $payload = [
                    'success'                 => true,
                    'requires_role_selection' => false,
                    'token'                   => $token,
                    'user'                    => [
                        'id'         => $user->id,
                        'name'       => $user->name,
                        'email'      => $user->email,
                        'role'       => $user->role,
                        'avatar'     => $user->avatar,
                        'no_telepon' => $user->no_telepon,
                    ],
                ];

                return $isRedirectMode
                    ? $this->redirectResponse($payload)
                    : $this->postMessageResponse($payload);
            }

            // Handler default untuk Website
            if (!$user) {
                session(['google_user' => [
                    'name'  => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                ]]);
                return redirect()->route('auth.google.role');
            }

            Auth::login($user);
            request()->session()->regenerate();

            return $this->redirectByRole($user);
        } catch (Exception $e) {
            if ($isMobile) {
                session()->forget('oauth_source');
                session()->forget('oauth_mode');
                $payload = [
                    'success' => false,
                    'error'   => 'Login Google gagal: ' . $e->getMessage(),
                ];
                return $isRedirectMode
                    ? $this->redirectResponse($payload)
                    : $this->postMessageResponse($payload);
            }

            return redirect()->route('login')->with('error', 'Login dengan Google gagal! ' . $e->getMessage());
        }
    }

    /**
     * Menampilkan halaman untuk memilih role setelah login Google pertama kali (Website).
     */
    public function chooseRole()
    {
        if (!session('google_user')) {
            return redirect()->route('login')->with('error', 'Sesi Google sudah berakhir.');
        }
        return view('auth.choose-role');
    }

    /**
     * Menyimpan role dan buat akun baru (Website).
     */
    public function saveRole(Request $request)
    {
        $request->validate(['role' => 'required|in:penjual,pembeli']);

        $googleData = session('google_user');

        if (!$googleData) {
            return redirect()->route('login')->with('error', 'Sesi Google sudah berakhir.');
        }

        $user = User::create([
            'name'     => $googleData['name'],
            'email'    => $googleData['email'],
            'password' => bcrypt(Str::random(16)),
            'role'     => $request->role,
        ]);

        Auth::login($user);
        $request->session()->regenerate();
        session()->forget('google_user');

        return $this->redirectByRole($user);
    }

    /**
     * Render halaman HTML yang mengirim data via window.postMessage lalu tutup popup.
     */
    private function postMessageResponse(array $data): \Illuminate\Http\Response
    {
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
        $html = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Menghubungkan Akun Google...</title>
  <style>
    body {
      font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
      margin: 0;
      background: #f8fafc;
      color: #334155;
    }
    .card {
      background: white;
      padding: 32px;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
      text-align: center;
      max-width: 320px;
    }
    .spinner {
      width: 44px;
      height: 44px;
      border: 4px solid #e2e8f0;
      border-top-color: #10b981;
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
      margin: 0 auto 16px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
    h3 { margin: 0 0 8px; font-size: 16px; font-weight: 700; color: #0f172a; }
    p { margin: 0; font-size: 13px; color: #64748b; }
  </style>
</head>
<body>
  <div class="card">
    <div class="spinner"></div>
    <h3>Menghubungkan Google</h3>
    <p>Sinkronisasi akun ke aplikasi...</p>
  </div>
  <script>
    var payload = {$json};
    function sendAndClose() {
      if (window.opener) {
        window.opener.postMessage({ type: 'GOOGLE_AUTH_RESULT', data: payload }, '*');
        setTimeout(function() { window.close(); }, 400);
      } else {
        document.querySelector('p').textContent = 'Selesai. Silakan tutup tab ini.';
      }
    }
    window.addEventListener('load', sendAndClose);
  </script>
</body>
</html>
HTML;
        return response($html, 200)->header('Content-Type', 'text/html');
    }

    /**
     * Redirect ke Flutter web app dengan data auth di URL params (untuk redirect flow di mobile).
     */
    private function redirectResponse(array $data): \Illuminate\Http\RedirectResponse
    {
        // URL kembali ke Flutter app (jika tidak ada, kembali ke root server)
        $returnTo = session('oauth_return_to');
        session()->forget('oauth_return_to');

        $encoded = base64_encode(json_encode($data, JSON_UNESCAPED_UNICODE));
        $sep = $returnTo && parse_url($returnTo, PHP_URL_QUERY) ? '&' : '?';
        $url = ($returnTo ?: url('/')) . $sep . 'google_auth=' . urlencode($encoded);

        return redirect($url);
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

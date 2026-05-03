<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use Illuminate\Support\Facades\Auth;

class Google2FAMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 🔥 FIX: Cari user di guard Admin, kalau gak ada cari di guard Siswa
        $user = Auth::guard('web')->user() ?? Auth::guard('pengguna')->user();

        // 1. Jika belum login, biarin aja (middleware Auth yang urus)
        if (!$user) {
            return $next($request);
        }

        // 2. Tentukan apakah 2FA aktif (Admin pake google2fa_enabled, Pengguna pake google2fa_secret)
        $is2faEnabled = false;
        if ($user instanceof \App\Models\User) {
            $is2faEnabled = (bool) $user->google2fa_enabled;
        } elseif ($user instanceof \App\Models\Pengguna) {
            $is2faEnabled = !empty($user->google2fa_secret);
            
            // 🔥 CCTV: Catat di log biar ketauan kenapa gak jalan
            \Log::info("2FA Debug Siswa: " . $user->username, [
                'has_secret_in_db' => !empty($user->getAttributes()['google2fa_secret']),
                'can_decrypt_secret' => $is2faEnabled ? 'YES' : 'NO',
            ]);
        }

        if (!$is2faEnabled) {
            // Biarkan lewat jika sedang di halaman setup, proses aktifasi, atau logout
            if ($request->is('admin/settings/security/2fa*') || $request->is('logout')) {
                return $next($request);
            }

            // Untuk Admin: Paksa ke halaman setup 2FA
            if ($user instanceof \App\Models\User) {
                return redirect()->route('admin.settings.2fa')->with('warning', 'Demi keamanan, Anda wajib mengaktifkan Google 2FA sebelum melanjutkan.');
            }

            // Untuk Siswa/Guru: Jika mereka gak punya secret, biarin lewat aja (tidak wajib)
            return $next($request);
        }

        // 3. Jika user SUDAH mengaktifkan 2FA
        
        // Jangan cegat jika sedang berada di halaman verifikasi 2FA atau sedang logout
        if ($request->is('2fa/verify') || $request->is('logout')) {
            return $next($request);
        }

        // Gunakan authenticator dari paket Google2FA
        $authenticator = app(Authenticator::class)->boot($request);

        // Jika sudah login tapi belum verifikasi 2FA di sesi ini
        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }

        // Lempar ke halaman verifikasi
        return redirect()->route('2fa.verify');
    }
}

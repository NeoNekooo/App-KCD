<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use PragmaRX\Google2FALaravel\Support\Authenticator;

class Google2FAMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // 1. Jika belum login, biarin aja (middleware Auth yang urus)
        if (!$user) {
            return $next($request);
        }

        // 2. Jika user BELUM mengaktifkan 2FA
        if (!$user->google2fa_enabled) {
            // Biarkan lewat jika sedang di halaman setup, proses aktifasi, atau logout
            if ($request->is('admin/settings/security/2fa*') || $request->is('logout')) {
                return $next($request);
            }

            // Paksa ke halaman setup 2FA
            return redirect()->route('admin.settings.2fa')->with('warning', 'Demi keamanan, Anda wajib mengaktifkan Google 2FA sebelum melanjutkan.');
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
        return response()->view('auth.2fa_verify');
    }
}

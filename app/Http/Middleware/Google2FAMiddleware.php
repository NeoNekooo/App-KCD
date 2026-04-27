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

        // Jika user belum login atau tidak mengaktifkan 2FA, loloskan
        if (!$user || !$user->google2fa_enabled) {
            return $next($request);
        }

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

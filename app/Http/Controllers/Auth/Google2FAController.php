<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use PragmaRX\Google2FALaravel\Support\Authenticator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Google2FAController extends Controller
{
    /**
     * Tampilkan halaman verifikasi OTP saat login
     */
    public function showVerifyForm()
    {
        return view('auth.2fa_verify');
    }

    /**
     * Proses verifikasi kode OTP saat login
     */
    public function verify(Request $request)
    {
        $authenticator = app(Authenticator::class)->boot($request);

        if ($authenticator->isAuthenticated()) {
            return redirect()->intended(route('home'));
        }

        return redirect()->back()->withErrors(['one_time_password' => 'Kode OTP salah atau sudah kadaluwarsa.']);
    }

    /**
     * Tampilkan halaman pengaturan 2FA (Enable/Disable)
     */
    public function showSettings()
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');
        
        $qrCodeUrl = null;
        if (!$user->google2fa_secret) {
            // Generate secret baru jika belum punya
            $user->google2fa_secret = $google2fa->generateSecretKey();
            $user->save();
        }

        $qrCodeUrl = $google2fa->getQRCodeUrl(
            'KCD Wilayah VI',
            $user->email,
            $user->google2fa_secret
        );

        return view('admin.settings.2fa', [
            'user' => $user,
            'qrCodeUrl' => $qrCodeUrl
        ]);
    }

    /**
     * Aktifkan 2FA setelah user berhasil scan dan masukin kode pertama kali
     */
    public function enable(Request $request)
    {
        $user = Auth::user();
        $google2fa = app('pragmarx.google2fa');

        $secret = $user->google2fa_secret;
        $valid = $google2fa->verifyKey($secret, $request->one_time_password);

        if ($valid) {
            $user->google2fa_enabled = true;
            $user->save();
            return redirect()->back()->with('success', 'Google Authenticator berhasil diaktifkan!');
        }

        return redirect()->back()->withErrors(['one_time_password' => 'Kode verifikasi salah.']);
    }

    /**
     * Matikan 2FA
     */
    public function disable(Request $request)
    {
        $user = Auth::user();
        $user->google2fa_enabled = false;
        $user->save();

        return redirect()->back()->with('success', 'Google Authenticator dinonaktifkan.');
    }
}

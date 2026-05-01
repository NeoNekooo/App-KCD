<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Menampilkan halaman form login.
     * INI BAGIAN YANG HILANG DAN SUDAH DITAMBAHKAN KEMBALI
     */
    public function create(): View
    {
        // Arahkan ke view login kustom Anda
        return view('auth.login-custom');
    }

    /**
     * Menangani permintaan otentikasi yang masuk.
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();
        $request->session()->regenerate();
    
        // Cek login dari guard mana
        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $isAdmin = in_array(strtolower($user->role ?? ''), ['admin', 'administrator', 'operator kcd']);
            $redirectUrl = $isAdmin ? route('admin.dashboard') : route('admin.dashboard.pegawai');
        } else {
            // Login via guard 'pengguna' (Siswa/Guru)
            $redirectUrl = route('admin.dashboard.sekolah');
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'redirect' => $redirectUrl
            ]);
        }
        
        return redirect()->intended($redirectUrl);
    }



    /**
     * Hancurkan sesi otentikasi (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        Auth::guard('pengguna')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

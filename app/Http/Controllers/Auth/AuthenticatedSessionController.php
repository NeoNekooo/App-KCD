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
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
    
        $request->session()->regenerate();
    
        return redirect()->intended(route('admin.dashboard'));
    }



    /**
     * Hancurkan sesi otentikasi (logout).
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}

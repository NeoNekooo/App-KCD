<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Tentukan apakah user diizinkan membuat request ini.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi input login.
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            // Validasi tahun_pelajaran dihapus karena inputnya di-hide/dihapus di view
            // 'tahun_pelajaran' => ['required', 'string'], 
        ];
    }

    /**
     * Proses autentikasi user.
     */
    public function authenticate(): void
    {
        // 1. Cek Rate Limiter
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('username', 'password');

        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            
            // Jika gagal, catat percobaan login
            RateLimiter::hit($this->throttleKey());

            // Jika sudah mencapai batas (3x), langsung lempar error throttle
            if (RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
                $this->ensureIsNotRateLimited();
            }

            // Jika belum limit, lempar error kustom bahasa Indonesia
            throw ValidationException::withMessages([
                'username' => 'Username atau Password yang Anda masukkan salah.',
            ]);
        }

        // Jika login berhasil
        $user = Auth::user();
        RateLimiter::clear($this->throttleKey());

        // SETUP SESSION
        session([
            'role'             => $user->role,
            'user_id'          => $user->id,
            'nama'             => $user->name,
            'sub_role'         => null,
            'ptk_id'           => null,
            'peserta_didik_id' => null,
            'tahun_pelajaran'  => '2025/2026 Ganjil',
        ]);
    }

    /**
     * Memastikan user tidak terkena limit login (terlalu banyak percobaan gagal).
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => "Terlalu banyak percobaan login. Silakan coba lagi dalam $seconds detik.",
        ]);
    }

    /**
     * Key unik untuk Rate Limiter (berdasarkan username + IP address).
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->input('username')).'|'.$this->ip());
    }
}
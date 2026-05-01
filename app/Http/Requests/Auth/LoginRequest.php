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
        $this->ensureIsNotRateLimited();

        $credentials = $this->only('username', 'password');
        $remember = $this->boolean('remember');

        // 1. Coba Login sebagai User KCD (Tabel users)
        if (Auth::guard('web')->attempt($credentials, $remember)) {
            $user = Auth::guard('web')->user();
            Auth::shouldUse('web'); // Set default guard ke web
            $this->setupSession($user, 'web');
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // 2. Coba Login sebagai Pengguna (Tabel penggunas - Guru/Siswa)
        if (Auth::guard('pengguna')->attempt($credentials, $remember)) {
            $user = Auth::guard('pengguna')->user();
            Auth::shouldUse('pengguna'); // Set default guard ke pengguna
            $this->setupSession($user, 'pengguna');
            RateLimiter::clear($this->throttleKey());
            return;
        }

        // Jika dua-duanya gagal
        RateLimiter::hit($this->throttleKey());
        if (RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            $this->ensureIsNotRateLimited();
        }

        throw ValidationException::withMessages([
            'username' => 'Username atau Password yang Anda masukkan salah.',
        ]);
    }

    /**
     * Setup session data setelah login berhasil.
     */
    protected function setupSession($user, $guard): void
    {
        if ($guard === 'web') {
            session([
                'role'             => $user->role,
                'user_id'          => $user->id,
                'nama'             => $user->name,
                'sub_role'         => null,
                'ptk_id'           => null,
                'peserta_didik_id' => null,
                'tahun_pelajaran'  => '2025/2026 Ganjil',
                'guard'            => 'web',
            ]);
        } else {
            // Tentukan role berdasarkan peran_id_str
            // Contoh: peran_id_str 2 biasanya Guru, 0 biasanya Siswa (tergantung sistem sinkron)
            $role = 'tamu';
            if ($user->peran_id_str == '2') $role = 'guru';
            elseif ($user->peran_id_str == '0') $role = 'siswa';

            session([
                'role'             => $role,
                'user_id'          => $user->pengguna_id,
                'nama'             => $user->username, // Atau kolom nama jika ada di tabel penggunas
                'sub_role'         => $user->peran_id_str,
                'ptk_id'           => $user->ptk_id ?? null,
                'peserta_didik_id' => $user->peserta_didik_id ?? null,
                'tahun_pelajaran'  => '2025/2026 Ganjil',
                'sekolah_id'       => $user->sekolah_id,
                'guard'            => 'pengguna',
            ]);
        }
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
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
        // 1. Cek Rate Limiter (Mencegah Brute Force Attack)
        $this->ensureIsNotRateLimited();

        // 2. Ambil kredensial (username & password)
        $credentials = $this->only('username', 'password');

        // 3. PROSES LOGIN (Menggunakan Tabel 'users')
        // Auth::attempt otomatis mengecek ke tabel users, mencocokkan username,
        // dan memverifikasi password yang di-hash.
        if (! Auth::attempt($credentials, $this->boolean('remember'))) {
            
            // Jika gagal, catat percobaan login
            RateLimiter::hit($this->throttleKey());

            // Lempar error validasi ke view
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        // 4. Jika login berhasil, ambil data user
        $user = Auth::user();

        // 5. Bersihkan counter Rate Limiter
        RateLimiter::clear($this->throttleKey());

        // 6. SETUP SESSION (Penting untuk hak akses di aplikasi)
        // Kita set session berdasarkan data dari tabel users
        session([
            'role'             => $user->role, // Role: 'Admin', 'Kepala', 'Staff', dll.
            'user_id'          => $user->id,
            'nama'             => $user->name,
            // Nilai default untuk kompatibilitas sistem lama (bisa disesuaikan nanti)
            'sub_role'         => null,
            'ptk_id'           => null,
            'peserta_didik_id' => null,
            'tahun_pelajaran'  => '2025/2026 Ganjil', // Default Tapel
        ]);
    }

    /**
     * Memastikan user tidak terkena limit login (terlalu banyak percobaan gagal).
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
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
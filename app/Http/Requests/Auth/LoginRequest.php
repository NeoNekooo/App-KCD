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
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        // === BAGIAN YANG DIPERBAIKI 1 ===
        // Mengganti 'email' menjadi 'username' dan menghapus validasi format email.
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
<<<<<<< HEAD
            'tahun_pelajaran' => ['required', 'string'], // Pastikan validasi tahun pelajaran tetap ada jika diperlukan
=======
>>>>>>> origin/modul/kepegawaian
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        // === BAGIAN YANG DIPERBAIKI 2 ===
        // Menggunakan 'username' untuk percobaan login dan menampilkan pesan error.
        if (! Auth::attempt($this->only('username', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'username' => trans('auth.failed'), // Tampilkan error di field 'username'
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        // Tampilkan error di field yang benar jika terlalu banyak percobaan
        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        // === BAGIAN YANG DIPERBAIKI 3 ===
        // Menggunakan 'username' untuk kunci rate limiter.
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}
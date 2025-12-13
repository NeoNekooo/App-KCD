<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use App\Models\Pengguna; // pastikan modelnya benar

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
            'tahun_pelajaran' => ['required', 'string'],
        ];
    }

    public function authenticate()
    {
        $credentials = $this->only('username', 'password');

        // Ambil user berdasarkan username
        $user = Pengguna::where('username', $credentials['username'])->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'username' => trans('auth.failed'),
            ]);
        }

        // Cek apakah password sudah di-hash atau masih plain text
        if (Hash::needsRehash($user->password)) {
            // Password masih plain text → bandingkan langsung
            if ($credentials['password'] !== $user->password) {
                throw ValidationException::withMessages([
                    'username' => trans('auth.failed'),
                ]);
            }

            // Rehash password otomatis
            $user->password = Hash::make($credentials['password']);
            $user->save();
        } else {
            // Password sudah di-hash, pakai Hash::check
            if (!Hash::check($credentials['password'], $user->password)) {
                throw ValidationException::withMessages([
                    'username' => trans('auth.failed'),
                ]);
            }
        }

        // Login user
        Auth::login($user, $this->boolean('remember'));

        // Ambil role dari peran_id_str
        $role = $user->peran_id_str;
            
        // Jika PTK, ambil sub_role dari gtks
        $sub_role = null;
        $gtk_id = null;
        if ($role === 'PTK' && $user->ptk_id) {
            $gtk = \DB::table('gtks')
                ->where('ptk_id', $user->ptk_id)
                ->first();
            $sub_role = $gtk->jenis_ptk_id_str;
            
        }
        
        // Set session
        session([
            'role' => $role,
            'sub_role' => $sub_role,
            'ptk_id'   => $user->ptk_id,
        ]);

    }

    public function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) return;

        event(new Lockout($this));
        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')) . '|' . $this->ip());
    }

    protected function guard()
    {
        return Auth::guard('web');
    }
}

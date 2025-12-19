<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use App\Models\Siswa;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'nik' => ['required', 'digits:16', 'exists:siswas,nik'],
        'email' => ['required', 'email', 'max:255', 'unique:penggunas,username'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
    ]);

    $siswa = Siswa::where('nik', $request->nik)->firstOrFail();

    $sudahAdaWali = Pengguna::where('peserta_didik_id', $siswa->peserta_didik_id)
        ->where('peran_id_str', 'wali')
        ->exists();

    if ($sudahAdaWali) {
        return back()->withErrors([
            'nik' => 'Siswa ini sudah memiliki akun wali'
        ])->withInput();
    }

    Pengguna::create([
        'pengguna_id'       => (string) Str::uuid(),
        'username'          => $request->email,
        'password'          => Hash::make($request->password),
        'peran_id_str'      => 'wali',
        'peserta_didik_id'  => $siswa->peserta_didik_id,
    ]);

    return redirect()
        ->route('login')
        ->with('success', 'Registrasi berhasil. Silakan login.');
}




    public function cekNik(Request $request)
    {
        $request->validate([
            'nik' => ['required', 'digits:16']
        ]);

        $siswa = Siswa::where('nik', $request->nik)->first();

        if (!$siswa) {
            return response()->json([
                'status' => false,
                'message' => 'Siswa dengan NIK tersebut tidak ditemukan'
            ], 200);
        }

        return response()->json([
            'status' => true,
            'data' => [
                'nama' => $siswa->nama,
                'kelas' => $siswa->nama_rombel ?? '-',
                'tanggal_lahir' => $siswa->tanggal_lahir,
            ]
        ], 200);
    }



}
